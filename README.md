# 3J Labs ACF CSS Plugin Family

프로젝트 경로: `C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS`
메인 플러그인 버전: **v23.0.10** (ACF CSS Manager Master)
빌드 매니저 버전: **v23.0.1**
최신 업데이트: 2026년 1월 5일 (Phase 46 완료 - 버전 관리 및 대시보드 연동 개선)

---

## 플러그인 패밀리

### 핵심 플러그인 (Core)

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| **ACF CSS Manager (Master)** | **v23.0.4** | 메인 플러그인 - WordPress 스타일 통합 관리 시스템 + 롤백 기능 완전 구현 |
| ACF MBA (Nudge Flow) | **v22.10.1** | 마케팅 자동화 및 넛지 시스템 (MAB 포함) + 템플릿 탭 수정 |
| ACF Code Snippets Box | **v4.0.0** | CSS/JS/PHP 코드 스니펫 관리 + 프리셋 토글 |
| ACF CSS Neural Link | **v6.3.5** | 패턴 학습 및 업데이트 관리 + 보안 강화 |
| WP Bulk Manager | **v23.1.1** | 플러그인/테마 대량 설치 및 관리 + 마스터 에디션 감지 수정 |

### 확장 플러그인 (Extensions)

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| ACF CSS WooCommerce Toolkit | **v2.4.1** | WooCommerce 스타일 및 기능 확장 |
| ACF CSS AI Extension | **v3.3.1** | AI 기반 스타일 추천 및 생성 |
| ACF CSS Woo License Bridge | **v22.0.6** | WooCommerce 라이센스 브릿지 |
| Admin Menu Editor Pro | **v2.0.2** | 관리자 메뉴 커스터마이저 |

### 신규 플러그인 (New)

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| **ACF User Journey Analytics** | **v1.0.1** | **무료** 트래픽 분석 대시보드 + 캐싱 시스템 + 보안 강화 |
| JJ Analytics Dashboard | **v1.0.1** | 전체 플러그인 통합 분석 대시보드 |
| JJ Marketing Automation Dashboard | **v1.0.2** | 종합 마케팅 자동화 대시보드 |

### SEO 플러그인 (개발 중)

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| WP 1-Click SEO Pro | **v0.1.0** | 원클릭 SEO 최적화 (개발 중) |
| WP Bulk SEO AEO | **v0.1.0** | 대량 SEO/AEO 관리 (개발 중) |
| 3J SEO Algorithm | - | 고급 SEO 알고리즘 엔진 (기획 중) |

---

## 빌드 매니저 (v23.0.1)

### 주요 기능
- ✅ 플러그인 빌드 및 ZIP 패키징
- ✅ 버전 관리 (자동/수동 모드)
- ✅ 대시보드 자동 업데이트
- ✅ 이전 버전 자동 아카이브
- ✅ 서명 생성 및 검증

### GUI 모드
```bash
python 3j_build_manager.py
```

**기능**:
- 📦 플러그인 목록 및 상태 확인
- 🚀 빌드 실행 (전체/선택)
- 📊 버전 관리 (자동/수동 모드)
- ⚙️ 설정 관리
- 📈 빌드 히스토리

### CLI 모드
```bash
# 모든 플러그인 빌드 (Master 에디션)
python 3j_build_manager.py --cli --all

# 특정 플러그인만 빌드
python 3j_build_manager.py --cli --plugins acf-css-manager wp-bulk-manager

# 특정 에디션으로 빌드
python 3j_build_manager.py --cli --all --editions free premium master
```

### 버전 관리 기능

**자동 모드**:
- 버전을 0.1씩 자동 증가 (마지막 숫자에 1 추가)
- 예: `23.0.10` → `23.0.11`
- 전체 플러그인 일괄 업데이트 지원

**수동 모드**:
- 현재 버전 표시
- 새 버전 직접 입력
- 버전 내리기 방지 (현재 버전보다 높은 버전만 허용)
- 버전 형식 검증

**사용 방법**:
1. 빌드 매니저 실행
2. "📦 버전 관리" 탭 클릭
3. 자동/수동 모드 선택
4. 플러그인 선택 후 버전 업데이트

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
├── memory & context/             # 52개 메모리 파일
│
├── acf-css-really-simple-style-management-center-master/  # 메인 플러그인
├── acf-nudge-flow/               # 마케팅 자동화
├── acf-code-snippets-box/        # 코드 스니펫
├── acf-css-neural-link/          # 업데이트 관리
├── wp-bulk-manager/              # 대량 설치
├── acf-user-journey-analytics/   # 트래픽 분석 (신규, 무료)
│
├── SEO/                          # SEO 플러그인 (개발 중)
│   ├── oneclick-seo-pro/
│   ├── wp-bulk-seo-aeo/
│   └── 3j-seo-algorithm/
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

### Phase 46 (2026-01-05) - 버전 관리 및 대시보드 연동 개선

**주요 개선 사항**:
- ✅ 버전 추출 함수 개선 (플러그인 헤더 + 상수 정의 모두 확인)
- ✅ 빌드 완료 시 자동 대시보드 업데이트 (GUI/CLI 모드 모두 지원)
- ✅ 대시보드 업데이트 로직 개선 (plugin-fullname 기반 정확한 매칭)
- ✅ Windows 콘솔 인코딩 문제 해결 (UTF-8 설정)
- ✅ 버전 관리 기능 대폭 개선 (자동/수동 모드, 버전 내리기 방지)
- ✅ 모든 플러그인 버전 정보와 대시보드 완벽 동기화

**상세 내용**: `PHASE_45_VERSION_SYNC_COMPLETE.md`, `VERSION_MANAGEMENT_FEATURE.md`, `memory & context/20260105-Phase-46-버전-관리-및-대시보드-연동-개선.md` 참조

### Phase 45 (2026-01-05) - 플러그인 활성화 문제 해결

**문제**: 스타일 센터, 벌크 매니저, 코드 박스, 라이센스 관리 말고는 그 어떠한 플러그인도 활성화가 되지 않음

**근본 원인**:
- 업데이트 보안 모듈(`class-jj-update-security-shared.php`)이 모든 플러그인 업로드를 차단
- 로컬 파일 업로드 및 WordPress.org 플러그인 차단
- "허가되지 않은 업데이트 소스입니다." 오류 발생

**해결책**:
- 업데이트 보안 모듈 v25.0.1로 업데이트
- 로컬 파일 업로드 허용 (ZIP 파일 직접 업로드)
- WordPress.org 플러그인 허용
- 3J Labs 플러그인만 엄격하게 검증, 다른 플러그인은 기본적으로 허용
- WP Bulk SEO AEO 및 ACF User Journey Analytics에 보안 모듈 추가

**수정된 파일**:
- `shared-ui-assets/class-jj-update-security-shared.php` (v25.0.0 → v25.0.1)
- `acf-css-really-simple-style-management-center-master/includes/class-jj-update-security-v25.php` (v25.0.0 → v25.0.1)
- `SEO/wp-bulk-seo-aeo/wp-bulk-seo-aeo.php` (보안 모듈 추가)
- `acf-user-journey-analytics/acf-user-journey-analytics.php` (보안 모듈 추가)

**결과**:
- ✅ 모든 플러그인 정상 활성화 가능
- ✅ 로컬 파일 업로드 가능
- ✅ WordPress.org 플러그인 업데이트 가능
- ✅ 3J Labs 플러그인 보안 검증 유지

### Phase 44 (2026-01-05) - WP Bulk Manager 에디션 감지 수정 및 대시보드 업그레이드

**WP Bulk Manager v23.1.1 (Critical Bug Fix)**
- 마스터 에디션 감지 실패 수정 (WP_BULK_MANAGER_EDITION 상수 추가)
- "Premium 이상 기능" 제한 메시지 표시 버그 해결
- 에디션 감지 로직 우선순위 재정립

**HTML 대시보드 v25.0.1**
- 완전 재작성 (모던 UI/UX)
- 14개 플러그인 최신 버전 반영
- 글래스모피즘, 그라데이션 애니메이션
- Quick Stats 섹션, 필터 탭 추가
- HMAC-SHA256 보안 정보 표시

**빌드 매니저 v23.0.0**
- 버전 표시 업데이트 (v22.4.0 → v23.0.0)
- Phase 44 변경사항 반영

### Phase 43 (2026-01-05) - 완전한 프로젝트 전수 검사 및 롤백 시스템 완성

**전체 프로젝트 전수 검사**
- ✅ 모든 PHP 파일 문법 검사 (146개 파일, 오류 0개)
- ✅ JavaScript 코드 검사 및 이스케이프 처리
- ✅ 기능 오류 검사 및 수정 (4개 발견 및 수정)
- ✅ 버전 불일치 수정
- ✅ 상세한 문서 작성 (README, 릴리즈 노트, 개발자 가이드, 메모리)

**ACF CSS Manager v23.0.4**
- 롤백 시스템 완전 구현 완료
- 버전 불일치 수정 (25.0.1 → 23.0.3)
- JavaScript 변수 이스케이프 처리 완료
- 모든 플러그인에 롤백 기능 자동 적용

**ACF Nudge Flow v22.10.1**
- 템플릿 센터 탭 전환 문법 오류 수정

**빌드 매니저 v23.0.0**
- Phase 43 반영
- 완전한 롤백 시스템 지원

### Phase 42.2 (2026-01-05) - 보안, 성능, 롤백 완성

**ACF CSS Manager v23.0.3**
- 플러그인 롤백 기능 완전 구현 (+270줄)
- WordPress Core Plugin_Upgrader 활용
- 3J Labs 및 WordPress.org 플러그인 지원
- JavaScript $ 변수 이스케이프 수정

**ACF User Journey Analytics v1.0.1**
- AJAX nonce 검증 추가 (보안 강화)
- Transient 캐싱 시스템 도입 (1분~1시간 TTL)
- 캐시 무효화 메서드 추가

**ACF Nudge Flow v22.10.1**
- 템플릿 센터 탭 전환 문법 오류 수정

**Shared Rollback Module**
- class-jj-rollback-shared.php 클래스 구조 수정

### Phase 42.1 (2026-01-05) - 1-Click SEO Pro v2.0.0 개발

**1-Click SEO Pro v2.0.0**
- v25 디자인 시스템 통합
- 공유 보안 모듈 연동
- 1-Click SEO 설정 버튼
- 크로스 프로모션 배너

### Phase 42 (2026-01-05) - User Journey Analytics 분리

**ACF User Journey Analytics v1.0.0 (신규, 무료)**
- 50+ 광고 플랫폼 자동 감지 (Google, Meta, Naver, Kakao, TikTok 등)
- AI 검색엔진 트래픽 추적 (ChatGPT, Perplexity, Claude)
- 5단계 전환 퍼널 분석
- ROI/ROAS/CPA 계산
- Chart.js 기반 실시간 시각화
- CSV 내보내기 (UTF-8 BOM)

**ACF Nudge Flow v22.10.0**
- 트래픽 분석 기능을 User Journey Analytics로 분리
- "트래픽 분석" 메뉴를 "트래픽 연동"으로 변경
- User Journey Analytics 연동 인터페이스 구현

### Phase 41 (2026-01-03) - 플러그인 목록 UI/UX 개선

- JJ_Global_Plugin_List_Enhancer 클래스 구현
- 모든 플러그인에 자동 업데이트 버튼 추가
- 플러그인별 맞춤 기능 링크

### Phase 40.1 (2026-01-04) - Code Snippets Box & Nudge Flow 개선

- 프리셋 토글 기능 추가
- RealDeal WooCommerce 프리셋 7개 추가
- 워크플로우 빌더 드래그 앤 드롭 구현

### 보안 강화 (Phase 39.3)

- WP Bulk Manager AJAX 핸들러 보안 강화
- License Tampering Detection
- Update Hijacking 방지
- 비정상적인 사용 패턴 감지

### 공유 보안 모듈 (shared-ui-assets/)

- `class-jj-security-module-v25.php`: 통합 보안 모듈
- `class-jj-license-security-shared.php`: 라이센스 보안
- `class-jj-update-security-shared.php`: 업데이트 보안
- `class-jj-file-integrity-shared.php`: 파일 무결성

---

*작성일: 2026-01-05*
*작성자: 3J Labs (제이x제니x제이슨 연구소)*
