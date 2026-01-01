---

**User**

'구라 제거기'라고, TEUS ME에서 제공하는 오픈 소스 소프트웨어가 있는데, 그 파일을 첨부해줄게. 우리에게 있어 중요한 메모리를 잡아 먹고, 종료 이후에도 계속 백그라운드 프로세스에 상주하는 사악한 '자칭 보안 프로그램'이라네. 웹 검색을 해봐도 좋아. 이걸 우리 식대로 하나 만들어서 '구라'를 제거하고, 태스크를 킬하도록 하지.


---

**Cursor**

사장님, 이해했습니다! "구라 제거기"에 대해 검색해보겠습니다.

완벽히 이해했습니다! J&Jenny Labs 버전의 "구라 제거기"를 만들겠습니다!

```python
"""
🧹 J&Jenny Labs Cleaner (JJ Cleaner)
- 구라 제거기 J&J Labs Edition
- 불필요한 "보안" 프로그램 프로세스 종료
- 메모리 확보 및 시스템 정리
"""
import subprocess
import os
import ctypes
import sys

# 제거 대상 프로세스 목록 (한국 가짜 보안 프로그램들)
TARGET_PROCESSES = [
    # nProtect 시리즈
    "nProtect", "npupdate", "npsvc", "npkcmsvc", "npkcsvc",
    # AhnLab
    "AhnLab", "ASDSvc", "V3Lite", "V3SP", "AhnSD",
    # TouchEn
    "TouchEn", "TouchEnNxKey", "TouchEnKey", "TKMain",
    # INISAFE
    "INISAFE", "INISAFEWeb", "CrossWeb", "CrossWebEx",
    # Veraport
    "veraport", "VeraPort",
    # 이니텍
    "IPInside", "interezen",
    # 마크애니
    "MagicLine", "magicline",
    # 기타 자주 발견되는 것들
    "KeySharp", "KSWeb", "AST", "astx", "delfino",
    "XecureWeb", "XecureSC", "wizvera", "Wizvera",
    "NOS", "SoftCamp", "softcamp", "ahnrpt", "astsvc",
    "AntiVirus", "Rexpert", "WebCube", "vcdrm",
    # ActiveX 관련
    "activex", "axinstall",
    # 추가 보안 프로그램들
    "fasoo", "Fasoo", "DRM", "drm",
    "SecuBrowser", "secubrowser",
    "Safe Transaction", "safetransaction",
    "BankSign", "banksign",
]

def is_admin():
    """관리자 권한 확인"""
    try:
        return ctypes.windll.shell32.IsUserAnAdmin()
    except:
        return False

def get_running_processes():
    """실행 중인 프로세스 목록 가져오기"""
    try:
        result = subprocess.run(
            ["tasklist", "/FO", "CSV", "/NH"],
            capture_output=True, text=True, encoding='cp949'
        )
        processes = []
        for line in result.stdout.strip().split('\n'):
            if line:
                parts = line.replace('"', '').split(',')
                if len(parts) >= 2:
                    name = parts[0]
                    pid = parts[1]
                    try:
                        mem = int(parts[4].replace(' K', '').replace(',', '')) if len(parts) > 4 else 0
                    except:
                        mem = 0
                    processes.append((name, pid, mem))
        return processes
    except Exception as e:
        print(f"프로세스 목록 조회 실패: {e}")
        return []

def find_target_processes(processes):
    """제거 대상 프로세스 찾기"""
    targets = []
    for name, pid, mem in processes:
        for target in TARGET_PROCESSES:
            if target.lower() in name.lower():
                targets.append((name, pid, mem))
                break
    return targets

def kill_process(pid, name):
    """프로세스 종료"""
    try:
        result = subprocess.run(
            ["taskkill", "/F", "/PID", str(pid)],
            capture_output=True, text=True
        )
        return result.returncode == 0
    except:
        return False

def format_memory(kb):
    """메모리 크기 포맷"""
    if kb >= 1024 * 1024:
        return f"{kb / (1024*1024):.1f} GB"
    elif kb >= 1024:
        return f"{kb / 1024:.1f} MB"
    else:
        return f"{kb} KB"

def main():
    print()
    print("╔" + "═" * 58 + "╗")
    print("║  🧹 J&Jenny Labs Cleaner v1.0                           ║")
    print("║  불필요한 '보안' 프로그램 정리기                         ║")
    print("╠" + "═" * 58 + "╣")
    print("║  CTO: Jason | CEO: 사장님                               ║")
    print("╚" + "═" * 58 + "╝")
    print()
    
    # 관리자 권한 확인
    if not is_admin():
        print("⚠️  관리자 권한이 필요합니다!")
        print("   마우스 오른쪽 클릭 → '관리자 권한으로 실행'")
        print()
        input("아무 키나 누르면 종료...")
        return
    
    print("🔍 프로세스 검색 중...")
    processes = get_running_processes()
    targets = find_target_processes(processes)
    
    if not targets:
        print()
        print("✨ 구라가 없습니다! 시스템이 깨끗합니다.")
        print()
        input("아무 키나 누르면 종료...")
        return
    
    print()
    print(f"🎯 발견된 '구라' 프로세스: {len(targets)}개")
    print("-" * 60)
    
    total_memory = 0
    for name, pid, mem in targets:
        total_memory += mem
        print(f"  • {name:<30} (PID: {pid:<6}) {format_memory(mem)}")
    
    print("-" * 60)
    print(f"  💾 총 점유 메모리: {format_memory(total_memory)}")
    print()
    
    # 사용자 확인
    answer = input("🗑️  모두 종료하시겠습니까? (y/n): ").strip().lower()
    
    if answer != 'y':
        print("취소되었습니다.")
        input("아무 키나 누르면 종료...")
        return
    
    print()
    print("🧹 정리 중...")
    
    killed = 0
    freed_memory = 0
    
    for name, pid, mem in targets:
        if kill_process(pid, name):
            killed += 1
            freed_memory += mem
            print(f"  ✅ {name} (PID: {pid}) 종료됨")
        else:
            print(f"  ❌ {name} (PID: {pid}) 종료 실패")
    
    print()
    print("═" * 60)
    print(f"  🎉 결과: {killed}/{len(targets)}개 프로세스 종료")
    print(f"  💾 확보된 메모리: {format_memory(freed_memory)}")
    print("═" * 60)
    print()
    print("  💡 팁: 완전 제거는 '프로그램 추가/제거'에서 진행하세요.")
    print()
    input("아무 키나 누르면 종료...")

if __name__ == "__main__":
    main()

```

J&Jenny Labs Cleaner 완성! 바로 실행해서 현재 시스템의 "구라"를 찾아보겠습니다!
