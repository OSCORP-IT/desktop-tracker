const { app, BrowserWindow, Tray, Menu, ipcMain } = require('electron');
const axios = require('axios');
const activeWin = require('active-win');
const FormData = require('form-data');
const fs = require('node:fs');
const path = require('node:path');
const screenshot = require('screenshot-desktop');
const sharp = require('sharp');
const sqlite3 = require('sqlite3').verbose();

let mainWindow;
let tray;
let authToken = null;

let db;
let lastApp = null;
let lastTitle = null;

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

    const contextMenu = Menu.buildFromTemplate([
        {
            label: 'Show App',
            click: () => mainWindow.show()
        },
        {
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

function initializeDatabase() {
    const dbPath = path.join(__dirname, 'tracking_app.db');

    db = new sqlite3.Database(dbPath, (err) => {
        if (err) {
            console.error('Failed to connect to SQLite database:', err.message);
            return;
        }
        console.log('Connected to SQLite database');
    });

    db.serialize(() => {
        db.run(`
            CREATE TABLE IF NOT EXISTS application_activities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                attendance_id TEXT,
                app_name TEXT,
                app_title TEXT,
                total_second INTEGER DEFAULT 0,
                uploaded INTEGER DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        `, (err) => {
            if (err) {
                console.error('Failed to create table:', err.message);
            }
        });

        // Create indexes for performance
        db.run(`CREATE INDEX IF NOT EXISTS idx_attendance_id ON application_activities(attendance_id)`);
        db.run(`CREATE INDEX IF NOT EXISTS idx_uploaded ON application_activities(uploaded)`);
    });
}

async function applicationTracking() {
    if (!attendanceState.attendanceId || !attendanceState.isCheckedIn || attendanceState.isOnBreak) {
        setTimeout(applicationTracking, 1000);
        return;
    }

    try {
        const result = await activeWin();

        if (result) {
            let app = result.owner.name;
            let title = result.title;

            if (title.endsWith(` - ${app}`)) {
                title = title.replace(` - ${app}`, '');
            }

            mainWindow.webContents.send('window-changed', { app, title });

            db.serialize(() => {
                db.run('BEGIN TRANSACTION');
                db.get(`
                    SELECT id FROM application_activities 
                    WHERE attendance_id = ? AND app_name = ? AND app_title = ?
                `, [attendanceState.attendanceId, app, title], (err, row) => {
                    if (err) {
                        console.error('Error querying database:', err.message);
                        db.run('ROLLBACK');
                        return;
                    }

                    if (row) {
                        db.run(`
                            UPDATE application_activities 
                            SET total_second = total_second + 1 
                            WHERE id = ?
                        `, [row.id], (err) => {
                            if (err) {
                                console.error('Error updating total second:', err.message);
                                db.run('ROLLBACK');
                            } else {
                                db.run('COMMIT');
                            }
                        });
                    } else {
                        db.run(`
                            INSERT INTO application_activities (attendance_id, app_name, app_title, total_second, uploaded)
                            VALUES (?, ?, ?, 1, 0)
                        `, [attendanceState.attendanceId, app, title], (err) => {
                            if (err) {
                                console.error('Error inserting into database:', err.message);
                                db.run('ROLLBACK');
                            } else {
                                db.run('COMMIT');
                            }
                        });
                    }
                });
            });

            lastApp = app;
            lastTitle = title;
        }
    } catch (err) {
        console.error('Error in applicationTracking:', err.message);
    }

    setTimeout(applicationTracking, 1000);
}

function startActivityUploader() {
    async function uploadActivities() {
        if (!authToken) {
            console.log('No auth token, skipping upload.');
            return;
        }

        db.all(`
            SELECT id, attendance_id, app_name, app_title, total_second
            FROM application_activities
            WHERE total_second > 0
        `, async (err, rows) => {
            if (err) {
                console.error('Error querying application_activities:', err.message);
                return;
            }

            if (rows.length === 0) {
                console.log('No activities to upload.');
                return;
            }

            const activities = rows.map(row => ({
                attendance_id: row.attendance_id,
                app_name: row.app_name,
                app_title: row.app_title || null,
                total_second: row.total_second
            }));

            const maxRetries = 3;
            let retryCount = 0;

            while (retryCount < maxRetries) {
                try {
                    const response = await axios.post(
                        'https://worksuite.abidurrahman.com/api/user-panel/application-activity-upload',
                        { application_activities: activities },
                        {
                            headers: {
                                Authorization: `Bearer ${authToken}`,
                                'Content-Type': 'application/json'
                            }
                        }
                    );

                    if (response.data.success) {
                        const ids = rows.map(row => row.id);
                        db.serialize(() => {
                            db.run('BEGIN TRANSACTION');
                            // Reset total_second and mark as uploaded
                            db.run(`
                                UPDATE application_activities 
                                SET total_second = 0, uploaded = 1 
                                WHERE id IN (${ids.map(() => '?').join(',')})
                            `, ids, (err) => {
                                if (err) {
                                    console.error('Error resetting total_second and marking as uploaded:', err.message);
                                    db.run('ROLLBACK');
                                    return;
                                }
                                console.log(`Reset total_second and marked ${ids.length} records as uploaded`);

                                // Delete old records with uploaded = 1 and created_at < 7 days ago
                                db.run(`
                                    DELETE FROM application_activities 
                                    WHERE uploaded = 1 AND created_at < datetime('now', '-7 days')
                                `, (err) => {
                                    if (err) {
                                        console.error('Error deleting old uploaded records:', err.message);
                                        db.run('ROLLBACK');
                                    } else {
                                        db.get(`SELECT CHANGES() AS deleted_rows`, (err, result) => {
                                            if (err) {
                                                console.error('Error checking deleted rows:', err.message);
                                            } 
                                            db.run('COMMIT');
                                        });
                                    }
                                });
                            });
                        });
                        break;
                    } else {
                        console.error('Upload failed:', response.data.message, response.data.errors || {});
                    }
                } catch (err) {
                    console.error('Error uploading activities:', err.message, err.response?.data);
                    retryCount++;
                    if (retryCount === maxRetries) {
                        console.error('Max retries reached, skipping upload');
                    }
                    await new Promise(resolve => setTimeout(resolve, 2000));
                }
            }
        });
    }

    setInterval(uploadActivities, (10 * 60 * 1000));
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
            } catch (err) {
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
                        `https://worksuite.abidurrahman.com/api/user-panel/screenshot-upload/${parsedAttendanceId}`,
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
                } 
                catch (err) {
                    console.error(`Error uploading ${file}:`, err.message, err.response?.data);
                }
            }
        }
    }

    setInterval(uploadScreenshots, (10 * 60 * 1000));
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
    initializeDatabase();
    applicationTracking();
    startActivityUploader();
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
    if (db) {
        db.close((err) => {
            if (err) {
                console.error('Error closing database:', err.message);
            } else {
                console.log('Database closed.');
            }
        });
    }

    app.isQuitting = true;
});
