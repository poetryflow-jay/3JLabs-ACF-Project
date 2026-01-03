# 3J Labs ACF CSS 플러그인 패밀리 - 릴리즈 노트

## 릴리즈 개요

**릴리즈 날짜**: 2026년 1월 3일  
**릴리즈 버전**: Phase 32 Deep Innovation & Intelligence (v22.2.0)  
**개발팀**: 3J Labs (제이x제니x제이슨 연구소) - Mikael(Algorithm) + Jason(Implementation) + Jenny(UX)

---

## 📦 플러그인 버전 업데이트 (Master Edition Only)

| 플러그인 | 이전 버전 | 새 버전 | 변경 유형 |
|----------|-----------|---------|-----------|
| ACF CSS Manager (Master) | 22.1.2 | **22.1.4** | AI Optimizer |
| ACF MBA Nudge Flow | 22.1.0 | **22.2.0** | AI/Intelligence |
| ACF CSS WooCommerce Toolkit | 2.1.1 | **2.2.0** | Templates/UX |
| ACF CSS Neural Link | 6.0.1 | **6.1.0** | AI Pattern Learning |
| WP Bulk Manager | 22.1.1 | **22.2.0** | HMAC Security |

---

## 🚀 주요 변경사항 (Phase 32 - Deep Innovation)

### ACF MBA Nudge Flow v22.2.0 (Mikael x Jason x Jenny Edition)
- ✅ **Multi-Armed Bandit (MAB) Auto-Optimization**:
    - Thompson Sampling 알고리즘으로 전환율이 높은 넛지를 자동 학습
    - Beta 분포 기반 확률적 선택으로 탐색(Exploration)과 활용(Exploitation) 균형
    - 실시간 성과 추적 및 자동 최적화
- ✅ **MAB 최적화 엔진** (`class-mab-optimizer.php`):
    - Thompson Sampling 구현 (Beta/Gamma 분포)
    - 넛지별 성공/실패 카운팅
    - 전환율 자동 계산 및 대시보드 제공
- ✅ **관리자 설정**:
    - "MAB 자동 최적화" 토글 추가 (설정 페이지)
    - 활성화 시 성과가 좋은 넛지를 더 자주 노출
- ✅ **프론트엔드 통합**:
    - CTA 클릭 시 자동 전환 추적
    - `acf_nudge_conversion` AJAX 엔드포인트
    - 실시간 MAB 학습 데이터 수집

### ACF CSS WooCommerce Toolkit v2.2.0 (Jenny x Jason Edition)
- ✅ **One-Click Page Style Templates**:
    - 4가지 전문가 디자인 템플릿: Modern Grid, Luxury Single, Minimal Cart, Clean Checkout
    - 제품 목록, 단일 상품, 장바구니, 결제 페이지별 최적화 스타일
    - 호버 애니메이션, 그라데이션, 그림자 효과 포함
- ✅ **Product Page Styler** (`class-product-page-styler.php`):
    - 템플릿 적용/제거 시스템
    - 페이지 타겟별 조건부 스타일 출력
    - 실시간 적용 상태 추적
- ✅ **관리자 UI** (🎨 Page Styler):
    - 카드 기반 템플릿 브라우저
    - 원클릭 적용/제거 버튼
    - 적용 상태 시각적 표시
    - AJAX 기반 즉시 적용

### ACF CSS Neural Link v6.1.0 (Jenny x Jason x Mikael Edition)
- ✅ **AI Pattern Learning System**:
    - 사용자의 CSS 수정 패턴 자동 학습 및 추적
    - 빈도 분석 + 동시 발생 감지 + 순서 패턴 학습
    - 머신러닝 라이트 알고리즘으로 패턴 추출
- ✅ **Pattern Learner** (`class-jj-pattern-learner.php`):
    - 5가지 변경 유형 자동 분류 (색상, 폰트, 여백, 테두리, 버튼)
    - 스타일 수정 히스토리 (최근 100개)
    - 공동 발생 및 순차 패턴 감지
    - 신뢰도 기반 제안 시스템 (High/Medium/Low)
- ✅ **AI Learning Dashboard** (🧠 AI Learning):
    - 학습 통계 실시간 시각화
    - Chart.js 기반 변경 유형 차트
    - AI 추천 사항 표시
    - 학습 데이터 초기화 기능

### WP Bulk Manager v22.2.0 (Mikael x Jason Edition)
- ✅ **HMAC-SHA256 Authentication** (`class-jj-bulk-hmac-auth.php`):
    - 평문 시크릿 키를 암호화 서명으로 교체
    - HMAC-SHA256 기반 요청 서명 및 검증
    - Constant-time 비교로 타이밍 공격 방지
- ✅ **Replay Attack Prevention**:
    - 타임스탬프 검증 (5분 허용 오차)
    - Nonce 재사용 감지 및 차단
    - 자동 nonce 캐시 정리 (최근 100개 유지)
- ✅ **Enhanced Security**:
    - 암호학적으로 안전한 키 생성 (random_bytes)
    - 요청 파라미터 정렬로 일관된 서명 보장
    - 레거시 시크릿 키 방식 폴백 지원

### ACF CSS Manager v22.1.4 (Jason x Mikael Edition)
- ✅ **AI CSS Performance Optimizer** (`class-jj-css-optimizer-ai.php`):
    - CSS 중복 규칙 자동 감지 및 제거
    - 불필요한 속성 탐지
    - 병합 가능한 선택자 추천
    - 자동 압축 및 최적화
- ✅ **Optimization Dashboard** (`view-section-optimizer.php`):
    - 실시간 최적화 통계 (원본/최적화 크기, 압축률)
    - 심각도별 제안 사항 (High/Medium/Low)
    - 원클릭 자동 최적화
    - 최적화 CSS 다운로드
- ✅ **Industry-Specific Design Templates** (v22.1.3):
    - E-commerce Pro, Blog & Magazine, Portfolio Creative, Agency Bold, SaaS Clean
    - Modified: `class-jj-style-presets.php` (총 10개 프리셋)

---

## 🚀 주요 변경사항 (Phase 31.5 - Marketing & Innovation)

### ACF CSS Manager v22.1.2 (Jenny x Jason Edition)
- ✅ **One-Click Demo Importer**: 전문가가 설계한 디자인 시스템을 버튼 하나로 즉시 구축
- ✅ **Design Preset Library**: 5가지 핵심 테마(Modern, Classic, Minimal, Nordic, Cyberpunk) 탑재
- ✅ **Spectrum Color Picker**: 더 세밀하고 아름다운 색상 제어를 위한 업그레이드
- ✅ **Inline Live Preview**: 에디터 내에서 실시간으로 웹사이트 변화를 확인하는 사이드바 프리뷰
- ✅ **System Insights Dashboard**: 색상, 폰트 사용량 및 디자인 일관성 지표 시각화
- ✅ **Auto-Rollback System**: 모든 설정 변경 전 자동 스냅샷 생성 및 원클릭 복구 지원
- ✅ **Onboarding Welcome Modal**: 첫 사용자를 위한 3단계 안내 가이드 및 추천 설정 퀵 스타트
- ✅ **Conflict Detector**: 타 플러그인과의 디자인 충돌을 자동 감지하고 해결 제안

### ACF MBA Nudge Flow v22.1.0
- ✅ **권한 보안 패치 (Critical)**:
    - 모든 렌더링 함수에 `current_user_can('manage_options')` 가드 추가
    - 권한 없는 사용자 접근 시 `wp_die()` 처리

---

## 🚀 주요 변경사항 (Phase 30 - GUI Recovery & Security)

### ACF CSS Manager v22.1.1
- ✅ **GUI 렌더링 엔진 복구**: Style Center가 텍스트로만 표시되던 문제 해결
    - 누락된 6개 핵심 클래스 파일 복구:
      - `class-jj-simple-style-guide.php`
      - `class-jj-common-utils.php`
      - `class-jj-style-guide-frontend.php`
      - `class-jj-font-manager.php`
      - `class-jj-palette-manager.php`
      - `class-jj-typography-manager.php`
- ✅ **Safe Loader 진단 강화**: 파일 로드 실패 시 상세 진단 정보 제공

### ACF MBA Nudge Flow v22.1.0
- ✅ **권한 보안 패치 (Critical)**:
    - 모든 렌더링 함수에 `current_user_can('manage_options')` 가드 추가
    - 권한 없는 사용자 접근 시 `wp_die()` 처리
- ✅ **수정된 함수**: `render_template_center`, `render_dashboard`, `render_workflows`, `render_builder`, `render_analytics`, `render_settings`

---

## 🚀 주요 변경사항 (Grand Upgrade - Phase 28)

### WP Bulk Manager v5.0.3
- ✅ **멀티 사이트(Multisite) 통합 관리 완성**:
    - 네트워크 내 사이트 목록 조회 및 개별 사이트별 플러그인/테마 통합 관리 UI 구현.
    - 사이트 간 순차적(Local -> Multisite) 대량 설치 로직 완성.
- ✅ **원격 사이트(Remote Sites) 일괄 제어**:
    - 연결된 원격 사이트 목록 영구 저장 및 관리 시스템 구축.
    - REST API를 통한 원격 사이트 플러그인/테마 조회 및 제어(활성화/삭제 등) 기능.
    - 서버 사이드 Proxy 전송 방식을 통한 원격 사이트 ZIP 파일 직접 전송/설치 기능.
- ✅ **보안 및 권한 강화**:
    - '일방향 관리(🛡️)' 토글 추가: 최상위 관리자 권한 수락 시에만 일방향 원격 제어 허용.
    - 시크릿 키 기반 REST API 인증 체계 고도화.
- ✅ **Master 무제한 용량 해제**:
    - Master 에디션 전용 동시 업로드 및 대량 관리 수량 제한을 무제한(9999)으로 상향.
    - 서버 용량 제한 관련 주의 문구 및 공식 문서 링크 강화.

---

## 🚀 주요 변경사항 (UI/UX Hotfix - Phase 27.5)

### ACF CSS Manager v22.0.1
- ✅ **Fatal Error 해결**: `get_admin_colors()` 미정의 오류로 인한 페이지 중단 문제 해결.
- ✅ **메뉴 중복 제거**: 설정, 모양, 도구 메뉴의 중복 링크를 제거하고 최상위 메뉴 하나만 유지.
- ✅ **아이콘 위치 변경**: '🎨 스타일 센터' → '스타일 센터 🎨'로 사용자 요청 반영.
- ✅ **실험실 통합**: 실험실 센터를 스타일 센터 하위 서브메뉴로 배치하여 구조 최적화.

### WP Bulk Manager v5.0.2
- ✅ **마스터 권한 최종 해제**: 라이센스 감지 로직 고도화로 삭제 등 모든 유료 기능 잠금 해제.
- ✅ **업데이트 버튼 강조**: '선택 업데이트 🚀' 버튼에 브랜드 컬러를 적용하여 시인성 극대화.
- ✅ **자동 활성화 복구**: 인스톨러 페이지에서 '즉시 활성화' 체크박스가 사라졌던 문제 수정.
- ✅ **ID 배지 추가**: 플러그인 목록에 기술적 ID(폴더명)를 배지 형태로 추가하여 개발 편의성 증대.

---

## 🚀 주요 변경사항 (Master Clean Build - Phase 26)

### WP Bulk Manager v5.0.1 (기능 보완)
- ✅ **마스터 라이센스 감지 강화**: 플러그인 폴더명, ACF CSS Manager(Master) 존재 여부 및 상수 정의를 통한 다각도 검증 로직 적용.
- ✅ **삭제 기능 해제**: 마스터 에디션에서 삭제 기능이 잠겨 있던 문제(Basic 이상 메시지) 최종 해결.
- ✅ **선택 업데이트/롤백 추가**: 벌크 에디터 툴바에 '선택 업데이트' 및 '선택 롤백' 버튼 신설 및 백엔드 로직 연동.
- ✅ **인스톨러 UI 복구**: ZIP 파일 설치 시 '즉시 활성화' 체크박스를 다시 노출하여 사용자 편의성 증대.
- ✅ **번역 명칭 표시**: 플러그인 목록에 원문 이름과 함께 번역된 이름(i18n) 표시 기능 추가.

### ACF CSS Manager v22.0.1 (UI 개선 및 실험실 센터 복구)
- ✅ **메뉴 중복 제거**: 설정, 모양, 도구 하위의 중복된 스타일 센터 메뉴를 제거하고 최상위 메뉴 하나로 통합.
- ✅ **아이콘 위치 조정**: '스타일 센터 🎨'와 같이 아이콘을 텍스트 뒤로 배치하여 가독성 개선.
- ✅ **실험실 센터 복구**: `JJ_Labs_Center` 클래스 로드 누락으로 인한 권한 오류 및 접근 불가 문제 수정.
- ✅ **서브메뉴 통합**: 'ACF CSS 실험실 센터'를 스타일 센터 하위 서브메뉴로 등록하여 접근성 강화.

### WP Bulk Manager v5.0.0 (Grand Upgrade)
- ✅ **멀티 사이트 지원**: 워드프레스 멀티 사이트 네트워크 내의 여러 사이트에 플러그인/테마 대량 설치 및 관리 기능 추가.
- ✅ **원격 사이트 연결 (Connected Sites)**: 시크릿 키를 통한 타 워드프레스 사이트 원격 관리 및 제어 시스템 구축.
- ✅ **신규 전문 관리 탭**: '멀티 사이트 인스톨러/에디터', '싱글 사이트 벌크 인스톨러/에디터' 등 4개의 신규 탭 추가.
- ✅ **서버 용량 안내 강화**: 업로드 용량 제한 경고 문구 및 공식 문서 링크 추가로 관리자 편의성 증대.
- ✅ **인증 시스템**: 시크릿 키 기반의 원격 REST API 통신 및 보안 검증 로직 적용.

### 전체 패밀리 플러그인
- ✅ **Master 전용 빌드**: 모든 패밀리 플러그인의 Master 에디션 ZIP 파일을 일괄 생성하여 배포 효율성 극대화.
- ✅ **버전 메이저 업그레이드**: 시스템 안정화 및 Clean Master 롤백 완료를 기념하여 전체 버전 체계 상향 조정.
- ✅ **빌드 관리자 최적화**: `3j_build_manager.py`를 통해 Master 에디션 중심의 클린 빌드 프로세스 정립.

### ACF CSS Manager v22.0.0
- ✅ **Clean Master Rollback 완료**: 타 플러그인과의 물리적 결합을 제거하고 '마스터 키' 역할에 충실하도록 구조 최종 정비.
- ✅ **고유 기능 최적화**: Style Center 및 Style Guide 기능의 독립적 작동 보장 및 안정성 강화.

### ACF MBA Nudge Flow v22.0.0
- ✅ **전략적 프리셋 안정화**: 설치 즉시 활용 가능한 6종의 마케팅 전략 프리셋 탑재 및 원클릭 설치 기능 검증.
- ✅ **UI/UX 개선**: 좌측 메뉴 '넛지 플로우' 명칭 적용 및 한국어 사용자 최적화 완료.

---

## 🚀 주요 변경사항 (Phase 26 긴급 재정비)

### ACF CSS Manager v21.0.1
- ✅ **Clean Master Rollback**: Nudge Flow, Bulk Manager 등 타 패밀리 플러그인의 강제 통합 로직을 전면 제거하여 코드 경량화 및 안정성 확보.
- ✅ **마스터 키 권한 집중**: 타 플러그인의 기능을 직접 수행하는 대신, 패밀리 플러그인 전체의 라이센스 잠금을 해제하고 모든 프리미엄 기능을 활성화하는 '마스터 통합 제어기' 역할로 정체성 재정립.
- ✅ **자체 기능 무제한**: 마스터 에디션 내 모든 고유 기능(Style Center, Style Guide 등)의 제약을 해제하고 개발 중인 내부 확장 모듈(AI 어시스턴트 등) 로딩 구조 마련.
- ✅ **시스템 오작동 수정**: 무리한 통합으로 인한 메뉴 충돌 및 "권한 없음" 오류 방지 조치.

---

## 🚀 주요 변경사항 (Phase 26 - 전략적 프리셋 & 템플릿 마켓)

### ACF MBA Nudge Flow v20.2.4
- ✅ **전략적 프리셋 6종 탑재**: '개인화 마케팅 타당성 보고서'의 시나리오를 바탕으로 첫 방문 큐레이션, 가입 유도, 장바구니 회수, 무료배송 유도 등 6종의 고효율 템플릿 기본 제공.
- ✅ **템플릿 센터(Marketplace) 고도화**: 보고서 기반 3J Labs 추천 전략 탭 추가 및 유/무료 템플릿의 시각적 구분 강화.
- ✅ **원클릭 프리셋 설치**: 프리셋 설치 시 워크플로우 메뉴에 '초기 비활성화(Draft)' 상태로 자동 생성 및 메타 설정 완료.
- ✅ **수익화 시스템 강화**: 템플릿 판매자 신청 및 월간 정산액 노출을 통해 사장님의 노하우 자산화 유도.
- ✅ **UI/UX 현지화**: 좌측 메뉴바 '넛지 플로우' 명칭 및 주요 버튼/설명 한글화 완료.

## 🚀 주요 변경사항 (Phase 25 - Nudge Flow 개편)

### ACF MBA Nudge Flow v20.2.2 (v20.2.2)
- ✅ **메뉴 배치 최적화**: WooCommerce '마케팅' 메뉴 하단(Position 58)으로 이동하여 커머스 운영자의 접근성 및 워크플로우 효율성 증대.
- ✅ **서브메뉴 구조 개편**: 대시보드(통계), 워크플로우(관리), 분석(데이터), 템플릿 센터(공유)로 메뉴 체계화.
- ✅ **수익화 모델 UI 설계**: 템플릿 센터 내 유/무료 템플릿 구분 및 판매자 등록/정산 프로세스 UI 기반 구축.
- ✅ **공유 템플릿 생태계**: 사용자 템플릿 공유 및 판매 허용 옵션을 위한 데이터 구조 마련.

## 🚀 주요 변경사항 (Phase 24)

### ACF CSS Manager v20.2.2
- ✅ **WordPress 6.7.0+ 번역 로딩 최적화**: `_load_textdomain_just_in_time` 경고 해결을 위해 클래스 초기화 시점을 `init` 훅으로 연기.
- ✅ **메뉴 시스템 강화**: '알림판' 바로 아래 '벌크 매니저', 'ACF 스타일 센터' 순서 강제 및 강조 배경색 적용.

### ACF CSS Woo License Bridge v20.2.2
- ✅ **Master/Partner 에디션 시스템 도입**: 빌드 시 에디션에 따라 기능 및 대시보드 접근 제어.
- ✅ **판매 및 정산 대시보드**: 파트너 전용 판매 내역 확인 및 정산 현황 뷰 추가.
- ✅ **Neural Link 서버 연동**: WooCommerce 결제와 Neural Link 라이센스 발행 API 연동 강화.

### WP Bulk Manager v2.5.1
- ✅ **메뉴 가시성 문제 해결**: `custom_menu_order` 필터를 사용하여 최상위 메뉴 위치 고정.
- ✅ **선택 활성화 기능**: 플러그인 목록에서 여러 플러그인을 한 번에 활성화하는 기능 추가.
- ✅ **업로드 오류 핸들링 개선**: 서버측 PHP 업로드 오류 및 클라이언트측 AJAX 오류 메시지 상세화.

### ACF CSS Neural Link v4.3.0
- ✅ **업데이트 채널 관리**: Stable, Beta, Dev 채널별 배포 로직 통합.
- ✅ **순차 배포(Rolling Updates)**: 사이트별 그룹 할당(A/B/C) 및 점진적 업데이트 배포 기능.

---

## 🚀 주요 변경사항

### ACF CSS Manager v13.4.0

**Phase 18: 전체 로드맵 구현 및 에디션 빌드 시스템**

- ✅ 조건 빌더 UI 완전 구현 (AND/OR 논리 조합, 13가지 조건 타입)
- ✅ 넛지 마케팅 시스템 (Toast, Banner, Modal, Tooltip, Spotlight, Walkthrough)
- ✅ 내보내기/가져오기 기능 (JSON, ZIP 지원)
- ✅ 클라우드 동기화 API 기반 구조
- ✅ 서드파티 연동 프레임워크 (ACF, FacetWP, Perfmatters, Elementor, Gutenberg)
- ✅ 22개 언어 다국어 지원 완료
- ✅ Python 개발 툴킷 CLI 모드 추가

### ACF CSS AI Extension v2.1.0

- 🔄 버전 동기화 및 안정성 개선
- 📦 빌드 시스템 호환성 향상

### ACF CSS Neural Link v4.1.0

- 🔄 라이선스 API 버전 동기화
- 📦 에디션 빌드 시스템 통합

### ACF Code Snippets Box v1.1.0

**첫 마이너 업데이트**

- ✅ 조건 빌더 UI JavaScript 완전 구현
- ✅ 넛지 시스템 JavaScript 완전 구현
- ✅ 내보내기/가져오기 JavaScript 완전 구현
- ✅ 라이선스 기반 기능 접근 제어
- ✅ WooCommerce 연동 프리셋 추가
- ✅ 22개 언어 번역 파일 완료

### ACF CSS WooCommerce Toolkit v1.1.0

**첫 마이너 업데이트**

- ✅ 상품 Q&A 시스템 완전 구현
- ✅ Q&A JavaScript UI 구현
- ✅ 가격 엔진 안정성 개선
- ✅ 22개 언어 번역 파일 완료

---

## 🛠️ 개발 도구 업데이트

### Python Development Toolkit v2.0.0

**CLI 모드 추가**

```bash
# 플러그인 목록 확인
python 3j_dev_toolkit.py --list

# 간단 빌드
python 3j_dev_toolkit.py --simple

# 에디션별 빌드
python 3j_dev_toolkit.py --version 13.4.0 --edition all --user-type all --bundle
```

**지원 에디션**:
- Free (무료)
- Pro Basic
- Pro Premium
- Pro Unlimited

**지원 사용자 타입**:
- Standard (일반 사용자)
- Partner (파트너)
- Master (개발용)

---

## 🌐 지원 언어 (22개)

1. 🇰🇷 한국어 (ko_KR)
2. 🇺🇸 영어 (en_US)
3. 🇬🇧 영어 - 영국 (en_GB)
4. 🇨🇳 중국어 - 간체 (zh_CN)
5. 🇹🇼 중국어 - 번체 (zh_TW)
6. 🇭🇰 중국어 - 홍콩 (zh_HK)
7. 🇯🇵 일본어 (ja)
8. 🇪🇸 스페인어 (es_ES)
9. 🇧🇷 포르투갈어 - 브라질 (pt_BR)
10. 🇫🇷 프랑스어 (fr_FR)
11. 🇨🇦 프랑스어 - 캐나다 (fr_CA)
12. 🇩🇪 독일어 (de_DE)
13. 🇨🇭 독일어 - 스위스 (de_CH)
14. 🇳🇱 네덜란드어 (nl_NL)
15. 🇧🇪 네덜란드어 - 벨기에 (nl_BE)
16. 🇮🇹 이탈리아어 (it_IT)
17. 🇮🇳 힌디어 (hi_IN)
18. 🇻🇳 베트남어 (vi)
19. 🇹🇭 태국어 (th)
20. 🇹🇷 튀르키예어 (tr_TR)
21. 🇷🇺 러시아어 (ru_RU)
22. 🇺🇦 우크라이나어 (uk)

---

## 📁 생성된 빌드 패키지

### 기본 빌드 (5개)
- `acf-css-manager-master-v13.4.0.zip`
- `acf-css-ai-extension-v2.1.0.zip`
- `acf-css-neural-link-v4.1.0.zip`
- `acf-code-snippets-box-v1.1.0.zip`
- `acf-css-woocommerce-toolkit-v1.1.0.zip`

### 에디션 빌드 (12개)
- Free × (Standard, Partner, Master) = 3개
- Pro Basic × (Standard, Partner, Master) = 3개
- Pro Premium × (Standard, Partner, Master) = 3개
- Pro Unlimited × (Standard, Partner, Master) = 3개

### 번들 패키지
- `3J-Labs-ACF-CSS-Bundle-v13.4.0.zip`

---

## 🔗 GitHub 저장소

**Repository**: [poetryflow-jay/3JLabs-ACF-Project](https://github.com/poetryflow-jay/3JLabs-ACF-Project)

---

## 📋 다음 버전 예고 (Phase 19)

- 🎯 Figma 통합 심화 (W Design Kit 스타일)
- 🎯 AI 폰트 추천 기능 고도화
- 🎯 로컬 WordPress 테스트 환경 자동화
- 🎯 성능 최적화 및 코드 리팩토링

---

## 📞 지원 및 문의

- **웹사이트**: https://3j-labs.com
- **GitHub**: https://github.com/poetryflow-jay
- **이메일**: support@3j-labs.com

---

**© 2026 3J Labs (제이x제니x제이슨 연구소). All rights reserved.**
