import os
import shutil
import re

# 설정
SOURCE_DIR = 'acf-css-really-simple-style-management-center-license-update-manager'
TARGET_DIR = 'acf-css-neural-link'
MAIN_FILE = 'acf-css-neural-link.php'

# 1. 폴더 복사
if os.path.exists(TARGET_DIR):
    shutil.rmtree(TARGET_DIR)
shutil.copytree(SOURCE_DIR, TARGET_DIR)
print(f"Created new project folder: {TARGET_DIR}")

# 2. 메인 파일 리네임
old_main = os.path.join(TARGET_DIR, 'acf-css-really-simple-license-manager.php')
new_main = os.path.join(TARGET_DIR, MAIN_FILE)
if os.path.exists(old_main):
    os.rename(old_main, new_main)
    print(f"Renamed main file to: {MAIN_FILE}")

# 3. 메인 파일 내용 수정 (리브랜딩)
with open(new_main, 'r', encoding='utf-8') as f:
    content = f.read()

# Header 수정
header_replacements = {
    'Plugin Name:       ACF CSS: License & Update Manager': 'Plugin Name:       ACF CSS: Neural Link (License & Update Manager)',
    'Description:       .*': 'Description:       ACF CSS 플러그인의 라이센스 인증, 자동 업데이트, 원격 제어를 담당하는 신경망(Neural Link) 플러그인입니다.',
    'Version:           .*': 'Version:           3.0.0',
    'Text Domain:       .*': 'Text Domain:       acf-css-neural-link',
}

for pattern, replacement in header_replacements.items():
    content = re.sub(pattern, replacement, content)

# 클래스명 변경 (JJ_License_Manager -> JJ_Neural_Link)
# 주의: 메인 플러그인에도 JJ_License_Manager가 있으므로 충돌 방지 위해 변경 필수
content = content.replace('class JJ_License_Manager', 'class JJ_Neural_Link')
content = content.replace('new JJ_License_Manager', 'new JJ_Neural_Link')
content = content.replace('JJ_License_Manager::instance', 'JJ_Neural_Link::instance')

# 상수 변경
content = content.replace('JJ_LICENSE_MANAGER_VERSION', 'JJ_NEURAL_LINK_VERSION')
content = content.replace('JJ_LICENSE_MANAGER_PATH', 'JJ_NEURAL_LINK_PATH')
content = content.replace('JJ_LICENSE_MANAGER_URL', 'JJ_NEURAL_LINK_URL')

with open(new_main, 'w', encoding='utf-8') as f:
    f.write(content)

print("Project Neural Link initialized.")

