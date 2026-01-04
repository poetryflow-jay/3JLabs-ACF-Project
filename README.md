# 3J Labs ACF CSS Plugin Family

프로젝트 경로: `C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS`
메인 플러그인 버전: **v22.4.7** (ACF CSS Manager Master)
최신 업데이트: 2026년 1월 4일 (Phase 39.2 완료)

---

## 📦 플러그인 패밀리

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| **ACF CSS Manager (Master)** | **v22.4.7** | 메인 플러그인 - WordPress 스타일 통합 관리 시스템 + UI System 2026 |
| WP Bulk Manager | **v22.4.9** | 플러그인/테마 대량 설치 및 관리 + 보안 강화 |
| ACF Code Snippets Box | **v2.3.1** | CSS/JS/PHP 코드 스니펫 관리 + 링크 수정 |
| ACF CSS Neural Link | **v6.3.2** | 패턴 학습 및 업데이트 관리 + 보안 강화 |
| ACF CSS WooCommerce Toolkit | **v2.4.0** | WooCommerce 스타일 및 기능 확장 + 보안 강화 |
| ACF CSS AI Extension | **v3.3.0** | AI 기반 스타일 추천 및 생성 + 보안 강화 |
| ACF MBA (Nudge Flow) | **v22.4.0** | 마케팅 자동화 및 넛지 시스템 (MAB 포함) + 보안 강화 |
| Admin Menu Editor Pro | **v2.0.1** | 관리자 메뉴 커스터마이저 |
| ACF CSS Woo License Bridge | **v22.0.1** | WooCommerce 라이센스 브릿지 |
| JJ Analytics Dashboard | **v1.0.0** | 전체 플러그인 통합 분석 대시보드 (신규) |

---

## 🚀 빌드 매니저

### GUI 모드
```bash
python 3j_build_manager.py
```

### CLI 모드
```bash
# 모든 플러그인 빌드 (Master + Partner 에디션)
python 3j_build_manager.py --cli --all

# 특정 플러그인만 빌드
python 3j_build_manager.py --cli --plugins acf-css-manager wp-bulk-manager

# 특정 에디션으로 빌드
python 3j_build_manager.py --cli --all --editions free premium master
```

### 에디션
- **Free**: 무료 버전
- **Basic**: 기본 기능
- **Premium**: 프리미엄 기능
- **Unlimited**: 무제한 라이센스
- **Partner**: 파트너/리셀러용
- **Master**: 올인원 전체 기능 (개발자용)

---

## 📂 폴더 구조

```
3J-ACF-CSS/
├── 3j_build_manager.py          # 메인 빌드 매니저 (GUI/CLI)
├── dev_scripts/                  # 개발용 스크립트
│   ├── deprecated/              # 더 이상 사용하지 않는 스크립트
│   ├── i18n/                    # 번역 관련 스크립트
│   ├── fixes/                   # 일회성 수정 스크립트
│   ├── testing/                 # 테스트 스크립트
│   └── deployment/              # 배포 관련 스크립트
├── dist/                         # 빌드된 ZIP 파일
│   └── old/                     # 이전 버전 아카이브
├── acf-css-really-simple-style-management-center-master/  # 메인 플러그인
├── wp-bulk-manager/
├── acf-code-snippets-box/
├── acf-css-neural-link/
├── acf-css-woocommerce-toolkit/
├── acf-css-ai-extension/
├── acf-nudge-flow/
└── admin-menu-editor-pro/
```

---

## 🔧 작업 원칙 (Development Principles)

### 터미널 및 빌드 작업 원칙

1. **Python REPL 상태 감지 및 대응**
   - 터미널 프롬프트가 `>>>`로 표시되면 Python REPL 상태입니다.
   - **해결 방법**: `exit()` 명령으로 Python을 종료한 후 정상 셸로 복구합니다.

2. **타임아웃 및 재시도 전략**
   - 터미널 작업이 40초 이상 응답이 없으면 다른 방법으로 재시도합니다.

3. **ZIP 빌드 주의사항**
   - WordPress 플러그인 ZIP 파일은 **플러그인 폴더가 포함**되어야 합니다.
   - ✅ 올바른 방법: ZIP 안에 폴더가 포함됨

4. **dist 폴더 자동 관리**
   - 빌드 완료 시 자동으로 구 버전 파일을 `dist/old/` 폴더로 이동
   - 최신 버전만 `dist/` 폴더에 유지

### 필수 원칙 (메모리 기반)

1. **문법/참조 오류 방지**
   - 모든 PHP 클래스/함수 호출 전 `class_exists()`, `function_exists()` 검증
   - static 클래스에는 `instance()` 호출 금지

2. **파일명/경로 검증**
   - 파일 수정 전 `file_exists()` 확인
   - 상대 경로 대신 `plugin_dir_path()` 상수 활용

3. **버전 관리**
   - 헤더 `Version:`과 `define()` 상수 동시 업데이트
   - CHANGELOG.md 동기화

4. **마스터 버전 원칙**
   - 마스터는 모든 기능 통합 올인원
   - 에디션/요금제 구분 없음

---

## 🔗 관련 문서

- [개발자 가이드](DEVELOPER_GUIDE.md)
- [사용자 가이드](USER_GUIDE.md)
- [릴리즈 노트](RELEASE_NOTES.md)
- [빌드 시스템](BUILD_SYSTEM.md)

---

## 🆕 최근 변경사항 (Phase 39.3)

### 보안 강화 (v22.4.9 / v6.3.2)
- WP Bulk Manager AJAX 핸들러 8개에 JJ_Ajax_Helper 통합
- License Tampering Detection: 라이센스 변조 감지 기능
- Update Hijacking 방지: 도메인 화이트리스트, HTTPS 강제, 패키지 서명 검증
- 비정상적인 사용 패턴 감지 (다수 IP/라이센스 동시 사용 등)

### 코드 품질 개선 (Phase 39.2)
- 깨진 링크 수정: `jj-style-center` → `jj-style-guide-cockpit`
- 깨진 링크 수정: `options-general.php?page=jj-labs-center` → `admin.php?page=jj-labs-center`
- WP Bulk Manager 플러그인 목록 링크 정렬 개선 (인라인 스타일 및 이모지 제거)

### 공통 유틸리티 추가 (shared-ui-assets/php/)
- `JJ_Ajax_Helper`: AJAX 보안 검증 통합 클래스
- `JJ_File_Validator`: 파일 업로드 검증 유틸리티
- `JJ_Singleton_Trait`: 싱글톤 패턴 트레이트
- `JJ_Shared_Loader`: 공통 유틸리티 로더

### 신규 프로젝트
- JJ Analytics Dashboard v1.0.0: 전체 플러그인 통합 분석 대시보드

---

*작성일: 2026-01-04*
*작성자: Jason (CTO, 3J Labs)*
