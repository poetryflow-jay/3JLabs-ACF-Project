#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
═══════════════════════════════════════════════════════════════════════════════
  3J Labs ACF CSS Plugin Build Manager v22.0.0
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
@version: 22.0.0 (Master Clean)
@date: 2026-01-03
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
        'editions': ['free', 'basic', 'premium', 'unlimited', 'partner', 'master'],
        'is_core': True,
        'description': '메인 플러그인 - WordPress 스타일 통합 관리 시스템'
    },
    'wp-bulk-manager': {
        'id': 'wp-bulk-manager',
        'name': 'WP Bulk Manager',
        'full_name': 'WP Bulk Manager - Plugin & Theme Bulk Installer and Editor',
        'folder': 'wp-bulk-manager',
        'main_file': 'wp-bulk-installer.php',
        'text_domain': 'wp-bulk-manager',
        'editions': ['free', 'premium', 'partner', 'master'],
        'is_core': False,
        'description': '플러그인/테마 대량 설치 및 관리'
    },
    'acf-code-snippets': {
        'id': 'acf-code-snippets',
        'name': 'ACF Code Snippets Box',
        'full_name': 'ACF Code Snippets Box - Advanced Custom Function Manager',
        'folder': 'acf-code-snippets-box',
        'main_file': 'acf-code-snippets-box.php',
        'text_domain': 'acf-code-snippets-box',
        'editions': ['free', 'premium', 'partner', 'master'],
        'is_core': False,
        'description': 'CSS/JS/PHP 코드 스니펫 관리'
    },
    'acf-neural-link': {
        'id': 'acf-neural-link',
        'name': 'ACF CSS Neural Link',
        'full_name': 'ACF CSS Neural Link - License & Update Manager',
        'folder': 'acf-css-neural-link',
        'main_file': 'acf-css-neural-link.php',
        'text_domain': 'acf-css-neural-link',
        'editions': ['master'],
        'is_core': False,
        'description': '라이센스 및 업데이트 관리 시스템'
    },
    'acf-woocommerce': {
        'id': 'acf-woocommerce',
        'name': 'ACF CSS WooCommerce Toolkit',
        'full_name': 'ACF CSS WooCommerce Toolkit - Advanced Commerce Styling',
        'folder': 'acf-css-woocommerce-toolkit',
        'main_file': 'acf-css-woocommerce-toolkit.php',
        'text_domain': 'acf-css-woocommerce-toolkit',
        'editions': ['free', 'premium', 'partner', 'master'],
        'is_core': False,
        'description': 'WooCommerce 스타일 및 기능 확장'
    },
    'acf-ai-extension': {
        'id': 'acf-ai-extension',
        'name': 'ACF CSS AI Extension',
        'full_name': 'ACF CSS AI Extension - Intelligent Style Generator',
        'folder': 'acf-css-ai-extension',
        'main_file': 'acf-css-ai-extension.php',
        'text_domain': 'acf-css-ai-extension',
        'editions': ['premium', 'partner', 'master'],
        'is_core': False,
        'description': 'AI 기반 스타일 추천 및 생성'
    },
    'acf-nudge-flow': {
        'id': 'acf-nudge-flow',
        'name': 'ACF MBA (Nudge Flow)',
        'full_name': 'ACF MBA - Marketing Booster Accelerator (Advanced Custom Funnel)',
        'folder': 'acf-nudge-flow',
        'main_file': 'acf-nudge-flow.php',
        'text_domain': 'acf-nudge-flow',
        'editions': ['free', 'premium', 'partner', 'master'],
        'is_core': False,
        'description': '마케팅 자동화 및 넛지 시스템'
    },
    'admin-menu-editor': {
        'id': 'admin-menu-editor',
        'name': 'Admin Menu Editor Pro',
        'full_name': 'Admin Menu Editor Pro - Advanced Admin Customizer',
        'folder': 'admin-menu-editor-pro',
        'main_file': 'admin-menu-editor-pro.php',
        'text_domain': 'admin-menu-editor-pro',
        'editions': ['free', 'pro', 'master', 'partner'],
        'is_core': False,
        'description': '관리자 메뉴 커스터마이저'
    },
    'acf-css-woo-license': {
        'id': 'acf-css-woo-license',
        'name': 'ACF CSS Woo License Bridge',
        'full_name': 'ACF CSS License Bridge for WooCommerce',
        'folder': 'acf-css-woo-license',
        'main_file': 'acf-css-woo-license.php',
        'text_domain': 'acf-css-woo-license',
        'editions': ['partner', 'master'],
        'is_core': False,
        'description': 'WooCommerce 결제 연동 및 라이센스 발행 브릿지'
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
            self.log(f"❌ 알 수 없는 플러그인: {plugin_id}")
            return False
        
        plugin = PLUGINS[plugin_id]
        source_dir = BASE_DIR / plugin['folder']
        
        if not source_dir.exists():
            self.log(f"❌ 소스 폴더 없음: {source_dir}")
            return False
        
        # 빌드할 에디션 결정
        if editions is None:
            editions = plugin['editions']
        else:
            editions = [e for e in editions if e in plugin['editions']]
        
        if not editions:
            self.log(f"⚠️ {plugin['name']}: 빌드할 에디션 없음")
            return False
        
        self.log(f"🏭 {plugin['name']} 빌드 시작 ({len(editions)}개 에디션)")
        
        success_count = 0
        for edition in editions:
            if self.build_edition(plugin, edition, source_dir):
                success_count += 1
        
        self.log(f"✅ {plugin['name']}: {success_count}/{len(editions)} 에디션 빌드 완료")
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
            
            # 빌드 후 폴더 삭제 (ZIP만 유지)
            shutil.rmtree(output_dir)
            
            self.log(f"   📦 {edition.upper()}: {file_count}개 파일 → {zip_filename}")
            return True
            
        except Exception as e:
            self.log(f"   ❌ {edition.upper()} 빌드 실패: {e}")
            return False
    
    def _archive_old_files(self, folder_base, suffix):
        """기존 ZIP 파일을 old 폴더로 이동"""
        output_dir = Path(self.config['output_dir'])
        old_dir = output_dir / "old"
        
        # 같은 폴더+에디션의 기존 ZIP 찾기
        pattern = f"{folder_base}{suffix}-v*.zip"
        matching_files = list(output_dir.glob(pattern))
        
        if matching_files:
            # 타임스탬프 폴더 생성
            timestamp = datetime.datetime.now().strftime("%Y%m%d-%H%M%S")
            archive_dir = old_dir / f"archive-{timestamp}"
            archive_dir.mkdir(parents=True, exist_ok=True)
            
            for old_file in matching_files:
                try:
                    shutil.move(str(old_file), str(archive_dir / old_file.name))
                except Exception:
                    pass
    
    def build_all(self, plugin_ids=None, editions=None):
        """전체 빌드"""
        if plugin_ids is None:
            plugin_ids = list(PLUGINS.keys())
        
        self.log("═" * 60)
        self.log("🚀 3J Labs ACF CSS 전체 빌드 시작")
        self.log("═" * 60)
        
        total = len(plugin_ids)
        success = 0
        
        for i, plugin_id in enumerate(plugin_ids):
            if self.progress_callback:
                self.progress_callback(i, total, plugin_id)
            
            if self.build_plugin(plugin_id, editions):
                success += 1
        
        if self.progress_callback:
            self.progress_callback(total, total, "완료")
        
        self.log("═" * 60)
        self.log(f"🎉 빌드 완료: {success}/{total} 플러그인 성공")
        self.log(f"📂 출력 폴더: {self.config['output_dir']}")
        self.log("═" * 60)
        
        # 빌드 시간 저장
        self.config['last_build_time'] = datetime.datetime.now().isoformat()
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
        
        # 앱 아이콘 (이모지 사용)
        icon_label = tk.Label(title_row, text="🔧", font=("Segoe UI", 32), bg=self.colors['bg_window'])
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
        cards_data = [
            ("📦", "전체 플러그인", len(PLUGINS), "개", self.colors['accent']),
            ("⭐", "Core 플러그인", sum(1 for p in PLUGINS.values() if p['is_core']), "개", self.colors['success']),
            ("🧩", "Family 플러그인", sum(1 for p in PLUGINS.values() if not p['is_core']), "개", self.colors['info']),
            ("🕐", "마지막 빌드", self.config_data.get('last_build_time', '없음')[:10] if self.config_data.get('last_build_time') else "없음", "", self.colors['text_secondary'])
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
            str(Path.home() / 'Desktop' / 'JJ_Distributions_v8.0.0_Master_Control' / 'dashboard.html')))
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
        ttk.Checkbutton(options_frame, text="빌드 완료 시 외부 대시보드 자동 업데이트", variable=self.auto_dashboard_var).pack(anchor="w", pady=5)
        
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
                self.after(0, lambda: messagebox.showinfo("성공", "빌드가 완료되었습니다!"))
                
                if self.auto_open_var.get():
                    self.after(0, self.open_dist_folder)
                
                # 자동 대시보드 업데이트
                if self.auto_dashboard_var.get():
                    self.after(100, self._auto_update_dashboard)
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
        """대시보드 HTML 생성"""
        timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        
        # 플러그인 카드 생성
        plugin_cards = ""
        for plugin_id, info in plugins_info.items():
            status_class = "success" if info['exists'] else "error"
            status_text = "✅ Ready" if info['exists'] else "❌ Missing"
            editions_html = " ".join([f'<span class="edition">{e}</span>' for e in info['editions']])
            
            plugin_cards += f'''
            <div class="plugin-card">
                <div class="plugin-header">
                    <h3>{info['name']}</h3>
                    <span class="version">v{info['version']}</span>
                </div>
                <p class="description">{info['description']}</p>
                <div class="editions">{editions_html}</div>
                <div class="status {status_class}">{status_text}</div>
                <div class="folder">{info['folder']}</div>
            </div>
            '''
        
        html_content = f'''<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3J Labs ACF CSS - Distribution Dashboard</title>
    <style>
        * {{ margin: 0; padding: 0; box-sizing: border-box; }}
        body {{
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #F5F5F0 0%, #E8E6E1 100%);
            min-height: 100vh;
            padding: 40px;
        }}
        .container {{ max-width: 1400px; margin: 0 auto; }}
        header {{
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }}
        header h1 {{ color: #1D1D1F; font-size: 2.5rem; margin-bottom: 8px; }}
        header p {{ color: #6E6E73; font-size: 1.1rem; }}
        .meta {{ display: flex; gap: 20px; margin-top: 15px; color: #8E8E93; font-size: 0.9rem; }}
        .meta span {{ background: #F5F5F0; padding: 6px 12px; border-radius: 6px; }}
        .plugins-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }}
        .plugin-card {{
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }}
        .plugin-card:hover {{
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }}
        .plugin-header {{ display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }}
        .plugin-header h3 {{ color: #1D1D1F; font-size: 1.2rem; }}
        .version {{ background: #007AFF; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }}
        .description {{ color: #6E6E73; font-size: 0.95rem; margin-bottom: 15px; line-height: 1.5; }}
        .editions {{ display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }}
        .edition {{ background: #F5F5F0; color: #1D1D1F; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; }}
        .status {{ font-weight: 600; margin-bottom: 8px; }}
        .status.success {{ color: #34C759; }}
        .status.error {{ color: #FF3B30; }}
        .folder {{ color: #8E8E93; font-size: 0.85rem; font-family: 'SF Mono', Consolas, monospace; }}
        footer {{
            margin-top: 40px;
            text-align: center;
            color: #8E8E93;
            font-size: 0.9rem;
        }}
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🔧 3J Labs ACF CSS Distribution Dashboard</h1>
            <p>ACF CSS Plugin Family • Build & Distribution Management</p>
            <div class="meta">
                <span>📅 Updated: {timestamp}</span>
                <span>📦 Plugins: {len(plugins_info)}</span>
                <span>🏭 Build Manager v3.2.0</span>
            </div>
        </header>
        
        <div class="plugins-grid">
            {plugin_cards}
        </div>
        
        <footer>
            <p>© 2026 3J Labs (제이×제니×제이슨 연구소). All rights reserved.</p>
        </footer>
    </div>
</body>
</html>'''
        
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write(html_content)
    
    def save_settings_action(self):
        """설정 저장"""
        self.config_data['output_dir'] = self.output_dir_var.get()
        self.config_data['auto_open_folder'] = self.auto_open_var.get()
        self.config_data['auto_shortcut'] = self.auto_shortcut_var.get()
        self.config_data['dashboard_path'] = self.dashboard_path_var.get()
        self.config_data['auto_dashboard_update'] = self.auto_dashboard_var.get()
        save_config(self.config_data)
        messagebox.showinfo("성공", "설정이 저장되었습니다.")
    
    def create_desktop_shortcut(self):
        """데스크톱 숏컷 생성"""
        if not HAS_PYWIN32:
            messagebox.showwarning("경고", "pywin32가 설치되어 있지 않습니다.\npip install pywin32 를 실행해주세요.")
            return
        
        desktop = Path.home() / 'Desktop'
        shortcut_path = desktop / '3J Labs Build Manager.lnk'
        
        if create_shortcut(str(Path(__file__)), str(shortcut_path), "3J Labs ACF CSS Build Manager"):
            messagebox.showinfo("성공", f"숏컷이 생성되었습니다:\n{shortcut_path}")
        else:
            messagebox.showerror("오류", "숏컷 생성에 실패했습니다.")
    
    def check_shortcut(self):
        """숏컷 존재 여부 확인 및 생성 제안"""
        if not HAS_PYWIN32:
            return
        
        desktop = Path.home() / 'Desktop'
        shortcut_path = desktop / '3J Labs Build Manager.lnk'
        
        if not shortcut_path.exists() and self.config_data.get('auto_shortcut', True):
            if messagebox.askyesno("숏컷 생성", "데스크톱에 바로가기를 생성하시겠습니까?"):
                self.create_desktop_shortcut()

# ═══════════════════════════════════════════════════════════════════════════════
# CLI 빌드 함수
# ═══════════════════════════════════════════════════════════════════════════════
def cli_build(plugins=None, editions=None):
    """CLI에서 빌드 실행"""
    print("=" * 70)
    print("  3J Labs ACF CSS Plugin Build Manager v22.0 - CLI Mode")
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
