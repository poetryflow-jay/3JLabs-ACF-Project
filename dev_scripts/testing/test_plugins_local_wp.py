#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 20] 로컬 WordPress 플러그인 테스트 스크립트

설치된 플러그인을 활성화하고 기본 기능을 테스트합니다.
"""

import os
import subprocess
from pathlib import Path
from typing import List, Dict

def check_wp_cli(wp_path: Path) -> bool:
    """WP-CLI 사용 가능 여부 확인"""
    try:
        result = subprocess.run(
            ['wp', '--info'],
            cwd=str(wp_path),
            capture_output=True,
            text=True,
            timeout=5
        )
        return result.returncode == 0
    except:
        return False

def activate_plugin_wpcli(wp_path: Path, plugin_name: str) -> bool:
    """WP-CLI로 플러그인 활성화"""
    try:
        result = subprocess.run(
            ['wp', 'plugin', 'activate', plugin_name, '--path=' + str(wp_path)],
            capture_output=True,
            text=True,
            timeout=10
        )
        return result.returncode == 0
    except Exception as e:
        print(f"   ⚠️ WP-CLI 오류: {e}")
        return False

def check_plugin_status(wp_path: Path) -> Dict[str, bool]:
    """플러그인 설치 및 활성화 상태 확인"""
    plugins_path = wp_path / 'wp-content' / 'plugins'
    
    plugins_status = {}
    
    # 설치된 플러그인 확인
    plugin_dirs = [
        'acf-css-really-simple-style-management-center-master',
        'wp-bulk-manager',
        'acf-code-snippets-box',
        'acf-css-neural-link',
    ]
    
    for plugin_dir in plugin_dirs:
        plugin_path = plugins_path / plugin_dir
        plugins_status[plugin_dir] = plugin_path.exists()
    
    return plugins_status

def check_plugin_files(plugin_path: Path) -> Dict[str, bool]:
    """플러그인 핵심 파일 존재 확인"""
    critical_files = {
        'main_file': None,
        'includes': False,
        'languages': False,
        'security_files': False,
    }
    
    # 메인 파일 찾기
    main_files = list(plugin_path.glob('*.php'))
    if main_files:
        critical_files['main_file'] = main_files[0].name
    
    # includes 폴더 확인
    if (plugin_path / 'includes').exists():
        critical_files['includes'] = True
    
    # languages 폴더 확인
    if (plugin_path / 'languages').exists():
        critical_files['languages'] = True
        # MO 파일 확인
        mo_files = list((plugin_path / 'languages').glob('*.mo'))
        critical_files['mo_files_count'] = len(mo_files)
    
    # 보안 파일 확인 (Phase 20)
    security_files = [
        'class-jj-file-integrity-monitor.php',
        'class-jj-security-enhancer.php',
    ]
    found_security = 0
    for sec_file in security_files:
        if (plugin_path / 'includes' / sec_file).exists():
            found_security += 1
    critical_files['security_files'] = found_security == len(security_files)
    critical_files['security_files_count'] = found_security
    
    return critical_files

def main():
    """메인 함수"""
    base_path = Path(__file__).parent
    wp_path = base_path / 'wordpress' / 'public'
    
    if not wp_path.exists():
        print("❌ WordPress 경로를 찾을 수 없습니다.")
        return
    
    print("=" * 60)
    print("Phase 20: 로컬 WordPress 플러그인 테스트")
    print("=" * 60)
    print()
    
    # WP-CLI 확인
    wp_cli_available = check_wp_cli(wp_path)
    print(f"🔧 WP-CLI 사용 가능: {'✅' if wp_cli_available else '❌'}")
    print()
    
    # 플러그인 상태 확인
    print("📦 플러그인 설치 상태 확인...")
    print("-" * 60)
    
    plugins_status = check_plugin_status(wp_path)
    plugins_path = wp_path / 'wp-content' / 'plugins'
    
    for plugin_name, is_installed in plugins_status.items():
        status_icon = "✅" if is_installed else "❌"
        print(f"{status_icon} {plugin_name}: {'설치됨' if is_installed else '미설치'}")
        
        if is_installed:
            plugin_path = plugins_path / plugin_name
            files_status = check_plugin_files(plugin_path)
            
            print(f"   📄 메인 파일: {files_status['main_file'] or '없음'}")
            print(f"   📁 includes: {'✅' if files_status['includes'] else '❌'}")
            print(f"   🌐 languages: {'✅' if files_status['languages'] else '❌'}")
            if files_status.get('mo_files_count'):
                print(f"      - MO 파일: {files_status['mo_files_count']}개")
            print(f"   🔒 보안 파일: {files_status['security_files_count']}/{2} ({'✅' if files_status['security_files'] else '❌'})")
    
    print()
    
    # WP-CLI로 플러그인 활성화 시도
    if wp_cli_available:
        print("🔌 플러그인 활성화 시도...")
        print("-" * 60)
        
        plugins_to_activate = [
            'acf-css-really-simple-style-management-center-master',
            'wp-bulk-manager',
            'acf-code-snippets-box',
            'acf-css-neural-link',
        ]
        
        for plugin_name in plugins_to_activate:
            if plugins_status.get(plugin_name):
                print(f"📦 [{plugin_name}] 활성화 중...")
                if activate_plugin_wpcli(wp_path, plugin_name):
                    print(f"   ✅ 활성화 완료")
                else:
                    print(f"   ⚠️ 활성화 실패 (수동 활성화 필요)")
            else:
                print(f"⚠️ [{plugin_name}] 설치되지 않음")
    
    print()
    print("=" * 60)
    print("✅ 테스트 완료!")
    print("=" * 60)
    print()
    print("📋 다음 단계:")
    print("  1. WordPress 관리자 페이지 접속")
    print("  2. 플러그인 메뉴에서 활성화 상태 확인")
    print("  3. ACF CSS 설정 관리자 페이지 접속")
    print("  4. 다국어 번역 확인 (언어 설정 변경)")
    print("  5. 보안 기능 테스트 (파일 무결성 모니터)")
    print()
    print(f"💡 WordPress URL: {wp_path}")

if __name__ == '__main__':
    main()
