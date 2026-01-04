#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
═══════════════════════════════════════════════════════════════════════════════
  3J Labs ACF CSS Plugin Build Manager v22.4.0
  플러그인 빌드, 버전 관리, 에디션 관리를 위한 통합 관리 프로그램
═══════════════════════════════════════════════════════════════════════════════

Features:
- 모든 ACF CSS 패밀리 플러그인 관리
- 플러그인 빌드 및 ZIP 패키징
- 버전 관리 및 자동 업데이트
- 에디션별 빌드 관리 (Master Only 클린 빌드 지원)
- Windows 숏컷 생성
- 현대적인 macOS 스타일 라이트 테마 GUI (베이지/크림색)
- 외부 대시보드 연동 및 업데이트

@author: 3J Labs (Jay & Jason & Jenny)
@version: 22.4.1 (Phase 39.3: Package Signature Generation)
@date: 2026-01-04
"""

import tkinter as tk
from tkinter import ttk, messagebox, scrolledtext, filedialog
import subprocess
import os
import sys
import threading
import datetime
import json
import shutil
import zipfile
import re
import hashlib
import hmac
from pathlib import Path

# pywin32는 선택적 (없으면 숏컷 기능 비활성화)
try:
    import win32com.client
    HAS_PYWIN32 = True
except ImportError:
    HAS_PYWIN32 = False

# ═══════════════════════════════════════════════════════════════════════════════
# 설정 (Configuration)
# ═══════════════════════════════════════════════════════════════════════════════
BASE_DIR = Path(__file__).parent.absolute()
CONFIG_FILE = BASE_DIR / ".3j_build_config.json"
DIST_DIR = BASE_DIR / "dist"
BUILDS_DIR = BASE_DIR / "builds"

# 제외할 파일/폴더 패턴
EXCLUDE_PATTERNS = [
    r'^\.git', r'^\.vscode', r'^\.idea', r'__pycache__', r'\.DS_Store$',
    r'^tests', r'^phpunit\.xml', r'^composer\.json', r'node_modules',
    r'^package\.json', r'^package-lock\.json', r'^gulpfile\.js',
    r'^\.editorconfig', r'\.bak$', r'^\.env', r'Thumbs\.db$',
    r'local-server/venv', r'^README\.md$', r'^CHANGELOG\.md$'
]

# ═══════════════════════════════════════════════════════════════════════════════
# [v22.4.1] 패키지 서명 (Package Signature) - 업데이트 하이재킹 방지
# ═══════════════════════════════════════════════════════════════════════════════
SIGNATURE_SECRET_KEY = "JJ3LABS_UPDATE_SECRET_KEY_2026"  # 서버와 동일한 키
SIGNATURES_FILE = "package_signatures.json"

def generate_package_signature(zip_path):
    """
    ZIP 파일의 HMAC-SHA256 서명 생성

    이 서명은 Neural Link의 verify_package_signature() 메서드와 호환됩니다.
    업데이트 패키지의 무결성을 검증하는 데 사용됩니다.
    """
    try:
        with open(zip_path, 'rb') as f:
            file_content = f.read()

        # HMAC-SHA256 서명 생성
        signature = hmac.new(
            SIGNATURE_SECRET_KEY.encode('utf-8'),
            file_content,
            hashlib.sha256
        ).hexdigest()

        # MD5 체크섬도 함께 저장 (추가 검증용)
        md5_hash = hashlib.md5(file_content).hexdigest()

        return {
            'signature': signature,
            'md5': md5_hash,
            'size': len(file_content),
            'generated_at': datetime.datetime.now().isoformat()
        }
    except Exception as e:
        print(f"Error generating signature: {e}")
        return None

def save_package_signatures(output_dir, signatures):
    """패키지 서명을 JSON 파일로 저장"""
    signatures_path = Path(output_dir) / SIGNATURES_FILE
    try:
        # 기존 서명 파일 로드
        existing = {}
        if signatures_path.exists():
            with open(signatures_path, 'r', encoding='utf-8') as f:
                existing = json.load(f)

        # 새 서명 병합
        existing.update(signatures)
        existing['_updated_at'] = datetime.datetime.now().isoformat()
        existing['_version'] = '1.0.0'

        # 저장
        with open(signatures_path, 'w', encoding='utf-8') as f:
            json.dump(existing, f, indent=2, ensure_ascii=False)

        return True
    except Exception as e:
        print(f"Error saving signatures: {e}")
        return False

# ═══════════════════════════════════════════════════════════════════════════════
# 플러그인 정보 (Plugin Registry)
# ═══════════════════════════════════════════════════════════════════════════════
PLUGINS = {
    'acf-css-manager': {
        'id': 'acf-css-manager',
        'name': 'ACF CSS 설정 관리자',
        'full_name': 'ACF CSS - Advanced Custom Fonts & Colors & Styles Setting Manager',
        'folder': 'acf-css-really-simple-style-management-center-master',
        'main_file': 'acf-css-really-simple-style-guide.php',
        'text_domain': 'acf-css-really-simple-style-management-center',
        'editions': ['master'], # 클린 마스터 전용
        'is_core': True,
        'description': 'WordPress 스타일 통합 관리 시스템 및 마스터 키 시스템'
    },
    'wp-bulk-manager': {
        'id': 'wp-bulk-manager',
        'name': 'WP Bulk Manager',
        'full_name': 'WP Bulk Manager - Really Simple WordPress Plugin & Theme Bulk Installer and Editor',
        'folder': 'wp-bulk-manager',
        'main_file': 'wp-bulk-installer.php',
        'text_domain': 'wp-bulk-manager',
        'editions': ['master'], # 현재는 마스터만 빌드
        'is_core': True,  # 독립적으로 사용 가능
        'description': '멀티 사이트 및 원격 연결 지원 대량 설치 관리자'
    },
    'acf-neural-link': {
        'id': 'acf-neural-link',
        'name': 'ACF CSS Neural Link',
        'full_name': 'ACF CSS Neural Link - License & Update Manager (Advanced Custom Fonts & Colors & Styles Setting)',
        'folder': 'acf-css-neural-link',
        'main_file': 'acf-css-neural-link.php',
        'text_domain': 'acf-css-neural-link',
        'editions': ['master'],
        'is_core': False,  # 애드온: 결합해서 사용해야 함
        'description': '라이센스 인증 및 채널별 업데이트 배포 센터'
    },
    'acf-nudge-flow': {
        'id': 'acf-nudge-flow',
        'name': '넛지 플로우 (Nudge Flow)',
        'full_name': '넛지 플로우 (Nudge Flow) - ACF MBA (Advanced Custom Funnels for Marketing Boosting Accelerator)',
        'folder': 'acf-nudge-flow',
        'main_file': 'acf-nudge-flow.php',
        'text_domain': 'acf-nudge-flow',
        'editions': ['master'],
        'is_core': True,  # 독립적으로 사용 가능
        'description': '전략적 캠페인 시나리오 기반 마케팅 자동화'
    },
    'acf-code-snippets': {
        'id': 'acf-code-snippets',
        'name': 'ACF Code Snippets Box',
        'full_name': 'ACF Code Snippets Box - Advanced Custom Function Manager',
        'folder': 'acf-code-snippets-box',
        'main_file': 'acf-code-snippets-box.php',
        'text_domain': 'acf-code-snippets-box',
        'editions': ['master'],
        'is_core': True,  # 독립적으로 사용 가능
        'description': 'CSS/JS/PHP 코드 스니펫 관리'
    },
    'acf-woocommerce': {
        'id': 'acf-woocommerce',
        'name': 'ACF CSS WooCommerce Toolkit',
        'full_name': 'ACF CSS WooCommerce Toolkit - Advanced Commerce Funnel - Custom Smart Sales',
        'folder': 'acf-css-woocommerce-toolkit',
        'main_file': 'acf-css-woocommerce-toolkit.php',
        'text_domain': 'acf-css-woocommerce-toolkit',
        'editions': ['master'],
        'is_core': True,  # 독립적으로 사용 가능
        'description': 'WooCommerce 스타일 및 기능 확장'
    },
    'acf-ai-extension': {
        'id': 'acf-ai-extension',
        'name': 'ACF CSS AI Extension',
        'full_name': 'ACF CSS AI Extension - Intelligent Style Generator (Advanced Custom Fonts & Colors & Styles Setting)',
        'folder': 'acf-css-ai-extension',
        'main_file': 'acf-css-ai-extension.php',
        'text_domain': 'acf-css-ai-extension',
        'editions': ['master'],
        'is_core': False,  # 애드온: 결합해서 사용해야 함
        'description': 'AI 기반 스타일 추천 및 생성'
    },
    'admin-menu-editor': {
        'id': 'admin-menu-editor',
        'name': 'ACF Admin Menu Editor Pro',
        'full_name': 'ACF Admin Menu Editor Pro - Advanced Custom Fix Admin Menu Editor (Pro Version)',
        'folder': 'admin-menu-editor-pro',
        'main_file': 'admin-menu-editor-pro.php',
        'text_domain': 'admin-menu-editor-pro',
        'editions': ['master'],
        'is_core': True,  # 독립적으로 사용 가능
        'description': '관리자 메뉴 커스터마이저'
    },
    'acf-css-woo-license': {
        'id': 'acf-css-woo-license',
        'name': 'ACF CSS Woo License Bridge',
        'full_name': 'ACF CSS License Bridge for WooCommerce',
        'folder': 'acf-css-woo-license',
        'main_file': 'acf-css-woo-license.php',
        'text_domain': 'acf-css-woo-license',
        'editions': ['master'],
        'is_core': False,  # 애드온: 결합해서 사용해야 함
        'description': 'WooCommerce 결제 연동 및 라이센스 발행 브릿지 (모든 판매 가능한 플러그인과 연계)'
    },
    'wp-bulk-seo': {
        'id': 'wp-bulk-seo',
        'name': 'WP Bulk SEO & AEO(AIO)',
        'full_name': 'WP Bulk SEO & AEO(AIO) - WordPress Really Simple and Easy Search Engine Optimization and AI Optimization Automation. 1, 2, 3 Click Auto Optimization.',
        'folder': 'SEO/wp-bulk-seo-aeo',
        'main_file': 'wp-bulk-seo-aeo.php',
        'text_domain': 'wp-bulk-seo-aeo',
        'editions': ['master'],
        'is_core': True,
        'description': 'WordPress SEO 및 AI 최적화 자동화 (Rank Math Pro 스타일)'
    },
    'acf-mail-smtp': {
        'id': 'acf-mail-smtp',
        'name': 'ACF Mail SMTP',
        'full_name': 'ACF Mail SMTP - Advanced Custom Form & Mail & SMTP',
        'folder': 'acf-mail-smtp',
        'main_file': 'acf-mail-smtp.php',
        'text_domain': 'acf-mail-smtp',
        'editions': ['master'],
        'is_core': True,
        'description': '폼 빌더, SMTP 이메일 발송, 자동화'
    },
    'acf-user-journey-analytics': {
        'id': 'acf-user-journey-analytics',
        'name': 'ACF User Journey Analytics',
        'full_name': 'ACF User Journey Analytics - Free Traffic Analysis Dashboard',
        'folder': 'acf-user-journey-analytics',
        'main_file': 'acf-user-journey-analytics.php',
        'text_domain': 'acf-user-journey-analytics',
        'editions': ['master'],
        'is_core': True,
        'description': '무료 트래픽 분석 대시보드 (50+ 광고 플랫폼 지원)'
    },
    'jj-analytics-dashboard': {
        'id': 'jj-analytics-dashboard',
        'name': 'JJ Analytics Dashboard',
        'full_name': 'JJ Analytics Dashboard - Integrated Analytics Dashboard',
        'folder': 'jj-analytics-dashboard',
        'main_file': 'jj-analytics-dashboard.php',
        'text_domain': 'jj-analytics-dashboard',
        'editions': ['master'],
        'is_core': True,
        'description': '전체 플러그인 통합 분석 대시보드'
    },
    'jj-marketing-automation-dashboard': {
        'id': 'jj-marketing-automation-dashboard',
        'name': 'JJ Marketing Automation Dashboard',
        'full_name': 'JJ Marketing Automation Dashboard - Comprehensive Marketing Automation Dashboard',
        'folder': 'jj-marketing-automation-dashboard',
        'main_file': 'jj-marketing-dashboard.php',
        'text_domain': 'jj-marketing-automation-dashboard',
        'editions': ['master'],
        'is_core': True,
        'description': '종합 마케팅 자동화 대시보드'
    }
}

# 에디션 정보
EDITIONS = {
    'free': {'label': 'Free', 'suffix': '', 'color': '#808080'},
    'basic': {'label': 'Basic', 'suffix': '-basic', 'color': '#3498db'},
    'premium': {'label': 'Premium', 'suffix': '-premium', 'color': '#9b59b6'},
    'unlimited': {'label': 'Unlimited', 'suffix': '-unlimited', 'color': '#e67e22'},
    'partner': {'label': 'Partner', 'suffix': '-partner', 'color': '#27ae60'},
    'master': {'label': 'Master', 'suffix': '-master', 'color': '#f39c12'},
    'pro': {'label': 'Pro', 'suffix': '-pro', 'color': '#e74c3c'}
}

# ═══════════════════════════════════════════════════════════════════════════════
# 유틸리티 함수
# ═══════════════════════════════════════════════════════════════════════════════
def load_config():
    """설정 파일 로드"""
    default_config = {
        'output_dir': str(BASE_DIR / 'dist'),
        'auto_shortcut': True,
        'auto_open_folder': True,
        'include_source_map': False,
        'last_build_time': None,
        'default_editions': ['master']
    }
    if CONFIG_FILE.exists():
        try:
            with open(CONFIG_FILE, 'r', encoding='utf-8') as f:
                saved = json.load(f)
                default_config.update(saved)
        except:
            pass
    return default_config

def save_config(config):
    """설정 파일 저장"""
    try:
        with open(CONFIG_FILE, 'w', encoding='utf-8') as f:
            json.dump(config, f, indent=2, ensure_ascii=False)
    except Exception as e:
        print(f"설정 저장 오류: {e}")

def format_kst_time(iso_string):
    """한국 표준시(서울 기준) 12시간제로 시간 포맷팅"""
    if not iso_string:
        return "없음"
    try:
        # ISO 문자열을 datetime으로 변환
        dt = datetime.datetime.fromisoformat(iso_string.replace('Z', '+00:00'))
        
        # UTC로 변환 (timezone 정보가 없으면 로컬 시간으로 가정)
        if dt.tzinfo is None:
            # timezone 정보가 없으면 UTC로 가정
            dt = dt.replace(tzinfo=datetime.timezone.utc)
        
        # 한국 시간대(UTC+9)로 변환
        kst_offset = datetime.timedelta(hours=9)
        kst_timezone = datetime.timezone(kst_offset)
        kst_time = dt.astimezone(kst_timezone)
        
        # 12시간제로 포맷팅
        hour = kst_time.hour
        minute = kst_time.minute
        
        if hour == 0:
            period = "오전"
            display_hour = 12
        elif hour < 12:
            period = "오전"
            display_hour = hour
        elif hour == 12:
            period = "오후"
            display_hour = 12
        else:
            period = "오후"
            display_hour = hour - 12
        
        date_str = kst_time.strftime("%Y년 %m월 %d일")
        time_str = f"{period} {display_hour}시 {minute}분"
        
        return f"{date_str} {time_str}"
    except Exception as e:
        # 오류 발생 시 원본 문자열의 날짜 부분만 반환
        try:
            return iso_string[:10] if len(iso_string) >= 10 else iso_string
        except:
            return "없음"

def get_version_from_file(file_path):
    """PHP 파일에서 버전 추출"""
    if not file_path.exists():
        return "N/A"
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read(2000)  # 처음 2000자만 읽기
            match = re.search(r'\*\s*Version:\s*([\d.]+)', content)
            if match:
                return match.group(1)
    except:
        pass
    return "N/A"

def should_exclude(path, base_path):
    """파일/폴더 제외 여부 확인"""
    rel_path = str(path.relative_to(base_path))
    for pattern in EXCLUDE_PATTERNS:
        if re.search(pattern, rel_path, re.IGNORECASE):
            return True
    return False

def create_shortcut(target_script, shortcut_path, description=""):
    """Windows 숏컷 생성"""
    if not HAS_PYWIN32:
        return False
    try:
        shell = win32com.client.Dispatch("WScript.Shell")
        shortcut = shell.CreateShortCut(str(shortcut_path))
        
        # Python 스크립트를 실행하는 숏컷
        shortcut.Targetpath = sys.executable
        shortcut.Arguments = f'"{target_script}"'
        shortcut.WorkingDirectory = str(Path(target_script).parent)
        shortcut.Description = description
        shortcut.save()
        return True
    except Exception as e:
        print(f"숏컷 생성 오류: {e}")
        return False

# ═══════════════════════════════════════════════════════════════════════════════
# 빌드 엔진
# ═══════════════════════════════════════════════════════════════════════════════
class BuildEngine:
    """플러그인 빌드 엔진"""
    
    def __init__(self, log_callback=None, progress_callback=None):
        self.log_callback = log_callback or print
        self.progress_callback = progress_callback
        self.config = load_config()
        
    def log(self, message):
        """로그 출력"""
        timestamp = datetime.datetime.now().strftime("%H:%M:%S")
        self.log_callback(f"[{timestamp}] {message}\n")
    
    def build_plugin(self, plugin_id, editions=None):
        """단일 플러그인 빌드"""
        if plugin_id not in PLUGINS:
            self.log(f"X Unknown plugin: {plugin_id}")
            return False
        
        plugin = PLUGINS[plugin_id]
        source_dir = BASE_DIR / plugin['folder']
        
        if not source_dir.exists():
            self.log(f"X Source folder missing: {source_dir}")
            return False
        
        # 빌드할 에디션 결정
        if editions is None:
            editions = plugin['editions']
        else:
            editions = [e for e in editions if e in plugin['editions']]
        
        if not editions:
            self.log(f"W {plugin['name']}: No editions to build")
            return False
        
        self.log(f"Building {plugin['name']} ({len(editions)} editions)")
        
        success_count = 0
        for edition in editions:
            if self.build_edition(plugin, edition, source_dir):
                success_count += 1
        
        self.log(f"OK {plugin['name']}: {success_count}/{len(editions)} editions complete")
        return success_count > 0
    
    def build_edition(self, plugin, edition, source_dir):
        """특정 에디션 빌드"""
        edition_info = EDITIONS.get(edition, {'suffix': f'-{edition}'})
        suffix = edition_info['suffix']
        
        # 버전 정보 가져오기
        main_file = source_dir / plugin['main_file']
        version = get_version_from_file(main_file)
        if version == "N/A":
            version = "1.0.0"
        
        # 출력 폴더명 생성 (버전 포함)
        output_folder_name = f"{plugin['folder']}{suffix}"
        zip_filename = f"{plugin['folder']}{suffix}-v{version}.zip"
        output_dir = Path(self.config['output_dir']) / output_folder_name
        zip_path = Path(self.config['output_dir']) / zip_filename
        
        try:
            # 기존 ZIP 파일이 있으면 old로 이동
            self._archive_old_files(plugin['folder'], suffix)
            
            # 기존 폴더 삭제 후 생성
            if output_dir.exists():
                shutil.rmtree(output_dir)
            output_dir.mkdir(parents=True)
            
            # 파일 복사
            file_count = 0
            for item in source_dir.rglob('*'):
                if item.is_file() and not should_exclude(item, source_dir):
                    rel_path = item.relative_to(source_dir)
                    dest_path = output_dir / rel_path
                    dest_path.parent.mkdir(parents=True, exist_ok=True)
                    shutil.copy2(item, dest_path)
                    file_count += 1
            
            # ZIP 파일 생성
            with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
                for item in output_dir.rglob('*'):
                    if item.is_file():
                        arcname = Path(output_folder_name) / item.relative_to(output_dir)
                        zipf.write(item, arcname)

            # [v22.4.1] 패키지 서명 생성 및 저장
            sig_info = generate_package_signature(zip_path)
            if sig_info:
                sig_info['plugin_id'] = plugin['id']
                sig_info['edition'] = edition
                sig_info['version'] = version
                save_package_signatures(
                    Path(self.config['output_dir']),
                    {zip_filename: sig_info}
                )
                self.log(f"   SIG: {zip_filename} signature generated")

            # 빌드 후 폴더 삭제 (ZIP만 유지)
            shutil.rmtree(output_dir)

            self.log(f"   ZIP: {edition.upper()}: {file_count} files -> {zip_filename}")
            return True
            
        except Exception as e:
            self.log(f"   X {edition.upper()} build failed: {e}")
            return False
    
    def _archive_old_files(self, folder_base, suffix):
        """기존 ZIP 파일을 old 폴더로 이동 (개선된 버전)"""
        output_dir = Path(self.config['output_dir'])
        old_dir = output_dir / "old"
        old_dir.mkdir(parents=True, exist_ok=True)
        
        # 같은 폴더+에디션의 기존 ZIP 찾기
        pattern = f"{folder_base}{suffix}-v*.zip"
        matching_files = list(output_dir.glob(pattern))
        
        if matching_files:
            # 날짜별 폴더 생성 (YYYY-MM-DD 형식)
            date_str = datetime.datetime.now().strftime("%Y-%m-%d")
            archive_dir = old_dir / date_str
            archive_dir.mkdir(parents=True, exist_ok=True)
            
            # 파일을 날짜 폴더로 이동
            for old_file in matching_files:
                try:
                    # 파일명에 타임스탬프 추가 (중복 방지)
                    timestamp = datetime.datetime.now().strftime("%H%M%S")
                    new_name = f"{old_file.stem}_{timestamp}{old_file.suffix}"
                    shutil.move(str(old_file), str(archive_dir / new_name))
                    self.log(f"   ARCHIVED: {old_file.name} -> old/{date_str}/{new_name}")
                except Exception as e:
                    self.log(f"   WARNING: Failed to archive {old_file.name}: {e}")
            
            # old 폴더 정리 (30일 이상 된 폴더 삭제)
            self._cleanup_old_archives(old_dir)
    
    def _cleanup_old_archives(self, old_dir, days=30):
        """30일 이상 된 아카이브 폴더 삭제"""
        if not old_dir.exists():
            return
        
        cutoff_date = datetime.datetime.now() - datetime.timedelta(days=days)
        
        for item in old_dir.iterdir():
            if item.is_dir():
                try:
                    # 폴더명이 날짜 형식인지 확인 (YYYY-MM-DD)
                    folder_date = datetime.datetime.strptime(item.name, "%Y-%m-%d")
                    if folder_date < cutoff_date:
                        shutil.rmtree(item)
                        self.log(f"   CLEANUP: Removed old archive {item.name}")
                except ValueError:
                    # 날짜 형식이 아니면 무시
                    pass
    
    def build_all(self, plugin_ids=None, editions=None):
        """전체 빌드"""
        if plugin_ids is None:
            # 개발 예정 플러그인 제외
            plugin_ids = [pid for pid, p in PLUGINS.items() if p.get('status') != 'planned']
        
        self.log("=" * 60)
        self.log("3J Labs ACF CSS Total Build Start")
        self.log("=" * 60)
        
        total = len(plugin_ids)
        success = 0
        
        for i, plugin_id in enumerate(plugin_ids):
            if self.progress_callback:
                self.progress_callback(i, total, plugin_id)
            
            if self.build_plugin(plugin_id, editions):
                success += 1
        
        if self.progress_callback:
            self.progress_callback(total, total, "완료")
        
        self.log("=" * 60)
        self.log(f"Build Complete: {success}/{total} plugins success")
        self.log(f"Output Folder: {self.config['output_dir']}")
        self.log("=" * 60)
        
        # 빌드 시간 저장 (한국 시간대 기준, UTC+9)
        kst_offset = datetime.timedelta(hours=9)
        kst_timezone = datetime.timezone(kst_offset)
        kst_time = datetime.datetime.now(kst_timezone)
        self.config['last_build_time'] = kst_time.isoformat()
        save_config(self.config)
        
        return success == total

# ═══════════════════════════════════════════════════════════════════════════════
# GUI 애플리케이션
# ═══════════════════════════════════════════════════════════════════════════════
class JJBuildManager(tk.Tk):
    """3J Labs Build Manager GUI - macOS Style Light Theme"""
    
    def __init__(self):
        super().__init__()
        
        self.title("3J Labs ACF CSS Build Manager")
        self.geometry("1300x900")
        self.minsize(1000, 700)
        
        # macOS 스타일 베이지/크림색 배경
        self.configure(bg="#F5F5F0")
        
        self.config_data = load_config()
        self.is_building = False
        
        self.setup_styles()
        self.create_widgets()
        self.refresh_plugin_list()
        self.refresh_version_info()
        
        # 시작 시 숏컷 생성 확인
        self.after(500, self.check_shortcut)
    
    def setup_styles(self):
        """macOS 스타일 라이트 테마 설정"""
        style = ttk.Style()
        style.theme_use('clam')
        
        # macOS 스타일 색상 팔레트 (베이지/크림 라이트 테마)
        self.colors = {
            # 배경색 (베이지/크림 계열)
            'bg_window': '#F5F5F0',      # 윈도우 배경 (웜 화이트)
            'bg_card': '#FFFFFF',         # 카드 배경 (순수 화이트)
            'bg_sidebar': '#ECEAE5',      # 사이드바 배경
            'bg_input': '#FFFFFF',        # 입력 필드 배경
            'bg_hover': '#E8E6E1',        # 호버 배경
            
            # 텍스트 색상
            'text_primary': '#1D1D1F',    # 주요 텍스트 (거의 검정)
            'text_secondary': '#6E6E73',  # 보조 텍스트 (회색)
            'text_tertiary': '#8E8E93',   # 3차 텍스트
            
            # 강조색 (macOS 블루)
            'accent': '#007AFF',          # macOS 블루
            'accent_light': '#5AC8FA',    # 밝은 블루
            'accent_dark': '#0051A8',     # 어두운 블루
            
            # 상태 색상
            'success': '#34C759',         # macOS 그린
            'warning': '#FF9500',         # macOS 오렌지
            'error': '#FF3B30',           # macOS 레드
            'info': '#5856D6',            # macOS 퍼플
            
            # 테두리
            'border': '#D1D1D6',          # 테두리 색상
            'border_light': '#E5E5EA',    # 밝은 테두리
            
            # 그림자 효과용
            'shadow': '#00000010'
        }
        
        # macOS 시스템 폰트 (Windows에서는 Segoe UI, Mac에서는 SF Pro)
        self.fonts = {
            'title': ('SF Pro Display', 28, 'bold') if sys.platform == 'darwin' else ('Segoe UI', 26, 'bold'),
            'subtitle': ('SF Pro Text', 14) if sys.platform == 'darwin' else ('Segoe UI', 12),
            'heading': ('SF Pro Display', 13, 'bold') if sys.platform == 'darwin' else ('Segoe UI', 11, 'bold'),
            'body': ('SF Pro Text', 12) if sys.platform == 'darwin' else ('Segoe UI', 10),
            'caption': ('SF Pro Text', 11) if sys.platform == 'darwin' else ('Segoe UI', 9),
            'mono': ('SF Mono', 11) if sys.platform == 'darwin' else ('Consolas', 10)
        }
        
        # 기본 프레임 스타일
        style.configure("TFrame", background=self.colors['bg_window'])
        
        # 카드 프레임 스타일
        style.configure("Card.TFrame", background=self.colors['bg_card'])
        
        # 레이블 스타일
        style.configure("TLabel", 
                       background=self.colors['bg_window'], 
                       foreground=self.colors['text_primary'], 
                       font=self.fonts['body'])
        
        # 레이블프레임 스타일 (macOS 그룹박스 스타일)
        style.configure("TLabelframe", 
                       background=self.colors['bg_card'], 
                       foreground=self.colors['text_primary'],
                       borderwidth=1,
                       relief="solid")
        style.configure("TLabelframe.Label", 
                       background=self.colors['bg_card'], 
                       foreground=self.colors['text_primary'], 
                       font=self.fonts['heading'])
        
        # 헤더 스타일
        style.configure("Header.TLabel", 
                       font=self.fonts['title'], 
                       foreground=self.colors['text_primary'], 
                       background=self.colors['bg_window'])
        style.configure("SubHeader.TLabel", 
                       font=self.fonts['subtitle'], 
                       foreground=self.colors['text_secondary'], 
                       background=self.colors['bg_window'])
        
        # 버튼 스타일 (macOS 스타일 둥근 버튼)
        style.configure("TButton", 
                       font=self.fonts['body'], 
                       padding=(16, 8),
                       background=self.colors['bg_card'],
                       foreground=self.colors['text_primary'],
                       borderwidth=1,
                       relief="solid")
        style.map("TButton", 
                 background=[('active', self.colors['bg_hover']), ('pressed', self.colors['border'])],
                 relief=[('pressed', 'sunken')])
        
        # Primary 버튼 (macOS 블루 버튼)
        style.configure("Primary.TButton", 
                       font=self.fonts['heading'], 
                       padding=(20, 10),
                       background=self.colors['accent'],
                       foreground='#FFFFFF')
        style.map("Primary.TButton", 
                 background=[('active', self.colors['accent_dark'])])
        
        # Success 버튼
        style.configure("Success.TButton", 
                       font=self.fonts['body'],
                       background=self.colors['success'],
                       foreground='#FFFFFF')
        
        # 노트북 (탭) 스타일 - macOS 세그먼트 컨트롤 스타일
        style.configure("TNotebook", 
                       background=self.colors['bg_window'], 
                       borderwidth=0,
                       tabmargins=[0, 0, 0, 0])
        style.configure("TNotebook.Tab", 
                       background=self.colors['bg_sidebar'], 
                       foreground=self.colors['text_primary'], 
                       padding=[20, 10], 
                       font=self.fonts['body'])
        style.map("TNotebook.Tab", 
                 background=[("selected", self.colors['bg_card'])], 
                 foreground=[("selected", self.colors['accent'])])
        
        # 체크박스 스타일
        style.configure("TCheckbutton", 
                       background=self.colors['bg_window'], 
                       foreground=self.colors['text_primary'],
                       font=self.fonts['body'])
        
        # 진행바 스타일 (macOS 스타일)
        style.configure("TProgressbar", 
                       background=self.colors['accent'], 
                       troughcolor=self.colors['border_light'],
                       borderwidth=0,
                       thickness=6)
        
        # Treeview 스타일 (macOS 테이블 스타일)
        style.configure("Treeview", 
                       background=self.colors['bg_card'],
                       foreground=self.colors['text_primary'],
                       fieldbackground=self.colors['bg_card'],
                       font=self.fonts['body'],
                       rowheight=28)
        style.configure("Treeview.Heading", 
                       background=self.colors['bg_sidebar'],
                       foreground=self.colors['text_primary'],
                       font=self.fonts['heading'],
                       relief="flat")
        style.map("Treeview", 
                 background=[("selected", self.colors['accent'])],
                 foreground=[("selected", "#FFFFFF")])
        
        # Entry 스타일
        style.configure("TEntry",
                       fieldbackground=self.colors['bg_input'],
                       foreground=self.colors['text_primary'],
                       borderwidth=1,
                       relief="solid",
                       padding=8)
    
    def create_widgets(self):
        """위젯 생성"""
        # 메인 컨테이너
        main_frame = ttk.Frame(self)
        main_frame.pack(fill="both", expand=True, padx=20, pady=20)
        
        # 헤더
        self.create_header(main_frame)
        
        # 탭 컨테이너
        self.notebook = ttk.Notebook(main_frame)
        self.notebook.pack(fill="both", expand=True, pady=(20, 0))
        
        # 탭 생성
        self.tab_dashboard = ttk.Frame(self.notebook)
        self.tab_build = ttk.Frame(self.notebook)
        self.tab_version = ttk.Frame(self.notebook)
        self.tab_settings = ttk.Frame(self.notebook)
        
        self.notebook.add(self.tab_dashboard, text="  📊 대시보드  ")
        self.notebook.add(self.tab_build, text="  🏭 빌드 센터  ")
        self.notebook.add(self.tab_version, text="  📦 버전 관리  ")
        self.notebook.add(self.tab_settings, text="  ⚙️ 설정  ")
        
        self.create_dashboard_tab()
        self.create_build_tab()
        self.create_version_tab()
        self.create_settings_tab()
        
        # 상태바
        self.create_statusbar(main_frame)
    
    def create_header(self, parent):
        """macOS 스타일 헤더 생성"""
        header = ttk.Frame(parent)
        header.pack(fill="x", pady=(0, 10))
        
        # 로고 및 제목 (왼쪽)
        title_frame = ttk.Frame(header)
        title_frame.pack(side="left")
        
        # 앱 아이콘 + 제목
        title_row = ttk.Frame(title_frame)
        title_row.pack(anchor="w")
        
        # 앱 아이콘 (깃털 이모지 사용)
        icon_label = tk.Label(title_row, text="🪶", font=("Segoe UI", 32), bg=self.colors['bg_window'])
        icon_label.pack(side="left", padx=(0, 12))
        
        title_text_frame = ttk.Frame(title_row)
        title_text_frame.pack(side="left")
        
        ttk.Label(title_text_frame, text="3J Labs Build Manager", style="Header.TLabel").pack(anchor="w")
        ttk.Label(title_text_frame, text="ACF CSS Plugin Family • Build & Version Management", style="SubHeader.TLabel").pack(anchor="w")
        
        # 버전 및 상태 (오른쪽)
        status_frame = ttk.Frame(header)
        status_frame.pack(side="right")
        
        # 버전 배지 (macOS 스타일 pill 배지)
        version_badge = tk.Frame(status_frame, bg=self.colors['accent'], padx=12, pady=4)
        version_badge.pack(anchor="e", pady=(0, 4))
        tk.Label(version_badge, text="v3.2.0", font=self.fonts['caption'], fg="#FFFFFF", bg=self.colors['accent']).pack()
        
        # 상태 표시
        if HAS_PYWIN32:
            status_indicator = tk.Frame(status_frame, bg=self.colors['bg_window'])
            status_indicator.pack(anchor="e")
            tk.Label(status_indicator, text="●", font=("Segoe UI", 8), fg=self.colors['success'], bg=self.colors['bg_window']).pack(side="left")
            tk.Label(status_indicator, text=" 숏컷 생성 가능", font=self.fonts['caption'], fg=self.colors['text_secondary'], bg=self.colors['bg_window']).pack(side="left")
        else:
            status_indicator = tk.Frame(status_frame, bg=self.colors['bg_window'])
            status_indicator.pack(anchor="e")
            tk.Label(status_indicator, text="●", font=("Segoe UI", 8), fg=self.colors['warning'], bg=self.colors['bg_window']).pack(side="left")
            tk.Label(status_indicator, text=" pywin32 미설치", font=self.fonts['caption'], fg=self.colors['text_secondary'], bg=self.colors['bg_window']).pack(side="left")
    
    def create_statusbar(self, parent):
        """macOS 스타일 상태바 생성"""
        # 구분선
        separator = tk.Frame(parent, height=1, bg=self.colors['border_light'])
        separator.pack(fill="x", pady=(15, 0))
        
        status_frame = ttk.Frame(parent)
        status_frame.pack(fill="x", pady=(8, 0))
        
        # 상태 텍스트
        self.status_label = tk.Label(status_frame, 
                                     text="● 준비 완료", 
                                     font=self.fonts['caption'], 
                                     fg=self.colors['text_secondary'],
                                     bg=self.colors['bg_window'])
        self.status_label.pack(side="left")
        
        # 진행바 (macOS 스타일)
        self.progress_bar = ttk.Progressbar(status_frame, mode='determinate', length=200)
        self.progress_bar.pack(side="right")
    
    def create_dashboard_tab(self):
        """macOS 스타일 대시보드 탭"""
        # 상단 요약 카드 (macOS 스타일)
        summary_frame = ttk.Frame(self.tab_dashboard)
        summary_frame.pack(fill="x", padx=10, pady=15)
        
        # 카드 데이터
        last_build_time = format_kst_time(self.config_data.get('last_build_time'))
        cards_data = [
            ("📦", "전체 플러그인", len(PLUGINS), "개", self.colors['accent']),
            ("⭐", "Core 플러그인", sum(1 for p in PLUGINS.values() if p['is_core']), "개", self.colors['success']),
            ("🧩", "애드온 플러그인", sum(1 for p in PLUGINS.values() if not p['is_core']), "개", self.colors['info']),
            ("🕐", "마지막 빌드", last_build_time, "", self.colors['text_secondary'])
        ]
        
        for i, (icon, title, value, suffix, color) in enumerate(cards_data):
            # macOS 스타일 카드 (그림자 효과)
            card_outer = tk.Frame(summary_frame, bg=self.colors['border_light'], padx=1, pady=1)
            card_outer.pack(side="left", fill="x", expand=True, padx=6)
            
            card = tk.Frame(card_outer, bg=self.colors['bg_card'], padx=20, pady=16)
            card.pack(fill="both", expand=True)
            
            # 아이콘 + 제목
            header_row = tk.Frame(card, bg=self.colors['bg_card'])
            header_row.pack(anchor="w", fill="x")
            
            tk.Label(header_row, text=icon, font=("Segoe UI", 16), bg=self.colors['bg_card']).pack(side="left")
            tk.Label(header_row, text=f"  {title}", bg=self.colors['bg_card'], fg=self.colors['text_secondary'], font=self.fonts['caption']).pack(side="left")
            
            # 값
            tk.Label(card, text=f"{value}{suffix}", bg=self.colors['bg_card'], fg=color, font=(self.fonts['title'][0], 28, 'bold')).pack(anchor="w", pady=(8, 0))
        
        # 플러그인 목록
        list_frame = ttk.LabelFrame(self.tab_dashboard, text=" 플러그인 목록 ", padding=10)
        list_frame.pack(fill="both", expand=True, padx=10, pady=10)
        
        # Treeview
        columns = ('name', 'version', 'editions', 'folder', 'status')
        self.plugin_tree = ttk.Treeview(list_frame, columns=columns, show='headings', height=15)
        
        self.plugin_tree.heading('name', text='플러그인 이름')
        self.plugin_tree.heading('version', text='버전')
        self.plugin_tree.heading('editions', text='에디션')
        self.plugin_tree.heading('folder', text='폴더')
        self.plugin_tree.heading('status', text='상태')
        
        self.plugin_tree.column('name', width=250)
        self.plugin_tree.column('version', width=80, anchor='center')
        self.plugin_tree.column('editions', width=200)
        self.plugin_tree.column('folder', width=300)
        self.plugin_tree.column('status', width=80, anchor='center')
        
        scrollbar = ttk.Scrollbar(list_frame, orient="vertical", command=self.plugin_tree.yview)
        self.plugin_tree.configure(yscrollcommand=scrollbar.set)
        
        self.plugin_tree.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
        # 버튼
        btn_frame = ttk.Frame(self.tab_dashboard)
        btn_frame.pack(fill="x", padx=10, pady=10)
        
        ttk.Button(btn_frame, text="🔄 새로고침", command=self.refresh_plugin_list).pack(side="left", padx=5)
        ttk.Button(btn_frame, text="📂 dist 폴더 열기", command=self.open_dist_folder).pack(side="left", padx=5)
        ttk.Button(btn_frame, text="📂 소스 폴더 열기", command=self.open_source_folder).pack(side="left", padx=5)
    
    def create_build_tab(self):
        """빌드 센터 탭"""
        # 상단 컨트롤
        control_frame = ttk.LabelFrame(self.tab_build, text=" 빌드 옵션 ", padding=10)
        control_frame.pack(fill="x", padx=10, pady=10)
        
        # 플러그인 선택
        plugin_select_frame = ttk.Frame(control_frame)
        plugin_select_frame.pack(fill="x", pady=5)
        
        ttk.Label(plugin_select_frame, text="빌드할 플러그인:").pack(side="left", padx=5)
        
        self.plugin_vars = {}
        for plugin_id, plugin_info in PLUGINS.items():
            var = tk.BooleanVar(value=True)
            self.plugin_vars[plugin_id] = var
            cb = ttk.Checkbutton(plugin_select_frame, text=plugin_info['name'], variable=var)
            cb.pack(side="left", padx=10)
        
        # 에디션 선택
        edition_frame = ttk.Frame(control_frame)
        edition_frame.pack(fill="x", pady=5)
        
        ttk.Label(edition_frame, text="빌드할 에디션:").pack(side="left", padx=5)
        
        self.edition_vars = {}
        for edition_id, edition_info in EDITIONS.items():
            var = tk.BooleanVar(value=edition_id in ['master', 'partner'])
            self.edition_vars[edition_id] = var
            cb = ttk.Checkbutton(edition_frame, text=edition_info['label'], variable=var)
            cb.pack(side="left", padx=10)
        
        # 빌드 버튼
        build_btn_frame = ttk.Frame(control_frame)
        build_btn_frame.pack(fill="x", pady=10)
        
        self.build_btn = ttk.Button(build_btn_frame, text="🚀 빌드 시작", command=self.start_build, style="Primary.TButton")
        self.build_btn.pack(side="left", padx=5)
        
        ttk.Button(build_btn_frame, text="전체 선택", command=lambda: self.toggle_all_plugins(True)).pack(side="left", padx=5)
        ttk.Button(build_btn_frame, text="전체 해제", command=lambda: self.toggle_all_plugins(False)).pack(side="left", padx=5)
        ttk.Button(build_btn_frame, text="Core만 선택", command=self.select_core_only).pack(side="left", padx=5)
        
        # 빌드 로그 (macOS 터미널 스타일 - 라이트 버전)
        log_frame = ttk.LabelFrame(self.tab_build, text=" 빌드 로그 ", padding=10)
        log_frame.pack(fill="both", expand=True, padx=10, pady=10)
        
        self.log_text = scrolledtext.ScrolledText(
            log_frame, 
            bg='#FAFAF8',                           # 밝은 베이지 배경
            fg=self.colors['text_primary'],          # 어두운 텍스트
            font=self.fonts['mono'],
            relief="solid",
            borderwidth=1,
            insertbackground=self.colors['text_primary'],
            selectbackground=self.colors['accent'],
            selectforeground='#FFFFFF',
            padx=12,
            pady=8
        )
        self.log_text.pack(fill="both", expand=True)
    
    def create_version_tab(self):
        """버전 관리 탭"""
        # 버전 정보 (macOS 스타일)
        version_frame = ttk.LabelFrame(self.tab_version, text=" 플러그인 버전 정보 ", padding=10)
        version_frame.pack(fill="both", expand=True, padx=10, pady=10)
        
        self.version_text = scrolledtext.ScrolledText(
            version_frame,
            bg='#FAFAF8',                           # 밝은 베이지 배경
            fg=self.colors['text_primary'],          # 어두운 텍스트
            font=self.fonts['mono'],
            relief="solid",
            borderwidth=1,
            padx=12,
            pady=8
        )
        self.version_text.pack(fill="both", expand=True)
        
        # 버튼
        btn_frame = ttk.Frame(self.tab_version)
        btn_frame.pack(fill="x", padx=10, pady=10)
        
        ttk.Button(btn_frame, text="🔄 새로고침", command=self.refresh_version_info).pack(side="left", padx=5)
    
    def create_settings_tab(self):
        """설정 탭"""
        settings_frame = ttk.LabelFrame(self.tab_settings, text=" 설정 ", padding=20)
        settings_frame.pack(fill="both", expand=True, padx=10, pady=10)
        
        # 출력 디렉토리
        dir_frame = ttk.Frame(settings_frame)
        dir_frame.pack(fill="x", pady=10)
        
        ttk.Label(dir_frame, text="출력 디렉토리:", width=15).pack(side="left")
        self.output_dir_var = tk.StringVar(value=self.config_data.get('output_dir', str(DIST_DIR)))
        ttk.Entry(dir_frame, textvariable=self.output_dir_var, width=60).pack(side="left", padx=5, fill="x", expand=True)
        ttk.Button(dir_frame, text="찾아보기", command=self.browse_output_dir).pack(side="left", padx=5)
        
        # 외부 대시보드 경로
        dashboard_frame = ttk.Frame(settings_frame)
        dashboard_frame.pack(fill="x", pady=10)
        
        ttk.Label(dashboard_frame, text="대시보드 경로:", width=15).pack(side="left")
        self.dashboard_path_var = tk.StringVar(value=self.config_data.get('dashboard_path', 
            str(BASE_DIR / 'dashboard.html')))
        ttk.Entry(dashboard_frame, textvariable=self.dashboard_path_var, width=60).pack(side="left", padx=5, fill="x", expand=True)
        ttk.Button(dashboard_frame, text="찾아보기", command=self.browse_dashboard_path).pack(side="left", padx=5)
        
        # 옵션
        options_frame = ttk.Frame(settings_frame)
        options_frame.pack(fill="x", pady=10)
        
        self.auto_open_var = tk.BooleanVar(value=self.config_data.get('auto_open_folder', True))
        ttk.Checkbutton(options_frame, text="빌드 완료 시 출력 폴더 자동 열기", variable=self.auto_open_var).pack(anchor="w", pady=5)
        
        self.auto_shortcut_var = tk.BooleanVar(value=self.config_data.get('auto_shortcut', True))
        ttk.Checkbutton(options_frame, text="데스크톱 숏컷 자동 생성", variable=self.auto_shortcut_var).pack(anchor="w", pady=5)
        
        self.auto_dashboard_var = tk.BooleanVar(value=self.config_data.get('auto_dashboard_update', True))
        ttk.Checkbutton(options_frame, text="빌드 완료 시 대시보드 자동 업데이트", variable=self.auto_dashboard_var).pack(anchor="w", pady=5)

        self.auto_git_var = tk.BooleanVar(value=self.config_data.get('auto_git_push', False))
        ttk.Checkbutton(options_frame, text="빌드 완료 시 Git 자동 푸시 (origin main)", variable=self.auto_git_var).pack(anchor="w", pady=5)
        
        # 저장 버튼
        ttk.Button(settings_frame, text="💾 설정 저장", command=self.save_settings_action).pack(anchor="e", pady=20)
        
        # 숏컷 및 대시보드 관리
        shortcut_frame = ttk.LabelFrame(self.tab_settings, text=" 숏컷 및 대시보드 관리 ", padding=20)
        shortcut_frame.pack(fill="x", padx=10, pady=10)
        
        ttk.Button(shortcut_frame, text="🔗 데스크톱 숏컷 생성", command=self.create_desktop_shortcut).pack(side="left", padx=5)
        ttk.Label(shortcut_frame, text="pywin32 필요" if not HAS_PYWIN32 else "✅ 준비됨", 
                 foreground=self.colors['warning'] if not HAS_PYWIN32 else self.colors['success']).pack(side="left", padx=10)
        
        # 대시보드 업데이트 버튼
        ttk.Button(shortcut_frame, text="📊 대시보드 업데이트", command=self.update_external_dashboard).pack(side="left", padx=20)
    
    # ───────────────────────────────────────────────────────────────────────
    # 기능 메서드
    # ───────────────────────────────────────────────────────────────────────
    def refresh_plugin_list(self):
        """플러그인 목록 새로고침"""
        self.plugin_tree.delete(*self.plugin_tree.get_children())
        
        for plugin_id, plugin_info in PLUGINS.items():
            source_path = BASE_DIR / plugin_info['folder']
            main_file = source_path / plugin_info['main_file']
            
            exists = source_path.exists()
            version = get_version_from_file(main_file) if exists else "N/A"
            editions = ", ".join([EDITIONS.get(e, {}).get('label', e) for e in plugin_info['editions']])
            status = "✅" if exists else "❌"
            
            self.plugin_tree.insert('', 'end', values=(
                plugin_info['name'],
                version,
                editions,
                plugin_info['folder'],
                status
            ))
        
        self.set_status(f"플러그인 목록 새로고침 완료 ({len(PLUGINS)}개)")
    
    def refresh_version_info(self):
        """버전 정보 새로고침"""
        self.version_text.delete("1.0", tk.END)
        
        lines = []
        lines.append("═" * 70)
        lines.append("  3J Labs ACF CSS Plugin Family - Version Information")
        lines.append("═" * 70)
        lines.append("")
        
        for plugin_id, plugin_info in PLUGINS.items():
            source_path = BASE_DIR / plugin_info['folder']
            main_file = source_path / plugin_info['main_file']
            
            lines.append(f"📦 {plugin_info['name']}")
            lines.append(f"   ID: {plugin_id}")
            lines.append(f"   폴더: {plugin_info['folder']}")
            
            if source_path.exists():
                version = get_version_from_file(main_file)
                lines.append(f"   버전: {version}")
                lines.append(f"   상태: ✅ 존재함")
            else:
                lines.append(f"   상태: ❌ 폴더 없음")
            
            lines.append(f"   에디션: {', '.join(plugin_info['editions'])}")
            lines.append(f"   설명: {plugin_info['description']}")
            lines.append("")
        
        lines.append("═" * 70)
        
        self.version_text.insert("1.0", "\n".join(lines))
    
    def toggle_all_plugins(self, value):
        """전체 플러그인 선택/해제"""
        for var in self.plugin_vars.values():
            var.set(value)
    
    def select_core_only(self):
        """Core 플러그인만 선택"""
        for plugin_id, var in self.plugin_vars.items():
            var.set(PLUGINS[plugin_id]['is_core'])
    
    def start_build(self):
        """빌드 시작"""
        if self.is_building:
            messagebox.showwarning("경고", "빌드가 이미 진행 중입니다.")
            return
        
        # 선택된 플러그인
        selected_plugins = [pid for pid, var in self.plugin_vars.items() if var.get()]
        if not selected_plugins:
            messagebox.showwarning("경고", "빌드할 플러그인을 선택해주세요.")
            return
        
        # 선택된 에디션
        selected_editions = [eid for eid, var in self.edition_vars.items() if var.get()]
        if not selected_editions:
            messagebox.showwarning("경고", "빌드할 에디션을 선택해주세요.")
            return
        
        self.is_building = True
        self.build_btn.config(state="disabled")
        self.log_text.delete("1.0", tk.END)
        
        threading.Thread(target=self._run_build, args=(selected_plugins, selected_editions), daemon=True).start()
    
    def _run_build(self, plugin_ids, editions):
        """빌드 실행 (스레드)"""
        def log_callback(msg):
            self.after(0, lambda: self.log_text.insert(tk.END, msg))
            self.after(0, lambda: self.log_text.see(tk.END))
        
        def progress_callback(current, total, name):
            percent = int((current / total) * 100) if total > 0 else 0
            self.after(0, lambda: self.progress_bar.config(value=percent))
            self.after(0, lambda: self.set_status(f"빌드 중: {name} ({current}/{total})"))
        
        try:
            # 설정 저장
            self.config_data['output_dir'] = self.output_dir_var.get()
            save_config(self.config_data)
            
            engine = BuildEngine(log_callback, progress_callback)
            engine.config = self.config_data
            
            success = engine.build_all(plugin_ids, editions)
            
            self.after(0, lambda: self.progress_bar.config(value=100))
            
            if success:
                self.after(0, lambda: self.set_status("✅ 빌드 완료!"))
                
                # 자동 대시보드 업데이트
                if self.auto_dashboard_var.get():
                    self.after(100, self._auto_update_dashboard)
                
                # Git 자동 푸시
                if self.auto_git_var.get():
                    commit_msg = f"Build Release: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M')}"
                    self.after(200, lambda: self.git_commit_push(commit_msg))

                self.after(500, lambda: messagebox.showinfo("성공", "빌드 및 후속 작업이 완료되었습니다!"))
                
                if self.auto_open_var.get():
                    self.after(600, self.open_dist_folder)
            else:
                self.after(0, lambda: self.set_status("⚠️ 일부 빌드 실패"))
                
        except Exception as e:
            self.after(0, lambda: log_callback(f"\n❌ 치명적 오류: {e}\n"))
            self.after(0, lambda: self.set_status(f"❌ 오류: {e}"))
        finally:
            self.is_building = False
            self.after(0, lambda: self.build_btn.config(state="normal"))
    
    def _auto_update_dashboard(self):
        """빌드 완료 후 자동 대시보드 업데이트 (조용히)"""
        dashboard_path = Path(self.dashboard_path_var.get())
        
        if not dashboard_path.parent.exists():
            return  # 조용히 실패
        
        try:
            # 플러그인 정보 수집
            plugins_info = {}
            for plugin_id, plugin_data in PLUGINS.items():
                source_path = BASE_DIR / plugin_data['folder']
                main_file = source_path / plugin_data['main_file']
                version = get_version_from_file(main_file) if main_file.exists() else "N/A"
                
                plugins_info[plugin_id] = {
                    'name': plugin_data['name'],
                    'full_name': plugin_data['full_name'],
                    'version': version,
                    'editions': plugin_data['editions'],
                    'description': plugin_data['description'],
                    'folder': plugin_data['folder'],
                    'exists': source_path.exists()
                }
            
            self._generate_dashboard_html(dashboard_path, plugins_info)
            self.log_text.insert(tk.END, f"\n📊 대시보드 자동 업데이트 완료: {dashboard_path}\n")
            self.log_text.see(tk.END)
        except Exception as e:
            self.log_text.insert(tk.END, f"\n⚠️ 대시보드 업데이트 실패: {e}\n")
    
    def set_status(self, message):
        """상태바 업데이트"""
        # 상태에 따라 색상 변경
        if "완료" in message or "✅" in message:
            color = self.colors['success']
            prefix = "●"
        elif "오류" in message or "❌" in message or "실패" in message:
            color = self.colors['error']
            prefix = "●"
        elif "중" in message or "진행" in message:
            color = self.colors['accent']
            prefix = "◐"
        else:
            color = self.colors['text_secondary']
            prefix = "●"
        
        self.status_label.config(text=f"{prefix} {message}", fg=color)
    
    def open_dist_folder(self):
        """dist 폴더 열기"""
        output_dir = Path(self.output_dir_var.get())
        if output_dir.exists():
            os.startfile(str(output_dir))
        else:
            output_dir.mkdir(parents=True, exist_ok=True)
            os.startfile(str(output_dir))
    
    def open_source_folder(self):
        """소스 폴더 열기"""
        os.startfile(str(BASE_DIR))
    
    def browse_output_dir(self):
        """출력 디렉토리 선택"""
        dir_path = filedialog.askdirectory(initialdir=self.output_dir_var.get())
        if dir_path:
            self.output_dir_var.set(dir_path)
    
    def browse_dashboard_path(self):
        """대시보드 파일 선택"""
        file_path = filedialog.askopenfilename(
            initialdir=str(Path(self.dashboard_path_var.get()).parent) if Path(self.dashboard_path_var.get()).parent.exists() else str(Path.home() / 'Desktop'),
            filetypes=[("HTML 파일", "*.html"), ("모든 파일", "*.*")]
        )
        if file_path:
            self.dashboard_path_var.set(file_path)
    
    def update_external_dashboard(self):
        """외부 대시보드 업데이트"""
        dashboard_path = Path(self.dashboard_path_var.get())
        
        if not dashboard_path.parent.exists():
            messagebox.showwarning("경고", f"대시보드 폴더가 존재하지 않습니다:\n{dashboard_path.parent}")
            return
        
        try:
            # 플러그인 정보 수집
            plugins_info = {}
            for plugin_id, plugin_data in PLUGINS.items():
                source_path = BASE_DIR / plugin_data['folder']
                main_file = source_path / plugin_data['main_file']
                version = get_version_from_file(main_file) if main_file.exists() else "N/A"
                
                plugins_info[plugin_id] = {
                    'name': plugin_data['name'],
                    'full_name': plugin_data['full_name'],
                    'version': version,
                    'editions': plugin_data['editions'],
                    'description': plugin_data['description'],
                    'folder': plugin_data['folder'],
                    'exists': source_path.exists()
                }
            
            # 대시보드 HTML 생성
            self._generate_dashboard_html(dashboard_path, plugins_info)
            
            self.set_status("✅ 대시보드 업데이트 완료!")
            messagebox.showinfo("성공", f"대시보드가 업데이트되었습니다.\n\n경로: {dashboard_path}")
            
        except Exception as e:
            self.set_status(f"❌ 대시보드 업데이트 실패: {e}")
            messagebox.showerror("오류", f"대시보드 업데이트 실패:\n{e}")
    
    def _generate_dashboard_html(self, output_path, plugins_info):
        """대시보드 HTML 업데이트 (Search & Replace 방식)"""
        if not output_path.exists():
            return

        try:
            with open(output_path, 'r', encoding='utf-8') as f:
                content = f.read()

            # 1. 전체 빌드 버전 및 날짜 업데이트
            timestamp = datetime.datetime.now().strftime("%Y-%m-%d")
            build_id = f"RELEASE-{timestamp.replace('-', '')}-AUTO"
            
            content = re.sub(r'<span>v\d+\.\d+\.\d+.*?</span>', f'<span>v{timestamp.replace("-", ".")} (Build)</span>', content, 1)
            content = re.sub(r'id="last-updated">.*?</span>', f'id="last-updated">{timestamp}</span>', content)
            content = re.sub(r'id="build-id">.*?</span>', f'id="build-id">{build_id}</span>', content)

            # 2. 개별 플러그인 카드 버전 및 링크 업데이트
            for plugin_id, info in plugins_info.items():
                # 카드 내 버전 태그 업데이트
                # 예: <div class="plugin-name">WP Bulk Manager</div> ... <span class="version-tag">v5.0.3</span>
                pattern = f'({re.escape(info["name"])})</div>\\s*<span class="version-tag">v.*?</span>'
                content = re.sub(pattern, f'\\1</div>\n                        <span class="version-tag">v{info["version"]}</span>', content)

                # 다운로드 링크 업데이트
                # 예: href="dist/wp-bulk-manager-master-v5.0.3.zip"
                link_pattern = f'href="dist/{re.escape(info["folder"])}-master-v.*?\\.zip"'
                new_link = f'href="dist/{info["folder"]}-master-v{info["version"]}.zip"'
                content = re.sub(link_pattern, new_link, content)

            # 3. 빌드 정보 테이블 업데이트
            # <tr><td>WP Bulk Manager</td><td>v5.0.3</td>...
            for plugin_id, info in plugins_info.items():
                table_pattern = f'<tr><td>{re.escape(info["name"])}</td><td>v.*?</td>'
                content = re.sub(table_pattern, f'<tr><td>{info["name"]}</td><td>v{info["version"]}</td>', content)

            with open(output_path, 'w', encoding='utf-8') as f:
                f.write(content)
                
            self.log(f"📊 대시보드 HTML 파일 업데이트 완료: {output_path.name}")

        except Exception as e:
            self.log(f"⚠️ 대시보드 업데이트 오류: {e}")

    def git_commit_push(self, message=None):
        """Git 커밋 및 푸시 자동화"""
        if not message:
            message = f"Build Release: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}"
            
        try:
            self.log("🚀 Git 자동 업데이트 시작...")
            subprocess.run(["git", "add", "."], check=True, capture_output=True)
            subprocess.run(["git", "commit", "-m", message], check=True, capture_output=True)
            subprocess.run(["git", "push", "origin", "main"], check=True, capture_output=True)
            self.log("✅ Git Push 완료!")
            return True
        except Exception as e:
            self.log(f"❌ Git 오류: {e}")
            return False
    
    def save_settings_action(self):
        """설정 저장"""
        self.config_data['output_dir'] = self.output_dir_var.get()
        self.config_data['auto_open_folder'] = self.auto_open_var.get()
        self.config_data['auto_shortcut'] = self.auto_shortcut_var.get()
        self.config_data['dashboard_path'] = self.dashboard_path_var.get()
        self.config_data['auto_dashboard_update'] = self.auto_dashboard_var.get()
        self.config_data['auto_git_push'] = self.auto_git_var.get()
        save_config(self.config_data)
        messagebox.showinfo("성공", "설정이 저장되었습니다.")
    
    def create_desktop_shortcut(self):
        """데스크톱 숏컷 생성"""
        if not HAS_PYWIN32:
            messagebox.showwarning("경고", "pywin32가 설치되어 있지 않습니다.\npip install pywin32 를 실행해주세요.")
            return
        
        desktop = Path.home() / 'Desktop'
        shortcut_path = desktop / '🪶 3J Labs Build Manager.lnk'
        
        if create_shortcut(str(Path(__file__)), str(shortcut_path), "🪶 3J Labs ACF CSS Build Manager - 깃털 아이콘"):
            messagebox.showinfo("성공", f"숏컷이 생성되었습니다:\n{shortcut_path}")
        else:
            messagebox.showerror("오류", "숏컷 생성에 실패했습니다.")
    
    def check_shortcut(self):
        """숏컷 존재 여부 확인 및 생성 제안"""
        if not HAS_PYWIN32:
            return
        
        desktop = Path.home() / 'Desktop'
        shortcut_path = desktop / '🪶 3J Labs Build Manager.lnk'
        
        if not shortcut_path.exists() and self.config_data.get('auto_shortcut', True):
            if messagebox.askyesno("숏컷 생성", "데스크톱에 바로가기를 생성하시겠습니까?"):
                self.create_desktop_shortcut()

# ═══════════════════════════════════════════════════════════════════════════════
# CLI 빌드 함수
# ═══════════════════════════════════════════════════════════════════════════════
def cli_build(plugins=None, editions=None):
    """CLI에서 빌드 실행"""
    print("=" * 70)
    print("  3J Labs ACF CSS Plugin Build Manager v22.1 - CLI Mode")
    print("=" * 70)
    
    if editions is None:
        editions = ['master']
    
    engine = BuildEngine(log_callback=lambda msg: print(msg, end=''))
    success = engine.build_all(plugins, editions)
    
    return success

# ═══════════════════════════════════════════════════════════════════════════════
# 메인 실행
# ═══════════════════════════════════════════════════════════════════════════════
if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description='3J Labs ACF CSS Build Manager')
    parser.add_argument('--cli', action='store_true', help='CLI 모드로 실행 (GUI 없이)')
    parser.add_argument('--all', action='store_true', help='모든 플러그인 빌드')
    parser.add_argument('--plugins', nargs='+', help='빌드할 플러그인 ID 목록')
    parser.add_argument('--editions', nargs='+', default=['master'], help='빌드할 에디션 목록')
    
    args = parser.parse_args()
    
    if args.cli or args.all:
        # CLI 모드
        plugins = args.plugins if args.plugins else None
        success = cli_build(plugins, args.editions)
        sys.exit(0 if success else 1)
    else:
        # GUI 모드
        try:
            app = JJBuildManager()
            app.mainloop()
        except Exception as e:
            print(f"프로그램 실행 오류: {e}")
            sys.exit(1)
