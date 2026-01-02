import os
import shutil
import zipfile
import re
from datetime import datetime

# ============================================================
# 설정 (Configuration)
# ============================================================
VERSION = '3.2.0'  # Neural Link와 동기화된 버전
SOURCE_DIR = 'acf-css-really-simple-style-management-center-master'
OUTPUT_DIR = os.path.join(os.environ['USERPROFILE'], 'Desktop', 'JJ_Distributions_v3.2')

# 빌드할 에디션 목록
EDITIONS = [
    'free', 
    'basic', 
    'premium', 
    'partner', 
    'master'
]

# 제외할 파일/폴더 (Clean Build)
EXCLUDE_PATTERNS = [
    r'\.git',
    r'\.vscode',
    r'\.idea',
    r'__pycache__',
    r'\.DS_Store',
    r'tests',
    r'phpunit\.xml',
    r'composer\.json',
    r'node_modules',
    r'package\.json',
    r'package-lock\.json',
    r'gulpfile\.js',
    r'\.editorconfig',
    r'README\.md', # 플러그인 루트의 개발용 리드미 제외
]

# ============================================================
# 빌드 함수
# ============================================================
def clean_create_dir(path):
    if os.path.exists(path):
        try:
            shutil.rmtree(path, ignore_errors=True)
        except:
            pass
    os.makedirs(path, exist_ok=True)

def copy_files(src, dst):
    for root, dirs, files in os.walk(src):
        # 제외 폴더 필터링
        dirs[:] = [d for d in dirs if not any(re.search(p, d) for p in EXCLUDE_PATTERNS)]
        
        for file in files:
            if any(re.search(p, file) for p in EXCLUDE_PATTERNS):
                continue
                
            src_file = os.path.join(root, file)
            rel_path = os.path.relpath(src_file, src)
            dst_file = os.path.join(dst, rel_path)
            
            os.makedirs(os.path.dirname(dst_file), exist_ok=True)
            shutil.copy2(src_file, dst_file)

def update_main_file(work_dir, edition):
    """메인 플러그인 파일의 버전과 에디션 정보를 업데이트"""
    main_file = os.path.join(work_dir, 'acf-css-really-simple-style-guide.php')
    
    if not os.path.exists(main_file):
        print(f"⚠️ Warning: Main file not found in {work_dir}")
        return

    with open(main_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. 버전 업데이트
    content = re.sub(
        r"Version:\s*[\d\.]+", 
        f"Version:           {VERSION}", 
        content
    )
    content = re.sub(
        r"define\(\s*'JJ_STYLE_GUIDE_VERSION',\s*'[^']+'\s*\);", 
        f"define( 'JJ_STYLE_GUIDE_VERSION', '{VERSION}' );", 
        content
    )
    
    # 2. 에디션 상수 업데이트
    content = re.sub(
        r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);", 
        f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );", 
        content
    )
    
    # 3. 라이선스 타입 업데이트 (대문자)
    content = re.sub(
        r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);", 
        f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{edition.upper()}' );", 
        content
    )

    with open(main_file, 'w', encoding='utf-8') as f:
        f.write(content)

def create_zip(source, zip_path):
    """폴더를 ZIP으로 압축"""
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(source):
            for file in files:
                file_path = os.path.join(root, file)
                arcname = os.path.relpath(file_path, os.path.dirname(source))
                zipf.write(file_path, arcname)

# ============================================================
# 메인 실행
# ============================================================
def main():
    print("=" * 60)
    print(f"🚀 JJ CSS Manager Build System v{VERSION}")
    print("=" * 60)
    
    clean_create_dir(OUTPUT_DIR)
    
    for edition in EDITIONS:
        print(f"\n📦 Building [{edition}] edition...")
        
        # 1. 작업 폴더 준비
        work_dir_name = f"acf-css-really-simple-style-management-center-{edition}"
        work_dir = os.path.join(OUTPUT_DIR, "temp", work_dir_name)
        
        # 2. 파일 복사
        copy_files(SOURCE_DIR, work_dir)
        
        # 3. 코드 수정 (버전/에디션 주입)
        update_main_file(work_dir, edition)
        
        # 4. ZIP 압축
        zip_name = f"acf-css-{edition}-v{VERSION}.zip"
        zip_path = os.path.join(OUTPUT_DIR, zip_name)
        create_zip(work_dir, zip_path)
        
        print(f"  ✓ Created: {zip_name}")
        
    # 임시 폴더 정리
    try:
        shutil.rmtree(os.path.join(OUTPUT_DIR, "temp"), ignore_errors=True)
    except:
        pass
    
    print("\n" + "=" * 60)
    print(f"🎉 Build Complete! Artifacts: {OUTPUT_DIR}")
    print("=" * 60)

if __name__ == '__main__':
    main()


