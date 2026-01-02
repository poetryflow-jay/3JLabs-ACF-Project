#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
3J Labs - 모든 플러그인 에디션별 빌드 스크립트
모든 플러그인을 모든 에디션(Free, Basic, Premium, Unlimited)과 사용자 타입(Standard, Partner, Master)으로 빌드합니다.

사용법:
    python build_all_plugins_all_editions.py
"""

import sys
import os
import zipfile
import time
from pathlib import Path
from typing import Dict

# 3j_dev_toolkit 모듈 임포트
sys.path.insert(0, str(Path(__file__).parent))
exec(open('3j_dev_toolkit.py', encoding='utf-8').read().split('if __name__')[0])


def get_all_plugin_versions(base_path: Path) -> Dict[str, str]:
    """모든 플러그인의 버전을 읽어옵니다."""
    builder = EditionBuilder(base_path)
    versions = {}
    
    print("\n📦 플러그인 버전 확인 중...")
    print("-" * 60)
    
    for plugin_key, plugin_config in builder.EDITION_PLUGINS.items():
        plugin_path = base_path / plugin_config['source_dir']
        if plugin_path.exists():
            info = PluginInfo(str(plugin_path))
            version = info.version or '1.0.0'
            versions[plugin_key] = version
            print(f"  ✅ {plugin_config['display_name']}: v{version}")
        else:
            versions[plugin_key] = '1.0.0'
            print(f"  ⚠️ {plugin_config['display_name']}: 소스 폴더 없음 (기본값 1.0.0 사용)")
    
    return versions


def build_all_editions():
    """모든 플러그인을 모든 에디션으로 빌드"""
    base_path = Path(__file__).parent
    
    print("\n" + "=" * 60)
    print("🚀 3J Labs - 모든 플러그인 에디션별 빌드 시작")
    print("=" * 60)
    
    # 플러그인 버전 읽기
    versions = get_all_plugin_versions(base_path)
    
    # 빌더 초기화
    builder = EditionBuilder(base_path, print)
    
    # 마스터 전용 플러그인 제외
    # Neural Link와 WooCommerce License Bridge는 개발사 내부에서만 사용하는 라이센스/업데이트 관리 플러그인
    master_only_plugins = {'acf-css-neural-link', 'acf-css-woo-license'}
    buildable_plugins = [k for k in builder.EDITION_PLUGINS.keys() if k not in master_only_plugins]
    
    # 빌드 매트릭스 확인
    print(f"\n📋 빌드 매트릭스:")
    print(f"  - 총 플러그인: {len(builder.EDITION_PLUGINS)}개")
    print(f"  - 빌드 대상 플러그인: {len(buildable_plugins)}개 (마스터 전용 제외)")
    print(f"  - 에디션 조합: {len(EditionConfig.BUILD_MATRIX)}개")
    print(f"  - 예상 빌드 수: {len(buildable_plugins) * len(EditionConfig.BUILD_MATRIX)}개")
    print(f"  - 마스터 전용 플러그인: {', '.join(master_only_plugins)}")
    
    # 사용자 확인 (자동 진행 옵션)
    print("\n⚠️  이 작업은 많은 시간이 소요될 수 있습니다.")
    print("💡 자동 진행 모드: 모든 플러그인을 빌드합니다.")
    print("   (취소하려면 Ctrl+C를 누르세요)")
    time.sleep(2)  # 사용자가 취소할 시간 제공
    
    # 모든 플러그인 빌드
    print("\n🔨 빌드 시작...")
    print("-" * 60)
    
    results = builder.build_all_plugins_all_editions(versions)
    
    # 마스터 전용 플러그인 별도 빌드
    print("\n" + "-" * 60)
    print("🎯 마스터 전용 플러그인 빌드 (별도)")
    print("-" * 60)
    
    master_only_plugins_info = {
        'acf-css-manager': {
            'source_dir': 'acf-css-really-simple-style-management-center-master',
            'name': 'ACF CSS 설정 관리자 (Master 올인원)',
        },
        'acf-css-neural-link': {
            'source_dir': 'acf-css-neural-link',
            'name': 'ACF CSS Neural Link',
        },
        'acf-css-woo-license': {
            'source_dir': 'marketing/wordpress-plugins/acf-css-woo-license',
            'name': 'ACF CSS WooCommerce License Bridge',
        },
    }
    
    for plugin_key, plugin_info in master_only_plugins_info.items():
        plugin_path = base_path / plugin_info['source_dir']
        if plugin_path.exists():
            plugin_info_obj = PluginInfo(str(plugin_path))
            plugin_version = plugin_info_obj.version or versions.get(plugin_key, '1.0.0')
            
            folder_name = plugin_info['source_dir'].replace('/', '-').replace('\\', '-')
            master_zip_name = f"{folder_name}-v{plugin_version}.zip"
            master_zip_path = builder.output_dir / master_zip_name
            
            # 마스터 ZIP 생성 (폴더 포함)
            with zipfile.ZipFile(master_zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                for root, dirs, files in os.walk(plugin_path):
                    # 제외할 디렉토리
                    dirs[:] = [d for d in dirs if d not in {'.git', '__pycache__', 'node_modules', 'tests', '.DS_Store'}]
                    for file in files:
                        if not file.startswith('.') and file not in {'Thumbs.db'}:
                            file_path = Path(root) / file
                            arcname = file_path.relative_to(plugin_path.parent)
                            zf.write(file_path, arcname)
            
            size_kb = master_zip_path.stat().st_size / 1024
            print(f"✅ {plugin_info['name']}: {master_zip_name} ({size_kb:.1f} KB)")
            results.append(master_zip_path)
        else:
            print(f"⚠️ {plugin_info['name']} 폴더를 찾을 수 없습니다: {plugin_path}")
    
    # 결과 요약
    print("\n" + "=" * 60)
    print("✅ 빌드 완료!")
    print("=" * 60)
    print(f"  총 생성된 ZIP 파일: {len(results)}개")
    print(f"  출력 위치: {builder.output_dir}")
    
    # 번들 생성 제안
    print("\n📦 번들 패키지 생성")
    print("💡 모든 ZIP 파일을 하나의 번들로 묶습니다...")
    main_version = versions.get('acf-css-manager', '13.4.7')
    bundle_name = f"3J-Labs-ACF-CSS-Complete-Bundle-v{main_version}.zip"
    bundle_path = builder.create_bundle(results, bundle_name)
    if bundle_path:
        size_mb = bundle_path.stat().st_size / (1024 * 1024)
        print(f"✅ 번들 생성 완료: {bundle_name} ({size_mb:.2f} MB)")
    
    print("\n🎉 모든 작업이 완료되었습니다!")


if __name__ == '__main__':
    try:
        build_all_editions()
    except KeyboardInterrupt:
        print("\n\n❌ 사용자에 의해 중단되었습니다")
        sys.exit(1)
    except Exception as e:
        print(f"\n\n❌ 오류 발생: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)

"""
3J Labs - 모든 플러그인 에디션별 빌드 스크립트
모든 플러그인을 모든 에디션(Free, Basic, Premium, Unlimited)과 사용자 타입(Standard, Partner, Master)으로 빌드합니다.

사용법:
    python build_all_plugins_all_editions.py
"""

import sys
import os
import zipfile
from pathlib import Path
from typing import Dict

# 3j_dev_toolkit 모듈 임포트
base_path = Path(__file__).parent
sys.path.insert(0, str(base_path))

# 3j_dev_toolkit의 클래스들 임포트
toolkit_path = base_path / '3j_dev_toolkit.py'
with open(toolkit_path, 'r', encoding='utf-8') as f:
    toolkit_code = f.read()
    # if __name__ 이전까지만 실행
    exec(toolkit_code.split('if __name__')[0])

def get_all_plugin_versions(base_path: Path) -> Dict[str, str]:
    """모든 플러그인의 버전을 읽어옵니다."""
    builder = EditionBuilder(base_path)
    versions = {}
    
    print("\n📦 플러그인 버전 확인 중...")
    print("-" * 60)
    
    for plugin_key, plugin_config in builder.EDITION_PLUGINS.items():
        plugin_path = base_path / plugin_config['source_dir']
        if plugin_path.exists():
            info = PluginInfo(str(plugin_path))
            version = info.version or '1.0.0'
            versions[plugin_key] = version
            print(f"  ✅ {plugin_config['display_name']}: v{version}")
        else:
            versions[plugin_key] = '1.0.0'
            print(f"  ⚠️ {plugin_config['display_name']}: 소스 폴더 없음 (기본값 1.0.0 사용)")
    
    return versions


def build_all_editions():
    """모든 플러그인을 모든 에디션으로 빌드"""
    base_path = Path(__file__).parent
    
    print("\n" + "=" * 60)
    print("🚀 3J Labs - 모든 플러그인 에디션별 빌드 시작")
    print("=" * 60)
    
    # 플러그인 버전 읽기
    versions = get_all_plugin_versions(base_path)
    
    # 빌더 초기화
    builder = EditionBuilder(base_path, print)
    
    # 빌드 매트릭스 확인
    print(f"\n📋 빌드 매트릭스:")
    print(f"  - 총 플러그인: {len(builder.EDITION_PLUGINS)}개")
    print(f"  - 에디션 조합: {len(EditionConfig.BUILD_MATRIX)}개")
    print(f"  - 예상 빌드 수: {len(builder.EDITION_PLUGINS) * len(EditionConfig.BUILD_MATRIX)}개")
    print(f"\n  에디션 조합:")
    for edition, user_type in EditionConfig.BUILD_MATRIX:
        edition_name = EditionConfig.EDITIONS[edition]['display_name']
        user_name = EditionConfig.USER_TYPES[user_type]['display_name']
        print(f"    - {edition_name} ({user_name})")
    
    # 자동 실행
    print("\n🔨 빌드 시작...")
    print("-" * 60)
    
    # 모든 플러그인 빌드
    results = builder.build_all_plugins_all_editions(versions)
    
    # 마스터 올인원 플러그인 별도 빌드
    print("\n" + "-" * 60)
    print("🎯 마스터 올인원 플러그인 빌드 (별도)")
    print("-" * 60)
    
    master_path = base_path / 'acf-css-really-simple-style-management-center-master'
    if master_path.exists():
        master_info = PluginInfo(str(master_path))
        master_version = master_info.version or versions.get('acf-css-manager', '13.4.7')
        
        master_zip_name = f"acf-css-really-simple-style-management-center-master-v{master_version}.zip"
        master_zip_path = builder.output_dir / master_zip_name
        
        # 마스터 ZIP 생성 (폴더 포함)
        with zipfile.ZipFile(master_zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
            for root, dirs, files in os.walk(master_path):
                # 제외할 디렉토리
                dirs[:] = [d for d in dirs if d not in {'.git', '__pycache__', 'node_modules', 'tests', '.DS_Store'}]
                for file in files:
                    if not file.startswith('.') and file not in {'Thumbs.db'}:
                        file_path = Path(root) / file
                        arcname = file_path.relative_to(master_path.parent)
                        zf.write(file_path, arcname)
        
        size_kb = master_zip_path.stat().st_size / 1024
        print(f"✅ 마스터 올인원: {master_zip_name} ({size_kb:.1f} KB)")
        results.append(master_zip_path)
    else:
        print("⚠️ 마스터 플러그인 폴더를 찾을 수 없습니다")
    
    # 결과 요약
    print("\n" + "=" * 60)
    print("✅ 빌드 완료!")
    print("=" * 60)
    print(f"  총 생성된 ZIP 파일: {len(results)}개")
    print(f"  출력 위치: {builder.output_dir}")
    
    # 번들 자동 생성
    print("\n📦 번들 패키지 생성 중...")
    main_version = versions.get('acf-css-manager', '13.4.7')
    bundle_name = f"3J-Labs-ACF-CSS-Complete-Bundle-v{main_version}.zip"
    bundle_path = builder.create_bundle(results, bundle_name)
    if bundle_path:
        size_mb = bundle_path.stat().st_size / (1024 * 1024)
        print(f"✅ 번들 생성 완료: {bundle_name} ({size_mb:.2f} MB)")
    
    # 자동 정리 실행
    print("\n🧹 dist 폴더 자동 정리 중...")
    try:
        import cleanup_dist_folder
        cleanup_dist_folder.cleanup_dist_folder()
        print("✅ 자동 정리 완료")
    except Exception as e:
        print(f"⚠️ 자동 정리 실패: {e}")
        print("   수동으로 cleanup_dist_folder.py를 실행하세요.")
    
    print("\n🎉 모든 작업이 완료되었습니다!")


if __name__ == '__main__':
    try:
        build_all_editions()
    except KeyboardInterrupt:
        print("\n\n❌ 사용자에 의해 중단되었습니다")
        sys.exit(1)
    except Exception as e:
        print(f"\n\n❌ 오류 발생: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
