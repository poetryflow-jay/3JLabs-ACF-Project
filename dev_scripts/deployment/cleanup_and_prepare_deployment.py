#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 20] 배포 준비: dist 및 builds 폴더 정리 스크립트

1. dist 폴더: 최신 v20.0.0만 남기고 나머지는 old로 이동
2. builds 폴더: 오래된 빌드 폴더 정리
3. 대시보드 및 Python 프로그램 연동 확인
"""

import os
import shutil
from pathlib import Path
from datetime import datetime
from typing import List, Tuple

def get_file_version(filename: str) -> Tuple[str, str]:
    """파일명에서 버전 추출"""
    # 예: acf-css-really-simple-style-management-center-master-v20.0.0.zip
    #     -> ('acf-css-really-simple-style-management-center-master', '20.0.0')
    parts = filename.replace('.zip', '').split('-v')
    if len(parts) >= 2:
        plugin_name = '-v'.join(parts[:-1])
        version = parts[-1]
        return plugin_name, version
    return filename, '0.0.0'

def compare_versions(v1: str, v2: str) -> int:
    """버전 비교 (1: v1 > v2, -1: v1 < v2, 0: 같음)"""
    def version_tuple(v):
        return tuple(map(int, v.split('.')))
    
    try:
        t1 = version_tuple(v1)
        t2 = version_tuple(v2)
        if t1 > t2:
            return 1
        elif t1 < t2:
            return -1
        return 0
    except:
        return 0

def cleanup_dist_folder(base_path: Path) -> dict:
    """dist 폴더 정리"""
    dist_path = base_path / 'dist'
    if not dist_path.exists():
        return {'moved': 0, 'kept': 0, 'errors': []}
    
    old_path = dist_path / 'old'
    old_path.mkdir(exist_ok=True)
    
    # 타임스탬프 폴더 생성
    timestamp = datetime.now().strftime('%Y%m%d-%H%M%S')
    archive_path = old_path / f'pre-v20-cleanup-{timestamp}'
    archive_path.mkdir(exist_ok=True)
    
    zip_files = list(dist_path.glob('*.zip'))
    
    # 플러그인별 최신 버전 찾기
    plugin_versions = {}
    for zip_file in zip_files:
        plugin_name, version = get_file_version(zip_file.name)
        if plugin_name not in plugin_versions:
            plugin_versions[plugin_name] = (zip_file, version)
        else:
            current_file, current_version = plugin_versions[plugin_name]
            if compare_versions(version, current_version) > 0:
                plugin_versions[plugin_name] = (zip_file, version)
    
    # 최신 파일 목록
    latest_files = {f.name for _, (f, _) in plugin_versions.items()}
    
    moved_count = 0
    kept_count = 0
    errors = []
    
    for zip_file in zip_files:
        try:
            if zip_file.name in latest_files:
                # 최신 버전은 유지
                kept_count += 1
            else:
                # 오래된 버전은 이동
                dest = archive_path / zip_file.name
                shutil.move(str(zip_file), str(dest))
                moved_count += 1
        except Exception as e:
            errors.append(f"{zip_file.name}: {e}")
    
    return {
        'moved': moved_count,
        'kept': kept_count,
        'errors': errors,
        'archive_path': str(archive_path)
    }

def cleanup_builds_folder(base_path: Path) -> dict:
    """builds 폴더 정리"""
    builds_path = base_path / 'builds'
    if not builds_path.exists():
        return {'deleted': 0, 'kept': 0, 'errors': []}
    
    # 오래된 빌드 폴더 목록 (v20.0.0 이전)
    old_build_folders = []
    current_build_folders = []
    
    for item in builds_path.iterdir():
        if not item.is_dir():
            continue
        
        # 타임스탬프 폴더는 보존 (최근 7일 이내)
        if item.name.startswith('2026') or item.name.startswith('2025'):
            try:
                # 날짜 파싱
                date_str = item.name.split('-')[0] if '-' in item.name else item.name[:8]
                if len(date_str) == 8:
                    folder_date = datetime.strptime(date_str, '%Y%m%d')
                    days_old = (datetime.now() - folder_date).days
                    if days_old > 7:
                        old_build_folders.append(item)
                    else:
                        current_build_folders.append(item)
                else:
                    old_build_folders.append(item)
            except:
                old_build_folders.append(item)
        elif item.name.startswith('_temp_'):
            # 임시 폴더는 삭제
            old_build_folders.append(item)
        elif 'v13.' in item.name or 'v12.' in item.name or 'v11.' in item.name:
            # 오래된 버전 폴더
            old_build_folders.append(item)
        else:
            current_build_folders.append(item)
    
    # old 폴더로 이동
    old_path = builds_path / 'old'
    old_path.mkdir(exist_ok=True)
    
    timestamp = datetime.now().strftime('%Y%m%d-%H%M%S')
    archive_path = old_path / f'old-builds-{timestamp}'
    archive_path.mkdir(exist_ok=True)
    
    moved_count = 0
    errors = []
    
    for folder in old_build_folders:
        try:
            dest = archive_path / folder.name
            if dest.exists():
                shutil.rmtree(str(dest))
            shutil.move(str(folder), str(dest))
            moved_count += 1
        except Exception as e:
            errors.append(f"{folder.name}: {e}")
    
    return {
        'moved': moved_count,
        'kept': len(current_build_folders),
        'errors': errors,
        'archive_path': str(archive_path)
    }

def verify_dashboard_integration(base_path: Path) -> dict:
    """대시보드 및 Python 프로그램 연동 확인"""
    dashboard_path = base_path / 'dashboard.html'
    launcher_path = base_path / '3j_launcher.py'
    toolkit_path = base_path / '3j_dev_toolkit.py'
    
    results = {
        'dashboard_exists': dashboard_path.exists(),
        'launcher_exists': launcher_path.exists(),
        'toolkit_exists': toolkit_path.exists(),
        'dashboard_version': None,
        'issues': []
    }
    
    # 대시보드 버전 확인
    if dashboard_path.exists():
        try:
            with open(dashboard_path, 'r', encoding='utf-8') as f:
                content = f.read()
                if 'v20.0.0' in content:
                    results['dashboard_version'] = '20.0.0'
                elif 'v13.4.7' in content:
                    results['dashboard_version'] = '13.4.7'
                    results['issues'].append('대시보드가 v13.4.7을 표시하고 있습니다. v20.0.0으로 업데이트 필요')
        except Exception as e:
            results['issues'].append(f'대시보드 읽기 오류: {e}')
    
    # Python 프로그램 확인
    if launcher_path.exists():
        try:
            with open(launcher_path, 'r', encoding='utf-8') as f:
                content = f.read()
                if 'build_distribution_final.py' in content or 'build_all_plugins_all_editions.py' in content:
                    results['launcher_ok'] = True
                else:
                    results['issues'].append('런처가 올바른 빌드 스크립트를 참조하지 않습니다')
        except Exception as e:
            results['issues'].append(f'런처 읽기 오류: {e}')
    
    return results

def main():
    """메인 함수"""
    base_path = Path(__file__).parent
    
    print("=" * 60)
    print("Phase 20: 배포 준비 - 파일 정리 및 연동 확인")
    print("=" * 60)
    print()
    
    # 1. dist 폴더 정리
    print("📦 dist 폴더 정리 중...")
    dist_result = cleanup_dist_folder(base_path)
    print(f"   ✅ 보존: {dist_result['kept']}개")
    print(f"   📦 이동: {dist_result['moved']}개 → {dist_result['archive_path']}")
    if dist_result['errors']:
        print(f"   ⚠️ 오류: {len(dist_result['errors'])}개")
        for error in dist_result['errors'][:5]:
            print(f"      - {error}")
    print()
    
    # 2. builds 폴더 정리
    print("🔨 builds 폴더 정리 중...")
    builds_result = cleanup_builds_folder(base_path)
    print(f"   ✅ 보존: {builds_result['kept']}개")
    print(f"   📦 이동: {builds_result['moved']}개 → {builds_result['archive_path']}")
    if builds_result['errors']:
        print(f"   ⚠️ 오류: {len(builds_result['errors'])}개")
        for error in builds_result['errors'][:5]:
            print(f"      - {error}")
    print()
    
    # 3. 대시보드 및 Python 프로그램 연동 확인
    print("🔗 대시보드 및 Python 프로그램 연동 확인...")
    integration_result = verify_dashboard_integration(base_path)
    print(f"   ✅ 대시보드 존재: {integration_result['dashboard_exists']}")
    print(f"   ✅ 런처 존재: {integration_result['launcher_exists']}")
    print(f"   ✅ 툴킷 존재: {integration_result['toolkit_exists']}")
    if integration_result['dashboard_version']:
        print(f"   📋 대시보드 버전: {integration_result['dashboard_version']}")
    if integration_result['issues']:
        print(f"   ⚠️ 발견된 문제:")
        for issue in integration_result['issues']:
            print(f"      - {issue}")
    print()
    
    print("=" * 60)
    print("✅ 정리 완료!")
    print("=" * 60)
    print()
    print("📋 다음 단계:")
    print("  1. 로컬 WordPress에 플러그인 설치")
    print("  2. 플러그인 활성화 및 기능 테스트")
    print("  3. 다국어 번역 확인")
    print("  4. 보안 기능 테스트")

if __name__ == '__main__':
    main()
