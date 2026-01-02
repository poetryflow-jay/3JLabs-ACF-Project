#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 20] 로컬 WordPress에 플러그인 설치 스크립트

로컬 WordPress 환경에 최신 플러그인을 설치하고 테스트합니다.
"""

import os
import shutil
import zipfile
from pathlib import Path
from typing import Optional

def find_local_wordpress(base_path: Path) -> Optional[Path]:
    """로컬 WordPress 경로 찾기"""
    # 가능한 경로들
    possible_paths = [
        base_path / 'wordpress' / 'public',
        base_path / 'local-wordpress' / 'wordpress',
        base_path / 'wp' / 'public',
    ]
    
    for wp_path in possible_paths:
        wp_config = wp_path / 'wp-config.php'
        wp_content = wp_path / 'wp-content'
        if wp_config.exists() and wp_content.exists():
            return wp_path
    
    return None

def install_plugin(zip_path: Path, wp_path: Path, plugin_name: str) -> bool:
    """플러그인 ZIP 파일을 WordPress에 설치"""
    plugins_path = wp_path / 'wp-content' / 'plugins'
    plugins_path.mkdir(parents=True, exist_ok=True)
    
    # 기존 플러그인 제거 (있는 경우)
    plugin_dirs = list(plugins_path.glob(f'{plugin_name}*'))
    for plugin_dir in plugin_dirs:
        if plugin_dir.is_dir():
            try:
                shutil.rmtree(str(plugin_dir))
                print(f"   🗑️ 기존 플러그인 제거: {plugin_dir.name}")
            except Exception as e:
                print(f"   ⚠️ 제거 실패: {e}")
    
    # ZIP 파일 압축 해제
    try:
        with zipfile.ZipFile(zip_path, 'r') as zip_ref:
            zip_ref.extractall(str(plugins_path))
        print(f"   ✅ 설치 완료: {plugin_name}")
        return True
    except Exception as e:
        print(f"   ❌ 설치 실패: {e}")
        return False

def main():
    """메인 함수"""
    base_path = Path(__file__).parent
    
    print("=" * 60)
    print("Phase 20: 로컬 WordPress 플러그인 설치")
    print("=" * 60)
    print()
    
    # WordPress 경로 찾기
    wp_path = find_local_wordpress(base_path)
    if not wp_path:
        print("❌ 로컬 WordPress 환경을 찾을 수 없습니다.")
        print()
        print("💡 가능한 경로:")
        print("   - wordpress/public")
        print("   - local-wordpress/wordpress")
        print("   - wp/public")
        return
    
    print(f"✅ WordPress 경로 발견: {wp_path}")
    print()
    
    # 설치할 플러그인 목록 (최신 버전)
    dist_path = base_path / 'dist'
    plugins_to_install = [
        {
            'name': 'ACF CSS Manager (Master)',
            'pattern': 'acf-css-really-simple-style-management-center-master-v20.0.0.zip',
            'plugin_dir': 'acf-css-really-simple-style-management-center-master',
            'fallback_patterns': [
                'acf-css-really-simple-style-management-center-master-*.zip',
            ]
        },
        {
            'name': 'WP Bulk Manager',
            'pattern': 'wp-bulk-manager-master-master-v2.3.1.zip',
            'plugin_dir': 'wp-bulk-manager'
        },
        {
            'name': 'ACF Code Snippets Box',
            'pattern': 'acf-code-snippets-box-master-master-v1.1.0.zip',
            'plugin_dir': 'acf-code-snippets-box'
        },
        {
            'name': 'ACF CSS Neural Link',
            'pattern': 'acf-css-neural-link-v4.2.0.zip',
            'plugin_dir': 'acf-css-neural-link'
        },
    ]
    
    print("📦 플러그인 설치 중...")
    print("-" * 60)
    
    success_count = 0
    fail_count = 0
    
    for plugin_info in plugins_to_install:
        zip_file = dist_path / plugin_info['pattern']
        
        # 정확한 파일명이 없으면 fallback 패턴 시도
        if not zip_file.exists() and 'fallback_patterns' in plugin_info:
            for fallback_pattern in plugin_info['fallback_patterns']:
                matching_files = list(dist_path.glob(fallback_pattern))
                if matching_files:
                    # 가장 최신 파일 선택 (버전 번호 기준)
                    zip_file = max(matching_files, key=lambda f: f.stat().st_mtime)
                    print(f"   ℹ️ 대체 파일 사용: {zip_file.name}")
                    break
        
        if not zip_file.exists():
            print(f"⚠️ [{plugin_info['name']}] ZIP 파일 없음: {plugin_info['pattern']}")
            fail_count += 1
            continue
        
        print(f"📦 [{plugin_info['name']}] 설치 중...")
        
        if install_plugin(zip_file, wp_path, plugin_info['plugin_dir']):
            success_count += 1
        else:
            fail_count += 1
    
    print()
    print("=" * 60)
    print(f"✅ 설치 완료: {success_count}개")
    if fail_count > 0:
        print(f"❌ 실패: {fail_count}개")
    print("=" * 60)
    print()
    print("📋 다음 단계:")
    print("  1. WordPress 관리자 페이지 접속")
    print("  2. 플러그인 활성화")
    print("  3. 기능 테스트")
    print(f"  4. WordPress URL: {wp_path}")

if __name__ == '__main__':
    main()
