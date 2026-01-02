import tkinter as tk
from tkinter import ttk, messagebox, scrolledtext
import subprocess
import os
import sys
import threading
import datetime
import webbrowser
import requests # API 통신용 (없으면 pip install requests 필요하지만, 기본 라이브러리인 urllib 사용도 가능)
import json

# ============================================================
# 설정
# ============================================================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
VENV_PYTHON = r"C:\my-ai\venv\Scripts\python.exe" # 기존 AI 런처 환경 활용
BUILD_SCRIPT = os.path.join(BASE_DIR, "build_distribution_final.py")
CHANGELOG_FILE = os.path.join(BASE_DIR, "changelog.md")
ROADMAP_FILE = os.path.join(BASE_DIR, "FUTURE_ROADMAP.md")

# Neural Link 서버 주소 (기본값)
NEURAL_SERVER_URL = "https://j-j-labs.com/wp-json/acf-neural-link/v1/launcher/status"

# ============================================================
# 유틸리티 함수
# ============================================================
def read_file_content(path):
    if not os.path.exists(path):
        return "파일을 찾을 수 없습니다."
    try:
        with open(path, "r", encoding="utf-8") as f:
            return f.read()
    except Exception as e:
        return f"읽기 오류: {e}"

# ============================================================
# GUI 애플리케이션
# ============================================================
class JJLauncher(tk.Tk):
    def __init__(self):
        super().__init__()
        self.title("3J Labs ACF CSS Launcher v1.0")
        self.geometry("1000x700")
        self.configure(bg="#1e1e1e") # Dark Theme
        
        # 스타일 설정
        style = ttk.Style()
        style.theme_use('clam')
        style.configure("TFrame", background="#1e1e1e")
        style.configure("TLabel", background="#1e1e1e", foreground="#ffffff", font=("Segoe UI", 10))
        style.configure("Header.TLabel", font=("Segoe UI", 20, "bold"), foreground="#00a8ff")
        style.configure("Card.TFrame", background="#252526", relief="flat")
        style.configure("TButton", font=("Segoe UI", 10), padding=6, background="#333333", foreground="#ffffff")
        style.map("TButton", background=[('active', '#007acc')])
        
        style.configure("TNotebook", background="#1e1e1e", borderwidth=0)
        style.configure("TNotebook.Tab", background="#333333", foreground="#ffffff", padding=[10, 5], font=("Segoe UI", 10))
        style.map("TNotebook.Tab", background=[("selected", "#007acc")], foreground=[("selected", "#ffffff")])

        self.create_widgets()
        self.check_neural_status() # 시작 시 상태 체크
        
    def create_widgets(self):
        # 헤더
        header_frame = ttk.Frame(self)
        header_frame.pack(fill="x", padx=20, pady=20)
        ttk.Label(header_frame, text="3J Labs Command Center", style="Header.TLabel").pack(side="left")
        ttk.Label(header_frame, text="v1.0.0", font=("Segoe UI", 10), foreground="#888888").pack(side="left", padx=10, pady=(10,0))
        
        # 탭 컨테이너
        self.notebook = ttk.Notebook(self)
        self.notebook.pack(fill="both", expand=True, padx=20, pady=(0, 20))
        
        # 탭 1: Dashboard
        self.tab_dashboard = ttk.Frame(self.notebook)
        self.notebook.add(self.tab_dashboard, text="📊 대시보드")
        self.create_dashboard_tab()
        
        # 탭 2: Build Center
        self.tab_build = ttk.Frame(self.notebook)
        self.notebook.add(self.tab_build, text="🏭 빌드 센터")
        self.create_build_tab()
        
        # 탭 3: Neural Monitor
        self.tab_neural = ttk.Frame(self.notebook)
        self.notebook.add(self.tab_neural, text="🧠 뉴럴 모니터")
        self.create_neural_tab()

        # 하단 상태바
        self.status_bar = ttk.Label(self, text="Ready to Launch.", relief="sunken", anchor="w", font=("Consolas", 9), foreground="#cccccc")
        self.status_bar.pack(fill="x", side="bottom")

    def create_dashboard_tab(self):
        # 상단 정보 카드
        info_frame = ttk.Frame(self.tab_dashboard)
        info_frame.pack(fill="x", pady=20)
        
        self.card_version = self.create_info_card(info_frame, "Latest Version", "v7.0.0", "Release Ready")
        self.card_version.pack(side="left", fill="x", expand=True, padx=5)
        
        self.card_neural = self.create_info_card(info_frame, "Neural Link", "Checking...", "Connecting...")
        self.card_neural.pack(side="left", fill="x", expand=True, padx=5)
        
        self.card_ai = self.create_info_card(info_frame, "AI Engine", "Gemma 3", "Local")
        self.card_ai.pack(side="left", fill="x", expand=True, padx=5)

        # 문서 뷰어 (Changelog & Roadmap)
        doc_frame = ttk.Frame(self.tab_dashboard)
        doc_frame.pack(fill="both", expand=True, pady=10)
        
        # Changelog
        left_doc = ttk.LabelFrame(doc_frame, text="📜 변경 내역 (Changelog)")
        left_doc.pack(side="left", fill="both", expand=True, padx=5)
        self.changelog_text = scrolledtext.ScrolledText(left_doc, bg="#252526", fg="#d4d4d4", font=("Consolas", 10), relief="flat")
        self.changelog_text.pack(fill="both", expand=True, padx=5, pady=5)
        self.changelog_text.insert("1.0", read_file_content(CHANGELOG_FILE))
        self.changelog_text.config(state="disabled") # 읽기 전용

        # Roadmap
        right_doc = ttk.LabelFrame(doc_frame, text="🗺️ 로드맵 (Roadmap)")
        right_doc.pack(side="right", fill="both", expand=True, padx=5)
        self.roadmap_text = scrolledtext.ScrolledText(right_doc, bg="#252526", fg="#d4d4d4", font=("Consolas", 10), relief="flat")
        self.roadmap_text.pack(fill="both", expand=True, padx=5, pady=5)
        self.roadmap_text.insert("1.0", read_file_content(ROADMAP_FILE))
        self.roadmap_text.config(state="disabled")

        # 새로고침 버튼
        ttk.Button(self.tab_dashboard, text="문서 새로고침", command=self.refresh_docs).pack(anchor="e", pady=10)

    def create_build_tab(self):
        btn_frame = ttk.Frame(self.tab_build)
        btn_frame.pack(fill="x", pady=20)
        
        ttk.Button(btn_frame, text="🚀 전체 빌드 시작 (Full Build)", command=self.start_build).pack(side="left", padx=5)
        ttk.Button(btn_frame, text="📂 결과 폴더 열기", command=self.open_output_folder).pack(side="left", padx=5)
        
        log_frame = ttk.LabelFrame(self.tab_build, text="📟 빌드 로그 (Console Output)")
        log_frame.pack(fill="both", expand=True, pady=10)
        
        self.log_text = scrolledtext.ScrolledText(log_frame, bg="#1e1e1e", fg="#00ff00", font=("Consolas", 10), relief="flat")
        self.log_text.pack(fill="both", expand=True, padx=5, pady=5)

    def create_neural_tab(self):
        # Phase 2: Neural Link Monitor
        
        # 상단: 서버 상태
        status_frame = ttk.LabelFrame(self.tab_neural, text="📡 Neural Link Server Status")
        status_frame.pack(fill="x", padx=10, pady=10)
        
        self.lbl_server_url = ttk.Label(status_frame, text=f"Endpoint: {NEURAL_SERVER_URL}")
        self.lbl_server_url.pack(anchor="w", padx=10, pady=5)
        
        self.lbl_server_status = ttk.Label(status_frame, text="Status: Unknown", foreground="gray")
        self.lbl_server_status.pack(anchor="w", padx=10, pady=5)

        ttk.Button(status_frame, text="🔄 상태 새로고침", command=self.check_neural_status).pack(anchor="e", padx=10, pady=5)

        # 하단: 라이센스 통계 (그리드)
        stats_frame = ttk.LabelFrame(self.tab_neural, text="📈 License Statistics")
        stats_frame.pack(fill="both", expand=True, padx=10, pady=10)
        
        self.lbl_total_licenses = ttk.Label(stats_frame, text="Total Issued: -", font=("Segoe UI", 12))
        self.lbl_total_licenses.grid(row=0, column=0, padx=20, pady=20)
        
        self.lbl_active_licenses = ttk.Label(stats_frame, text="Active: -", font=("Segoe UI", 12), foreground="#00ff00")
        self.lbl_active_licenses.grid(row=0, column=1, padx=20, pady=20)
        
        self.lbl_expired_licenses = ttk.Label(stats_frame, text="Expired: -", font=("Segoe UI", 12), foreground="#ff4444")
        self.lbl_expired_licenses.grid(row=0, column=2, padx=20, pady=20)

    def create_info_card(self, parent, title, value, sub):
        card = ttk.Frame(parent, style="Card.TFrame", padding=15)
        ttk.Label(card, text=title, font=("Segoe UI", 10), foreground="#aaaaaa", background="#252526").pack(anchor="w")
        val_label = ttk.Label(card, text=value, font=("Segoe UI", 18, "bold"), foreground="#ffffff", background="#252526")
        val_label.pack(anchor="w", pady=5)
        sub_label = ttk.Label(card, text=sub, font=("Segoe UI", 9), foreground="#00a8ff", background="#252526")
        sub_label.pack(anchor="w")
        
        # 참조를 저장하기 위해 card에 속성으로 추가 (꼼수지만 효과적)
        card.val_label = val_label
        card.sub_label = sub_label
        return card

    def refresh_docs(self):
        self.changelog_text.config(state="normal")
        self.changelog_text.delete("1.0", tk.END)
        self.changelog_text.insert("1.0", read_file_content(CHANGELOG_FILE))
        self.changelog_text.config(state="disabled")
        
        self.roadmap_text.config(state="normal")
        self.roadmap_text.delete("1.0", tk.END)
        self.roadmap_text.insert("1.0", read_file_content(ROADMAP_FILE))
        self.roadmap_text.config(state="disabled")
        self.status_bar["text"] = "Documents Refreshed."

    def start_build(self):
        self.log_text.delete("1.0", tk.END)
        self.log_text.insert("1.0", ">>> Build Sequence Initiated...\n")
        self.status_bar["text"] = "Building..."
        
        threading.Thread(target=self._run_build_script, daemon=True).start()

    def _run_build_script(self):
        try:
            python_cmd = VENV_PYTHON if os.path.exists(VENV_PYTHON) else "python"
            
            process = subprocess.Popen(
                [python_cmd, BUILD_SCRIPT],
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True,
                cwd=BASE_DIR,
                encoding='utf-8',
                creationflags=subprocess.CREATE_NO_WINDOW
            )
            
            for line in process.stdout:
                self.after(0, self._append_log, line)
            
            for line in process.stderr:
                self.after(0, self._append_log, f"ERROR: {line}")
                
            process.wait()
            
            if process.returncode == 0:
                self.after(0, lambda: self.status_bar.config(text="Build Success! ✅"))
                self.after(0, lambda: messagebox.showinfo("성공", "모든 플러그인 빌드가 완료되었습니다."))
            else:
                self.after(0, lambda: self.status_bar.config(text="Build Failed ❌"))
                
        except Exception as e:
            self.after(0, self._append_log, f"\nCRITICAL ERROR: {str(e)}")

    def _append_log(self, text):
        self.log_text.insert(tk.END, text)
        self.log_text.see(tk.END)

    def open_output_folder(self):
        desktop = os.path.join(os.environ['USERPROFILE'], 'Desktop')
        target = desktop
        for item in os.listdir(desktop):
            if "JJ_Distributions" in item and "_Final" in item:
                target = os.path.join(desktop, item)
        os.startfile(target)

    # [Phase 2] Neural Link 상태 체크
    def check_neural_status(self):
        threading.Thread(target=self._fetch_neural_data, daemon=True).start()

    def _fetch_neural_data(self):
        try:
            # 1. requests 라이브러리 사용 시도 (없으면 urllib 폴백)
            # 여기선 간단히 urllib 사용 (내장 라이브러리라 안전)
            import urllib.request
            import ssl
            
            # SSL 인증서 무시 (개발환경용)
            ctx = ssl.create_default_context()
            ctx.check_hostname = False
            ctx.verify_mode = ssl.CERT_NONE
            
            req = urllib.request.Request(NEURAL_SERVER_URL)
            # req.add_header('Authorization', 'Bearer YOUR_KEY') # 필요시 추가
            
            with urllib.request.urlopen(req, context=ctx, timeout=5) as response:
                data = json.loads(response.read().decode('utf-8'))
                
                if data.get('success'):
                    stats = data.get('stats', {})
                    system = data.get('system', {})
                    
                    self.after(0, lambda: self._update_neural_ui(stats, system, True))
                else:
                    self.after(0, lambda: self._update_neural_ui({}, {}, False))
                    
        except Exception as e:
            print(f"Neural Link Connection Error: {e}")
            self.after(0, lambda: self._update_neural_ui({}, {}, False, str(e)))

    def _update_neural_ui(self, stats, system, success, error_msg=""):
        if success:
            # Dashboard 업데이트
            self.card_neural.val_label.config(text="Active", foreground="#00ff00")
            self.card_neural.sub_label.config(text=f"v{system.get('plugin_version')}")
            
            # Neural Tab 업데이트
            self.lbl_server_status.config(text="Status: Online ✅", foreground="#00ff00")
            self.lbl_total_licenses.config(text=f"Total Issued: {stats.get('total', 0)}")
            self.lbl_active_licenses.config(text=f"Active: {stats.get('active', 0)}")
            self.lbl_expired_licenses.config(text=f"Expired: {stats.get('expired', 0)}")
        else:
            self.card_neural.val_label.config(text="Offline", foreground="#ff4444")
            self.card_neural.sub_label.config(text="Check Server")
            self.lbl_server_status.config(text=f"Status: Offline ({error_msg})", foreground="#ff4444")

if __name__ == "__main__":
    app = JJLauncher()
    app.mainloop()
