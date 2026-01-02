#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 20] 로컬 WordPress 플러그인 활성화 및 테스트

WP-CLI 또는 직접 파일 수정을 통해 플러그인을 활성화하고 테스트합니다.
"""

import os
import subprocess
import json
from pathlib import Path
from typing import Dict, List

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

def activate_plugin_wpcli(wp_path: Path, plugin_name: str) -> Dict:
    """WP-CLI로 플러그인 활성화"""
    try:
        result = subprocess.run(
            ['wp', 'plugin', 'activate', plugin_name, '--path=' + str(wp_path)],
            capture_output=True,
            text=True,
            timeout=10
        )
        return {
            'success': result.returncode == 0,
            'output': result.stdout,
            'error': result.stderr
        }
    except Exception as e:
        return {
            'success': False,
            'output': '',
            'error': str(e)
        }

def get_active_plugins(wp_path: Path) -> List[str]:
    """활성화된 플러그인 목록 가져오기"""
    try:
        result = subprocess.run(
            ['wp', 'plugin', 'list', '--status=active', '--format=json', '--path=' + str(wp_path)],
            capture_output=True,
            text=True,
            timeout=10
        )
        if result.returncode == 0:
            plugins = json.loads(result.stdout)
            return [p['name'] for p in plugins]
        return []
    except:
        return []

def check_plugin_errors(wp_path: Path, plugin_name: str) -> Dict:
    """플러그인 에러 확인"""
    try:
        result = subprocess.run(
            ['wp', 'plugin', 'list', '--name=' + plugin_name, '--format=json', '--path=' + str(wp_path)],
            capture_output=True,
            text=True,
            timeout=10
        )
        if result.returncode == 0:
            plugins = json.loads(result.stdout)
            if plugins:
                plugin = plugins[0]
                return {
                    'status': plugin.get('status', 'unknown'),
                    'update': plugin.get('update', 'none'),
                    'version': plugin.get('version', 'unknown'),
                }
        return {}
    except:
        return {}

def test_plugin_files(plugin_path: Path) -> Dict:
    """플러그인 파일 무결성 테스트"""
    results = {
        'main_file': False,
        'includes': False,
        'languages': False,
        'security_files': False,
        'mo_files': 0,
    }
    
    # 메인 파일 확인
    main_files = list(plugin_path.glob('*.php'))
    if main_files:
        results['main_file'] = True
    
    # includes 폴더 확인
    if (plugin_path / 'includes').exists():
        results['includes'] = True
        
        # 보안 파일 확인
        security_files = [
            'class-jj-file-integrity-monitor.php',
            'class-jj-security-enhancer.php',
        ]
        found_security = sum(1 for f in security_files if (plugin_path / 'includes' / f).exists())
        results['security_files'] = found_security == len(security_files)
        results['security_files_count'] = found_security
    
    # languages 폴더 확인
    if (plugin_path / 'languages').exists():
        results['languages'] = True
        mo_files = list((plugin_path / 'languages').glob('*.mo'))
        results['mo_files'] = len(mo_files)
    
    return results

def main():
    """메인 함수"""
    base_path = Path(__file__).parent
    wp_path = base_path / 'wordpress' / 'public'
    
    if not wp_path.exists():
        print("❌ WordPress 경로를 찾을 수 없습니다.")
        return
    
    print("=" * 60)
    print("Phase 20: 로컬 WordPress 플러그인 활성화 및 테스트")
    print("=" * 60)
    print()
    
    # WP-CLI 확인
    wp_cli_available = check_wp_cli(wp_path)
    print(f"🔧 WP-CLI 사용 가능: {'✅' if wp_cli_available else '❌'}")
    print()
    
    # 활성화할 플러그인 목록
    plugins_to_activate = [
        {
            'name': 'acf-css-really-simple-style-management-center-master',
            'display': 'ACF CSS Manager (Master)',
            'version': '20.0.0'
        },
        {
            'name': 'wp-bulk-manager-master-master',
            'display': 'WP Bulk Manager',
            'version': '2.3.1'
        },
        {
            'name': 'acf-code-snippets-box-master-master',
            'display': 'ACF Code Snippets Box',
            'version': '1.1.0'
        },
        {
            'name': 'acf-css-neural-link',
            'display': 'ACF CSS Neural Link',
            'version': '4.2.0'
        },
    ]
    
    plugins_path = wp_path / 'wp-content' / 'plugins'
    
    print("📦 플러그인 활성화 및 테스트...")
    print("-" * 60)
    
    activation_results = []
    
    for plugin_info in plugins_to_activate:
        plugin_name = plugin_info['name']
        plugin_path = plugins_path / plugin_name
        
        print(f"\n🔌 [{plugin_info['display']}]")
        
        # 파일 존재 확인
        if not plugin_path.exists():
            print(f"   ❌ 플러그인 폴더 없음")
            activation_results.append({
                'plugin': plugin_info['display'],
                'status': 'not_found',
                'details': {}
            })
            continue
        
        # 파일 무결성 테스트
        file_test = test_plugin_files(plugin_path)
        print(f"   📄 파일 확인:")
        print(f"      - 메인 파일: {'✅' if file_test['main_file'] else '❌'}")
        print(f"      - includes: {'✅' if file_test['includes'] else '❌'}")
        print(f"      - languages: {'✅' if file_test['languages'] else '❌'}")
        if file_test.get('mo_files'):
            print(f"      - MO 파일: {file_test['mo_files']}개 ✅")
        if file_test.get('security_files_count'):
            print(f"      - 보안 파일: {file_test['security_files_count']}/2 {'✅' if file_test['security_files'] else '❌'}")
        
        # WP-CLI로 활성화 시도
        if wp_cli_available:
            print(f"   🔌 활성화 시도...")
            result = activate_plugin_wpcli(wp_path, plugin_name)
            
            if result['success']:
                print(f"      ✅ 활성화 완료")
                
                # 플러그인 상태 확인
                plugin_status = check_plugin_errors(wp_path, plugin_name)
                if plugin_status:
                    print(f"      📊 상태: {plugin_status.get('status', 'unknown')}")
                    print(f"      📦 버전: {plugin_status.get('version', 'unknown')}")
                
                activation_results.append({
                    'plugin': plugin_info['display'],
                    'status': 'activated',
                    'details': {
                        'file_test': file_test,
                        'plugin_status': plugin_status
                    }
                })
            else:
                print(f"      ⚠️ 활성화 실패: {result['error']}")
                activation_results.append({
                    'plugin': plugin_info['display'],
                    'status': 'activation_failed',
                    'details': {
                        'error': result['error'],
                        'file_test': file_test
                    }
                })
        else:
            print(f"   ⚠️ WP-CLI 없음 - 수동 활성화 필요")
            activation_results.append({
                'plugin': plugin_info['display'],
                'status': 'wp_cli_unavailable',
                'details': {
                    'file_test': file_test
                }
            })
    
    print()
    print("=" * 60)
    print("📊 활성화 결과 요약")
    print("=" * 60)
    
    activated = sum(1 for r in activation_results if r['status'] == 'activated')
    failed = sum(1 for r in activation_results if r['status'] in ['activation_failed', 'not_found'])
    
    print(f"✅ 활성화 완료: {activated}개")
    if failed > 0:
        print(f"❌ 실패: {failed}개")
    print()
    
    # 활성화된 플러그인 목록
    if wp_cli_available:
        active_plugins = get_active_plugins(wp_path)
        if active_plugins:
            print("📋 활성화된 플러그인 목록:")
            for plugin in active_plugins:
                print(f"   - {plugin}")
            print()
    
    print("=" * 60)
    print("✅ 테스트 완료!")
    print("=" * 60)
    print()
    print("📋 다음 단계:")
    print("  1. WordPress 관리자 페이지 접속")
    print("  2. 플러그인 메뉴에서 활성화 상태 확인")
    print("  3. ACF CSS 설정 관리자 페이지 접속")
    print("  4. 기능 테스트 진행")
    print()
    print("💡 테스트 가이드: TESTING_GUIDE_PHASE_20.md 참조")

if __name__ == '__main__':
    main()
