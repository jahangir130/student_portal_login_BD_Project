const { app, BrowserWindow, ipcMain, shell } = require("electron");
const { spawn } = require("child_process");
const fs = require("fs");
const path = require("path");
const http = require("http");

const PORTAL_URL = "http://127.0.0.1/student_admin_portal_v2/index.php";
const XAMPP_PATH_FILE = path.join(app.getPath("userData"), "xampp-path.txt");
let mainWindow;
let xamppProcess;

function readXamppPath() {
  if (
    process.env.XAMPP_CONTROL_EXE &&
    fs.existsSync(process.env.XAMPP_CONTROL_EXE)
  )
    return process.env.XAMPP_CONTROL_EXE;
  if (fs.existsSync(XAMPP_PATH_FILE)) {
    const saved = fs.readFileSync(XAMPP_PATH_FILE, "utf8").trim();
    if (saved && fs.existsSync(saved)) return saved;
  }
  const candidates = [
    "C:\\xampp\\xampp-control.exe",
    "D:\\xampp\\xampp-control.exe",
    "C:\\Program Files\\xampp\\xampp-control.exe",
  ];
  return candidates.find((p) => fs.existsSync(p)) || null;
}

function isPortalOnline() {
  return new Promise((resolve) => {
    const req = http.get(
      "http://127.0.0.1/student_admin_portal_v2/index.php",
      (res) => {
        res.resume();
        resolve(res.statusCode >= 200 && res.statusCode < 500);
      },
    );
    req.setTimeout(1800, () => {
      req.destroy();
      resolve(false);
    });
    req.on("error", () => resolve(false));
  });
}

function landingPage(
  message = "Start Apache and MySQL in XAMPP, then select Retry.",
) {
  const safeMessage = String(message).replace(
    /[&<>"']/g,
    (c) =>
      ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[
        c
      ],
  );
  return `<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>BUBT Student Admin Portal</title><style>
  *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:linear-gradient(135deg,#edf5f8,#e8efff);font-family:Segoe UI,Arial,sans-serif;color:#17324a}.card{width:min(560px,calc(100% - 32px));background:#fff;border:1px solid #dce7ee;border-radius:22px;padding:34px;box-shadow:0 22px 70px #16364d18}.logo{width:58px;height:58px;border-radius:16px;background:#e4f3f1;color:#087f8c;display:grid;place-items:center;font-weight:800;font-size:19px}.eyebrow{margin:22px 0 8px;color:#087f8c;font-size:11px;font-weight:800;letter-spacing:1.6px}h1{font-size:28px;margin:0 0 10px}p{line-height:1.65;color:#607487}.status{padding:13px 15px;background:#fff7e7;border:1px solid #f3dfb6;border-radius:11px;color:#77520c;font-size:13px;margin:20px 0}.actions{display:flex;gap:10px;flex-wrap:wrap}button{border:0;border-radius:10px;padding:12px 16px;font-weight:700;cursor:pointer;background:#087f8c;color:white}button.secondary{background:#eaf0f5;color:#23445e}small{display:block;color:#8493a1;margin-top:22px;line-height:1.6}</style></head><body><main class="card"><div class="logo">BUBT</div><div class="eyebrow">LOCAL DESKTOP APPLICATION</div><h1>Student Admin Portal</h1><p>The desktop window is ready. The local PHP website needs Apache and MySQL from XAMPP to be running.</p><div class="status">${safeMessage}</div><div class="actions"><button id="start">Open XAMPP Control Panel</button><button class="secondary" id="retry">Retry Connection</button></div><small>Expected website: ${PORTAL_URL}<br>Keep this desktop app open while using the portal.</small></main><script>document.getElementById('start').addEventListener('click',async()=>{const r=await window.portal.startXampp();alert(r.message)});document.getElementById('retry').addEventListener('click',async()=>{const ok=await window.portal.retry();if(!ok)alert('Portal not available yet. Check that Apache and MySQL are running in XAMPP.')});</script></body></html>`;
}

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1360,
    height: 900,
    minWidth: 980,
    minHeight: 680,
    title: "BUBT Student Admin Portal",
    backgroundColor: "#edf3f7",
    webPreferences: {
      preload: path.join(__dirname, "preload.js"),
      contextIsolation: true,
      nodeIntegration: false,
      sandbox: true,
    },
  });
  mainWindow.loadURL(
    "data:text/html;charset=utf-8," + encodeURIComponent(landingPage()),
  );
  connectPortal();
  mainWindow.webContents.setWindowOpenHandler(({ url }) => {
    if (
      url.startsWith("http://127.0.0.1/") ||
      url.startsWith("http://localhost/")
    )
      return { action: "allow" };
    shell.openExternal(url);
    return { action: "deny" };
  });
  mainWindow.webContents.on("will-navigate", (event, url) => {
    if (
      !url.startsWith("http://127.0.0.1/") &&
      !url.startsWith("http://localhost/") &&
      !url.startsWith("data:text/html")
    ) {
      event.preventDefault();
      shell.openExternal(url);
    }
  });
}

async function connectPortal() {
  if (!mainWindow || mainWindow.isDestroyed()) return;
  const online = await isPortalOnline();
  if (online) mainWindow.loadURL(PORTAL_URL);
}

ipcMain.handle("portal:retry", async () => {
  const online = await isPortalOnline();
  if (online && mainWindow && !mainWindow.isDestroyed())
    await mainWindow.loadURL(PORTAL_URL);
  return online;
});

ipcMain.handle("xampp:open", async () => {
  const exe = readXamppPath();
  if (!exe)
    return {
      ok: false,
      message:
        "xampp-control.exe was not found. Install XAMPP or set XAMPP_CONTROL_EXE.",
    };
  try {
    xamppProcess = spawn(exe, [], { detached: true, stdio: "ignore" });
    xamppProcess.unref();
    return {
      ok: true,
      message:
        "XAMPP Control Panel opened. Start Apache and MySQL, then click Retry Connection.",
    };
  } catch (e) {
    return { ok: false, message: e.message };
  }
});

app.whenReady().then(createWindow);
app.on("activate", () => {
  if (BrowserWindow.getAllWindows().length === 0) createWindow();
});
app.on("window-all-closed", () => {
  if (process.platform !== "darwin") app.quit();
});
