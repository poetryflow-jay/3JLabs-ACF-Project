import os
import shutil
import zipfile
import re
from datetime import datetime

# ============================================================
# 설정 (Configuration)
# ============================================================
VERSION_CORE = '8.0.0'
VERSION_NEURAL = '3.9.9'
VERSION_AI = '2.0.5'
VERSION_WOO = '2.0.0'

BASE_OUTPUT_DIR = os.path.join(os.environ['USERPROFILE'], 'Desktop', f'JJ_Distributions_v{VERSION_CORE}_Final')

# 소스 디렉토리 맵핑
SOURCES = {
    'core': 'acf-css-really-simple-style-management-center-master',
    'ai': 'acf-css-ai-extension',
    'neural': 'acf-css-neural-link',
    'woo': 'marketing/wordpress-plugins/acf-css-woo-license',
    'bulk': 'marketing/wordpress-plugins/wp-bulk-installer',
    'menu': 'marketing/wordpress-plugins/admin-menu-editor-lite'
}

# Core Edition 목록 (파일명 접미사 및 내부 코드 치환용)
CORE_EDITIONS = {
    'free':      {'suffix': '',             'license': 'FREE'},
    'basic':     {'suffix': '-Pro-Basic',   'license': 'BASIC'},
    'premium':   {'suffix': '-Pro-Premium', 'license': 'PREMIUM'},
    'unlimited': {'suffix': '-Pro-Unlimited','license': 'UNLIMITED'},
    'partner':   {'suffix': '-Partner',     'license': 'PARTNER'},
    'master':    {'suffix': '-Master',      'license': 'MASTER'}
}

# 제외할 파일/폴더 패턴 (소스 루트 기준 상대 경로로 매칭)
EXCLUDE_PATTERNS = [
    r'^\.git', r'^\.vscode', r'^\.idea', r'__pycache__', r'\.DS_Store$',
    r'^tests', r'^phpunit\.xml', r'^composer\.json', r'node_modules',
    r'^package\.json', r'^package-lock\.json', r'^gulpfile\.js', 
    r'^\.editorconfig', r'^README\.md', r'\.bak$', r'local-server/venv',
]

# ============================================================
# 유틸리티 함수
# ============================================================
def clean_create_dir(path):
    if os.path.exists(path):
        try:
            shutil.rmtree(path, ignore_errors=True)
        except: pass
    os.makedirs(path, exist_ok=True)

def copy_files(src, dst, excludes=None):
    if excludes is None: excludes = EXCLUDE_PATTERNS
    
    print(f"  - Copying from {src} to {dst}...")
    count = 0
    for root, dirs, files in os.walk(src):
        # 소스 루트 기준 상대 경로 계산
        rel_root = os.path.relpath(root, src)
        if rel_root == ".": rel_root = ""
        
        # 제외 폴더 필터링 (패턴은 rel_root 기준)
        dirs[:] = [d for d in dirs if not any(re.search(p, os.path.join(rel_root, d).replace('\\', '/')) for p in excludes)]
        
        for file in files:
            rel_file_path = os.path.join(rel_root, file).replace('\\', '/')
            if any(re.search(p, rel_file_path) for p in excludes):
                continue
                
            src_file = os.path.join(root, file)
            dst_file = os.path.join(dst, rel_file_path)
            
            os.makedirs(os.path.dirname(dst_file), exist_ok=True)
            shutil.copy2(src_file, dst_file)
            count += 1
    print(f"    ✓ Copied {count} files.")
    
    # 필수 폴더 체크 (Core의 경우)
    if 'acf-css-manager' in dst and count < 10:
        print(f"    ⚠️ WARNING: Very few files copied to {dst}. Potential exclusion bug!")

def create_zip(source, zip_path, folder_name_in_zip):
    print(f"  - Zipping {source} to {zip_path} (Folder: {folder_name_in_zip})")
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(source):
            for file in files:
                file_path = os.path.join(root, file)
                # ZIP 내부의 상대 경로: folder_name_in_zip / relative_path_from_source
                rel_path = os.path.relpath(file_path, source)
                arcname = os.path.join(folder_name_in_zip, rel_path)
                zipf.write(file_path, arcname)

# ============================================================
# 빌드 실행
# ============================================================
def main():
    print("=" * 60)
    print(f"🚀 3J Labs Deployment System (Robust Build)")
    print(f"   Target: {BASE_OUTPUT_DIR}")
    print("=" * 60)
    
    clean_create_dir(BASE_OUTPUT_DIR)
    temp_dir = os.path.join(BASE_OUTPUT_DIR, "temp")
    os.makedirs(temp_dir, exist_ok=True)
    
    # 1. Core Editions
    print("\n📦 [Core] Building Editions...")
    for edition, config in CORE_EDITIONS.items():
        print(f"  Processing {edition}...")
        
        # 폴더명 결정
        folder_name = f"acf-css-manager-{edition}" if edition != 'free' else "acf-css-manager"
        work_dir = os.path.join(temp_dir, folder_name)
        
        # 파일 복사
        copy_files(SOURCES['core'], work_dir)
        
        # 메인 파일 수정
        main_file = os.path.join(work_dir, 'acf-css-really-simple-style-guide.php')
        if os.path.exists(main_file):
            with open(main_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # 플러그인 이름 변경
            name_suffix = ""
            if edition != 'free':
                if edition == 'master': name_suffix = " (Master)"
                elif edition in ['basic', 'premium', 'unlimited']: name_suffix = " PRO"
                else: name_suffix = f" ({edition.capitalize()})"
            
            new_name = f"ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager{name_suffix}"
            content = re.sub(r"Plugin Name:.*", f"Plugin Name:       {new_name}", content)
            
            # 상수 변경
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );", content)
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{config['license']}' );", content)
            
            with open(main_file, 'w', encoding='utf-8') as f:
                f.write(content)
        
        # ZIP 생성
        zip_name = f"ACF-CSS{config['suffix']}-v{VERSION_CORE}.zip"
        create_zip(work_dir, os.path.join(BASE_OUTPUT_DIR, zip_name), folder_name)

    # 2. Add-ons & Infrastructure
    addons = [
        ('ai', 'ACF-CSS-AI-Extension', VERSION_AI, 'acf-css-ai-extension'),
        ('neural', 'ACF-CSS-Neural-Link', VERSION_NEURAL, 'acf-css-neural-link'),
        ('woo', 'ACF-CSS-Woo-License', VERSION_WOO, 'acf-css-woo-license'),
        ('bulk', 'WP-Bulk-Manager', '2.2.2', 'wp-bulk-installer'),
        ('menu', 'Admin-Menu-Editor-Lite', '2.0.0', 'admin-menu-editor-lite')
    ]
    
    for key, zip_base, ver, target_folder in addons:
        print(f"\n📦 [{key.upper()}] Building...")
        src = SOURCES[key]
        work_dir = os.path.join(temp_dir, target_folder)
        
        # 복사 (Add-on의 경우 소스 경로 자체가 marketing을 포함하므로 제외 패턴이 rel_root 기준임을 보장)
        copy_files(src, work_dir)
        
        # ZIP 생성
        zip_name = f"{zip_base}-v{ver}.zip"
        create_zip(work_dir, os.path.join(BASE_OUTPUT_DIR, zip_name), target_folder)

    # Cleanup
    try:
        shutil.rmtree(temp_dir, ignore_errors=True)
    except: pass
    
    print("\n" + "=" * 60)
    print(f"🎉 All Systems Go! Build Verified.")
    print("=" * 60)

if __name__ == '__main__':
    main()
