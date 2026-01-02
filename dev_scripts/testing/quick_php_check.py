#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""빠른 PHP 문법 검사"""

import subprocess
import sys
from pathlib import Path

def find_php():
    import shutil
    return shutil.which('php') or 'php'

def check_file(php_bin, file_path):
    try:
        result = subprocess.run(
            [php_bin, '-l', str(file_path)],
            capture_output=True,
            text=True,
            timeout=5
        )
        return result.returncode == 0, result.stdout.strip()
    except:
        return None, "Timeout or error"

def main():
    php_bin = find_php()
    
    # 주요 파일만 빠르게 검사
    key_files = [
        'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php',
        'acf-css-ai-extension/acf-css-ai-extension.php',
        'acf-css-neural-link/acf-css-neural-link.php',
    ]
    
    base = Path(__file__).parent
    errors = []
    
    for f in key_files:
        file_path = base / f
        if file_path.exists():
            ok, msg = check_file(php_bin, file_path)
            if ok is True:
                print(f"✓ {file_path.name}")
            elif ok is False:
                print(f"✗ {file_path.name}")
                errors.append((f, msg))
            else:
                print(f"? {file_path.name} (검사 불가)")
    
    if errors:
        print(f"\n❌ {len(errors)}개 오류 발견")
        sys.exit(1)
    else:
        print("\n✅ 모든 주요 파일 검사 통과!")
        sys.exit(0)

if __name__ == '__main__':
    main()
