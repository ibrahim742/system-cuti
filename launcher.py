import json
import os
import shutil
import socket
import subprocess
import sys
import tempfile
import threading
import time
import urllib.request
import webbrowser
import zipfile

GITHUB_OWNER = "ibrahim742"
GITHUB_REPO = "system-cuti"
API_URL = f"https://api.github.com/repos/{GITHUB_OWNER}/{GITHUB_REPO}/releases/latest"

BIND_HOST = "0.0.0.0"
PORT = 8000

server_proc = None
server_lock = threading.Lock()


def get_base_dir():
    if getattr(sys, "frozen", False):
        return os.path.dirname(sys.executable)
    return os.path.dirname(os.path.abspath(__file__))


BASE_DIR = get_base_dir()
LOG_FILE = os.path.join(BASE_DIR, "launcher.log")


def write_log(msg):
    try:
        with open(LOG_FILE, "a", encoding="utf-8") as f:
            f.write(f"[{time.strftime('%Y-%m-%d %H:%M:%S')}] {msg}\n")
    except Exception:
        pass


def find_laragon_php():
    php_root = r"D:\laragon\bin\php"
    if os.path.isdir(php_root):
        for entry in sorted(os.listdir(php_root), reverse=True):
            candidate = os.path.join(php_root, entry, "php.exe")
            if os.path.isfile(candidate):
                return candidate
    return "php"


LOCAL_LARAGON_APP = r"D:\laragon\www\system-cuti"
DIST_APP_DIR = os.path.join(BASE_DIR, "app")
APP_DIR = DIST_APP_DIR if os.path.isdir(DIST_APP_DIR) else LOCAL_LARAGON_APP

DIST_PHP_EXE = os.path.join(BASE_DIR, "php", "php.exe")
PHP_EXE = DIST_PHP_EXE if os.path.isfile(DIST_PHP_EXE) else find_laragon_php()

VERSION_FILE = os.path.join(BASE_DIR, "version.json")


def get_local_ip():
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    try:
        s.settimeout(1.0)
        s.connect(("8.8.8.8", 80))
        ip = s.getsockname()[0]
    except Exception:
        try:
            ip = socket.gethostbyname(socket.gethostname())
        except Exception:
            ip = "127.0.0.1"
    finally:
        s.close()
    return ip


def is_port_open(host, port):
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.settimeout(0.5)
        return s.connect_ex((host, port)) == 0


def start_laravel_server():
    global server_proc
    with server_lock:
        public_dir = os.path.join(APP_DIR, "public")
        if not os.path.isdir(public_dir):
            write_log(f"ERROR: Folder public tidak ditemukan di {public_dir}")
            return

        check_host = "127.0.0.1" if BIND_HOST == "0.0.0.0" else BIND_HOST
        if is_port_open(check_host, PORT):
            write_log(f"Port {PORT} sudah aktif.")
            return

        creation_flags = subprocess.CREATE_NO_WINDOW if os.name == "nt" else 0
        log_handle = open(LOG_FILE, "a", encoding="utf-8")
        try:
            server_proc = subprocess.Popen(
                [PHP_EXE, "-S", f"{BIND_HOST}:{PORT}", "-t", public_dir],
                cwd=APP_DIR,
                creationflags=creation_flags,
                stdout=log_handle,
                stderr=log_handle,
            )
            write_log(f"Menjalankan PHP server (PID={server_proc.pid}) menggunakan '{PHP_EXE}' di {BIND_HOST}:{PORT}")
        except Exception as e:
            write_log(f"ERROR saat menjalankan PHP server: {e}")
            log_handle.close()


def stop_laravel_server():
    global server_proc
    with server_lock:
        if server_proc is not None and server_proc.poll() is None:
            write_log(f"Menghentikan PHP server (PID={server_proc.pid}) untuk proses replace file...")
            server_proc.terminate()
            try:
                server_proc.wait(timeout=5)
            except subprocess.TimeoutExpired:
                server_proc.kill()
        server_proc = None


def open_browser_when_ready():
    local_ip = get_local_ip()
    url = f"http://{local_ip}:{PORT}"
    target_host = local_ip if local_ip != "127.0.0.1" else "127.0.0.1"

    for _ in range(40):
        if is_port_open(target_host, PORT) or is_port_open("127.0.0.1", PORT):
            write_log(f"Server siap, membuka browser ke {url}")
            webbrowser.open(url)
            return
        time.sleep(0.5)

    write_log(f"Timeout menunggu port {PORT}, membuka browser ke {url}")
    webbrowser.open(url)


def get_local_version():
    if not os.path.exists(VERSION_FILE):
        return "0.0.0"
    with open(VERSION_FILE, "r", encoding="utf-8") as f:
        return json.load(f).get("version", "0.0.0")


def set_local_version(ver):
    with open(VERSION_FILE, "w", encoding="utf-8") as f:
        json.dump({"version": ver}, f)


def replace_app_files(source_dir):
    entries = os.listdir(source_dir)
    if len(entries) == 1 and os.path.isdir(os.path.join(source_dir, entries[0])):
        source_dir = os.path.join(source_dir, entries[0])

    protected_files = [
        ".env",
        os.path.join("database", "database.sqlite"),
    ]
    protected_dirs = [
        os.path.join("storage", "app", "public"),
    ]

    with tempfile.TemporaryDirectory() as backup_dir:
        backed_up_files = {}
        for rel_path in protected_files:
            src_file = os.path.join(APP_DIR, rel_path)
            if os.path.isfile(src_file):
                dst_backup = os.path.join(backup_dir, "files", rel_path)
                os.makedirs(os.path.dirname(dst_backup), exist_ok=True)
                shutil.copy2(src_file, dst_backup)
                backed_up_files[rel_path] = dst_backup

        backed_up_dirs = {}
        for rel_dir in protected_dirs:
            src_dir = os.path.join(APP_DIR, rel_dir)
            if os.path.isdir(src_dir):
                dst_backup_dir = os.path.join(backup_dir, "dirs", rel_dir)
                os.makedirs(os.path.dirname(dst_backup_dir), exist_ok=True)
                shutil.copytree(src_dir, dst_backup_dir, dirs_exist_ok=True)
                backed_up_dirs[rel_dir] = dst_backup_dir

        if os.path.isdir(APP_DIR):
            for item in os.listdir(APP_DIR):
                item_path = os.path.join(APP_DIR, item)
                if os.path.isdir(item_path):
                    shutil.rmtree(item_path)
                else:
                    os.remove(item_path)
        else:
            os.makedirs(APP_DIR, exist_ok=True)

        shutil.copytree(source_dir, APP_DIR, dirs_exist_ok=True)

        for rel_path, bkp_file in backed_up_files.items():
            restored_path = os.path.join(APP_DIR, rel_path)
            os.makedirs(os.path.dirname(restored_path), exist_ok=True)
            shutil.copy2(bkp_file, restored_path)

        for rel_dir, bkp_dir in backed_up_dirs.items():
            restored_dir = os.path.join(APP_DIR, rel_dir)
            os.makedirs(os.path.dirname(restored_dir), exist_ok=True)
            shutil.copytree(bkp_dir, restored_dir, dirs_exist_ok=True)


def check_and_apply_update():
    if os.path.abspath(APP_DIR) != os.path.abspath(DIST_APP_DIR):
        return

    try:
        req = urllib.request.Request(API_URL, headers={"User-Agent": "SystemCuti-Launcher"})
        with urllib.request.urlopen(req, timeout=10) as resp:
            data = json.loads(resp.read().decode("utf-8"))

        latest_tag = data.get("tag_name", "").lstrip("v")
        current_ver = get_local_version()

        if not latest_tag or latest_tag == current_ver:
            return

        download_url = None
        for asset in data.get("assets", []):
            if asset.get("name", "").endswith(".zip"):
                download_url = asset.get("browser_download_url")
                break

        if not download_url:
            return

        write_log(f"Update ditemukan: {latest_tag} (versi saat ini: {current_ver}). Mengunduh...")

        with tempfile.TemporaryDirectory() as tmpdir:
            zip_path = os.path.join(tmpdir, "update.zip")
            extract_dir = os.path.join(tmpdir, "extracted")

            urllib.request.urlretrieve(download_url, zip_path)
            with zipfile.ZipFile(zip_path, "r") as zf:
                zf.extractall(extract_dir)

            stop_laravel_server()
            replace_app_files(extract_dir)

            creation_flags = subprocess.CREATE_NO_WINDOW if os.name == "nt" else 0
            artisan = os.path.join(APP_DIR, "artisan")
            if os.path.isfile(artisan):
                subprocess.run([PHP_EXE, artisan, "migrate", "--force"], cwd=APP_DIR, creationflags=creation_flags)
                subprocess.run([PHP_EXE, artisan, "optimize:clear"], cwd=APP_DIR, creationflags=creation_flags)

            set_local_version(latest_tag)
            write_log(f"Replace file selesai. Aplikasi sekarang di versi {latest_tag}.")
            start_laravel_server()
    except Exception as e:
        write_log(f"Gagal melakukan auto-update: {e}")
        start_laravel_server()


if __name__ == "__main__":
    start_laravel_server()
    threading.Thread(target=open_browser_when_ready, daemon=True).start()
    threading.Thread(target=check_and_apply_update, daemon=True).start()

    while True:
        time.sleep(60)  