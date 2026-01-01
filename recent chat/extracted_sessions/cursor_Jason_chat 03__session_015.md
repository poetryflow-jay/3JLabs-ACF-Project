---

**User**

Request ID: 9c068191-a971-48ab-88cb-83c07c5247c8
{"error":"ERROR_USER_ABORTED_REQUEST","details":{"title":"User aborted request.","detail":"Tool call ended before result was received","isRetryable":false,"additionalInfo":{},"buttons":[],"planChoices":[]},"isExpected":true}



---

**Cursor**

죄송합니다 사장님, 중간에 끊겼군요! 

"구라 제거기"의 핵심 기능인 **프로그램 완전 삭제(Uninstall) + 서비스 비활성화 + 프로세스 종료** 기능을 모두 담은 Python 스크립트를 제대로 작성하겠습니다.

### 🧹 JJ Cleaner Ultimate (완전 삭제 버전)

이 스크립트는 다음 작업을 수행합니다:
1. **프로세스 강제 종료** (Task Kill)
2. **Windows 서비스 비활성화 및 중지**
3. **삭제 명령어(Uninstall String) 추출 및 실행**
4. **시작 프로그램(레지스트리) 정리**

바로 작성하겠습니다

```python
"""
🧹 J&Jenny Labs Cleaner Ultimate
- 구라 제거기 완전판
- 프로세스 종료 + 서비스 중지 + 언인스톨러 실행 + 레지스트리 정리
"""
import winreg
import subprocess
import os
import ctypes
import sys
import time

# 제거 대상 키워드 (이 이름이 포함된 프로그램은 제거 대상)
TARGET_KEYWORDS = [
    "nProtect", "INCA Internet", "TouchEn", "RaonSecure",
    "INISAFE", "INIpay", "CrossWeb", "Initech",
    "Veraport", "Wizvera", "Delfino",
    "AnySign", "SoftCamp", "Xecure",
    "IPInside", "Interezen",
    "MarkAny", "ImageSafer", "MaWebSAFER",
    "AhnLab Safe Transaction", "V3 Lite", # 선택적
    "Fasoo", "NatService",
    "VestCert", "yessign", "UniSign",
    "K-Defense", "KeySharp",
]

def is_admin():
    try:
        return ctypes.windll.shell32.IsUserAnAdmin()
    except:
        return False

def run_as_admin():
    if not is_admin():
        ctypes.windll.shell32.ShellExecuteW(None, "runas", sys.executable, " ".join(sys.argv), None, 1)
        sys.exit()

def get_installed_programs():
    """레지스트리에서 설치된 프로그램 목록과 언인스톨 명령어 추출"""
    programs = []
    roots = [winreg.HKEY_LOCAL_MACHINE, winreg.HKEY_CURRENT_USER]
    subkeys = [
        r"SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall",
        r"SOFTWARE\WOW6432Node\Microsoft\Windows\CurrentVersion\Uninstall"
    ]
    
    for root in roots:
        for subkey in subkeys:
            try:
                with winreg.OpenKey(root, subkey) as key:
                    for i in range(winreg.QueryInfoKey(key)[0]):
                        try:
                            app_key_name = winreg.EnumKey(key, i)
                            with winreg.OpenKey(key, app_key_name) as app_key:
                                try:
                                    name = winreg.QueryValueEx(app_key, "DisplayName")[0]
                                    try:
                                        uninstall_string = winreg.QueryValueEx(app_key, "UninstallString")[0]
                                        programs.append({"name": name, "cmd": uninstall_string})
                                    except:
                                        pass
                                except:
                                    pass
                        except:
                            continue
            except:
                continue
    return programs

def find_targets(programs):
    """제거 대상 프로그램 찾기"""
    targets = []
    for prog in programs:
        for keyword in TARGET_KEYWORDS:
            if keyword.lower() in prog["name"].lower():
                targets.append(prog)
                break
    return targets

def kill_processes():
    """관련 프로세스 강제 종료"""
    print("\n[1/4] 관련 프로세스 종료 중...")
    for keyword in TARGET_KEYWORDS:
        try:
            subprocess.run(f"taskkill /F /FI \"IMAGENAME eq *{keyword}*\"", shell=True, capture_output=True)
        except:
            pass

def stop_services():
    """관련 서비스 중지 및 비활성화"""
    print("\n[2/4] 관련 서비스 중지 중...")
    try:
        result = subprocess.run("sc query state= all", shell=True, capture_output=True, text=True)
        for line in result.stdout.split('\n'):
            if "SERVICE_NAME" in line:
                svc_name = line.split(":")[1].strip()
                for keyword in TARGET_KEYWORDS:
                    if keyword.lower() in svc_name.lower():
                        print(f"  서비스 중지: {svc_name}")
                        subprocess.run(f"sc stop \"{svc_name}\"", shell=True, capture_output=True)
                        subprocess.run(f"sc config \"{svc_name}\" start= disabled", shell=True, capture_output=True)
    except:
        pass

def uninstall_programs(targets):
    """언인스톨러 실행"""
    print(f"\n[3/4] 프로그램 제거 시작 ({len(targets)}개)")
    
    for i, prog in enumerate(targets, 1):
        name = prog["name"]
        cmd = prog["cmd"]
        print(f"\n[{i}/{len(targets)}] {name} 제거 중...")
        
        # MsiExec 처리
        if "msiexec" in cmd.lower() and "/i" in cmd.lower():
            cmd = cmd.lower().replace("/i", "/x") # 설치(install) -> 제거(uninstall)
            cmd += " /quiet /norestart" # 조용히 제거
        
        # 일반 언인스톨러 처리 (가능하면 조용히)
        elif "uninstall.exe" in cmd.lower():
             # 일부 언인스톨러는 /S 옵션 지원
            cmd += " /S" 
            
        print(f"  명령어: {cmd}")
        try:
            subprocess.Popen(cmd, shell=True)
            # 팝업이 뜰 수 있으므로 잠시 대기
            time.sleep(2)
        except Exception as e:
            print(f"  오류: {e}")

def main():
    if not is_admin():
        print("관리자 권한으로 다시 실행합니다...")
        run_as_admin()
        return

    os.system('cls')
    print("=" * 60)
    print("🧹 J&Jenny Labs Cleaner Ultimate")
    print("   구라 제거기 완전판 (프로세스+서비스+삭제)")
    print("=" * 60)
    
    print("\n🔍 설치된 프로그램 검색 중...")
    all_programs = get_installed_programs()
    targets = find_targets(all_programs)
    
    if not targets:
        print("\n✨ 시스템이 깨끗합니다! 제거할 '구라'가 없습니다.")
        input("\n엔터를 누르면 종료합니다...")
        return
        
    print(f"\n🎯 발견된 제거 대상: {len(targets)}개")
    for prog in targets:
        print(f"  • {prog['name']}")
    
    print("\n" + "-" * 60)
    choice = input("전부 제거하시겠습니까? (y/n): ").strip().lower()
    
    if choice == 'y':
        kill_processes()
        stop_services()
        uninstall_programs(targets)
        print("\n" + "=" * 60)
        print("🎉 모든 작업이 완료되었습니다!")
        print("   일부 프로그램은 수동으로 '예/제거'를 눌러야 할 수 있습니다.")
        print("=" * 60)
    else:
        print("취소되었습니다.")
    
    input("\n엔터를 누르면 종료합니다...")

if __name__ == "__main__":
    main()

```

**JJ Cleaner Ultimate**가 완성되었습니다! 

이 스크립트는:
1. 레지스트리를 뒤져 설치된 모든 "구라" 프로그램(nProtect, AhnLab, TouchEn 등)을 찾습니다.
2. 관련 프로세스와 서비스를 강제로 죽입니다.
3. 각 프로그램의 **공식 언인스톨러**를 자동으로 실행합니다. (가능한 경우 `/quiet` 옵션 사용)

지금 바로 실행해서 시스템을 정화하겠습니다! 🧹✨

### 🧹 JJ Cleaner Ultimate 가동!

사장님 화면에 새 창이 뜨면서 **설치된 모든 "구라" 프로그램 목록**을 보여줄 것입니다. `y`를 입력하시면 대청소가 시작됩니다.
