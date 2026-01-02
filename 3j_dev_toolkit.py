#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
3J Labs Development Toolkit
제이x제니x제이슨 연구소 개발 도구 키트

AI 런처와 별도로 동작하는 인터랙티브 개발 도구입니다.
플러그인 빌드, 배포, 버전 관리를 GUI로 수행할 수 있습니다.

Version: 3.0.0
Author: 3J Labs (Jay & Jason & Jenny)
Updated: 2026-01-02 - Admin Menu Editor Pro 추가, AI Extension, Neural Link 반영

===============================================================================
작업 원칙 (Development Principles)
===============================================================================

1. 터미널 Python REPL 상태 감지:
   - 프롬프트가 ">>>"로 표시되면 exit() 후 재시도
   - 모든 명령이 Python 코드로 해석되어 SyntaxError 발생 가능

2. 타임아웃 및 재시도:
   - 40초 이상 응답 없거나 유의미한 진행 없으면 중지 후 다른 방법으로 재시도
   - 복잡한 PowerShell 명령은 .ps1 스크립트 파일로 분리

3. ZIP 빌드 주의사항:
   - WordPress 플러그인 ZIP은 플러그인 폴더가 포함되어야 함
   - Compress-Archive -Path $folder (not $folder\\*)
   - 이렇게 해야 WordPress 업로드 설치 시 올바르게 인식됨

===============================================================================
"""

import os
import sys
import json
import shutil
import zipfile
import subprocess
import re
import time
from datetime import datetime
from pathlib import Path
from typing import Dict, List, Optional, Tuple

# Tkinter GUI
try:
    import tkinter as tk
    from tkinter import ttk, messagebox, filedialog, scrolledtext
except ImportError:
    print("Tkinter가 설치되어 있지 않습니다.")
    sys.exit(1)


# ============================================================
# 에디션 및 사용자 타입 설정
# ============================================================

class EditionConfig:
    """플러그인 에디션 설정"""
    
    # 버전별 에디션 (요금제)
    EDITIONS = {
        'free': {
            'name': 'Free',
            'display_name': 'Free',
            'license_type': 'FREE',
            'features': ['basic_colors', 'basic_typography', 'css_variables'],
            'folder_suffix': 'free',
            'remove_pro_features': True,
        },
        'basic': {
            'name': 'Pro Basic',
            'display_name': 'Pro Basic',
            'license_type': 'BASIC',
            'features': ['all_free', 'presets', 'export_css', 'custom_fonts'],
            'folder_suffix': 'basic',
            'remove_pro_features': False,
        },
        'premium': {
            'name': 'Pro Premium',
            'display_name': 'Pro Premium',
            'license_type': 'PREMIUM',
            'features': ['all_basic', 'figma_export', 'pdf_export', 'ai_suggestions'],
            'folder_suffix': 'premium',
            'remove_pro_features': False,
        },
        'unlimited': {
            'name': 'Pro Unlimited',
            'display_name': 'Pro Unlimited',
            'license_type': 'UNLIMITED',
            'features': ['all_premium', 'white_label', 'multisite', 'priority_support'],
            'folder_suffix': 'unlimited',
            'remove_pro_features': False,
        },
    }
    
    # 사용자 타입별 설정
    USER_TYPES = {
        'standard': {
            'name': '일반 사용자',
            'display_name': 'Standard',
            'branding': True,
            'update_channel': 'stable',
            'debug_mode': False,
        },
        'partner': {
            'name': '파트너',
            'display_name': 'Partner',
            'branding': True,  # 파트너 브랜딩 가능
            'update_channel': 'beta',
            'debug_mode': True,
            'special_features': ['partner_dashboard', 'client_management'],
        },
        'master': {
            'name': '마스터 (개발용)',
            'display_name': 'Master',
            'branding': False,  # 3J Labs 브랜딩 유지
            'update_channel': 'alpha',
            'debug_mode': True,
            'special_features': ['all_features', 'dev_tools', 'testing_mode'],
        },
    }
    
    # 빌드 매트릭스 (생성할 조합)
    BUILD_MATRIX = [
        # (에디션, 사용자타입)
        ('free', 'standard'),
        ('basic', 'standard'),
        ('premium', 'standard'),
        ('unlimited', 'standard'),
        ('basic', 'partner'),
        ('premium', 'partner'),
        ('unlimited', 'partner'),
        ('free', 'master'),  # 마스터는 모든 에디션 접근 가능
        ('basic', 'master'),
        ('premium', 'master'),
        ('unlimited', 'master'),
    ]


class PluginInfo:
    """플러그인 정보 파서"""
    
    def __init__(self, plugin_path: str):
        self.path = Path(plugin_path)
        self.name = ""
        self.version = ""
        self.author = ""
        self.description = ""
        self.text_domain = ""
        self._parse_header()
    
    def _parse_header(self):
        """플러그인 헤더 파싱"""
        main_file = None
        for f in self.path.glob("*.php"):
            with open(f, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read(4096)
                if 'Plugin Name:' in content:
                    main_file = f
                    break
        
        if not main_file:
            return
        
        with open(main_file, 'r', encoding='utf-8', errors='ignore') as file:
            content = file.read(8192)
        
        patterns = {
            'name': r'Plugin Name:\s*(.+)',
            'version': r'Version:\s*(.+)',
            'author': r'Author:\s*(.+)',
            'description': r'Description:\s*(.+)',
            'text_domain': r'Text Domain:\s*(.+)',
        }
        
        for key, pattern in patterns.items():
            match = re.search(pattern, content)
            if match:
                setattr(self, key, match.group(1).strip())
    
    def update_version(self, new_version: str) -> bool:
        """버전 업데이트"""
        for f in self.path.glob("*.php"):
            try:
                with open(f, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                if 'Plugin Name:' in content:
                    # Version 헤더 업데이트
                    content = re.sub(
                        r'(Version:\s*)[\d.]+',
                        f'\\g<1>{new_version}',
                        content
                    )
                    
                    # define 상수 업데이트
                    content = re.sub(
                        r"(define\s*\(\s*['\"][A-Z_]+VERSION['\"]\s*,\s*['\"])[\d.]+(['\"])",
                        f'\\g<1>{new_version}\\g<2>',
                        content
                    )
                    
                    with open(f, 'w', encoding='utf-8') as file:
                        file.write(content)
                    
                    self.version = new_version
                    return True
            except Exception as e:
                print(f"Error updating {f}: {e}")
        
        return False


class EditionBuilder:
    """에디션별 플러그인 빌더"""
    
    # 에디션 빌드를 지원하는 플러그인 목록 (2026-01-02 최신 업데이트)
    EDITION_PLUGINS = {
        'acf-css-manager': {
            'source_dir': 'acf-css-really-simple-style-management-center-master',
            'main_file': 'acf-css-really-simple-style-guide.php',
            'version_constant': 'JJ_STYLE_GUIDE_VERSION',
            'license_constant': 'JJ_STYLE_GUIDE_LICENSE_TYPE',
            'edition_constant': 'JJ_STYLE_GUIDE_EDITION',
            'user_type_constant': 'JJ_STYLE_GUIDE_USER_TYPE',
            'display_name': 'ACF CSS 설정 관리자',
            'description': 'Advanced Custom Fonts & Colors & Styles Setting Manager',
        },
        'admin-menu-editor-pro': {
            'source_dir': 'admin-menu-editor-pro',
            'main_file': 'admin-menu-editor-pro.php',
            'version_constant': 'JJ_ADMIN_MENU_EDITOR_VERSION',
            'license_constant': 'JJ_ADMIN_MENU_EDITOR_LICENSE',
            'edition_constant': 'JJ_ADMIN_MENU_EDITOR_EDITION',
            'user_type_constant': 'JJ_ADMIN_MENU_EDITOR_USER_TYPE',
            'display_name': 'Admin Menu Editor Pro',
            'description': 'Advanced WordPress Menu Management',
        },
        'acf-code-snippets-box': {
            'source_dir': 'acf-code-snippets-box',
            'main_file': 'acf-code-snippets-box.php',
            'version_constant': 'ACF_CSB_VERSION',
            'license_constant': 'ACF_CSB_LICENSE_TYPE',
            'edition_constant': 'ACF_CSB_EDITION',
            'user_type_constant': 'ACF_CSB_USER_TYPE',
            'display_name': 'ACF Code Snippets Box',
            'description': 'Advanced Custom Function Manager',
        },
        'acf-css-woocommerce-toolkit': {
            'source_dir': 'acf-css-woocommerce-toolkit',
            'main_file': 'acf-css-woocommerce-toolkit.php',
            'version_constant': 'ACF_CSS_WC_VERSION',
            'license_constant': 'ACF_CSS_WC_LICENSE_TYPE',
            'edition_constant': 'ACF_CSS_WC_EDITION',
            'user_type_constant': 'ACF_CSS_WC_USER_TYPE',
            'display_name': 'ACF CSS WooCommerce Toolkit',
            'description': 'Advanced Commerce Styling',
        },
        'acf-css-ai-extension': {
            'source_dir': 'acf-css-ai-extension',
            'main_file': 'acf-css-ai-extension.php',
            'version_constant': 'ACF_CSS_AI_VERSION',
            'license_constant': 'ACF_CSS_AI_LICENSE_TYPE',
            'edition_constant': 'ACF_CSS_AI_EDITION',
            'user_type_constant': 'ACF_CSS_AI_USER_TYPE',
            'display_name': 'ACF CSS AI Extension',
            'description': 'AI-Powered Style Intelligence',
        },
        'acf-css-neural-link': {
            'source_dir': 'acf-css-neural-link',
            'main_file': 'acf-css-neural-link.php',
            'version_constant': 'ACF_CSS_NL_VERSION',
            'license_constant': 'ACF_CSS_NL_LICENSE_TYPE',
            'edition_constant': 'ACF_CSS_NL_EDITION',
            'user_type_constant': 'ACF_CSS_NL_USER_TYPE',
            'display_name': 'ACF CSS Neural Link',
            'description': 'License & Update Manager',
        },
        'acf-nudge-flow': {
            'source_dir': 'acf-nudge-flow',
            'main_file': 'acf-nudge-flow.php',
            'version_constant': 'ACF_NUDGE_FLOW_VERSION',
            'license_constant': 'ACF_NUDGE_FLOW_LICENSE_TYPE',
            'edition_constant': 'ACF_NUDGE_FLOW_EDITION',
            'user_type_constant': 'ACF_NUDGE_FLOW_USER_TYPE',
            'display_name': 'ACF MBA (Nudge Flow)',
            'description': 'Advanced Custom Funnel Marketing Boosting Accelerator',
        },
        'wp-bulk-manager': {
            'source_dir': 'wp-bulk-manager',
            'main_file': 'wp-bulk-installer.php',
            'version_constant': 'WP_BULK_MANAGER_VERSION',
            'license_constant': 'JJ_BULK_INSTALLER_LICENSE',
            'edition_constant': 'WP_BULK_MANAGER_EDITION',
            'user_type_constant': 'WP_BULK_MANAGER_USER_TYPE',
            'display_name': 'WP Bulk Manager',
            'description': 'Plugin & Theme Bulk Installer and Editor',
        },
    }
    
    def __init__(self, base_path: Path, log_callback=None):
        self.base_path = base_path
        self.source_dir = base_path / 'acf-css-really-simple-style-management-center-master'
        self.output_dir = base_path / 'dist'
        self.log = log_callback or print
    
    def build_edition(self, edition: str, user_type: str, version: str) -> Optional[Path]:
        """특정 에디션과 사용자 타입으로 플러그인 빌드"""
        
        edition_config = EditionConfig.EDITIONS.get(edition)
        user_config = EditionConfig.USER_TYPES.get(user_type)
        
        if not edition_config or not user_config:
            self.log(f"❌ 잘못된 설정: edition={edition}, user_type={user_type}")
            return None
        
        self.log(f"🔨 빌드 시작: {edition_config['display_name']} ({user_config['display_name']})")
        
        # 출력 디렉토리 생성
        self.output_dir.mkdir(exist_ok=True)
        
        # 폴더명 생성
        if user_type == 'standard':
            folder_name = f"acf-css-really-simple-style-management-center-{edition}"
        else:
            folder_name = f"acf-css-really-simple-style-management-center-{edition}-{user_type}"
        
        work_dir = self.output_dir / folder_name
        
        # 기존 폴더 삭제 후 복사
        self._safe_copy(self.source_dir, work_dir)
        
        # 메인 파일 수정
        self._modify_main_file(work_dir, edition, user_type, version, edition_config, user_config)
        
        # ZIP 생성
        zip_name = f"{folder_name}-v{version}.zip"
        zip_path = self.output_dir / zip_name
        
        self._create_zip(work_dir, zip_path)
        
        # 작업 디렉토리 삭제 (선택적)
        # shutil.rmtree(work_dir, ignore_errors=True)
        
        self.log(f"✅ 빌드 완료: {zip_name}")
        return zip_path
    
    def _safe_copy(self, src: Path, dst: Path):
        """안전하게 디렉토리 복사"""
        for _ in range(3):
            try:
                if dst.exists():
                    shutil.rmtree(dst, ignore_errors=True)
                    time.sleep(0.5)
                
                # 제외할 파일/폴더
                def ignore_patterns(directory, files):
                    ignore = {'.git', '__pycache__', 'node_modules', '.DS_Store', 
                              'Thumbs.db', '.github', 'tests', '.vscode'}
                    return [f for f in files if f in ignore or f.endswith('.pyc')]
                
                shutil.copytree(src, dst, ignore=ignore_patterns)
                return
            except Exception as e:
                self.log(f"⚠️ 복사 재시도... {e}")
                time.sleep(1)
    
    def _modify_main_file(self, work_dir: Path, edition: str, user_type: str, 
                         version: str, edition_config: dict, user_config: dict):
        """메인 플러그인 파일 수정"""
        main_file = work_dir / 'acf-css-really-simple-style-guide.php'
        
        if not main_file.exists():
            self.log(f"⚠️ 메인 파일을 찾을 수 없음: {main_file}")
            return
        
        with open(main_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 1. 라이센스 타입 변경
        content = re.sub(
            r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);",
            f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{edition_config['license_type']}' );",
            content
        )
        
        # 2. 에디션 상수 변경
        content = re.sub(
            r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);",
            f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );",
            content
        )
        
        # 3. 사용자 타입 상수 추가/변경
        if "JJ_STYLE_GUIDE_USER_TYPE" in content:
            content = re.sub(
                r"define\(\s*'JJ_STYLE_GUIDE_USER_TYPE',\s*'[^']+'\s*\);",
                f"define( 'JJ_STYLE_GUIDE_USER_TYPE', '{user_type.upper()}' );",
                content
            )
        else:
            # 상수가 없으면 추가
            insert_after = "define( 'JJ_STYLE_GUIDE_EDITION'"
            insert_text = f"\ndefine( 'JJ_STYLE_GUIDE_USER_TYPE', '{user_type.upper()}' );"
            content = content.replace(
                f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );",
                f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );{insert_text}"
            )
        
        # 4. 버전 업데이트
        content = re.sub(
            r"(Version:\s*)[\d.]+",
            f"\\g<1>{version}",
            content
        )
        content = re.sub(
            r"(define\(\s*'JJ_STYLE_GUIDE_VERSION',\s*')[\d.]+'",
            f"\\g<1>{version}'",
            content
        )
        
        # 5. 플러그인 이름 수정
        if edition != 'master' and user_type != 'master':
            content = content.replace(' (Master)', '')
        
        if user_type == 'partner':
            content = re.sub(
                r"(Plugin Name:\s*.+?)(\s*\*)",
                f"\\g<1> - Partner Edition\\g<2>",
                content
            )
        
        # 6. 디버그 모드 설정
        if user_config.get('debug_mode', False):
            content = re.sub(
                r"define\(\s*'JJ_STYLE_GUIDE_DEBUG',\s*(true|false)\s*\);",
                "define( 'JJ_STYLE_GUIDE_DEBUG', true );",
                content
            )
        else:
            content = re.sub(
                r"define\(\s*'JJ_STYLE_GUIDE_DEBUG',\s*(true|false)\s*\);",
                "define( 'JJ_STYLE_GUIDE_DEBUG', false );",
                content
            )
        
        with open(main_file, 'w', encoding='utf-8') as f:
            f.write(content)
    
    def _create_zip(self, source_dir: Path, zip_path: Path):
        """ZIP 파일 생성"""
        with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
            for root, dirs, files in os.walk(source_dir):
                # .git 등 제외
                dirs[:] = [d for d in dirs if not d.startswith('.')]
                
                for file in files:
                    if file.startswith('.'):
                        continue
                    
                    file_path = Path(root) / file
                    arcname = file_path.relative_to(source_dir.parent)
                    zf.write(file_path, arcname)
    
    def build_all_editions(self, version: str) -> List[Path]:
        """모든 에디션 빌드 (빌드 매트릭스 기반)"""
        results = []
        
        for edition, user_type in EditionConfig.BUILD_MATRIX:
            try:
                zip_path = self.build_edition(edition, user_type, version)
                if zip_path:
                    results.append(zip_path)
            except Exception as e:
                self.log(f"❌ 빌드 실패: {edition}/{user_type} - {e}")
        
        # 빌드 완료 후 대시보드 자동 업데이트
        try:
            update_dashboard_html()
            self.log("✅ 대시보드 자동 업데이트 완료")
        except Exception as e:
            self.log(f"⚠️ 대시보드 업데이트 실패: {e}")
        
        return results
    
    def build_selected_editions(self, selections: List[Tuple[str, str]], version: str) -> List[Path]:
        """선택한 에디션만 빌드"""
        results = []
        
        for edition, user_type in selections:
            try:
                zip_path = self.build_edition(edition, user_type, version)
                if zip_path:
                    results.append(zip_path)
            except Exception as e:
                self.log(f"❌ 빌드 실패: {edition}/{user_type} - {e}")
        
        # 빌드 완료 후 대시보드 자동 업데이트
        try:
            update_dashboard_html()
            self.log("✅ 대시보드 자동 업데이트 완료")
        except Exception as e:
            self.log(f"⚠️ 대시보드 업데이트 실패: {e}")
        
        return results
    
    def create_bundle(self, zip_files: List[Path], bundle_name: str) -> Optional[Path]:
        """번들 패키지 생성"""
        if not zip_files:
            return None
        
        bundle_path = self.output_dir / bundle_name
        
        with zipfile.ZipFile(bundle_path, 'w', zipfile.ZIP_DEFLATED) as zf:
            for zip_file in zip_files:
                if zip_file.exists():
                    zf.write(zip_file, zip_file.name)
        
        self.log(f"📦 번들 생성 완료: {bundle_name}")
        return bundle_path
    
    def build_plugin_edition(self, plugin_key: str, edition: str, user_type: str, 
                            versions: Dict[str, str]) -> Optional[Path]:
        """특정 플러그인을 에디션별로 빌드"""
        
        plugin_config = self.EDITION_PLUGINS.get(plugin_key)
        if not plugin_config:
            self.log(f"❌ 알 수 없는 플러그인: {plugin_key}")
            return None
        
        edition_config = EditionConfig.EDITIONS.get(edition)
        user_config = EditionConfig.USER_TYPES.get(user_type)
        
        if not edition_config or not user_config:
            return None
        
        source_dir = self.base_path / plugin_config['source_dir']
        if not source_dir.exists():
            self.log(f"⚠️ 소스 폴더 없음: {source_dir}")
            return None
        
        version = versions.get(plugin_key, '1.0.0')
        self.log(f"🔨 [{plugin_key}] 빌드: {edition_config['display_name']} ({user_config['display_name']})")
        
        # 출력 폴더명 생성
        base_name = plugin_config['source_dir']
        if user_type == 'standard':
            folder_name = f"{base_name}-{edition}"
        else:
            folder_name = f"{base_name}-{edition}-{user_type}"
        
        work_dir = self.output_dir / folder_name
        self._safe_copy(source_dir, work_dir)
        
        # 메인 파일 수정 (에디션/사용자 상수 추가)
        main_file = work_dir / plugin_config['main_file']
        if main_file.exists():
            self._inject_edition_constants(main_file, plugin_config, edition, user_type, 
                                          version, edition_config, user_config)
        
        # ZIP 생성
        zip_name = f"{folder_name}-v{version}.zip"
        zip_path = self.output_dir / zip_name
        self._create_zip(work_dir, zip_path)
        
        self.log(f"✅ [{plugin_key}] 완료: {zip_name}")
        return zip_path
    
    def _inject_edition_constants(self, main_file: Path, plugin_config: dict,
                                   edition: str, user_type: str, version: str,
                                   edition_config: dict, user_config: dict):
        """플러그인 파일에 에디션/사용자 상수 주입"""
        with open(main_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 버전 상수 업데이트
        version_const = plugin_config['version_constant']
        content = re.sub(
            rf"define\(\s*'{version_const}',\s*'[^']+'\s*\);",
            f"define( '{version_const}', '{version}' );",
            content
        )
        
        # 라이선스 상수 추가/업데이트
        license_const = plugin_config['license_constant']
        if license_const in content:
            content = re.sub(
                rf"define\(\s*'{license_const}',\s*'[^']+'\s*\);",
                f"define( '{license_const}', '{edition_config['license_type']}' );",
                content
            )
        else:
            # 상수가 없으면 버전 상수 다음에 추가
            insert_text = f"\ndefine( '{license_const}', '{edition_config['license_type']}' );"
            insert_text += f"\ndefine( '{plugin_config['edition_constant']}', '{edition}' );"
            insert_text += f"\ndefine( '{plugin_config['user_type_constant']}', '{user_type.upper()}' );"
            content = re.sub(
                rf"(define\(\s*'{version_const}',\s*'{version}'\s*\);)",
                f"\\1{insert_text}",
                content
            )
        
        with open(main_file, 'w', encoding='utf-8') as f:
            f.write(content)
    
    def build_all_plugins_all_editions(self, versions: Dict[str, str]) -> List[Path]:
        """모든 플러그인을 모든 에디션으로 빌드"""
        results = []
        
        for plugin_key in self.EDITION_PLUGINS.keys():
            for edition, user_type in EditionConfig.BUILD_MATRIX:
                try:
                    zip_path = self.build_plugin_edition(plugin_key, edition, user_type, versions)
                    if zip_path:
                        results.append(zip_path)
                except Exception as e:
                    self.log(f"❌ 빌드 실패: {plugin_key}/{edition}/{user_type} - {e}")
        
        # 빌드 완료 후 대시보드 자동 업데이트
        try:
            update_dashboard_html()
            self.log("✅ 대시보드 자동 업데이트 완료")
        except Exception as e:
            self.log(f"⚠️ 대시보드 업데이트 실패: {e}")
        
        return results


class DevToolkit(tk.Tk):
    """메인 GUI 애플리케이션"""
    
    def __init__(self):
        super().__init__()
        
        self.title("3J Labs Development Toolkit v2.0.0")
        self.geometry("1000x700")
        self.configure(bg='#1a1a2e')
        
        # 기본 경로
        self.base_path = Path(__file__).parent
        self.plugins = {}
        
        # 스타일 설정
        self.style = ttk.Style()
        self.style.theme_use('clam')
        self._configure_styles()
        
        # UI 구성
        self._create_header()
        self._create_main_content()
        self._create_status_bar()
        
        # 플러그인 로드
        self._load_plugins()
    
    def _configure_styles(self):
        """스타일 설정"""
        bg_color = '#1a1a2e'
        fg_color = '#eaeaea'
        accent_color = '#667eea'
        
        self.style.configure('TFrame', background=bg_color)
        self.style.configure('TLabel', background=bg_color, foreground=fg_color, font=('Segoe UI', 10))
        self.style.configure('TButton', font=('Segoe UI', 10, 'bold'))
        self.style.configure('Header.TLabel', font=('Segoe UI', 16, 'bold'), foreground=accent_color)
        self.style.configure('Title.TLabel', font=('Segoe UI', 24, 'bold'), foreground='#ffffff')
        self.style.configure('TNotebook', background=bg_color)
        self.style.configure('TNotebook.Tab', font=('Segoe UI', 10), padding=[15, 5])
    
    def _create_header(self):
        """헤더 생성"""
        header = ttk.Frame(self)
        header.pack(fill='x', padx=20, pady=15)
        
        # 로고 및 타이틀
        title_frame = ttk.Frame(header)
        title_frame.pack(side='left')
        
        ttk.Label(
            title_frame, 
            text="🛠️ 3J Labs Development Toolkit",
            style='Title.TLabel'
        ).pack(side='left')
        
        ttk.Label(
            title_frame,
            text="  제이x제니x제이슨 연구소",
            style='Header.TLabel'
        ).pack(side='left', padx=10)
        
        # 빠른 액션 버튼
        action_frame = ttk.Frame(header)
        action_frame.pack(side='right')
        
        ttk.Button(
            action_frame,
            text="🔄 새로고침",
            command=self._load_plugins
        ).pack(side='left', padx=5)
        
        ttk.Button(
            action_frame,
            text="📦 전체 빌드",
            command=self._build_all
        ).pack(side='left', padx=5)
    
    def _create_main_content(self):
        """메인 콘텐츠 생성"""
        # 탭 노트북
        self.notebook = ttk.Notebook(self)
        self.notebook.pack(fill='both', expand=True, padx=20, pady=10)
        
        # 탭 1: 플러그인 관리
        self.plugin_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.plugin_tab, text="📦 플러그인 관리")
        self._create_plugin_tab()
        
        # 탭 2: 에디션 빌드 (버전별/사용자별)
        self.edition_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.edition_tab, text="🏷️ 에디션 빌드")
        self._create_edition_tab()
        
        # 탭 3: 빌드 도구
        self.build_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.build_tab, text="🔨 빌드 도구")
        self._create_build_tab()
        
        # 탭 4: 배포
        self.deploy_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.deploy_tab, text="🚀 배포")
        self._create_deploy_tab()
        
        # 탭 5: 로그
        self.log_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.log_tab, text="📋 로그")
        self._create_log_tab()
    
    def _create_plugin_tab(self):
        """플러그인 관리 탭"""
        # 플러그인 리스트
        list_frame = ttk.Frame(self.plugin_tab)
        list_frame.pack(fill='both', expand=True, padx=10, pady=10)
        
        # 트리뷰
        columns = ('name', 'version', 'status')
        self.plugin_tree = ttk.Treeview(list_frame, columns=columns, show='headings', height=15)
        
        self.plugin_tree.heading('name', text='플러그인명')
        self.plugin_tree.heading('version', text='버전')
        self.plugin_tree.heading('status', text='상태')
        
        self.plugin_tree.column('name', width=400)
        self.plugin_tree.column('version', width=100)
        self.plugin_tree.column('status', width=150)
        
        # 스크롤바
        scrollbar = ttk.Scrollbar(list_frame, orient='vertical', command=self.plugin_tree.yview)
        self.plugin_tree.configure(yscrollcommand=scrollbar.set)
        
        self.plugin_tree.pack(side='left', fill='both', expand=True)
        scrollbar.pack(side='right', fill='y')
        
        # 버튼 프레임
        btn_frame = ttk.Frame(self.plugin_tab)
        btn_frame.pack(fill='x', padx=10, pady=10)
        
        ttk.Button(btn_frame, text="버전 업데이트", command=self._update_version).pack(side='left', padx=5)
        ttk.Button(btn_frame, text="ZIP 생성", command=self._create_zip).pack(side='left', padx=5)
        ttk.Button(btn_frame, text="문법 검사", command=self._check_syntax).pack(side='left', padx=5)
        ttk.Button(btn_frame, text="폴더 열기", command=self._open_folder).pack(side='left', padx=5)
    
    def _create_edition_tab(self):
        """에디션 빌드 탭 (버전별/사용자별)"""
        # 메인 프레임
        main_frame = ttk.Frame(self.edition_tab)
        main_frame.pack(fill='both', expand=True, padx=10, pady=10)
        
        # 상단: 버전 입력
        version_frame = ttk.LabelFrame(main_frame, text="📌 빌드 버전")
        version_frame.pack(fill='x', pady=5)
        
        ttk.Label(version_frame, text="버전:").pack(side='left', padx=10, pady=10)
        self.edition_version = ttk.Entry(version_frame, width=20)
        self.edition_version.insert(0, "13.3.0")
        self.edition_version.pack(side='left', padx=5, pady=10)
        
        ttk.Label(version_frame, text="(예: 13.3.0, 14.0.0-beta)").pack(side='left', padx=5)
        
        # 중앙: 에디션 선택
        selection_frame = ttk.Frame(main_frame)
        selection_frame.pack(fill='both', expand=True, pady=10)
        
        # 왼쪽: 요금제 (에디션) 선택
        edition_frame = ttk.LabelFrame(selection_frame, text="💰 요금제 (에디션)")
        edition_frame.pack(side='left', fill='both', expand=True, padx=5)
        
        self.edition_vars = {}
        for edition, config in EditionConfig.EDITIONS.items():
            var = tk.BooleanVar(value=True)
            self.edition_vars[edition] = var
            
            frame = ttk.Frame(edition_frame)
            frame.pack(fill='x', padx=10, pady=3)
            
            ttk.Checkbutton(frame, text=config['display_name'], variable=var).pack(side='left')
            ttk.Label(frame, text=f"  ({config['license_type']})", 
                     foreground='#888888').pack(side='left')
        
        # 오른쪽: 사용자 타입 선택
        user_frame = ttk.LabelFrame(selection_frame, text="👤 사용자 타입")
        user_frame.pack(side='left', fill='both', expand=True, padx=5)
        
        self.user_type_vars = {}
        for user_type, config in EditionConfig.USER_TYPES.items():
            var = tk.BooleanVar(value=(user_type == 'standard'))
            self.user_type_vars[user_type] = var
            
            frame = ttk.Frame(user_frame)
            frame.pack(fill='x', padx=10, pady=3)
            
            ttk.Checkbutton(frame, text=config['name'], variable=var).pack(side='left')
            ttk.Label(frame, text=f"  ({config['display_name']})", 
                     foreground='#888888').pack(side='left')
        
        # 빌드 매트릭스 미리보기
        preview_frame = ttk.LabelFrame(main_frame, text="📋 빌드 매트릭스 (생성될 패키지)")
        preview_frame.pack(fill='both', expand=True, pady=5)
        
        # 트리뷰로 빌드 목록 표시
        columns = ('edition', 'user_type', 'filename')
        self.matrix_tree = ttk.Treeview(preview_frame, columns=columns, show='headings', height=8)
        
        self.matrix_tree.heading('edition', text='요금제')
        self.matrix_tree.heading('user_type', text='사용자 타입')
        self.matrix_tree.heading('filename', text='파일명')
        
        self.matrix_tree.column('edition', width=120)
        self.matrix_tree.column('user_type', width=120)
        self.matrix_tree.column('filename', width=400)
        
        scrollbar = ttk.Scrollbar(preview_frame, orient='vertical', command=self.matrix_tree.yview)
        self.matrix_tree.configure(yscrollcommand=scrollbar.set)
        
        self.matrix_tree.pack(side='left', fill='both', expand=True, padx=5, pady=5)
        scrollbar.pack(side='right', fill='y', pady=5)
        
        # 미리보기 업데이트 버튼
        ttk.Button(preview_frame, text="🔄 미리보기 갱신", 
                  command=self._update_build_matrix_preview).pack(pady=5)
        
        # 하단: 빌드 버튼
        btn_frame = ttk.Frame(main_frame)
        btn_frame.pack(fill='x', pady=10)
        
        ttk.Button(btn_frame, text="🏷️ 선택 에디션 빌드", 
                  command=self._build_selected_editions).pack(side='left', padx=5)
        ttk.Button(btn_frame, text="📦 전체 에디션 빌드 (매트릭스)", 
                  command=self._build_all_editions).pack(side='left', padx=5)
        ttk.Button(btn_frame, text="🎁 번들 패키지 생성", 
                  command=self._create_bundle_package).pack(side='left', padx=5)
        ttk.Button(btn_frame, text="📂 출력 폴더 열기", 
                  command=self._open_dist_folder).pack(side='left', padx=5)
        
        # 초기 미리보기 업데이트
        self.after(100, self._update_build_matrix_preview)
    
    def _update_build_matrix_preview(self):
        """빌드 매트릭스 미리보기 업데이트"""
        self.matrix_tree.delete(*self.matrix_tree.get_children())
        
        version = self.edition_version.get() or "13.3.0"
        
        selected_editions = [e for e, v in self.edition_vars.items() if v.get()]
        selected_users = [u for u, v in self.user_type_vars.items() if v.get()]
        
        for edition in selected_editions:
            for user_type in selected_users:
                edition_config = EditionConfig.EDITIONS.get(edition, {})
                user_config = EditionConfig.USER_TYPES.get(user_type, {})
                
                if user_type == 'standard':
                    filename = f"acf-css-really-simple-style-management-center-{edition}-v{version}.zip"
                else:
                    filename = f"acf-css-really-simple-style-management-center-{edition}-{user_type}-v{version}.zip"
                
                self.matrix_tree.insert('', 'end', values=(
                    edition_config.get('display_name', edition),
                    user_config.get('name', user_type),
                    filename
                ))
    
    def _build_selected_editions(self):
        """선택한 에디션 빌드"""
        version = self.edition_version.get()
        if not version:
            messagebox.showwarning("경고", "빌드 버전을 입력해주세요.")
            return
        
        selected_editions = [e for e, v in self.edition_vars.items() if v.get()]
        selected_users = [u for u, v in self.user_type_vars.items() if v.get()]
        
        if not selected_editions or not selected_users:
            messagebox.showwarning("경고", "최소 하나의 에디션과 사용자 타입을 선택해주세요.")
            return
        
        # 빌드 조합 생성
        selections = [(e, u) for e in selected_editions for u in selected_users]
        
        self._log(f"🚀 에디션 빌드 시작: {len(selections)}개 패키지")
        
        # EditionBuilder 사용
        builder = EditionBuilder(self.base_path, self._log)
        results = builder.build_selected_editions(selections, version)
        
        self._log(f"✅ 빌드 완료: {len(results)}/{len(selections)}개 성공")
        
        if results:
            messagebox.showinfo("빌드 완료", 
                f"{len(results)}개 패키지가 생성되었습니다.\n\n출력 위치: {builder.output_dir}")
    
    def _build_all_editions(self):
        """전체 에디션 빌드 (빌드 매트릭스 기반)"""
        version = self.edition_version.get()
        if not version:
            messagebox.showwarning("경고", "빌드 버전을 입력해주세요.")
            return
        
        if not messagebox.askyesno("확인", 
            f"빌드 매트릭스 기반으로 {len(EditionConfig.BUILD_MATRIX)}개 패키지를 생성합니다.\n계속하시겠습니까?"):
            return
        
        self._log(f"🚀 전체 에디션 빌드 시작 (매트릭스: {len(EditionConfig.BUILD_MATRIX)}개)")
        
        builder = EditionBuilder(self.base_path, self._log)
        results = builder.build_all_editions(version)
        
        self._log(f"✅ 전체 빌드 완료: {len(results)}/{len(EditionConfig.BUILD_MATRIX)}개 성공")
        
        if results:
            # 번들 생성 제안
            if messagebox.askyesno("번들 생성", "번들 패키지도 생성하시겠습니까?"):
                bundle_name = f"3J-Labs-ACF-CSS-All-Editions-v{version}.zip"
                builder.create_bundle(results, bundle_name)
            
            messagebox.showinfo("빌드 완료", 
                f"{len(results)}개 패키지가 생성되었습니다.\n\n출력 위치: {builder.output_dir}")
    
    def _create_bundle_package(self):
        """번들 패키지 생성"""
        version = self.edition_version.get() or "13.3.0"
        dist_dir = self.base_path / 'dist'
        
        if not dist_dir.exists():
            messagebox.showwarning("경고", "dist 폴더가 없습니다. 먼저 빌드를 수행해주세요.")
            return
        
        zip_files = list(dist_dir.glob("acf-css-*.zip"))
        if not zip_files:
            messagebox.showwarning("경고", "빌드된 ZIP 파일이 없습니다.")
            return
        
        bundle_name = f"3J-Labs-ACF-CSS-Bundle-v{version}.zip"
        
        builder = EditionBuilder(self.base_path, self._log)
        bundle_path = builder.create_bundle(zip_files, bundle_name)
        
        if bundle_path:
            messagebox.showinfo("번들 생성 완료", 
                f"번들 패키지가 생성되었습니다.\n\n{bundle_path}")
    
    def _open_dist_folder(self):
        """dist 폴더 열기"""
        dist_dir = self.base_path / 'dist'
        dist_dir.mkdir(exist_ok=True)
        
        if sys.platform == 'win32':
            os.startfile(dist_dir)
        elif sys.platform == 'darwin':
            subprocess.run(['open', dist_dir])
        else:
            subprocess.run(['xdg-open', dist_dir])
    
    def _create_build_tab(self):
        """빌드 도구 탭"""
        frame = ttk.Frame(self.build_tab)
        frame.pack(fill='both', expand=True, padx=20, pady=20)
        
        ttk.Label(frame, text="🔨 빌드 옵션", style='Header.TLabel').pack(anchor='w', pady=10)
        
        # 빌드 옵션
        options_frame = ttk.Frame(frame)
        options_frame.pack(fill='x', pady=10)
        
        self.include_dev = tk.BooleanVar(value=False)
        ttk.Checkbutton(options_frame, text="개발 파일 포함 (tests, docs)", variable=self.include_dev).pack(anchor='w')
        
        self.minify_js = tk.BooleanVar(value=False)
        ttk.Checkbutton(options_frame, text="JavaScript 압축", variable=self.minify_js).pack(anchor='w')
        
        self.minify_css = tk.BooleanVar(value=False)
        ttk.Checkbutton(options_frame, text="CSS 압축", variable=self.minify_css).pack(anchor='w')
        
        self.generate_pot = tk.BooleanVar(value=True)
        ttk.Checkbutton(options_frame, text="POT 파일 생성", variable=self.generate_pot).pack(anchor='w')
        
        # 빌드 대상
        ttk.Label(frame, text="📦 빌드 대상", style='Header.TLabel').pack(anchor='w', pady=(20, 10))
        
        self.build_target = ttk.Combobox(frame, width=50)
        self.build_target.pack(anchor='w')
        
        # 빌드 버튼
        ttk.Button(frame, text="🔨 빌드 시작", command=self._start_build).pack(anchor='w', pady=20)
    
    def _create_deploy_tab(self):
        """배포 탭"""
        frame = ttk.Frame(self.deploy_tab)
        frame.pack(fill='both', expand=True, padx=20, pady=20)
        
        ttk.Label(frame, text="🚀 배포 옵션", style='Header.TLabel').pack(anchor='w', pady=10)
        
        # 로컬 WordPress 배포
        local_frame = ttk.LabelFrame(frame, text="로컬 WordPress 배포")
        local_frame.pack(fill='x', pady=10)
        
        ttk.Label(local_frame, text="Docker 컨테이너:").pack(side='left', padx=10, pady=10)
        self.docker_container = ttk.Combobox(local_frame, values=['3j_php'], width=30)
        self.docker_container.set('3j_php')
        self.docker_container.pack(side='left', padx=10)
        
        ttk.Button(local_frame, text="배포", command=self._deploy_local).pack(side='left', padx=10)
        
        # Git 커밋
        git_frame = ttk.LabelFrame(frame, text="Git 버전 관리")
        git_frame.pack(fill='x', pady=10)
        
        ttk.Label(git_frame, text="커밋 메시지:").pack(anchor='w', padx=10, pady=5)
        self.commit_msg = ttk.Entry(git_frame, width=60)
        self.commit_msg.pack(anchor='w', padx=10, pady=5)
        
        btn_git = ttk.Frame(git_frame)
        btn_git.pack(anchor='w', padx=10, pady=5)
        
        ttk.Button(btn_git, text="커밋", command=self._git_commit).pack(side='left', padx=5)
        ttk.Button(btn_git, text="푸시", command=self._git_push).pack(side='left', padx=5)
        ttk.Button(btn_git, text="📊 대시보드 업데이트", command=self._update_dashboard).pack(side='left', padx=20)
    
    def _create_log_tab(self):
        """로그 탭"""
        self.log_text = scrolledtext.ScrolledText(
            self.log_tab,
            wrap=tk.WORD,
            font=('Consolas', 10),
            bg='#0d1117',
            fg='#c9d1d9',
            insertbackground='white'
        )
        self.log_text.pack(fill='both', expand=True, padx=10, pady=10)
        
        # 초기 로그
        self._log("3J Labs Development Toolkit 시작됨")
        self._log(f"작업 디렉토리: {self.base_path}")
    
    def _create_status_bar(self):
        """상태 바 생성"""
        self.status_bar = ttk.Label(
            self,
            text="준비됨",
            style='TLabel',
            anchor='w'
        )
        self.status_bar.pack(fill='x', padx=20, pady=5)
    
    def _log(self, message: str):
        """로그 추가"""
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        self.log_text.insert(tk.END, f"[{timestamp}] {message}\n")
        self.log_text.see(tk.END)
        self.update_idletasks()
    
    def _load_plugins(self):
        """플러그인 로드"""
        self.plugins.clear()
        self.plugin_tree.delete(*self.plugin_tree.get_children())
        
        plugin_dirs = [
            'acf-css-really-simple-style-management-center-master',
            'acf-css-really-simple-style-management-center-free',
            'acf-css-ai-extension',
            'acf-css-neural-link',
            'acf-code-snippets-box',
            'acf-css-woocommerce-toolkit',
            'wp-bulk-manager',
            'acf-nudge-flow',
        ]
        
        for dir_name in plugin_dirs:
            plugin_path = self.base_path / dir_name
            if plugin_path.exists():
                try:
                    info = PluginInfo(plugin_path)
                    self.plugins[dir_name] = info
                    
                    self.plugin_tree.insert('', 'end', values=(
                        info.name or dir_name,
                        info.version or 'N/A',
                        '✅ 정상'
                    ))
                    
                    self._log(f"플러그인 로드: {info.name} v{info.version}")
                except Exception as e:
                    self._log(f"플러그인 로드 실패: {dir_name} - {e}")
        
        # 빌드 대상 업데이트
        self.build_target['values'] = list(self.plugins.keys())
        if self.plugins:
            self.build_target.set(list(self.plugins.keys())[0])
        
        self._update_status(f"{len(self.plugins)}개 플러그인 로드됨")
    
    def _update_status(self, message: str):
        """상태 바 업데이트"""
        self.status_bar.config(text=message)
    
    def _get_selected_plugin(self):
        """선택된 플러그인 반환"""
        selection = self.plugin_tree.selection()
        if not selection:
            messagebox.showwarning("경고", "플러그인을 선택해주세요.")
            return None
        
        item = self.plugin_tree.item(selection[0])
        name = item['values'][0]
        
        for key, info in self.plugins.items():
            if info.name == name or key == name:
                return key, info
        
        return None
    
    def _update_version(self):
        """버전 업데이트"""
        selected = self._get_selected_plugin()
        if not selected:
            return
        
        key, info = selected
        
        new_version = tk.simpledialog.askstring(
            "버전 업데이트",
            f"현재 버전: {info.version}\n새 버전을 입력하세요:",
            initialvalue=info.version
        )
        
        if new_version and new_version != info.version:
            if info.update_version(new_version):
                self._log(f"버전 업데이트: {info.name} → v{new_version}")
                self._load_plugins()
                messagebox.showinfo("성공", f"버전이 {new_version}으로 업데이트되었습니다.")
            else:
                messagebox.showerror("오류", "버전 업데이트에 실패했습니다.")
    
    def _create_zip(self):
        """ZIP 파일 생성"""
        selected = self._get_selected_plugin()
        if not selected:
            return
        
        key, info = selected
        plugin_path = self.base_path / key
        
        # ZIP 파일명
        zip_name = f"{key}-v{info.version}.zip"
        zip_path = self.base_path / zip_name
        
        # 제외할 파일/폴더
        exclude = {'.git', '__pycache__', 'node_modules', '.DS_Store', 'Thumbs.db', 'tests', '.github'}
        
        try:
            with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                for root, dirs, files in os.walk(plugin_path):
                    # 제외 폴더 스킵
                    dirs[:] = [d for d in dirs if d not in exclude]
                    
                    for file in files:
                        if file.startswith('.'):
                            continue
                        
                        file_path = Path(root) / file
                        arcname = file_path.relative_to(plugin_path.parent)
                        zf.write(file_path, arcname)
            
            self._log(f"ZIP 생성 완료: {zip_name}")
            messagebox.showinfo("성공", f"ZIP 파일이 생성되었습니다:\n{zip_path}")
        except Exception as e:
            self._log(f"ZIP 생성 실패: {e}")
            messagebox.showerror("오류", f"ZIP 생성 실패: {e}")
    
    def _check_syntax(self):
        """PHP 문법 검사"""
        selected = self._get_selected_plugin()
        if not selected:
            return
        
        key, info = selected
        plugin_path = self.base_path / key
        
        self._log(f"문법 검사 시작: {key}")
        errors = []
        
        for php_file in plugin_path.rglob("*.php"):
            try:
                result = subprocess.run(
                    ['php', '-l', str(php_file)],
                    capture_output=True,
                    text=True,
                    timeout=10
                )
                if result.returncode != 0:
                    errors.append(f"{php_file.name}: {result.stderr}")
            except FileNotFoundError:
                messagebox.showerror("오류", "PHP가 설치되어 있지 않습니다.")
                return
            except Exception as e:
                errors.append(f"{php_file.name}: {e}")
        
        if errors:
            self._log(f"문법 오류 발견: {len(errors)}개")
            messagebox.showwarning("문법 오류", "\n".join(errors[:5]))
        else:
            self._log("문법 검사 통과!")
            messagebox.showinfo("성공", "모든 PHP 파일의 문법이 정상입니다.")
    
    def _open_folder(self):
        """폴더 열기"""
        selected = self._get_selected_plugin()
        if not selected:
            return
        
        key, info = selected
        plugin_path = self.base_path / key
        
        if sys.platform == 'win32':
            os.startfile(plugin_path)
        elif sys.platform == 'darwin':
            subprocess.run(['open', plugin_path])
        else:
            subprocess.run(['xdg-open', plugin_path])
    
    def _build_all(self):
        """전체 빌드 (모든 플러그인 + 모든 에디션)"""
        # 일반 플러그인 빌드
        for key, info in self.plugins.items():
            self._log(f"빌드 중: {key}")
            self._create_zip_for_plugin(key)
        
        # 에디션 빌드 제안
        if messagebox.askyesno("에디션 빌드", "에디션별 빌드도 수행하시겠습니까?"):
            version = self.edition_version.get() if hasattr(self, 'edition_version') else "13.3.0"
            builder = EditionBuilder(self.base_path, self._log)
            builder.build_all_editions(version)
        
        messagebox.showinfo("완료", "전체 빌드가 완료되었습니다.")
    
    def _start_build(self):
        """빌드 시작"""
        target = self.build_target.get()
        if not target:
            messagebox.showwarning("경고", "빌드 대상을 선택해주세요.")
            return
        
        self._log(f"빌드 시작: {target}")
        self._create_zip_for_plugin(target)
    
    def _create_zip_for_plugin(self, key: str):
        """특정 플러그인 ZIP 생성"""
        if key not in self.plugins:
            return
        
        info = self.plugins[key]
        plugin_path = self.base_path / key
        zip_name = f"{key}-v{info.version}.zip"
        zip_path = self.base_path / zip_name
        
        exclude = {'.git', '__pycache__', 'node_modules', '.DS_Store', 'tests'}
        
        try:
            with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                for root, dirs, files in os.walk(plugin_path):
                    dirs[:] = [d for d in dirs if d not in exclude]
                    for file in files:
                        if file.startswith('.'):
                            continue
                        file_path = Path(root) / file
                        arcname = file_path.relative_to(plugin_path.parent)
                        zf.write(file_path, arcname)
            
            self._log(f"빌드 완료: {zip_name}")
            self._update_status(f"빌드 완료: {zip_name}")
        except Exception as e:
            self._log(f"빌드 실패: {e}")
    
    def _deploy_local(self):
        """로컬 WordPress 배포"""
        container = self.docker_container.get()
        if not container:
            messagebox.showwarning("경고", "Docker 컨테이너를 선택해주세요.")
            return
        
        self._log(f"로컬 배포 시작: {container}")
        
        # Docker 실행 확인
        try:
            result = subprocess.run(['docker', 'ps'], capture_output=True, text=True)
            if container not in result.stdout:
                messagebox.showerror("오류", f"Docker 컨테이너 '{container}'가 실행 중이 아닙니다.")
                return
        except FileNotFoundError:
            messagebox.showerror("오류", "Docker가 설치되어 있지 않습니다.")
            return
        
        self._log("로컬 배포 완료 (Docker 볼륨 마운트 사용 중)")
        messagebox.showinfo("성공", "플러그인이 로컬 WordPress에 배포되었습니다.\nDocker 볼륨 마운트를 통해 자동 동기화됩니다.")
    
    def _git_commit(self):
        """Git 커밋"""
        message = self.commit_msg.get()
        if not message:
            messagebox.showwarning("경고", "커밋 메시지를 입력해주세요.")
            return
        
        try:
            subprocess.run(['git', 'add', '-A'], cwd=self.base_path, check=True)
            subprocess.run(['git', 'commit', '-m', message], cwd=self.base_path, check=True)
            self._log(f"커밋 완료: {message}")
            messagebox.showinfo("성공", "커밋이 완료되었습니다.")
        except subprocess.CalledProcessError as e:
            self._log(f"커밋 실패: {e}")
            messagebox.showerror("오류", f"커밋 실패: {e}")
    
    def _git_push(self):
        """Git 푸시"""
        try:
            subprocess.run(['git', 'push'], cwd=self.base_path, check=True)
            self._log("푸시 완료")
            messagebox.showinfo("성공", "푸시가 완료되었습니다.")
        except subprocess.CalledProcessError as e:
            self._log(f"푸시 실패: {e}")
            messagebox.showerror("오류", f"푸시 실패: {e}")
    
    def _update_dashboard(self):
        """외부 대시보드 업데이트"""
        self._log("📊 대시보드 업데이트 시작...")
        
        try:
            update_external_dashboard()
            self._log("✅ 대시보드 업데이트 완료!")
            messagebox.showinfo("성공", "대시보드가 업데이트되었습니다.\n\n경로: C:/Users/computer/Desktop/JJ_Distributions_v8.0.0_Master_Control/dashboard.html")
        except Exception as e:
            self._log(f"❌ 대시보드 업데이트 실패: {e}")
            messagebox.showerror("오류", f"대시보드 업데이트 실패: {e}")


# simpledialog 임포트
try:
    from tkinter import simpledialog
except ImportError:
    pass


def main():
    """메인 함수"""
    app = DevToolkit()
    app.mainloop()


def cli_build(args):
    """CLI 빌드 모드"""
    import argparse
    
    parser = argparse.ArgumentParser(description='3J Labs Plugin Builder CLI')
    parser.add_argument('--version', '-v', default='13.3.0', help='빌드 버전 (기본: 13.3.0)')
    parser.add_argument('--edition', '-e', choices=['free', 'basic', 'premium', 'unlimited', 'all'], 
                       default='all', help='에디션 선택')
    parser.add_argument('--user-type', '-u', choices=['standard', 'partner', 'master', 'all'],
                       default='standard', help='사용자 타입 선택')
    parser.add_argument('--bundle', '-b', action='store_true', help='번들 패키지 생성')
    parser.add_argument('--list', '-l', action='store_true', help='플러그인 목록 출력')
    parser.add_argument('--simple', '-s', action='store_true', help='간단 빌드 (모든 플러그인 ZIP)')
    parser.add_argument('--dashboard', '-d', action='store_true', help='대시보드 HTML 업데이트')
    
    parsed = parser.parse_args(args)
    
    # 대시보드만 업데이트하는 경우
    if parsed.dashboard:
        print("📊 대시보드 업데이트 중...")
        try:
            update_dashboard_html()
            print("✅ 대시보드 업데이트 완료!")
            return
        except Exception as e:
            print(f"❌ 대시보드 업데이트 실패: {e}")
            return
    
    base_path = Path(__file__).parent
    
    # 대시보드 업데이트
    if parsed.dashboard:
        print("\n📊 대시보드 업데이트 중...")
        try:
            update_dashboard_html()
            print("✅ 대시보드 업데이트 완료!")
        except Exception as e:
            print(f"❌ 대시보드 업데이트 실패: {e}")
        return
    
    # 플러그인 목록 출력
    if parsed.list:
        print("\n📦 3J Labs 플러그인 목록:")
        print("-" * 50)
        plugin_dirs = [
            'acf-css-really-simple-style-management-center-master',
            'acf-css-ai-extension',
            'acf-css-neural-link',
            'acf-code-snippets-box',
            'acf-css-woocommerce-toolkit',
            'wp-bulk-manager',
            'acf-nudge-flow',
        ]
        for d in plugin_dirs:
            path = base_path / d
            if path.exists():
                info = PluginInfo(path)
                print(f"  ✅ {info.name or d} v{info.version or 'N/A'}")
            else:
                print(f"  ❌ {d} (없음)")
        return
    
    # 간단 빌드 모드
    if parsed.simple:
        print(f"\n🔨 간단 빌드 모드 - 버전: {parsed.version}")
        print("-" * 50)
        
        output_dir = base_path / 'builds' / f'cli-{datetime.now().strftime("%Y%m%d-%H%M%S")}'
        output_dir.mkdir(parents=True, exist_ok=True)
        
        plugin_dirs = [
            'acf-css-really-simple-style-management-center-master',
            'acf-css-ai-extension',
            'acf-css-neural-link',
            'acf-code-snippets-box',
            'acf-css-woocommerce-toolkit',
            'wp-bulk-manager',
            'acf-nudge-flow',
        ]
        
        for d in plugin_dirs:
            path = base_path / d
            if path.exists():
                info = PluginInfo(path)
                zip_name = f"{d}-v{info.version or parsed.version}.zip"
                zip_path = output_dir / zip_name
                
                with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                    for root, dirs, files in os.walk(path):
                        dirs[:] = [x for x in dirs if not x.startswith('.') and x not in {'node_modules', '__pycache__', 'tests'}]
                        for file in files:
                            if not file.startswith('.'):
                                fp = Path(root) / file
                                zf.write(fp, fp.relative_to(path.parent))
                
                size_kb = zip_path.stat().st_size / 1024
                print(f"  ✅ {zip_name} ({size_kb:.1f} KB)")
        
        print(f"\n📂 출력 위치: {output_dir}")
        return
    
    # 에디션 빌드 모드
    print(f"\n🏷️ 에디션 빌드 모드")
    print(f"  버전: {parsed.version}")
    print(f"  에디션: {parsed.edition}")
    print(f"  사용자 타입: {parsed.user_type}")
    print("-" * 50)
    
    builder = EditionBuilder(base_path, print)
    
    # 빌드 조합 결정
    if parsed.edition == 'all':
        editions = list(EditionConfig.EDITIONS.keys())
    else:
        editions = [parsed.edition]
    
    if parsed.user_type == 'all':
        user_types = list(EditionConfig.USER_TYPES.keys())
    else:
        user_types = [parsed.user_type]
    
    selections = [(e, u) for e in editions for u in user_types]
    
    print(f"\n🔨 {len(selections)}개 패키지 빌드 중...")
    results = builder.build_selected_editions(selections, parsed.version)
    
    print(f"\n✅ 빌드 완료: {len(results)}/{len(selections)}개 성공")
    
    # 번들 생성
    if parsed.bundle and results:
        bundle_name = f"3J-Labs-ACF-CSS-Bundle-v{parsed.version}.zip"
        bundle_path = builder.create_bundle(results, bundle_name)
        if bundle_path:
            print(f"📦 번들 생성: {bundle_path}")
    
    # 대시보드 자동 업데이트
    try:
        update_dashboard_html()
        print("✅ 대시보드 자동 업데이트 완료")
    except Exception as e:
        print(f"⚠️ 대시보드 업데이트 실패: {e}")
    
    print(f"\n📂 출력 위치: {builder.output_dir}")


def generate_dashboard(output_path: Path, plugins: dict, build_info: dict):
    """배포 대시보드 HTML 생성"""
    
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    build_date = datetime.now().strftime("%Y-%m-%d")
    
    html = f'''<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3J Labs Deployment Center - v{build_info.get("version", "13.4.0")}</title>
    <style>
        :root {{ --primary: #667eea; --success: #48bb78; --warning: #ed8936; --danger: #f56565; --info: #4299e1; }}
        * {{ box-sizing: border-box; margin: 0; padding: 0; }}
        body {{ font-family: 'Pretendard', -apple-system, sans-serif; background: linear-gradient(135deg, #1a1a2e, #16213e); min-height: 100vh; color: #e2e8f0; }}
        .container {{ max-width: 1400px; margin: 0 auto; padding: 40px 20px; }}
        h1 {{ font-size: 2.5em; font-weight: 700; background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 10px; text-align: center; }}
        .subtitle {{ color: #a0aec0; text-align: center; margin-bottom: 30px; }}
        .meta-bar {{ display: flex; justify-content: center; gap: 30px; margin: 20px 0 40px; flex-wrap: wrap; }}
        .meta-item {{ background: rgba(255,255,255,0.05); padding: 12px 24px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); }}
        .meta-label {{ color: #718096; font-size: 0.85em; }}
        .meta-value {{ color: #fff; font-weight: 600; margin-top: 4px; }}
        .grid {{ display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-bottom: 40px; }}
        .card {{ background: rgba(255,255,255,0.03); border-radius: 16px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; }}
        .card-header {{ padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: space-between; }}
        .card-title {{ font-size: 1.25em; font-weight: 600; }}
        .badge {{ font-size: 0.7em; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; font-weight: 600; }}
        .badge-stable {{ background: var(--success); color: #1a202c; }}
        .badge-new {{ background: var(--info); color: white; }}
        table {{ width: 100%; border-collapse: collapse; }}
        th, td {{ text-align: left; padding: 14px 24px; border-bottom: 1px solid rgba(255,255,255,0.05); }}
        th {{ background: rgba(255,255,255,0.02); color: #a0aec0; font-weight: 500; font-size: 0.85em; text-transform: uppercase; }}
        .file-link {{ color: var(--primary); text-decoration: none; font-weight: 500; }}
        .file-link:hover {{ text-decoration: underline; }}
        .version {{ font-family: monospace; color: var(--success); font-weight: 600; }}
        .footer {{ text-align: center; padding: 40px 0; color: #718096; border-top: 1px solid rgba(255,255,255,0.05); }}
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 3J Labs Deployment Center</h1>
        <p class="subtitle">제이x제니x제이슨 연구소 - 플러그인 배포 관리</p>
        
        <div class="meta-bar">
            <div class="meta-item">
                <div class="meta-label">빌드 버전</div>
                <div class="meta-value">v{build_info.get("version", "13.4.0")}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">빌드 날짜</div>
                <div class="meta-value">{build_date}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">생성 시간</div>
                <div class="meta-value">{timestamp}</div>
            </div>
        </div>
        
        <div class="grid">
'''
    
    # 플러그인 카드 생성
    for plugin_key, plugin_info in plugins.items():
        is_new = plugin_info.get('is_new', False)
        badge_class = 'badge-new' if is_new else 'badge-stable'
        badge_text = 'New' if is_new else 'Stable'
        
        html += f'''
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{plugin_info.get("icon", "📦")} {plugin_info.get("name", plugin_key)}</div>
                    <span class="badge {badge_class}">{badge_text}</span>
                </div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr><th>에디션</th><th>버전</th><th>파일</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Standard</td>
                                <td class="version">{plugin_info.get("version", "1.0.0")}</td>
                                <td><a href="{plugin_info.get("file", "#")}" class="file-link">📥 다운로드</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
'''
    
    html += '''
        </div>
        
        <footer class="footer">
            <p>© 2026 3J Labs (제이x제니x제이슨 연구소). All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
'''
    
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write(html)
    
    print(f"📊 대시보드 생성: {output_path}")
    return output_path


def update_dashboard_html(dashboard_path: Path = None, auto_detect_versions: bool = True):
    """
    대시보드 HTML 파일을 자동으로 업데이트합니다.
    
    Args:
        dashboard_path: 대시보드 HTML 파일 경로 (None이면 프로젝트 루트의 dashboard.html 사용)
        auto_detect_versions: True면 실제 플러그인 파일에서 버전을 읽어옴
    """
    if dashboard_path is None:
        dashboard_path = Path(__file__).parent / 'dashboard.html'
    else:
        dashboard_path = Path(dashboard_path)
    
    base_path = Path(__file__).parent
    builder = EditionBuilder(base_path)
    
    # 플러그인 정보 정의 (EDITION_PLUGINS 기반)
    plugin_icons = {
        'acf-css-manager': '🎨',
        'admin-menu-editor-pro': '📋',
        'acf-code-snippets-box': '📝',
        'acf-css-woocommerce-toolkit': '🛒',
        'acf-css-ai-extension': '🤖',
        'acf-css-neural-link': '🔗',
        'acf-nudge-flow': '📣',
        'wp-bulk-manager': '📦',
    }
    
    plugin_fullnames = {
        'acf-css-manager': 'Advanced Custom Fonts & Colors & Styles Setting Manager',
        'admin-menu-editor-pro': 'Advanced WordPress Menu Management',
        'acf-code-snippets-box': 'Advanced Custom Function Manager',
        'acf-css-woocommerce-toolkit': 'Advanced Commerce Styling',
        'acf-css-ai-extension': 'AI-Powered Style Intelligence',
        'acf-css-neural-link': 'License & Update Manager',
        'acf-nudge-flow': 'Advanced Custom Funnel Marketing Boosting Accelerator',
        'wp-bulk-manager': 'Plugin & Theme Bulk Installer and Editor',
    }
    
    plugin_editions = {
        'acf-css-manager': ['Free', 'Basic', 'Premium', 'Unlimited', 'Partner', 'Master'],
        'admin-menu-editor-pro': ['Free (Lite)', 'Pro', 'Master 통합'],
        'acf-code-snippets-box': ['Free', 'Premium', 'Master 통합'],
        'acf-css-woocommerce-toolkit': ['Premium', 'Unlimited', 'Master 통합'],
        'acf-css-ai-extension': ['Premium', 'Unlimited', 'Master 통합'],
        'acf-css-neural-link': ['Basic', 'Premium', 'Master 통합'],
        'acf-nudge-flow': ['Premium', 'Unlimited', 'Master 통합'],
        'wp-bulk-manager': ['Free', 'Unlimited', 'Master 통합'],
    }
    
    # 실제 플러그인 버전 읽기
    plugins_data = []
    total_plugins = 0
    
    for plugin_key, plugin_config in builder.EDITION_PLUGINS.items():
        plugin_path = base_path / plugin_config['source_dir']
        version = 'N/A'
        status = '❌ 없음'
        status_color = 'var(--accent-red)'
        
        if plugin_path.exists():
            total_plugins += 1
            if auto_detect_versions:
                info = PluginInfo(str(plugin_path))
                version = info.version or 'N/A'
            
            # 버전이 있으면 안정 상태
            if version != 'N/A':
                status = '✅ 안정'
                status_color = 'var(--accent-green)'
            else:
                status = '🔄 개발중'
                status_color = 'var(--accent-orange)'
        
        plugins_data.append({
            'key': plugin_key,
            'name': plugin_config['display_name'],
            'fullname': plugin_fullnames.get(plugin_key, plugin_config['description']),
            'icon': plugin_icons.get(plugin_key, '📦'),
            'version': version,
            'status': status,
            'status_color': status_color,
            'editions': plugin_editions.get(plugin_key, []),
            'description': plugin_config['description'],
        })
    
    # 대시보드 HTML 생성
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    build_date = datetime.now().strftime("%Y-%m-%d")
    main_version = next((p['version'] for p in plugins_data if p['key'] == 'acf-css-manager'), '13.4.7')
    
    html_content = f'''<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3J Labs - ACF CSS Plugin Distribution Dashboard</title>
    <style>
        :root {{
            --bg-primary: #0d1117;
            --bg-secondary: #161b22;
            --bg-tertiary: #21262d;
            --text-primary: #c9d1d9;
            --text-secondary: #8b949e;
            --accent-blue: #58a6ff;
            --accent-green: #3fb950;
            --accent-purple: #a371f7;
            --accent-orange: #d29922;
            --accent-red: #f85149;
            --accent-pink: #db61a2;
            --border-color: #30363d;
            --shadow: 0 8px 24px rgba(0,0,0,0.4);
        }}
        
        * {{ box-sizing: border-box; margin: 0; padding: 0; }}
        
        body {{
            font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }}
        
        .container {{
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }}
        
        header {{
            text-align: center;
            margin-bottom: 60px;
        }}
        
        .logo {{
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }}
        
        .subtitle {{
            color: var(--text-secondary);
            font-size: 1.1rem;
        }}
        
        .version-badge {{
            display: inline-block;
            background: var(--bg-tertiary);
            padding: 8px 16px;
            border-radius: 20px;
            margin-top: 15px;
            font-size: 0.9rem;
            border: 1px solid var(--border-color);
        }}
        
        .version-badge span {{
            color: var(--accent-green);
            font-weight: 600;
        }}
        
        .plugins-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }}
        
        .plugin-card {{
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }}
        
        .plugin-card:hover {{
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }}
        
        .plugin-header {{
            padding: 24px;
            border-bottom: 1px solid var(--border-color);
        }}
        
        .plugin-icon {{
            font-size: 2.5rem;
            margin-bottom: 12px;
        }}
        
        .plugin-name {{
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 6px;
        }}
        
        .plugin-fullname {{
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }}
        
        .plugin-version {{
            display: inline-block;
            background: var(--bg-tertiary);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }}
        
        .plugin-version.master {{ color: var(--accent-purple); border: 1px solid var(--accent-purple); }}
        .plugin-version.pro {{ color: var(--accent-blue); border: 1px solid var(--accent-blue); }}
        .plugin-version.free {{ color: var(--accent-green); border: 1px solid var(--accent-green); }}
        
        .plugin-body {{
            padding: 20px 24px;
        }}
        
        .plugin-description {{
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 16px;
        }}
        
        .editions-list {{
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }}
        
        .edition-tag {{
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }}
        
        .edition-tag.free {{ background: rgba(63, 185, 80, 0.2); color: var(--accent-green); }}
        .edition-tag.basic {{ background: rgba(88, 166, 255, 0.2); color: var(--accent-blue); }}
        .edition-tag.premium {{ background: rgba(163, 113, 247, 0.2); color: var(--accent-purple); }}
        .edition-tag.unlimited {{ background: rgba(210, 153, 34, 0.2); color: var(--accent-orange); }}
        .edition-tag.partner {{ background: rgba(219, 97, 162, 0.2); color: var(--accent-pink); }}
        .edition-tag.master {{ background: rgba(248, 81, 73, 0.2); color: var(--accent-red); }}
        
        .stats-section {{
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 30px;
            margin-bottom: 40px;
        }}
        
        .stats-title {{
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }}
        
        .stats-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }}
        
        .stat-item {{
            text-align: center;
            padding: 20px;
            background: var(--bg-tertiary);
            border-radius: 12px;
        }}
        
        .stat-number {{
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent-blue);
        }}
        
        .stat-label {{
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 5px;
        }}
        
        .build-info {{
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 30px;
        }}
        
        .build-info h2 {{
            font-size: 1.5rem;
            margin-bottom: 20px;
        }}
        
        .build-table {{
            width: 100%;
            border-collapse: collapse;
        }}
        
        .build-table th, .build-table td {{
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }}
        
        .build-table th {{
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.85rem;
        }}
        
        footer {{
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }}
        
        footer a {{
            color: var(--accent-blue);
            text-decoration: none;
        }}
        
        footer a:hover {{
            text-decoration: underline;
        }}
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">3J Labs</div>
            <p class="subtitle">제이 × 제니 × 제이슨 연구소 | ACF CSS Plugin Suite</p>
            <div class="version-badge">
                Dashboard Version <span>v9.1.0</span> | Last Updated: <span id="last-updated">{build_date}</span>
            </div>
        </header>
        
        <!-- 플러그인 카드 그리드 -->
        <section class="plugins-grid">
'''
    
    # 플러그인 카드 생성
    for plugin in plugins_data:
        version_class = 'master' if 'Master' in plugin['name'] or plugin['key'] == 'acf-css-manager' else 'pro' if 'Pro' in plugin['name'] else 'free'
        
        html_content += f'''
            <!-- {plugin['name']} -->
            <div class="plugin-card">
                <div class="plugin-header">
                    <div class="plugin-icon">{plugin['icon']}</div>
                    <div class="plugin-name">{plugin['name']}</div>
                    <div class="plugin-fullname">{plugin['fullname']}</div>
                    <span class="plugin-version {version_class}">v{plugin['version']}</span>
                </div>
                <div class="plugin-body">
                    <p class="plugin-description">
                        {plugin['description']}
                    </p>
                    <div class="editions-list">
'''
        for edition in plugin['editions']:
            edition_class = edition.lower().replace(' ', '-').replace('(', '').replace(')', '').split('-')[0]
            html_content += f'                        <span class="edition-tag {edition_class}">{edition}</span>\n'
        
        html_content += '''                    </div>
                </div>
            </div>
'''
    
    html_content += f'''        </section>
        
        <!-- 통계 섹션 -->
        <section class="stats-section">
            <h2 class="stats-title">📊 플러그인 통계</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">{total_plugins}</div>
                    <div class="stat-label">총 플러그인</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">6</div>
                    <div class="stat-label">에디션 종류</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">22</div>
                    <div class="stat-label">지원 언어</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">PHP 8.5</div>
                    <div class="stat-label">최소 PHP</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">WP 6.0+</div>
                    <div class="stat-label">최소 WordPress</div>
                </div>
            </div>
        </section>
        
        <!-- 빌드 정보 -->
        <section class="build-info">
            <h2>🔧 최신 빌드 정보</h2>
            <table class="build-table">
                <thead>
                    <tr>
                        <th>플러그인</th>
                        <th>버전</th>
                        <th>빌드 날짜</th>
                        <th>상태</th>
                    </tr>
                </thead>
                <tbody>
'''
    
    for plugin in plugins_data:
        html_content += f'''                    <tr>
                        <td>{plugin['name']}</td>
                        <td>v{plugin['version']}</td>
                        <td>{build_date}</td>
                        <td style="color: {plugin['status_color']};">{plugin['status']}</td>
                    </tr>
'''
    
    html_content += f'''                </tbody>
            </table>
        </section>
    </div>
    
    <footer>
        <p>© 2026 <a href="https://3j-labs.com" target="_blank">3J Labs (제이×제니×제이슨 연구소)</a></p>
        <p>Made with ❤️ by Jay, Jenny & Jason</p>
        <p style="margin-top: 10px; font-size: 0.8rem; color: var(--text-secondary);">
            자동 업데이트: {timestamp} | 메인 버전: v{main_version}
        </p>
    </footer>
    
    <script>
        // 현재 날짜로 업데이트
        document.getElementById('last-updated').textContent = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
'''
    
    # 파일 저장
    dashboard_path.parent.mkdir(parents=True, exist_ok=True)
    with open(dashboard_path, 'w', encoding='utf-8') as f:
        f.write(html_content)
    
    print(f"✅ 대시보드 업데이트 완료: {dashboard_path}")
    print(f"   - 총 {total_plugins}개 플러그인 반영")
    print(f"   - 메인 버전: v{main_version}")
    return dashboard_path


if __name__ == '__main__':
    if len(sys.argv) > 1:
        # CLI 모드
        cli_build(sys.argv[1:])
    else:
        # GUI 모드
        main()
