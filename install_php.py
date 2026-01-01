import os
import urllib.request
import zipfile
import pathlib
import sys

def install_php():
    url = 'https://windows.php.net/downloads/releases/latest/php-8.2-nts-Win32-vs16-x64.zip'
    dest_dir = pathlib.Path(r'C:\tools\php82')
    zip_path = pathlib.Path(os.environ.get('TEMP', '.')) / 'php82.zip'

    print(f"1. Creating directory: {dest_dir}")
    dest_dir.mkdir(parents=True, exist_ok=True)

    print(f"2. Downloading PHP from {url}...")
    try:
        urllib.request.urlretrieve(url, zip_path)
        print(f"   Downloaded to {zip_path}")
    except Exception as e:
        print(f"   Download failed: {e}")
        return False

    print(f"3. Extracting to {dest_dir}...")
    try:
        with zipfile.ZipFile(zip_path, 'r') as z:
            z.extractall(dest_dir)
        print("   Extraction complete.")
    except Exception as e:
        print(f"   Extraction failed: {e}")
        return False

    php_exe = dest_dir / 'php.exe'
    if php_exe.exists():
        print(f"SUCCESS: PHP installed at {php_exe}")
        return True
    else:
        print("ERROR: php.exe not found after extraction.")
        return False

if __name__ == '__main__':
    if install_php():
        sys.exit(0)
    else:
        sys.exit(1)
