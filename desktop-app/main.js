const { app, BrowserWindow, Tray, Menu } = require('electron');
const path = require('node:path');
const config = require('./config/app');

let mainWindow;
let tray;

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1000,
        height: 530,
        webPreferences: {
            nodeIntegration: true,
            contextIsolation: false,
        },
        icon: path.join(__dirname, 'public/logo.png'),
    });

    const startURL = config.auth_token ? 'src/pages/dashboard/home/index.html' : 'src/pages/authentication/login.html';
    mainWindow.loadFile(path.join(__dirname, startURL));

    mainWindow.on('close', (event) => {
        if (!app.isQuitting) {
            event.preventDefault();
            mainWindow.hide();
        }
    });
}

function createTray() {
    tray = new Tray(path.join(__dirname, 'public/logo.png'));

    const contextMenu = Menu.buildFromTemplate([
        { label: 'Show App', click: () => mainWindow.show() },
        { label: 'Quit', click: () => {
            app.isQuitting = true;
            app.quit();
        }},
    ]);

    tray.setToolTip('Work Pattern');
    tray.setContextMenu(contextMenu);

    tray.on('click', () => {
        mainWindow.show();
    });
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
