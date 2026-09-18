const { contextBridge } = require('electron');

contextBridge.exposeInMainWorld('corelDesktop', {
  isDesktop: true,
  platform: process.platform
});
