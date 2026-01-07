# 3J Labs ACF CSS Plugin Family

프로젝트 경로: `C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS`
메인 플러그인 버전: **v26.0.10** (ACF CSS Manager Master)
빌드 매니저 버전: **v23.0.1**
최신 업데이트: 2026년 1월 7일 (Phase 51 완료 - Visual Command Center 섹션 전환 핫픽스)

---

## 플러그인 패밀리

### 핵심 플러그인 (Core)

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| **ACF CSS Manager (Master)** | **v25.3.0** | 메인 플러그인 - WordPress 스타일 통합 관리 시스템 + 다크모드 프리셋 |
| ACF MBA (Nudge Flow) | **v23.0.1** | 마케팅 자동화 및 넛지 시스템 + 드래그 앤 드롭 워크플로우 빌더 |
| ACF Code Snippets Box | **v5.1.0** | CSS/JS/PHP 코드 스니펫 관리 + **고급 버전 관리** (태깅/브랜치/스냅샷) |
| ACF CSS Neural Link | **v8.2.0** | 패턴 학습 및 업데이트 관리 + 스마트 캐싱 시스템 + 멀티사이트 허브 |
| WP Bulk Manager | **v23.4.0** | 플러그인/테마 대량 설치 및 관리 + 드래그 정렬 + 재시도 버튼 |

### 확장 플러그인 (Extensions)

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| ACF CSS WooCommerce Toolkit | **v2.5.0** | WooCommerce 스타일 및 기능 확장 + **동적 가격 표시** |
| ACF CSS AI Extension | **v3.4.0** | AI 기반 스타일 추천 및 생성 + **AI 컬러 팔레트 추천** |
| ACF CSS Woo License Bridge | **v23.0.2** | WooCommerce 라이센스 브릿지 |
| Admin Menu Editor Pro | **v2.0.4** | 관리자 메뉴 커스터마이저 |

### 분석 & 마케팅 플러그인 (Analytics & Marketing)

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| **ACF User Journey Analytics** | **v1.0.3** | **무료** 트래픽 분석 대시보드 + 캐싱 시스템 |
| JJ Analytics Dashboard | **v1.2.0** | 전체 플러그인 통합 분석 대시보드 + **Command Center** |
| JJ Marketing Automation Dashboard | **v2.0.2** | 종합 마케팅 자동화 대시보드 |
| ACF Mail SMTP | **v2.3.0** | 메일 SMTP + Gmail API + **비주얼 이메일 템플릿 빌더** |

### SEO 플러그인

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| WP 1-Click SEO Pro | **v2.1.0** | 원클릭 SEO 최적화 |
| WP Bulk SEO AEO | **v2.1.0** | 대량 SEO/AEO 관리 |

---

## Phase 49 신규 기능 (2026-01-06)

### P49-1: ACF CSS AI Extension - AI 컬러 팔레트 추천 (v3.4.0)
- AI 기반 브랜드 컬러 분석 및 자동 팔레트 생성
- 60-30-10 색상 비율 자동 적용
- 접근성 대비율 검사 (WCAG AA/AAA)
- 무드보드 스타일 추천 (모던/클래식/미니멀/대담한)
- 산업별 최적화 팔레트 제안

### P49-2: ACF Mail SMTP - 비주얼 이메일 템플릿 빌더 (v2.3.0)
- 드래그 앤 드롭 템플릿 빌더
- 12+ 블록 타입 (텍스트, 이미지, 버튼, 소셜, 등)
- 실시간 미리보기
- 반응형 이메일 출력
- 템플릿 저장 및 복제

### P49-3: JJ Analytics Dashboard - 실시간 대시보드 위젯 (v1.1.0)
- 실시간 데이터 모니터링 (AJAX 폴링)
- 새로고침 간격 설정 (5초~5분)
- 자동 갱신 타이머 표시
- 실시간 플러그인 상태 추적
- 최근 활동 피드

### P49-4: ACF CSS WooCommerce Toolkit - 동적 가격 표시 (v2.5.0)
- 세일 카운트다운 타이머
- 재고 긴급도 표시
- 대량 구매 가격표
- 가격 히스토리 추적
- 사용자별 맞춤 가격
- REST API 지원

### P49-5: ACF Code Snippets Box - 고급 버전 관리 (v5.1.0)
- 버전 태깅 시스템 (컬러 태그)
- 브랜치 생성 및 병합
- 스냅샷 (수동 백업)
- 자동 백업 스케줄링
- 버전 통계 분석
- JSON 내보내기/가져오기

---

## 빌드 매니저 (v23.0.1)

### 주요 기능
- 플러그인 빌드 및 ZIP 패키징
- 버전 관리 (자동/수동 모드)
- 대시보드 자동 업데이트
- 이전 버전 자동 아카이브
- 서명 생성 및 검증

### GUI 모드
```bash
python 3j_build_manager.py
```

### CLI 모드
```bash
# 모든 플러그인 빌드 (Master 에디션)
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

## 폴더 구조

```
3J-ACF-CSS/
├── 3j_build_manager.py          # 메인 빌드 매니저 (GUI/CLI)
├── dev_scripts/                  # 개발용 스크립트
├── dist/                         # 빌드된 ZIP 파일
├── shared-ui-assets/             # 공유 보안/UI 모듈
├── memory & context/             # 메모리 파일
│
├── acf-css-really-simple-style-management-center-master/  # 메인 플러그인
├── acf-nudge-flow/               # 마케팅 자동화
├── acf-code-snippets-box/        # 코드 스니펫
├── acf-css-neural-link/          # 업데이트 관리
├── wp-bulk-manager/              # 대량 설치
├── acf-user-journey-analytics/   # 트래픽 분석 (무료)
├── jj-analytics-dashboard/       # 분석 대시보드
├── acf-mail-smtp/                # 메일 SMTP
│
├── SEO/                          # SEO 플러그인
│   ├── oneclick-seo-pro/
│   └── wp-bulk-seo-aeo/
│
└── docs/                         # 문서
```

---

## 작업 원칙 (Development Principles)

### 필수 원칙

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

## 관련 문서

- [개발자 가이드](DEVELOPER_GUIDE.md)
- [사용자 가이드](USER_GUIDE.md)
- [릴리즈 노트](RELEASE_NOTES.md)
- [빌드 시스템](BUILD_SYSTEM.md)

---

## 최근 변경사항

### Hotfix (2026-01-06) - Fatal Error 수정

**긴급 수정**:
- **ACF Nudge Flow v23.0.1**: Data Source Integration 누락 메서드 추가
  - `get_user_purchase_data()` 메서드 추가 (구매 데이터 조회)
  - `get_user_membership_data()` 메서드 추가 (멤버십 데이터 조회)
  - `get_analytics_data()` 메서드 추가 (분석 데이터 조회)
  - 이 수정으로 `class-visitor-tracker.php:90` Fatal Error 해결

**버전 동기화**:
- Neural Link: 헤더 8.1.0 → 8.2.0 동기화
- JJ Analytics: 헤더 1.1.0 → 1.2.0 동기화

### Phase 49 (2026-01-06) - AI/자동화/분석 기능 대폭 강화

**주요 신규 기능**:
- **AI 컬러 팔레트 추천**: 브랜드 컬러 기반 자동 팔레트 생성 (ACF CSS AI Extension v3.4.0)
- **비주얼 이메일 템플릿 빌더**: 드래그 앤 드롭 템플릿 편집기 (ACF Mail SMTP v2.3.0)
- **실시간 대시보드 위젯**: AJAX 폴링 기반 실시간 모니터링 (JJ Analytics v1.1.0)
- **동적 가격 표시**: 카운트다운, 긴급도, 대량구매 가격 (WooCommerce Toolkit v2.5.0)
- **고급 버전 관리**: 태깅, 브랜치, 스냅샷 기능 (Code Snippets Box v5.1.0)

### Phase 48 (2026-01-06) - 4개 플러그인 기능 추가

- Neural Link: 라이센스 스마트 캐싱 + 캐시 대시보드 (v8.1.0)
- ACF CSS Master: 다크모드 프리셋 선택기 6종 (v25.3.0)
- WP Bulk Manager: 드래그 정렬 + 설치 실패 재시도 (v23.4.0)
- ACF Mail SMTP: 이메일 로그 테이블 개선 (v2.2.0 → v2.3.0)

### Phase 47 (2026-01-06) - 스타일 센터 탭 시스템 완전 수정

- 탭 클릭 시 콘텐츠가 표시되지 않던 버그 수정
- JavaScript 셀렉터 `data-section` 속성 기반으로 변경

---

*작성일: 2026-01-06*
*작성자: 3J Labs (제이x제니x제이슨 연구소)*
