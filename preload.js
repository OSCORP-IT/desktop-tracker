const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('electronAPI', {
    requestAttendanceState: () => ipcRenderer.send('request-attendance-state'),
    onUpdateAttendanceState: (callback) => ipcRenderer.on('update-attendance-state', (event, state) => callback(state)),
    updateAttendanceState: (state) => ipcRenderer.send('update-attendance-state', state),
    sendAuthToken: (token) => ipcRenderer.send('auth-token', token),
});
