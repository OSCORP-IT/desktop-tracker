const { app, BrowserWindow, Tray, Menu, ipcMain } = require('electron');
const axios = require('axios');
const FormData = require('form-data');
const fs = require('node:fs');
const path = require('node:path');
const screenshot = require('screenshot-desktop');
const sharp = require('sharp');

let mainWindow;
let tray;
let authToken = null;

let attendanceState = {
    attendanceId: null,
    isCheckedIn: false,
    checkInTime: null,
    activeStartTime: null,
    totalActiveTime: 0,
    totalBreakTime: 0,
    isOnBreak: false,
    breakStartTime: null,
    serverTimeOffset: 0
};

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1000,
        height: 530,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        },
        icon: path.join(__dirname, 'public/images/logo.png'),
    });

    mainWindow.loadFile(path.join(__dirname, 'pages/user_panel/dashboard.html'));

    mainWindow.on('close', (event) => {
        if (!app.isQuitting) {
            event.preventDefault();
            mainWindow.hide();
        }
    });
}

function createTray() {
    tray = new Tray(path.join(__dirname, 'public/images/logo.png'));

    const contextMenu = Menu.buildFromTemplate([{
            label: 'Show App', 
            click: () => mainWindow.show() 
        }, {
            label: 'Quit',
            click: () => {
                app.isQuitting = true;
                app.quit();
            },
        },
    ]);

    tray.setToolTip('Tracking App');
    tray.setContextMenu(contextMenu);

    tray.on('click', () => {
        mainWindow.show();
    });
}

function startScreenshotLoop() {
    const screenshotsDir = path.join(__dirname, 'public/screenshots');

    if (!fs.existsSync(screenshotsDir)) {
        fs.mkdirSync(screenshotsDir, { recursive: true });
    }

    async function loop() {
        const delayMinutes = Math.floor(Math.random() * 6) + 5;
        const delayMs = delayMinutes * 60 * 1000;

        if (attendanceState.attendanceId && attendanceState.isCheckedIn && !attendanceState.isOnBreak) {
            const now = new Date();
            const bdOffset = 6 * 60 * 60 * 1000;
            const bdTime = new Date(now.getTime() + bdOffset);
            const timestamp = bdTime.toISOString().replace(/[:T]/g, '-').split('.')[0];
            const filename = `${attendanceState.attendanceId}-screenshot-${timestamp}.jpg`;
            const outputScreenshotPath = path.join(screenshotsDir, filename);

            try {
                const displays = await screenshot.listDisplays();

                const screenshots = await Promise.all(
                    displays.map((display) =>
                        screenshot({ format: 'jpg', screen: display.id }).then((img) => ({
                            img,
                            width: display.width,
                            height: display.height,
                        }))
                    )
                );

                const totalWidth = screenshots.reduce((sum, screen) => sum + screen.width, 0);
                const maxHeight = Math.max(...screenshots.map((screen) => screen.height));

                const compositeImages = [];
                let offsetX = 0;
                for (const { img, width } of screenshots) {
                    const tempPath = path.join(__dirname, `temp-${timestamp}-${offsetX}.jpg`);
                    fs.writeFileSync(tempPath, img);
                    compositeImages.push({ input: tempPath, left: offsetX, top: 0 });
                    offsetX += width;
                }

                await sharp({
                    create: {
                        width: totalWidth,
                        height: maxHeight,
                        channels: 3,
                        background: { r: 0, g: 0, b: 0 },
                    },
                })
                .composite(compositeImages)
                .jpeg({ quality: 10 })
                .toFile(outputScreenshotPath);

                compositeImages.forEach(({ input }) => fs.unlinkSync(input));
            } 
            catch (err) {
                console.error('Failed to take combined screenshot:', err);
            }
        }

        setTimeout(loop, delayMs);
    }

    loop();
}

function startScreenshotUploader() {
    const screenshotsDir = path.join(__dirname, 'public/screenshots');

    async function uploadScreenshots() {
        if (authToken) {
            const files = fs.readdirSync(screenshotsDir).filter(file => /\.(jpg|jpeg|png)$/i.test(file));
            
            for (const file of files) {
                const filePath = path.join(screenshotsDir, file);
                const form = new FormData();
        
                try {
                    const baseName = path.basename(file, path.extname(file));
                    const [idPart, ...rest] = baseName.split('-');
        
                    const parsedAttendanceId = idPart;
                    const screenshotTime = `${rest[1]}-${rest[2]}-${rest[3]}T${rest[4]}:${rest[5]}:${rest[6]}`;
        
                    form.append('screenshot_time', screenshotTime);
                    form.append('screenshot_image', fs.createReadStream(filePath));
        
                    const response = await axios.post(
                        `http://localhost:8000/api/user-panel/screenshot-upload/${parsedAttendanceId}`,
                        form,
                        {
                            headers: {
                                ...form.getHeaders(),
                                Authorization: `Bearer ${authToken}`,
                            }
                        }
                    );
        
                    if (response.data.success) {
                        fs.unlinkSync(filePath);
                    } else {
                        console.error(`Upload failed for ${file}:`, response.data.message);
                    }
        
                } catch (err) {
                    console.error(`Error uploading ${file}:`, err.message);
                }
            }
        }
    }

    setInterval(uploadScreenshots, ((10 * 60) * 1000));
}

ipcMain.on('request-attendance-state', (event) => {
    event.reply('update-attendance-state', attendanceState);
});

ipcMain.on('update-attendance-state', (event, updatedState) => {
    attendanceState = { ...attendanceState, ...updatedState };
});

ipcMain.on('auth-token', (event, token) => {
    authToken = token;
});

app.whenReady().then(() => {
    createWindow();
    createTray();
    startScreenshotLoop();
    startScreenshotUploader();

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});

app.on('window-all-closed', (event) => {
    if (process.platform !== 'darwin') {
        event.preventDefault();
    }
});

app.on('before-quit', () => {
    app.isQuitting = true;
});
