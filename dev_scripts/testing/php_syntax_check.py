#!/usr/bin/env python3
"""
PHP Syntax Checker for 3J Labs Plugins
모든 PHP 파일의 문법 오류를 검사합니다.
"""

import subprocess
import os
from pathlib import Path

def check_php_syntax(file_path):
    """Check PHP syntax for a single file"""
    try:
        result = subprocess.run(
            ['php', '-l', str(file_path)],
            capture_output=True,
            text=True,
            timeout=30
        )
        if 'No syntax errors' in result.stdout:
            return True, None
        else:
            return False, result.stdout + result.stderr
    except Exception as e:
        return False, str(e)

def main():
    base_path = Path(r'C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS')

    # 검사할 플러그인 폴더들
    plugin_folders = [
        'acf-css-really-simple-style-management-center-master',
        'acf-css-really-simple-style-management-center-free',
        'acf-css-neural-link',
        'wp-bulk-manager',
        'acf-code-snippets-box',
        'acf-css-ai-extension',
        'acf-css-woocommerce-toolkit',
        'acf-nudge-flow',
        'admin-menu-editor-pro',
        'jj-analytics-dashboard',
        'jj-marketing-automation-dashboard',
        'acf-css-woo-license',
        'shared-ui-assets',
    ]

    errors_found = []
    total_files = 0
    passed_files = 0

    print("=" * 60)
    print("PHP Syntax Check - 3J Labs Plugins")
    print("=" * 60)

    for folder in plugin_folders:
        folder_path = base_path / folder
        if not folder_path.exists():
            print(f"\n[SKIP] {folder} - 폴더 없음")
            continue

        print(f"\n[CHECK] {folder}")

        php_files = list(folder_path.rglob('*.php'))

        for php_file in php_files:
            total_files += 1
            success, error = check_php_syntax(php_file)

            if success:
                passed_files += 1
            else:
                rel_path = php_file.relative_to(base_path)
                errors_found.append((str(rel_path), error))
                print(f"  [ERROR] {rel_path}")

    print("\n" + "=" * 60)
    print("Results Summary")
    print("=" * 60)
    print(f"Total files checked: {total_files}")
    print(f"Passed: {passed_files}")
    print(f"Errors: {len(errors_found)}")

    if errors_found:
        print("\n" + "=" * 60)
        print("Error Details")
        print("=" * 60)
        for file_path, error in errors_found:
            print(f"\n{file_path}:")
            print(f"  {error}")
    else:
        print("\nAll PHP files passed syntax check!")

    return len(errors_found) == 0

if __name__ == '__main__':
    import sys
    success = main()
    sys.exit(0 if success else 1)
