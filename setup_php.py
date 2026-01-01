import os
import urllib.request
import zipfile
import pathlib
import sys
import shutil

def install_php():
    # Define paths
    home_dir = pathlib.Path(os.environ['USERPROFILE'])
    dest_dir = home_dir / 'php_cli'
    url = 'https://windows.php.net/downloads/releases/latest/php-8.2-nts-Win32-vs16-x64.zip'
    zip_path = home_dir / 'php_temp.zip'

    # Clean up previous attempts
    if dest_dir.exists():
        print(f"Directory {dest_dir} exists. Cleaning up...")
        try:
            shutil.rmtree(dest_dir)
        except Exception as e:
            print(f"Warning: Could not delete existing directory: {e}")

    print(f"1. Creating directory: {dest_dir}")
    dest_dir.mkdir(parents=True, exist_ok=True)

    print(f"2. Downloading PHP from {url}...")
    try:
        # User-Agent header is sometimes needed
        req = urllib.request.Request(
            url, 
            data=None, 
            headers={
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
            }
        )
        with urllib.request.urlopen(req) as response, open(zip_path, 'wb') as out_file:
            shutil.copyfileobj(response, out_file)
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

    # Verify
    php_exe = dest_dir / 'php.exe'
    if php_exe.exists():
        print(f"SUCCESS: PHP installed at {php_exe}")
        # Clean up zip
        try:
            os.remove(zip_path)
        except:
            pass
        return True
    else:
        print("ERROR: php.exe not found after extraction.")
        return False

if __name__ == '__main__':
    if install_php():
        sys.exit(0)
    else:
        sys.exit(1)
