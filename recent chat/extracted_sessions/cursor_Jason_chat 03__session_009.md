---

**User**

타임아웃을 막기 위해 종종 말을 걸어 주긴 해야 하지 않을까? 자네가 요청한 라이센스에 동의 처리했네. 아니면 먼저번처럼 파이썬 창을 띄워서 다운로드 진행율을 %로 표시하고, 남은 시간을 예측하여 예상 시간을 표시해주면 좋을 텐데.


---

**Cursor**

좋은 아이디어입니다 사장님! 라이선스 동의 감사합니다! 

실시간 진행률 모니터링 창을 만들어 띄우겠습니다!

```python
"""
실시간 다운로드 진행률 모니터
5초마다 갱신되며, 완료 시 자동 알림
"""
import os
import time
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
    if seconds < 60:
        return f"{int(seconds)}초"
    elif seconds < 3600:
        return f"{int(seconds//60)}분 {int(seconds%60)}초"
    else:
        hours = int(seconds // 3600)
        mins = int((seconds % 3600) // 60)
        return f"{hours}시간 {mins}분"

def main():
    print("=" * 70)
    print("  🚀 AI 모델 다운로드 실시간 모니터")
    print("  Ctrl+C로 종료 (다운로드는 계속 진행됨)")
    print("=" * 70)
    
    # 시작 시간과 크기 기록
    start_time = time.time()
    start_sizes = {}
    
    for model_name in EXPECTED_SIZES.keys():
        model_path = os.path.join(MODELS_DIR, model_name)
        if os.path.exists(model_path):
            start_sizes[model_name] = get_folder_size_gb(model_path)
    
    prev_sizes = start_sizes.copy()
    
    try:
        while True:
            os.system('cls' if os.name == 'nt' else 'clear')
            
            now = datetime.now().strftime("%H:%M:%S")
            elapsed = time.time() - start_time
            
            print("=" * 70)
            print(f"  🚀 AI 모델 다운로드 실시간 모니터 | 현재 시각: {now}")
            print("=" * 70)
            print()
            
            downloading = []
            completed = []
            
            for model_name, expected_gb in EXPECTED_SIZES.items():
                model_path = os.path.join(MODELS_DIR, model_name)
                
                if not os.path.exists(model_path):
                    continue
                
                current_gb = get_folder_size_gb(model_path)
                percent = min(100, (current_gb / expected_gb) * 100)
                
                # 속도 계산 (최근 5초 기준)
                prev_gb = prev_sizes.get(model_name, 0)
                speed_mbps = (current_gb - prev_gb) * 1024 / 5  # MB/s
                
                # 남은 시간 예측
                remaining_gb = max(0, expected_gb - current_gb)
                if speed_mbps > 0.01:
                    eta_seconds = (remaining_gb * 1024) / speed_mbps
                    eta_str = format_time(eta_seconds)
                else:
                    eta_str = "계산 중..."
                
                # 상태 표시
                if percent >= 99:
                    status = "✅ 완료"
                    completed.append(model_name)
                elif speed_mbps > 0.01:
                    status = "🔄 다운로드 중"
                    downloading.append(model_name)
                elif current_gb > 0.1:
                    status = "⏸️ 대기/완료"
                    completed.append(model_name)
                else:
                    status = "⏳ 시작 전"
                
                # 프로그레스 바
                bar_width = 30
                filled = int(bar_width * percent / 100)
                bar = "█" * filled + "░" * (bar_width - filled)
                
                print(f"  {model_name[:28]:<28}")
                print(f"    [{bar}] {percent:5.1f}%")
                print(f"    {current_gb:.2f} / {expected_gb:.1f} GB | {status} | 남은 시간: {eta_str}")
                if speed_mbps > 0.01:
                    print(f"    속도: {speed_mbps:.1f} MB/s")
                print()
                
                prev_sizes[model_name] = current_gb
            
            print("-" * 70)
            print(f"  📊 다운로드 중: {len(downloading)}개 | 완료: {len(completed)}개")
            print(f"  ⏱️ 경과 시간: {format_time(elapsed)}")
            print("-" * 70)
            print("  5초마다 자동 갱신 | Ctrl+C로 종료")
            
            time.sleep(5)
            
    except KeyboardInterrupt:
        print("\n\n모니터 종료. 다운로드는 백그라운드에서 계속 진행됩니다.")

if __name__ == "__main__":
    main()

```

모니터링 스크립트 생성 완료! 이제 별도 창으로 실행하고, Llama 3.1도 다시 시도합니다!

### ✅ 완료!

**화면에 2개 창이 떠야 합니다:**

1. **📊 다운로드 모니터 창** - 실시간 진행률, %, 남은 시간 표시
2. **Llama 3.1 다운로드 창** (최소화됨) - 백그라운드 다운로드
