import shutil
import sys
import subprocess
import os

print("--- SYSTEM DIAGNOSTIC ---")

# 1. PHP 찾기
php_path = shutil.which('php')
print(f"Detected PHP path: {php_path}")

# 2. Scoop 경로 확인 (백업)
scoop_php = os.path.expanduser('~/scoop/shims/php.exe')
if os.path.exists(scoop_php):
    print(f"Scoop PHP path: {scoop_php}")
else:
    print("Scoop PHP not found")

# 3. 실행 테스트
target_php = php_path or scoop_php
if target_php and os.path.exists(target_php):
    try:
        result = subprocess.run([target_php, '-v'], capture_output=True, text=True, timeout=5)
        print("\nPHP Version Output:")
        print(result.stdout)
    except Exception as e:
        print(f"\nPHP execution failed: {e}")
else:
    print("\nNO WORKING PHP FOUND.")
