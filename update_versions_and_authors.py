import os
import re

# 파일 경로 및 새 버전 설정
FILES = {
    'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php': '8.0.0',
    'acf-css-neural-link/acf-css-neural-link.php': '3.9.9',
    'acf-css-ai-extension/acf-css-ai-extension.php': '2.0.5',
    'marketing/wordpress-plugins/acf-css-woo-license/acf-css-woo-license.php': '2.0.0',
    'marketing/wordpress-plugins/wp-bulk-installer/wp-bulk-installer.php': '2.2.2',
    'marketing/wordpress-plugins/admin-menu-editor-lite/admin-menu-editor-lite.php': '2.0.0',
    'marketing/wordpress-plugins/acf-css-brevo-integration/acf-css-brevo-integration.php': '2.0.0',
    'marketing/wordpress-theme/acf-css-landing/style.css': '2.0.0'
}

AUTHOR_OLD_PATTERNS = [
    r'Author:\s+Jay & Jenny Labs',
    r'Author:\s+J&J Labs',
    r'Author:\s+JJ Labs',
    r'Author: Jay & Jenny Labs',
    r'Author: J&J Labs',
    r'Author: JJ Labs'
]

NEW_AUTHOR = 'Author:            3J Labs'
CREDIT_COMMENT = '\n * Created by:        Jay & Jason & Jenny'

def update_file(path, new_version):
    if not os.path.exists(path):
        print(f"❌ File not found: {path}")
        return

    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Author 수정
    for pattern in AUTHOR_OLD_PATTERNS:
        content = re.sub(pattern, NEW_AUTHOR, content)
    
    # 2. Version 수정
    content = re.sub(r'Version:\s*[\d\.]+', f'Version:           {new_version}', content)
    
    # 3. 상수 버전 수정 (PHP 파일만)
    if path.endswith('.php'):
        content = re.sub(r"define\(\s*'[^']+_VERSION',\s*'[^']+'\s*\);", lambda m: re.sub(r"'[^']+'", f"'{new_version}'", m.group(0)), content)

    # 4. Credit 추가 (Author 밑에 없으면 추가)
    if 'Created by:' not in content:
        content = content.replace(NEW_AUTHOR, NEW_AUTHOR + CREDIT_COMMENT)

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ Updated {os.path.basename(path)} to v{new_version}")

if __name__ == "__main__":
    print("🚀 Starting Update Sequence...")
    for path, ver in FILES.items():
        update_file(path, ver)
    print("🎉 Update Complete.")

