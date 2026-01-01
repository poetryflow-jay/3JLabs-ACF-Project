---

**User**

어쩌면 가상 공간을 만들어 설치해야 할지도... 우선 요청한대로 새로 토큰을 준비할 테니, 그 동안 내가 말한 방법을 사용해봐. 터미널 경로의 문제일 수도 있으니.


---

**Cursor**

알겠습니다 사장님! 가상 환경 경로 문제일 수 있습니다. 다른 방식으로 시도해보겠습니다.

가상환경 활성화됨! 이제 토큰을 직접 전달하는 스크립트를 만들어서 실행해보겠습니다.

```python
"""
Gated 모델 다운로드 스크립트
토큰을 직접 전달하여 다운로드
"""
import os
import sys

# 토큰을 환경변수로 설정
TOKEN = "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"
os.environ["HF_TOKEN"] = TOKEN
os.environ["HUGGING_FACE_HUB_TOKEN"] = TOKEN

from huggingface_hub import snapshot_download, login

print("=" * 60)
print("Gated 모델 다운로드 스크립트")
print("=" * 60)

# 명시적 로그인
print("\n[1/4] 토큰으로 로그인 중...")
try:
    login(token=TOKEN, add_to_git_credential=False)
    print("✓ 로그인 성공!")
except Exception as e:
    print(f"✗ 로그인 실패: {e}")

# 다운로드할 모델 목록
MODELS = [
    ("google/gemma-3-4b-it", "gemma-3-4b-it"),
    ("black-forest-labs/FLUX.1-schnell", "FLUX.1-schnell"),
    ("meta-llama/Llama-3.1-8B-Instruct", "Llama-3.1-8B-Instruct"),
]

for i, (repo_id, local_name) in enumerate(MODELS, 2):
    print(f"\n[{i}/4] {repo_id} 다운로드 시도 중...")
    local_dir = f"./models/{local_name}"
    
    try:
        snapshot_download(
            repo_id=repo_id,
            local_dir=local_dir,
            local_dir_use_symlinks=False,
            resume_download=True,
            token=TOKEN,  # 토큰 직접 전달
        )
        print(f"✓ {local_name} 다운로드 완료!")
    except Exception as e:
        print(f"✗ {local_name} 실패: {type(e).__name__}")
        print(f"  오류 내용: {str(e)[:200]}")
        continue

print("\n" + "=" * 60)
print("다운로드 작업 완료!")
print("=" * 60)

```

### 📊 결과 분석

| 모델 | 오류 유형 | 원인 |
|------|----------|------|
| Gemma 3 | `LocalEntryNotFoundError` | 네트워크/연결 문제 또는 라이선스 미동의 |
| FLUX.1 | `GatedRepoError 403` | **라이선스 미동의** 또는 토큰 권한 부족 |
| Llama 3.1 | `GatedRepoError 403` | **라이선스 미동의** 또는 토큰 권한 부족 |
