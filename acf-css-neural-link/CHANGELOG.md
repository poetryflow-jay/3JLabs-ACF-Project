# JJ License & Update Manager - Changelog

## Version 3.1.0 (2025-12-18) - Neural Link Platform Sync

### [IMPROVEMENT] 배포 버전 동기화
- 플러그인 헤더 버전 및 `JJ_NEURAL_LINK_VERSION` 상수 버전을 최신 배포 버전과 일치시킴

### [FEATURE] OTA 업데이트/버전 관리 기반 정리
- Neural Link 서버 API / Cloud API / Version Manager 기반의 OTA 업데이트 파이프라인을 위한 기반 코드가 포함됨

---

## Version 2.1.4 (2025-01-22) - Pro 버전 원격 활성화 시스템 지원

### [FEATURE] Pro 버전 원격 활성화 API 지원
- **원격 활성화 엔드포인트 추가**:
  - `/api/pro-activate`: 라이센스 키로 활성화 코드 발급
  - `/api/pro-activate?action=verify`: 활성화 코드 유효성 검증
  - `/api/pro-activate?action=deactivate`: 활성화 코드 비활성화
- **활성화 코드 시스템**:
  - 라이센스 키 검증 후 고유한 활성화 코드 발급
  - 사이트별 바인딩으로 무단 사용 방지
  - 만료일 관리 및 자동 비활성화

### [IMPROVEMENT] 플러그인 슬러그 업데이트
- Free 버전: `acf-css-really-simple-style-management-center`
- Pro 버전: `acf-css-really-simple-style-management-center-pro`
- 자동 업데이트 제어 기능 지원

### [MAINTENANCE] 버전 업데이트
- license-manager: 2.1.3 → 2.1.4

---

## Version 2.1.3 (2025-01-22) - 플러그인 폴더명 및 슬러그 업데이트

### [IMPROVEMENT] 플러그인 폴더명 및 슬러그 업데이트
- **새로운 폴더명 구조 반영**:
  - Free 버전 추가 및 슬러그 업데이트
  - 모든 버전의 새로운 폴더명에 맞게 슬러그 업데이트
  - 플러그인 선택 옵션에 모든 버전 포함
- **플러그인 이름 업데이트**:
  - 모든 버전에 "- Free Version", "- Basic Version" 등 명확한 표기 추가
  - Developer Partner 및 Master 버전 이름 업데이트

### [MAINTENANCE] 버전 업데이트
- license-manager: 2.1.2 → 2.1.3

---

## Version 2.1.2 (2025-01-22) - 플러그인 버전별 자동 업데이트 제어 기능 추가

### [FEATURE] 플러그인 버전별 자동 업데이트 제어
- **모든 버전 제어 기능 추가**:
  - basic, premium, unlimited, live, dev 버전 자동 업데이트 제어
  - 각 플러그인 버전별 활성화/비활성화 토글 버튼
  - 실시간 상태 표시 및 업데이트
- **AJAX 기반 토글 시스템**:
  - 클릭 시 즉시 자동 업데이트 상태 변경
  - WordPress `auto_update_plugins` 옵션과 완전 통합
  - 상태 피드백 및 오류 처리

### [IMPROVEMENT] dev 버전과의 호환성 개선
- **동시 활성화 지원**:
  - dev 버전과 라이센스 매니저 플러그인 동시 활성화 가능
  - 활성화 훅 안전성 강화
  - WooCommerce 의존성 체크를 plugins_loaded로 이동

### [IMPROVEMENT] 업데이트 페이지 UI 개선
- 플러그인 버전별 제어 테이블 추가
- 상태 표시 개선 (활성화됨/비활성화됨)
- live 및 dev 버전 옵션 추가

---

## Version 2.1.1 (2025-01-22) - 버전 관리 및 기술 문서 보강

### [MAINTENANCE] 버전 관리 업데이트
- **라이센스 매니저 버전 업데이트**: 2.1.0 → 2.1.1

### [DOCUMENTATION] 기술 문서 보강
- **README.md 보강**:
  - 플러그인 개요 및 주요 기능 상세 설명
  - 설치 및 설정 가이드 보강
  - API 엔드포인트 상세 문서화
  - 보안 가이드 및 모범 사례 추가
  - 개발자 참고 사항 확대
- **CHANGELOG.md 보강**:
  - 모든 버전별 변경 이력 상세 기록
  - 기능별 상세 설명 추가
  - 보안 개선 사항 명확화

### [IMPROVEMENT] 문서화 품질 향상
- 모든 문서에 일관된 형식 적용
- 코드 예제 및 사용 사례 추가
- API 사용 예제 상세화
- 보안 모범 사례 가이드 추가

---

## Version 2.1.0 (2025-01-22) - 원격 제어 및 업데이트 관리 시스템 완전 구현

### [FEATURE] 원격 제어 기능 추가
- **타 사이트 플러그인 원격 제어**:
  - 강제 활성화/비활성화 기능
  - 활성화/비활성화 현황 실시간 모니터링
  - 남은 라이센스 기간 자동 계산 및 관리
  - 서명 기반 보안 검증

### [FEATURE] 로그 수집 및 분석 기능
- **원격 사이트 로그 수집**:
  - 오류 및 문제 자동 수집
  - 로그 분석 및 문제 점검
  - 심각한 오류 자동 알림 (이메일)
  - 사이트별 통계 제공

### [FEATURE] 업데이트 배포 및 공지 기능
- **플러그인 업데이트 배포**:
  - 업데이트 자동 배포
  - 업데이트 채널 관리 (stable, beta, test, dev)
  - 대상 라이센스 선택적 배포
- **공지 전송**:
  - 공지 자동 전송
  - 공지 타입 관리 (info, warning, error, success)
  - 대상 라이센스 선택적 전송

### [IMPROVEMENT] 보안 강화
- 서명 기반 원격 명령 검증 (HMAC SHA256)
- IP 화이트리스트 지원
- Rate limiting 강화
- 모든 WordPress 함수 호출 안전 처리

### [IMPROVEMENT] WordPress 함수 호출 안전성 강화
- 모든 WordPress 함수 호출에 `function_exists()` 확인 추가
- `current_time()`, `home_url()`, `get_option()`, `wp_upload_dir()` 등 안전 호출
- WordPress 로드 전에도 안전하게 동작하도록 폴백 제공

### [NOTE] 사용 대상
- **라이센스/업데이트 매니저는 개발자 전용 플러그인입니다**
- 일반 사용자(free, basic, premium, unlimited 버전 소유자)는 라이센스 매니저를 사용할 필요가 없습니다
- dev 버전 소유자만 라이센스 매니저를 병행 사용합니다

---

## Version 2.0.2 (2025-01-22) - WordPress 함수 호출 안전성 강화

### [IMPROVEMENT] WordPress 함수 호출 안전성 강화
- 모든 WordPress 함수 호출에 `function_exists()` 확인 추가
- `current_time()`, `home_url()`, `get_option()`, `wp_upload_dir()` 등 안전 호출
- WordPress 로드 전에도 안전하게 동작하도록 폴백 제공

---

## Version 2.0.1 (2025-01-22) - 활성화 훅 안전성 강화

### [CRITICAL] 활성화 훅 안전성 강화
- 전체 활성화 함수를 try-catch로 감싸고 상수 정의 확인 추가
- 데이터베이스 테이블 생성 안전 처리
- Cron 작업 스케줄링 안전 처리

