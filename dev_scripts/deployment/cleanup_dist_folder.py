#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
3J Labs - dist 폴더 정리 스크립트
구 버전, 핫픽스, 잡다한 파일들을 old 폴더로 이동합니다.

보존 대상:
- 최신 버전 ZIP 파일 (v13.4.7 기준)
- 문서 파일 (README, 작업 원칙, 로드맵, 릴리즈 노트, 메모리, 콘텍스트, 레퍼런스)
"""

import os
import shutil
from pathlib import Path
from datetime import datetime
import re

def get_latest_versions():
    """최신 버전 정보 반환"""
    return {
        'acf-css-manager': '13.4.7',
        'admin-menu-editor-pro': '1.0.0',
        'acf-code-snippets-box': '1.1.0',
        'acf-css-woocommerce-toolkit': '1.1.0',
        'acf-css-ai-extension': '2.1.0',
        'acf-css-neural-link': '4.2.0',
        'acf-nudge-flow': '1.1.0',
        'wp-bulk-manager': '2.3.1',
        'acf-css-woo-license': '2.0.0',
    }

def extract_plugin_info(filename):
    """ZIP 파일명에서 플러그인 정보 추출"""
    # 버전 패턴: -v{version}.zip
    version_match = re.search(r'-v([\d.]+(?:-hotfix)?)\.zip$', filename)
    if not version_match:
        return None, None
    
    version = version_match.group(1)
    base_name = filename[:version_match.start()]
    
    return base_name, version

def is_old_version(version, latest_version):
    """구 버전인지 확인"""
    if version.endswith('-hotfix'):
        return True
    
    # 버전 비교 (간단한 문자열 비교)
    try:
        v_parts = [int(x) for x in version.split('.')]
        l_parts = [int(x) for x in latest_version.split('.')]
        
        # 메이저 버전이 다르면 구 버전
        if v_parts[0] < l_parts[0]:
            return True
        # 마이너 버전이 다르면 구 버전
        if len(v_parts) > 1 and len(l_parts) > 1:
            if v_parts[0] == l_parts[0] and v_parts[1] < l_parts[1]:
                return True
            # 패치 버전 비교
            if len(v_parts) > 2 and len(l_parts) > 2:
                if v_parts[0] == l_parts[0] and v_parts[1] == l_parts[1] and v_parts[2] < l_parts[2]:
                    return True
        
        return version != latest_version
    except:
        # 파싱 실패 시 버전이 다르면 구 버전으로 간주
        return version != latest_version

def should_preserve_file(filepath):
    """파일 보존 여부 확인
    
    보존 대상:
    - 문서 파일 (README, 작업 원칙, 로드맵, 릴리즈 노트, 메모리, 콘텍스트, 레퍼런스)
    - 최신 버전 ZIP 파일
    - 마스터 올인원 플러그인
    """
    name = filepath.name.lower()
    
    # 문서 파일 보존
    doc_extensions = ['.md', '.txt', '.html', '.json', '.yaml', '.yml', '.pdf', '.doc', '.docx']
    if any(name.endswith(ext) for ext in doc_extensions):
        return True
    
    # 특정 문서 파일명
    doc_keywords = ['readme', 'changelog', 'release', 'roadmap', 'guide', 'memory', 'context', 'reference', 
                    '작업원칙', '로드맵', '메모리', '콘텍스트', '레퍼런스', '개발자', '사용자']
    if any(keyword in name for keyword in doc_keywords):
        return True
    
    return False

def get_preserved_patterns():
    """보존할 파일 패턴 목록"""
    return [
        # 마스터 올인원 플러그인
        r'.*master-v13\.4\.7\.zip$',
        # Neural Link (마스터 전용)
        r'.*neural-link-v[\d.]+\.zip$',
        # WooCommerce License Bridge (마스터 전용)
        r'.*woo-license-v[\d.]+\.zip$',
        # 최신 번들
        r'.*Complete-Bundle-v13\.4\.7\.zip$',
    ]

def cleanup_dist_folder():
    """dist 폴더 정리"""
    base_path = Path(__file__).parent
    dist_path = base_path / 'dist'
    old_path = base_path / 'dist' / 'old'
    
    if not dist_path.exists():
        print("❌ dist 폴더가 없습니다.")
        return
    
    # old 폴더 생성
    old_path.mkdir(exist_ok=True)
    
    # 타임스탬프 폴더 생성
    timestamp = datetime.now().strftime('%Y%m%d-%H%M%S')
    archive_path = old_path / timestamp
    archive_path.mkdir(exist_ok=True)
    
    latest_versions = get_latest_versions()
    
    moved_files = []
    preserved_files = []
    moved_folders = []
    
    print("🧹 dist 폴더 정리 시작...")
    print("=" * 60)
    
    # ZIP 파일 처리
    zip_files = list(dist_path.glob('*.zip'))
    print(f"\n📦 ZIP 파일 처리: {len(zip_files)}개")
    
    preserved_patterns = get_preserved_patterns()
    
    for zip_file in zip_files:
        # 문서 파일 보존
        if should_preserve_file(zip_file):
            preserved_files.append(zip_file.name)
            continue
        
        # 보존 패턴 확인
        is_preserved = False
        for pattern in preserved_patterns:
            if re.match(pattern, zip_file.name, re.IGNORECASE):
                is_preserved = True
                break
        
        if is_preserved:
            preserved_files.append(zip_file.name)
            continue
        
        base_name, version = extract_plugin_info(zip_file.name)
        
        if not base_name or not version:
            # 번들 파일은 최신 것만 유지
            if 'Complete-Bundle' in zip_file.name and 'v13.4.7' in zip_file.name:
                preserved_files.append(zip_file.name)
            else:
                dest = archive_path / zip_file.name
                shutil.move(str(zip_file), str(dest))
                moved_files.append(zip_file.name)
            continue
        
        # 마스터 올인원 파일은 항상 보존
        if 'master-v13.4.7.zip' in zip_file.name and 'acf-css-really-simple-style-management-center-master-v13.4.7.zip' == zip_file.name:
            is_latest = True
        # 플러그인별 최신 버전 확인
        else:
            is_latest = False
            for plugin_key, latest_version in latest_versions.items():
                if plugin_key.replace('-', '-') in base_name.lower() or plugin_key in base_name.lower():
                    if version == latest_version or (version.startswith('13.4.7')):
                        is_latest = True
                        break
        
        # 번들 파일 처리
        if 'Bundle' in zip_file.name:
            if 'Complete-Bundle-v13.4.7' in zip_file.name:
                is_latest = True
            else:
                is_latest = False
        
        if is_latest:
            preserved_files.append(zip_file.name)
        else:
            dest = archive_path / zip_file.name
            shutil.move(str(zip_file), str(dest))
            moved_files.append(zip_file.name)
    
    # 빌드 중간 폴더 처리
    print(f"\n📁 빌드 중간 폴더 처리...")
    folders = [f for f in dist_path.iterdir() if f.is_dir() and f.name != 'old']
    
    for folder in folders:
        # 문서 폴더는 보존
        if folder.name in ['memory & context', 'docs', 'reference', '레퍼런스']:
            continue
        
        # 빌드 중간 폴더는 모두 이동
        dest = archive_path / folder.name
        try:
            shutil.move(str(folder), str(dest))
            moved_folders.append(folder.name)
        except Exception as e:
            print(f"  ⚠️ 폴더 이동 실패: {folder.name} - {e}")
    
    # 결과 요약
    print("\n" + "=" * 60)
    print("✅ 정리 완료!")
    print("=" * 60)
    print(f"  보존된 파일: {len(preserved_files)}개")
    print(f"  이동된 파일: {len(moved_files)}개")
    print(f"  이동된 폴더: {len(moved_folders)}개")
    print(f"  아카이브 위치: {archive_path}")
    
    if moved_files:
        print(f"\n📋 이동된 파일 목록 (처음 10개):")
        for f in moved_files[:10]:
            print(f"    - {f}")
        if len(moved_files) > 10:
            print(f"    ... 외 {len(moved_files) - 10}개")
    
    if preserved_files:
        print(f"\n✅ 보존된 파일 목록 (처음 10개):")
        for f in preserved_files[:10]:
            print(f"    - {f}")
        if len(preserved_files) > 10:
            print(f"    ... 외 {len(preserved_files) - 10}개")

if __name__ == '__main__':
    try:
        cleanup_dist_folder()
    except KeyboardInterrupt:
        print("\n\n❌ 사용자에 의해 중단되었습니다")
    except Exception as e:
        print(f"\n\n❌ 오류 발생: {e}")
        import traceback
        traceback.print_exc()
