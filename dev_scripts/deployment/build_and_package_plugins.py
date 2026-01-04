#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
3J Labs - 플러그인 빌드 및 패키징 스크립트
- dist 폴더에 최신 버전 압축 파일 생성
- 구 버전은 old 폴더로 자동 이동
- 버전 정보 자동 추출 및 관리
"""

import os
import sys
import shutil
import zipfile
from pathlib import Path
from datetime import datetime
import re

# Windows 콘솔 유니코드 출력 설정
if sys.platform == 'win32':
    try:
        sys.stdout.reconfigure(encoding='utf-8')
    except:
        pass

# 프로젝트 루트 경로
PROJECT_ROOT = Path(__file__).parent.parent.parent
DIST_DIR = PROJECT_ROOT / 'dist'
OLD_DIR = DIST_DIR / 'old'

# 플러그인 정의
PLUGINS = {
    'acf-css-really-simple-style-management-center-master': {
        'name': 'ACF CSS - Advanced Custom Fonts & Colors & Styles Setting Manager',
        'main_file': 'acf-css-really-simple-style-guide.php',
        'zip_prefix': 'acf-css-really-simple-style-management-center-master'
    },
    'SEO/wp-bulk-seo-aeo': {
        'name': 'WP Bulk SEO & AEO (원 클릭 SEO)',
        'main_file': 'wp-bulk-seo-aeo.php',
        'zip_prefix': 'wp-bulk-seo-aeo'
    },
    'jj-marketing-automation-dashboard': {
        'name': '3J Labs Marketing Automation Dashboard (마케팅대시보드)',
        'main_file': 'jj-marketing-dashboard.php',
        'zip_prefix': 'jj-marketing-automation-dashboard'
    },
    'acf-code-snippets-box': {
        'name': 'ACF Code Snippets Box (코드 박스)',
        'main_file': 'acf-code-snippets-box.php',
        'zip_prefix': 'acf-code-snippets-box'
    },
    'acf-css-woo-license': {
        'name': 'ACF CSS Woo License Bridge (3J 라이센스)',
        'main_file': 'acf-css-woo-license.php',
        'zip_prefix': 'acf-css-woo-license'
    },
    'wp-bulk-manager': {
        'name': 'WP Bulk Manager - Really Simple WordPress Plugin & Theme Bulk Installer and Editor',
        'main_file': 'wp-bulk-installer.php',
        'zip_prefix': 'wp-bulk-manager'
    }
}

def extract_version(plugin_path, main_file):
    """플러그인 메인 파일에서 버전 추출"""
    main_file_path = plugin_path / main_file
    if not main_file_path.exists():
        return None
    
    try:
        with open(main_file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            # Version: X.X.X 패턴 찾기
            version_match = re.search(r'Version:\s*([\d.]+)', content)
            if version_match:
                return version_match.group(1).strip()
    except Exception as e:
        print(f"경고: 버전 추출 실패 ({main_file}): {e}")
    
    return None

def move_old_versions(plugin_name, new_version):
    """구 버전 파일을 old 폴더로 이동"""
    if not DIST_DIR.exists():
        return
    
    # old 폴더 생성
    OLD_DIR.mkdir(parents=True, exist_ok=True)
    
    # 타임스탬프 폴더 생성
    timestamp = datetime.now().strftime('%Y%m%d-%H%M%S')
    archive_path = OLD_DIR / timestamp
    archive_path.mkdir(exist_ok=True)
    
    # 해당 플러그인의 구 버전 ZIP 파일 찾기
    pattern = f"{plugin_name}*.zip"
    zip_files = list(DIST_DIR.glob(pattern))
    
    moved_count = 0
    for zip_file in zip_files:
        # 새로 생성할 파일명과 비교
        new_filename = f"{plugin_name}-v{new_version}.zip"
        if zip_file.name != new_filename:
            try:
                dest = archive_path / zip_file.name
                shutil.move(str(zip_file), str(dest))
                moved_count += 1
                print(f"  구 버전 이동: {zip_file.name} -> old/{timestamp}/")
            except Exception as e:
                print(f"  경고: 파일 이동 실패: {zip_file.name} - {e}")
    
    if moved_count > 0:
        print(f"  {moved_count}개 구 버전 파일을 old 폴더로 이동했습니다.")

def create_zip(plugin_path, plugin_name, version):
    """플러그인 ZIP 파일 생성"""
    dist_dir = DIST_DIR
    dist_dir.mkdir(parents=True, exist_ok=True)
    
    zip_filename = f"{plugin_name}-v{version}.zip"
    zip_path = dist_dir / zip_filename
    
    # 기존 파일이 있으면 삭제
    if zip_path.exists():
        zip_path.unlink()
    
    print(f"  📦 압축 파일 생성 중: {zip_filename}")
    
    try:
        with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
            # 플러그인 폴더 내 모든 파일 추가
            for root, dirs, files in os.walk(plugin_path):
                # 제외할 파일/폴더
                dirs[:] = [d for d in dirs if d not in ['.git', 'node_modules', '__pycache__', '.DS_Store']]
                
                for file in files:
                    if file.startswith('.') and file != '.gitignore':
                        continue
                    
                    file_path = Path(root) / file
                    # 플러그인 폴더명을 기준으로 상대 경로 계산
                    arcname = file_path.relative_to(plugin_path.parent)
                    zipf.write(file_path, arcname)
        
        size_mb = zip_path.stat().st_size / (1024 * 1024)
        print(f"  ✅ 생성 완료: {zip_filename} ({size_mb:.2f} MB)")
        return zip_path
    except Exception as e:
        print(f"  ❌ 압축 실패: {e}")
        return None

def build_plugin(plugin_key, plugin_info):
    """플러그인 빌드 및 패키징"""
    plugin_path = PROJECT_ROOT / plugin_key
    
    if not plugin_path.exists():
        print(f"오류: 플러그인 경로를 찾을 수 없습니다: {plugin_key}")
        return False
    
    print(f"\n빌드 중: {plugin_info['name']}")
    print(f"   경로: {plugin_path}")
    
    # 버전 추출
    version = extract_version(plugin_path, plugin_info['main_file'])
    if not version:
        print(f"  경고: 버전을 찾을 수 없습니다. 건너뜁니다.")
        return False
    
    print(f"   버전: {version}")
    
    # 구 버전 이동
    move_old_versions(plugin_info['zip_prefix'], version)
    
    # ZIP 파일 생성
    zip_path = create_zip(plugin_path, plugin_info['zip_prefix'], version)
    
    if zip_path:
        print(f"  빌드 완료!")
        return True
    else:
        print(f"  빌드 실패!")
        return False

def main():
    """메인 실행 함수"""
    print("=" * 70)
    print("3J Labs 플러그인 빌드 시스템")
    print("=" * 70)
    print(f"프로젝트 루트: {PROJECT_ROOT}")
    print(f"출력 디렉토리: {DIST_DIR}")
    print(f"아카이브 디렉토리: {OLD_DIR}")
    print("=" * 70)
    
    success_count = 0
    fail_count = 0
    
    for plugin_key, plugin_info in PLUGINS.items():
        if build_plugin(plugin_key, plugin_info):
            success_count += 1
        else:
            fail_count += 1
    
    print("\n" + "=" * 70)
    print("빌드 결과 요약")
    print("=" * 70)
    print(f"  성공: {success_count}개")
    print(f"  실패: {fail_count}개")
    print(f"  출력 위치: {DIST_DIR}")
    print("=" * 70)
    
    if success_count > 0:
        print("\n모든 플러그인 빌드가 완료되었습니다!")
        print(f"   dist 폴더에서 압축 파일을 확인하세요.")

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n사용자에 의해 중단되었습니다")
    except Exception as e:
        print(f"\n\n오류 발생: {e}")
        import traceback
        traceback.print_exc()
