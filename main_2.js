const { app, BrowserWindow, Tray, Menu, ipcMain, desktopCapturer } = require('electron');
const axios = require('axios');
const activeWin = require('active-win');
const FormData = require('form-data');
const fs = require('node:fs');
const path = require('node:path');
const sharp = require('sharp');

const ACTIVITY_FILE = path.join(__dirname, 'public', 'application_activities.json');
const SCREENSHOTS_DIR = path.join(__dirname, 'public', 'screenshots');

let activity_data = {
    attendance_id: 1,
    is_checked_in: true,
    check_in_time: null,
    active_start_time: null,
    total_active_time: 0,
    total_break_time: 0,
    is_on_break: true,
    break_start_time: null,
    server_time_offset: 0,
    is_screenshot_image_blur: false
};

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1000,
        height: 530,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        },
        icon: path.join(__dirname, 'public/images/icon.png'),
    });

    Menu.setApplicationMenu(null);

    mainWindow.loadFile(path.join(__dirname, 'pages/dashboard.html'));

    mainWindow.on('close', (event) => {
        if (!app.isQuitting) {
            event.preventDefault();
            mainWindow.hide();
        }
    });
}

function createTray() {
    let tray = new Tray(path.join(__dirname, 'public/images/icon.png'));

    const contextMenu = Menu.buildFromTemplate([ {
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

    tray.setToolTip('Desktop Tracker');
    tray.setContextMenu(contextMenu);

    tray.on('click', () => {
        mainWindow.show();
    });
}

async function record_current_window_activity(attendance_id) {
    try {
        const window = await activeWin();
        if (!window) return null;

        let appName = window.owner?.name || 'Unknown';
        let title = window.title || 'Unknown';

        const appNameSuffix = ` - ${appName}`;
        if (title.endsWith(appNameSuffix)) {
            title = title.slice(0, -appNameSuffix.length);
        }

        let activities = [];
        if (fs.existsSync(ACTIVITY_FILE)) {
            try {
                const data = fs.readFileSync(ACTIVITY_FILE, 'utf-8');
                activities = data ? JSON.parse(data) : [];
            } catch (err) {
                console.warn('Failed to parse existing JSON. Starting fresh.');
                activities = [];
            }
        }

        const activity_format = {
            attendance_id: attendance_id,
            app_name: appName,
            app_title: title,
            total_second: 1,
            is_already_uploaded: false
        };

        const existingIndex = activities.findIndex(
            (act) =>
                act.attendance_id === activity_format.attendance_id &&
                act.app_name === activity_format.app_name &&
                act.app_title === activity_format.app_title
        );

        if (existingIndex >= 0) {
            activities[existingIndex].total_second += 1;
            activities[existingIndex].is_already_uploaded = false;
        } else {
            activities.push(activity_format);
        }

        try {
            fs.writeFileSync(ACTIVITY_FILE, JSON.stringify(activities, null, 2));
        } catch (err) {
            console.error('Failed to write JSON file:', err);
        }

        return activity_format;
    } catch (error) {
        console.error('Failed to record activity:', error);
        return null;
    }
}

async function take_display_screenshot(attendance_id) {
    try {
        const now = new Date();
        const bdOffset = 6 * 60 * 60 * 1000;
        const bdTime = new Date(now.getTime() + bdOffset);
        const timestamp = bdTime.toISOString().replace(/[:T]/g, '-').split('.')[0];

        if (!fs.existsSync(SCREENSHOTS_DIR)) fs.mkdirSync(SCREENSHOTS_DIR, { recursive: true });

        const output_path = path.join(SCREENSHOTS_DIR, `${attendance_id}-screenshot-${timestamp}.jpg`);

        const sources = await desktopCapturer.getSources({
            types: ['screen'],
            thumbnailSize: { width: 3840, height: 2160 }
        });

        const capturedBuffers = [];

        for (const source of sources) {
            const image = source.thumbnail;
            if (!image.isEmpty()) {
                capturedBuffers.push({
                    input: image.toPNG(),
                    width: image.getSize().width,
                    height: image.getSize().height
                });
            }
        }

        if (capturedBuffers.length === 0) {
            throw new Error('No screens captured');
        }

        const totalWidth = capturedBuffers.reduce((sum, buf) => sum + buf.width, 0);
        const maxHeight = Math.max(...capturedBuffers.map(buf => buf.height));

        const compositeImages = [];
        let offsetX = 0;
        for (const buf of capturedBuffers) {
            compositeImages.push({ input: buf.input, left: offsetX, top: 0 });
            offsetX += buf.width;
        }

        await sharp({
            create: {
                width: totalWidth,
                height: maxHeight,
                channels: 3,
                background: { r: 0, g: 0, b: 0 }
            }
        })
        .composite(compositeImages)
        .jpeg({ quality: 1 })
        .toFile(output_path);

        return output_path;

    } catch (error) {
        console.error('Failed to take multi-display screenshot:', error);
        return null;
    }
}

app.whenReady().then(() => {
    createWindow();
    createTray();

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
