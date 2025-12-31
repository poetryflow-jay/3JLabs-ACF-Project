# 2025-12-31 스냅샷 커밋 노트 (Stability/Updates 라운드 착수 전)

## 목적
- 다음 라운드(안정성 회귀 방어막 강화 + Updates 탭 1테이블 통합 UX) 착수 전에,
  현재까지의 기능/UX 개선 사항을 **재현 가능한 형태로 스냅샷 커밋**해 둡니다.

## 포함된 주요 변경(요약)
- **Core(Master) 추천 팔레트/AI 프리셋 UX**
  - AI 프리셋 **대량 선택 모드** + 보이는 항목 전체 선택/반전
  - 선택 **일괄 고정/해제/삭제/내보내기**
  - **공유 코드(JJAI1)** 생성/복사 + 코드 붙여넣기 가져오기(미리보기/확인/안전 제한)
  - 모달 **ESC 닫기**
  - 서버 bulk-op 결과 메시지 정확도 개선(적용/이미 상태 동일/누락)

- **AI Extension(WebGPU 스트리밍) 복구 UX**
  - 스트리밍 패널에 **재시도 / JSON 재파싱** 버튼 추가
  - AI Extension 버전: **2.0.5**

## 빌드/배포 실행(참고)
- 로컬 배포 시스템:
  - `python jj_deployment_system.py`
- 생성 대상(예):
  - `C:\Users\computer\Desktop\JJ_Distributions_v8.0.0_Master_Control\dashboard.html`
  - `ACF-CSS-AI-Extension-v2.0.5.zip` 포함

## 주의(저장소 관리)
- 이 저장소에는 대용량/바이너리(모델, zip, exe 등)를 커밋하지 않습니다.  
  `.gitignore`에 의해 제외됩니다.

