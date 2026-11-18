const { contextBridge, ipcRenderer } = require("electron");
contextBridge.exposeInMainWorld("portal", {
  retry: () => ipcRenderer.invoke("portal:retry"),
  startXampp: () => ipcRenderer.invoke("xampp:open"),
});
