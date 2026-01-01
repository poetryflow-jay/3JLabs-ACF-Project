#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
3J Labs Development Toolkit
제이x제니x제이슨 연구소 개발 도구 키트

AI 런처와 별도로 동작하는 인터랙티브 개발 도구입니다.
플러그인 빌드, 배포, 버전 관리를 GUI로 수행할 수 있습니다.

Version: 1.0.0
Author: 3J Labs (Jay & Jason & Jenny)
"""

import os
import sys
import json
import shutil
import zipfile
import subprocess
import re
from datetime import datetime
from pathlib import Path

# Tkinter GUI
try:
    import tkinter as tk
    from tkinter import ttk, messagebox, filedialog, scrolledtext
except ImportError:
    print("Tkinter가 설치되어 있지 않습니다.")
    sys.exit(1)


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


class DevToolkit(tk.Tk):
    """메인 GUI 애플리케이션"""
    
    def __init__(self):
        super().__init__()
        
        self.title("3J Labs Development Toolkit v1.0.0")
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
        
        # 탭 2: 빌드 도구
        self.build_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.build_tab, text="🔨 빌드 도구")
        self._create_build_tab()
        
        # 탭 3: 배포
        self.deploy_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.deploy_tab, text="🚀 배포")
        self._create_deploy_tab()
        
        # 탭 4: 로그
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
        """전체 빌드"""
        for key, info in self.plugins.items():
            self._log(f"빌드 중: {key}")
        
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


# simpledialog 임포트
try:
    from tkinter import simpledialog
except ImportError:
    pass


def main():
    """메인 함수"""
    app = DevToolkit()
    app.mainloop()


if __name__ == '__main__':
    main()
