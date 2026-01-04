# 3J Labs ACF CSS Plugin Family

프로젝트 경로: `C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS`
메인 플러그인 버전: **v23.0.3** (ACF CSS Manager Master)
최신 업데이트: 2026년 1월 5일 (Phase 42.2 완료)

---

## 플러그인 패밀리

### 핵심 플러그인 (Core)

| 플러그인 | 버전 | 설명 |
|----------|------|------|
| **ACF CSS Manager (Master)** | **v23.0.3** | 메인 플러그인 - WordPress 스타일 통합 관리 시스템 + 롤백 기능 완성 |
| ACF MBA (Nudge Flow) | **v22.10.1** | 마케팅 자동화 및 넛지 시스템 (MAB 포함) + 템플릿 탭 수정 |
| ACF Code Snippets Box | **v2.3.4** | CSS/JS/PHP 코드 스니펫 관리 + 프리셋 토글 |
| ACF CSS Neural Link | **v6.3.5** | 패턴 학습 및 업데이트 관리 + 보안 강화 |
| WP Bulk Manager | **v22.5.2** | 플러그인/테마 대량 설치 및 관리 |

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

## 빌드 매니저

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
