import os
import shutil
import zipfile
import re

# 설정
SOURCE_DIR = 'acf-css-really-simple-style-management-center-master'
VERSION = '5.5.0'
EDITIONS = ['free', 'basic', 'premium', 'partner', 'master']
OUTPUT_DIR = os.path.join(os.environ['USERPROFILE'], 'Desktop', 'JJ_Distributions')

# 출력 디렉토리 생성
if not os.path.exists(OUTPUT_DIR):
    os.makedirs(OUTPUT_DIR)

def build_edition(edition):
    print(f"Building {edition} edition...")
    
    # 임시 작업 디렉토리 이름
    # Free 버전은 폴더명을 다르게 하고 싶을 수도 있지만, 일단 통일하거나 접미사 붙임
    work_dir = f"acf-css-really-simple-style-management-center-{edition}"
    
    # 소스 복사
    if os.path.exists(work_dir):
        try:
            shutil.rmtree(work_dir)
        except Exception:
            pass # 삭제 실패 시 무시 (어차피 덮어쓸 것임, 하지만 copytree는 빈 폴더 요구함)
            # 만약 rmtree가 실패하면 copytree도 실패할 것임. 
            # 대안: distutils.dir_util.copy_tree 사용 (덮어쓰기 가능)
            # 하지만 import distutils가 deprecated됨.
            
    # shutil.copytree는 대상 폴더가 없어야 함.
    # 안전하게: 반복 시도
    import time
    for _ in range(3):
        try:
            if os.path.exists(work_dir):
                shutil.rmtree(work_dir, ignore_errors=True)
            shutil.copytree(SOURCE_DIR, work_dir)
            break
        except Exception as e:
            print(f"Retrying copy... {e}")
            time.sleep(1)
    
    # 메인 파일 수정
    main_file = os.path.join(work_dir, 'acf-css-really-simple-style-guide.php')
    with open(main_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. 라이센스 타입 변경
    # define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'FREE' );
    pattern_license = r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);"
    replace_license = f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{edition.upper()}' );"
    content = re.sub(pattern_license, replace_license, content)
    
    # 2. 에디션 상수 변경
    # define( 'JJ_STYLE_GUIDE_EDITION', 'free' );
    pattern_edition = r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);"
    replace_edition = f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );"
    content = re.sub(pattern_edition, replace_edition, content)
    
    # 3. 플러그인 이름 변경 (선택사항)
    # Master가 아닌 경우 (Master) 텍스트 제거 등
    if edition != 'master':
        content = content.replace(' (Master)', '')
        # Free인 경우 (Free) 추가? 
        # 일단 그대로 둠.
        
    with open(main_file, 'w', encoding='utf-8') as f:
        f.write(content)
        
    # 압축 생성
    zip_name = f"acf-css-{edition}-v{VERSION}.zip"
    zip_path = os.path.join(OUTPUT_DIR, zip_name)
    
    # shutil.make_archive는 폴더 자체를 포함할지 여부가 까다로움.
    # zipfile로 직접 처리
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(work_dir):
            for file in files:
                file_path = os.path.join(root, file)
                # zip 내 경로: work_dir 이름을 최상위 폴더로 사용
                arcname = os.path.relpath(file_path, os.path.dirname(work_dir))
                zipf.write(file_path, arcname)
                
    # 임시 폴더 삭제 (오류 시 무시)
    try:
        shutil.rmtree(work_dir)
    except Exception as e:
        print(f"Warning: Could not remove temp dir {work_dir}: {e}")
    
    print(f"Created: {zip_path}")

# 실행
print("=== Starting Distribution Build ===")
for edition in EDITIONS:
    build_edition(edition)
print("=== Build Complete ===")
print(f"Artifacts are in: {OUTPUT_DIR}")

