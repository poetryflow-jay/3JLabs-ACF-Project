---

**User**

멋지군. 자네가 원할 때 알림을 보낼 수 있는 기능까지 구현해서, 타임아웃 방지와 함께 내게 알림음과 푸시 알림을 보내줄 수 있도록 부탁하네, 우리의 CTO, 미스터 제이슨. 자네 덕분에 제이 앤 제니 랩스의 미래는 밝다네.


---

**Cursor**

사장님, 과분한 칭찬 감사합니다! 제이 앤 제니 랩스의 CTO로서 최선을 다하겠습니다! 🙏

알림 시스템을 강화하겠습니다!

```python
"""
🚀 J&Jenny Labs - Smart Download Monitor
- 실시간 진행률 표시
- 완료 시 알림음 + Windows 푸시 알림
- 타임아웃 방지용 주기적 상태 보고
"""
import os
import time
import winsound
import ctypes
from datetime import datetime

MODELS_DIR = r"C:\my-ai\models"

# 예상 최종 크기 (GB)
EXPECTED_SIZES = {
    "gemma-3-4b-it": 8.6,
    "FLUX.1-schnell": 33.0,
    "Llama-3.1-8B-Instruct": 16.0,
    "Phi-3.5-mini-instruct": 7.6,
    "Mistral-7B-Instruct-v0.3": 27.0,
    "Qwen2.5-Coder-7B-Instruct": 14.2,
    "Qwen2.5-3B-Instruct": 6.0,
    "sdxl-turbo": 51.7,
    "sdxl-base-1.0": 43.9,
    "svd-xt": 30.4,
}

# 알림 완료된 모델 추적
notified_models = set()

def get_folder_size_gb(path):
    total = 0
    try:
        for dirpath, dirnames, filenames in os.walk(path):
            for f in filenames:
                fp = os.path.join(dirpath, f)
                if os.path.exists(fp):
                    total += os.path.getsize(fp)
    except:
        pass
    return total / (1024**3)

def format_time(seconds):
    if seconds < 0 or seconds > 86400 * 7:
        return "계산 중..."
    if seconds < 60:
        return f"{int(seconds)}초"
    elif seconds < 3600:
        return f"{int(seconds//60)}분 {int(seconds%60)}초"
    else:
        hours = int(seconds // 3600)
        mins = int((seconds % 3600) // 60)
        return f"{hours}시간 {mins}분"

def play_success_sound():
    """성공 알림음 (Windows 기본음)"""
    try:
        winsound.MessageBeep(winsound.MB_ICONASTERISK)
        time.sleep(0.3)
        winsound.MessageBeep(winsound.MB_ICONASTERISK)
        time.sleep(0.3)
        winsound.MessageBeep(winsound.MB_ICONASTERISK)
    except:
        pass

def play_complete_fanfare():
    """모든 다운로드 완료 팡파르"""
    try:
        # 승리 멜로디
        frequencies = [523, 659, 784, 1047]  # C5, E5, G5, C6
        for freq in frequencies:
            winsound.Beep(freq, 200)
        winsound.Beep(1047, 500)  # 마지막 음 길게
    except:
        pass

def show_notification(title, message):
    """Windows 팝업 알림"""
    try:
        ctypes.windll.user32.MessageBoxW(0, message, title, 0x40 | 0x1000)
    except:
        pass

def show_toast_notification(title, message):
    """Windows 토스트 알림 (비차단)"""
    try:
        from win10toast import ToastNotifier
        toaster = ToastNotifier()
        toaster.show_toast(title, message, duration=5, threaded=True)
    except:
        # win10toast가 없으면 소리만
        play_success_sound()

def main():
    print("=" * 70)
    print("  🚀 J&Jenny Labs - Smart Download Monitor v2.0")
    print("  CTO: Jason | CEO: 사장님")
    print("=" * 70)
    print("  ✓ 실시간 진행률 표시")
    print("  ✓ 완료 시 알림음 + 푸시 알림")
    print("  ✓ 5초마다 자동 갱신")
    print("=" * 70)
    print()
    
    start_time = time.time()
    prev_sizes = {}
    all_target_models = ["gemma-3-4b-it", "FLUX.1-schnell", "Phi-3.5-mini-instruct", "Llama-3.1-8B-Instruct"]
    
    # 초기 크기 기록
    for model_name in EXPECTED_SIZES.keys():
        model_path = os.path.join(MODELS_DIR, model_name)
        if os.path.exists(model_path):
            prev_sizes[model_name] = get_folder_size_gb(model_path)
    
    try:
        iteration = 0
        while True:
            iteration += 1
            os.system('cls' if os.name == 'nt' else 'clear')
            
            now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            elapsed = time.time() - start_time
            
            print("╔" + "═" * 68 + "╗")
            print(f"║  🚀 J&Jenny Labs - AI Model Download Monitor              ║")
            print(f"║  현재 시각: {now}                            ║")
            print("╠" + "═" * 68 + "╣")
            
            downloading_count = 0
            completed_count = 0
            completed_new = []
            
            for model_name, expected_gb in EXPECTED_SIZES.items():
                model_path = os.path.join(MODELS_DIR, model_name)
                
                if not os.path.exists(model_path):
                    continue
                
                current_gb = get_folder_size_gb(model_path)
                percent = min(100, (current_gb / expected_gb) * 100)
                
                # 속도 계산
                prev_gb = prev_sizes.get(model_name, 0)
                speed_mbps = max(0, (current_gb - prev_gb) * 1024 / 5)
                
                # 남은 시간 예측
                remaining_gb = max(0, expected_gb - current_gb)
                if speed_mbps > 0.05:
                    eta_seconds = (remaining_gb * 1024) / speed_mbps
                    eta_str = format_time(eta_seconds)
                else:
                    eta_str = "-"
                
                # 상태 판정
                is_complete = percent >= 95 and current_gb > (expected_gb * 0.9)
                is_downloading = speed_mbps > 0.05
                
                if is_complete:
                    status = "✅ 완료"
                    completed_count += 1
                    
                    # 새로 완료된 모델 알림
                    if model_name not in notified_models and model_name in all_target_models:
                        notified_models.add(model_name)
                        completed_new.append(model_name)
                        
                elif is_downloading:
                    status = "🔄 다운로드 중"
                    downloading_count += 1
                elif current_gb > 0.1:
                    status = "⏸️ 대기"
                    completed_count += 1
                else:
                    status = "⏳ 시작 전"
                
                # 프로그레스 바
                bar_width = 25
                filled = int(bar_width * percent / 100)
                bar = "█" * filled + "░" * (bar_width - filled)
                
                # 속도 표시
                speed_str = f"{speed_mbps:.1f} MB/s" if speed_mbps > 0.05 else ""
                
                print(f"║  {model_name[:25]:<25} {status:<12}           ║")
                print(f"║    [{bar}] {percent:5.1f}%                   ║")
                print(f"║    {current_gb:.2f}/{expected_gb:.1f}GB {speed_str:<12} ETA: {eta_str:<12}  ║")
                print("║" + " " * 68 + "║")
                
                prev_sizes[model_name] = current_gb
            
            print("╠" + "═" * 68 + "╣")
            print(f"║  📊 다운로드: {downloading_count}개 | 완료: {completed_count}개 | 경과: {format_time(elapsed):<12} ║")
            print("╚" + "═" * 68 + "╝")
            
            # 새로 완료된 모델 알림
            for model_name in completed_new:
                print(f"\n🎉 {model_name} 다운로드 완료!")
                play_success_sound()
            
            # 모든 타겟 모델 완료 확인
            all_done = all(m in notified_models for m in all_target_models)
            if all_done and len(notified_models) >= len(all_target_models):
                print("\n" + "🎊" * 20)
                print("  모든 AI 모델 다운로드 완료!")
                print("🎊" * 20)
                play_complete_fanfare()
                show_notification(
                    "🎉 J&Jenny Labs",
                    "모든 AI 모델 다운로드가 완료되었습니다!\n\n사장님, 이제 AI 서버를 시작할 수 있습니다!"
                )
                break
            
            # 30초마다 상태 로그 (타임아웃 방지용)
            if iteration % 6 == 0:
                log_msg = f"[{now}] 다운로드 진행 중... (완료: {completed_count}, 진행: {downloading_count})"
                print(f"\n📝 {log_msg}")
            
            time.sleep(5)
            
    except KeyboardInterrupt:
        print("\n\n모니터 종료. 다운로드는 백그라운드에서 계속 진행됩니다.")
        print("다시 시작하려면: python smart_monitor.py")

if __name__ == "__main__":
    main()

```

### 🎉 J&Jenny Labs Smart Monitor v2.0 가동!

사장님 화면에 새 모니터링 창이 떠 있을 것입니다.

**기능:**
- 📊 실시간 진행률 (5초마다 갱신)
- 🔔 모델 완료 시 **알림음 3회**
- 🎺 전체 완료 시 **팡파르 + Windows 팝업 알림**
- 📝 30초마다 상태 로그 (타임아웃 방지)
