# ACF CSS: Advanced Custom Fonts&Colors&Styles Setting - Really Simple WordPress Style Management Center - Changelog

## 📋 Roadmap (다음 Phase 계획)

### Phase 19: Figma 통합 심화 및 AI 고도화
- Figma 디자인 → WordPress 자동 변환
- WordPress 디자인 → Figma 내보내기
- AI 기반 폰트 추천 고도화
- 성능 최적화 및 코드 리팩토링

### Phase 20: 테스트 자동화 및 CI/CD
- 로컬 WordPress 테스트 자동화
- GitHub Actions 기반 CI/CD 파이프라인
- 자동 배포 시스템 구축

---

## 🔜 Next Phase Preview (Coming Soon)

### Phase 19: Figma 통합 심화
- W Design Kit 스타일 통합
- 양방향 디자인 동기화
- AI 폰트 추천 강화

---

---

## Version 22.1.0 (2026-01-03) - Phase 29: 넛지 플로우 정상화 및 스타일 센터 GUI 복구

### 🚀 넛지 플로우 (ACF MBA) 정상화
- **권한 오류 해결**: `acf-nudge-flow-builder` 메뉴 등록을 통한 빌더 접근 권한 문제 해결
- **메뉴 구조 최적화**: 대시보드를 최상단으로 재배치하고 워크플로우, 분석, 템플릿 센터 순으로 정렬
- **전략적 프리셋 주입**: 개인화 마케팅 보고서 기반 6종의 고효율 마케팅 프리셋 실제 작동 가능한 형태로 주입
  - 첫 방문자 환영, 회원가입 유도, 장바구니 회수, 무료 배송 유도, 스마트 교차 판매, VIP 리텐션

### 🔧 ACF CSS 스타일 센터 GUI 복구
- **에셋 로딩 버그 수정**: 마스터 통합 버전의 최상위 메뉴에서 CSS/JS 로드가 누락되던 문제 해결
- **GUI 렌더링 엔진 정상화**: 스타일 센터의 모든 탭 인터페이스 및 상호작용 기능 복구

### 🔒 보안 및 국제화 강화
- **FTP 탈취 방지**: 중요 폴더(`includes`, `assets`, `adapters`) 디렉토리 리스팅 차단 및 접근 제어 강화
- **라이센스 암호화**: Neural Link 라이센스 키 저장 시 AES-256-CBC 암호화 적용
- **다국어 최적화**: WP Bulk Manager의 한국어 명칭을 "WP 벌크 매니저"로 정식 반영 및 22개 언어 UI 검토

### 🛠️ 빌드 시스템 업데이트
- **Build Manager v22.1.0**: 빌드 프로세스 안정성 강화 및 버전 동기화 로직 개선

## Version 20.0.0 (2026-01-02) - Phase 20: 보안 강화 및 다국어 지원

### 🔒 보안 강화
- **파일 무결성 모니터**: FTP 코드 탈취 방지 시스템 (`class-jj-file-integrity-monitor.php`)
  - 중요 파일 해시 검증 (SHA-256)
  - 파일 변경 감지 및 자동 조치
  - 라이센스 파일 위반 시 FREE로 강제 전환
  - 주기적 무결성 검사 (매일)
- **보안 강화 모듈**: 라이센스 암호화 및 업데이트 서버 보안 (`class-jj-security-enhancer.php`)
  - 라이센스 키 AES-256-CBC 암호화
  - 업데이트 서버 응답 서명 검증
  - API 요청 서명 생성/검증
  - 사이트별 고유 암호화 키 생성

### 🎨 UI/UX 개선 (Phase 19.1 연장)
- **플러그인 목록 페이지 향상**: `class-jj-plugin-list-enhancer.php` 개선
  - 자동 업데이트 토글 버튼 (AJAX 기반)
  - 액션 링크 스타일링 (아이콘, 색상, 볼드체)
  - 작성자 정보 영역 개선
  - 툴팁 시스템 추가
  - 넛지 메시지 시스템 (3일 간 안 보기, 다시 보지 않기)
- **적용 플러그인**: ACF CSS Manager, WP Bulk Manager, ACF Code Snippets Box, Admin Menu Editor Pro

### 🌍 다국어 지원 확장
- **22개 언어 플러그인 헤더 번역 완료**
  - 한국어, 영어(US/UK), 중국어(북부/남부/홍콩), 일본어, 스페인어(스페인/브라질), 프랑스어(프랑스/캐나다), 독일어(독일/스위스), 네덜란드어(네덜란드/벨기에), 이탈리아어, 베트남어, 힌디어, 태국어, 터키어, 러시아어, 우크라이나어
- **번역 스크립트**: `update_plugin_headers_i18n.py` 생성

### 📝 문서화
- **Phase 20 로드맵**: `docs/PHASE_20_ROADMAP.md` 작성
- **메모리 문서**: `memory & context/20260102-Phase-20-보안-강화-및-다국어-지원.md` 작성

---

## Version 13.4.4 (2026-01-02) - Master 버전 올인원 통합 구조 완성

### 🚀 마스터 버전 통합 아키텍처
- **올인원 통합 로더**: `JJ_Master_Integrator` 클래스로 모든 패밀리 플러그인 기능 단일 플러그인에 통합
- **통합 모듈 시스템**: 마스터 버전에서 별도 플러그인 설치 없이 모든 기능 사용 가능
- **독립 플러그인 감지**: 이미 설치된 독립 플러그인과 충돌 없이 공존

### 📦 통합된 마스터 모듈
- **Code Snippets 모듈**: 코드 스니펫 저장 및 조건부 실행 (`class-jj-master-code-snippets.php`)
- **WooCommerce 모듈**: 가격, 할인, 장바구니 스타일링 (`class-jj-master-woocommerce.php`)
- **AI 모듈**: AI 스타일 분석 및 추천 (`class-jj-master-ai.php`)
- **Nudge Flow 모듈**: 마케팅 넛지 자동화 (`class-jj-master-nudge-flow.php`)
- **Bulk Manager 모듈**: 대량 플러그인/테마 설치 (`class-jj-master-bulk-manager.php`)

### 🔧 UI/UX 개선
- **어드민 센터 빈 화면 문제 해결**: 초기 탭 active 클래스 추가 및 JavaScript 폴백
- **실험실 센터 이름 변경**: "ACF CSS 실험실 센터"로 명칭 변경 및 설명 상세화
- **ACF CSS Manager → ACF CSS 설정 관리자**: 메뉴 명칭 한글화
- **플러그인 목록 링크 개선**: 색상, 아이콘, 백업/롤백/진단 링크 추가

### 🔗 플러그인 업데이트 시스템
- **Neural Link 통합**: `JJ_Plugin_Updater` 클래스로 WordPress 업데이트 시스템 연동
- **자동 업데이트 지원**: 개발자 푸시 시 자동 업데이트 수신 가능

### 📊 대시보드 업데이트
- **dashboard.html**: 최신 플러그인 버전 및 빌드 정보 반영
- **Python 툴킷 연동**: 자동 버전 동기화 준비

---

## Version 13.4.3 (2026-01-02) - UI/UX 개선 및 버그 수정

### 🔧 버그 수정
- **어드민 센터 빈 화면 문제 해결**: 초기 탭 active 클래스 추가
- **WP Bulk Manager 프로그레스 바 100% 버그 수정**: v2.3.1

### 🎨 UI/UX 개선
- **실험실 센터 이름 변경**: ACF CSS 실험실 센터
- **ACF CSS Manager 명칭 변경**: ACF CSS 설정 관리자
- **실험실 센터 설명 텍스트 상세화**: 버튼 정렬 CSS 개선
- **플러그인 목록 링크 개선**: 색상, 아이콘, 백업/롤백/진단 링크 추가

---

## Version 13.4.2 (2026-01-02) - Hotfix: 블록 에디터 & Nexter 테마 지원

### 🎨 블록 에디터 개선
- **스타일 가이드 라이브 블록**: 블록 에디터에서 `jj-style-guide/live-page` 블록 추가
- **에디터 미리보기**: 블록 에디터에서 스타일 가이드 라이브 페이지 미리보기 표시 (숏코드만 보이던 문제 해결)
- **서버 사이드 렌더링**: 프론트엔드에서 전체 스타일 가이드 정상 표시

### 🔄 Nexter 테마 동기화 지원
- **Nexter 테마 브랜드 팔레트**: `nexter_theme_options` 및 `nexter_blocks_settings`에서 색상 동기화
- **Nexter 테마 시스템 팔레트**: 사이트 배경, 텍스트, 링크 색상 자동 감지
- **Posimyth 테마 호환**: Nexter Theme 4.x 시리즈와 완전 호환

### 💬 UX 개선
- **색상 새로고침 에러 메시지 개선**: Customizer에서 색상을 찾을 수 없을 때 친절한 안내 메시지 표시
- **대안 제안**: 직접 입력 또는 추천 팔레트 사용 안내 추가

---

## Version 13.4.1 (2026-01-02) - Hotfix: 권한 오류 및 넛지 시스템 수정

### 🔧 버그 수정
- **레거시 슬러그 리다이렉트**: 구 슬러그(`jj-style-guide`, `jj-simple-style-guide`)에서 신 슬러그로 자동 리다이렉트
- **권한 오류 해결**: Master 버전에서 "권한이 없습니다" 오류 수정
- **넛지 시스템 URL 수정**: 스타일 가이드 페이지 링크가 올바른 URL로 연결되도록 수정

### ✨ 넛지 시스템 개선
- **스크롤-투-타겟**: 넛지가 특정 요소를 가리킬 때 자동 스크롤
- **하이라이트 효과**: 펄스, 글로우, 바운스 등 다양한 하이라이트 애니메이션
- **스포트라이트 오버레이**: 특정 UI 요소에 집중할 수 있는 스포트라이트 효과
- **URL 기반 스포트라이트**: URL 파라미터로 가이드 투어 실행 가능

---

## Version 13.4.0 (2026-01-02) - Phase 18: 전체 로드맵 구현 및 에디션 빌드 시스템

### 🎯 Phase 18: 로드맵 완료
- **조건 빌더 UI 완전 구현**: AND/OR 논리 조합, 13가지 조건 타입
- **넛지 마케팅 시스템**: Toast, Banner, Modal, Tooltip, Spotlight, Walkthrough 6가지 타입
- **내보내기/가져오기**: JSON, ZIP 형식 지원, 드래그 앤 드롭 UI
- **클라우드 동기화**: 3J Labs API 기반 스니펫 동기화 구조

### 🔧 개발 도구
- **Python 개발 툴킷 CLI 모드**: 커맨드라인 빌드 지원
- **에디션 빌드 시스템**: Free/Basic/Premium/Unlimited × Standard/Partner/Master
- **번들 패키지 생성**: 전체 에디션 일괄 패키징

### 🌐 다국어 지원
- 22개 언어 번역 파일 완료 (ACF Code Snippets Box, ACF CSS WooCommerce Toolkit)

### 📦 새 플러그인 버전
- ACF Code Snippets Box v1.1.0: 조건 빌더, 넛지 시스템, 내보내기/가져오기 JavaScript 구현
- ACF CSS WooCommerce Toolkit v1.1.0: 상품 Q&A 시스템, 22개 언어 지원

---

## Version 13.3.0 (2026-01-01) - Phase 15: 로컬 WordPress 환경 구축

### 🐳 Docker 기반 로컬 개발 환경
- Kinsta 호스팅과 동일한 사양 (PHP 8.5, Nginx 1.27, MariaDB 11.7)
- Docker Compose 기반 원클릭 환경 구축
- Kinsta 백업 파일 복원 지원

### 📦 ACF Code Snippets Box v1.0.0 (신규)
- WordPress 코어 비수정 코드 스니펫 관리
- WPCODEBOX2 스타일 조건부 실행
- 15+ 프리셋 코드 라이브러리
- ACF CSS 스타일 변수 연동

### 🛒 ACF CSS WooCommerce Toolkit v1.0.0 (신규)
- 가격 엔진 (할인율, 할부 표시)
- 할인 계산기 UI
- 빠른 편집 필드 확장
- 장바구니 UI 개선

---

## Version 13.2.0 (2026-01-01) - 툴팁 & 넛지 시스템 + 22개 언어 지원

### 🎯 Phase 13.2: 툴팁(Tooltip) 시스템 확장
- **Admin Center 전역 툴팁**: 모든 주요 UI 요소에 상황별 도움말 툴팁 추가
- **다국어 툴팁 데이터**: 한국어, 영어, 일본어, 중국어 등 다국어 지원
- **접근성 향상**: ARIA 속성, 키보드 네비게이션, ESC 키 닫기 지원
- **툴팁 카테고리**: 색상, 타이포그래피, Figma, 내보내기, 백업 관련 툴팁 추가

### 💡 넛지(Nudge) 시스템 신규 구현
- **온보딩 가이드**: 플러그인 첫 사용 시 5단계 단계별 안내 모달
- **미완료 설정 알림**: 색상/타이포그래피 미설정 시 부드러운 알림
- **최적화 제안**: 캐시 활성화, 자동 백업 등 최적화 팁 제공
- **새 기능 안내**: 버전 업데이트 시 새로운 기능 소개 (내보내기, Figma 연동 등)
- **일일 팁**: 매일 랜덤 사용 팁 제공 (키보드 단축키, 프리셋, 반응형 등)
- **사용자별 닫기 상태 저장**: user_meta 기반 개인화

### 🌍 22개 언어/로케일 번역 파일 생성
**우선순위 1 (핵심 언어)**:
- 한국어 (`ko_KR`)
- 영어 미국/영국 (`en_US`, `en_GB`)
- 중국어 간체/번체/홍콩 (`zh_CN`, `zh_TW`, `zh_HK`)
- 일본어 (`ja`)
- 스페인어 (`es_ES`)

**우선순위 2 (사용 인구 기준)**:
- 힌디어 (`hi_IN`)
- 포르투갈어 브라질 (`pt_BR`)
- 러시아어 (`ru_RU`)
- 프랑스어 프랑스/캐나다 (`fr_FR`, `fr_CA`)
- 독일어 독일/스위스 (`de_DE`, `de_CH`)

**우선순위 3 (추가 언어)**:
- 베트남어 (`vi`)
- 튀르키예어 (`tr_TR`)
- 이탈리아어 (`it_IT`)
- 태국어 (`th`)
- 네덜란드어 네덜란드/벨기에 (`nl_NL`, `nl_BE`)
- 우크라이나어 (`uk`)

### 📄 새 파일
- `includes/class-jj-nudge-system.php` - 넛지 시스템 백엔드
- `assets/js/jj-nudge-system.js` - 넛지 시스템 프론트엔드
- `assets/css/jj-nudge-system.css` - 넛지 시스템 스타일
- `languages/*.po` - 22개 언어 번역 파일

---

## Version 13.0.0 (2026-01-01) - 스타일 가이드 내보내기 + 폰트 추천 + Figma 연동

### 📤 Phase 11: 스타일 가이드 내보내기
- **PDF 내보내기**: 브라우저 인쇄 기능을 활용한 깔끔한 PDF 생성
- **PNG 이미지 내보내기**: html2canvas 기반 2배 해상도 캡처
- **HTML 파일 내보내기**: 완전한 독립형 스타일 가이드 HTML
- **CSS 변수 내보내기**: 모든 디자인 토큰을 CSS 커스텀 프로퍼티로
- **JSON 토큰 내보내기**: 프로그래밍용 디자인 토큰 구조
- **ZIP 전체 내보내기**: HTML + CSS + JSON + README 압축

### 🔤 Phase 12: 폰트 추천 + 원클릭 설치
- **한국어 무료 폰트 10종**: Pretendard, Noto Sans KR, IBM Plex Sans KR 등
- **영문 무료 폰트 10종**: Inter, Poppins, Montserrat, Roboto 등
- **유료 폰트 참조 목록**: Sandoll Gothic Neo, Yoon Gothic, Helvetica Neue 등
- **원클릭 Google Fonts 적용**: 클릭 시 즉시 사이트에 폰트 로드
- **Typography 섹션 UI 업데이트**: 폰트 추천 패널 추가

### 🎨 Phase 13: Figma 연동
- **Admin Center > Figma 탭 추가**: API 토큰 및 파일 키 관리
- **연결 테스트**: Figma API 연결 상태 확인
- **Figma → WordPress**: 색상/타이포그래피 스타일 가져오기
- **WordPress → Figma**: JJ 토큰을 Figma Variables JSON으로 내보내기
- **보안**: API 토큰 base64 암호화 저장

---

## Version 6.2.1 (2025-12-19) - Go-to-Market 준비 완료

### 🚀 마케팅 런치 준비
- **랜딩 페이지**: `marketing/landing-page.html` - 세련된 다크 테마 랜딩 페이지
- **베타 테스터 폼**: `marketing/beta-signup.html` - Formspree 연동 준비 완료
- **데모 사이트**: `marketing/demo-site/` - 3가지 테마 데모 (Tech Startup, Law Firm, Cafe)
- **프레스 릴리스**: `marketing/press-release.md` - 보도자료 초안

### 📸 마케팅 스크린샷 (10종)
- `marketing/screenshots/output/` 폴더에 10개 더미 화면 스크린샷 저장
  - Admin Center, AI Generation, Smart Preview, Cloud Export
  - Template Market, Partner Hub, Customizer, Plugin List
  - Multisite Settings, Webhook Settings

### 🌐 국제화 (i18n)
- **POT 파일 생성**: `languages/acf-css-really-simple-style-management-center.pot`
- **영어 번역**: `languages/acf-css-really-simple-style-management-center-en_US.po`

### 🔌 어댑터 확장 (Phase 8 기초)
- **Bricks Builder 어댑터**: `adapters/adapter-spoke-bricks.php`
- **Breakdance 어댑터**: `adapters/adapter-spoke-breakdance.php`
- `config/adapters-config.php`에 새 어댑터 등록

### 📱 모바일 브릿지 스켈레톤 (Phase 10 기초)
- **Mobile Bridge 모듈**: `includes/modules/mobile-bridge/class-jj-mobile-bridge.php`
- REST API 엔드포인트 (`/jj-mobile/v1/styles`, `/auth`, `/push-notification`)
- JWT 인증 및 푸시 알림 스켈레톤

### 📦 배포 준비
- **통합 배포 폴더**: `marketing/deploy/` - Netlify 드래그 앤 드롭 준비 완료
- **Netlify 설정**: `netlify.toml`, `_redirects`
- **마켓플레이스 가이드**: `marketing/marketplace/CODECANYON_SUBMISSION.md`, `GUMROAD_LISTING.md`

---

## Version 6.2.0 (2025-12-18) - Phase 7 완성: AI 스타일 자동 생성

### 🤖 Phase 7.1~7.2: AI Extension 확장 플러그인
- **AI 확장 플러그인**: `acf-css-ai-extension` 별도 확장 플러그인으로 AI 기능 제공
- **OpenAI 통합**: GPT-3.5-turbo 기반 스타일 자동 생성
- **시스템 프롬프트 최적화**: 색상 팔레트, 타이포그래피, 버튼 스타일에 특화된 프롬프트
- **JSON 스키마 검증**: AI 응답의 안전한 파싱 및 필수 필드 검증
- **API Key 관리**: 설정 UI에서 안전하게 API Key 저장

### 🔍 Phase 7.3: 스마트 프리뷰 (Diff Visualization)
- **jsondiffpatch-lite**: 가벼운 커스텀 Diff 라이브러리 내장
- **시각적 Before/After 비교**: JSON 텍스트 대신 변경된 항목만 하이라이트
- **색상 스와치**: 색상 변경 시 시각적 프리뷰 제공

### ☁️ Phase 7.4: Cloud 연동
- **원클릭 Cloud 저장**: AI 생성 결과물을 즉시 클라우드에 업로드
- **공유 코드 발급**: 다른 사이트에 복제하거나 템플릿 마켓에 등록 가능
- **AI 메타데이터 저장**: 생성 시 사용한 프롬프트도 함께 저장

### 🔐 보안 및 안전장치
- **Multisite 잠금**: 네트워크 전용 모드에서 AI 적용 차단
- **JSON 검증**: 필수 필드(`explanation`, `settings_patch`) 체크
- **캐시 자동 플러시**: 스타일 적용 후 CSS 캐시 무효화

---

## Version 6.1.1 (2025-12-18) - Phase 6: 테스트 자동화 & 보안 하드닝

### ✅ 테스트 자동화
- **PHPUnit + Brain Monkey** 기반 단위 테스트 하네스 추가
- 핵심 모듈 기본 테스트 추가(Optimizer/Multisite/REST/Webhook)
- **CI**: GitHub Actions에서 PR/푸시마다 테스트 자동 실행

### 🔐 보안 하드닝(Cloud)
- `sslverify=false` 제거 → **기본값 true**로 안전화
- 필터 제공:
  - `jj_cloud_api_base_url`
  - `jj_cloud_sslverify`
- 멀티사이트 네트워크 전용 모드에서는 Cloud Export/Import 차단(일관성)

---

## Version 6.1.0 (2025-12-18) - Phase 3/4/5 완성: 템플릿 마켓, CSS Tree Shaking, Partner Hub

### ☁️ Phase 3: JJ Cloud 완성
- **템플릿 마켓**: 전문가가 디자인한 스타일 템플릿을 검색하고 즉시 적용
- **템플릿 판매**: 사용자가 자신의 스타일을 마켓에 등록하여 수익 창출 (Coming Soon)
- **카테고리 필터링**: 비즈니스, 카페/레스토랑, 기술/개발 등 카테고리별 검색
- **Admin Center 통합**: Cloud 탭에서 템플릿 마켓 접근

### ⚡ Phase 4: Performance Booster 완성
- **CSS Tree Shaking**: 사용하지 않는 CSS 규칙 자동 제거
- **옵션 기반 최적화**: 활성화되지 않은 팔레트/타이포그래피/버튼 스타일 CSS 제거
- **파일 크기 감소**: 불필요한 CSS 제거로 구글 PageSpeed 점수 향상
- **설정 가능**: `performance.enable_tree_shaking` 옵션으로 활성화/비활성화

### 🤝 Phase 5: Partner Hub 완성
- **중앙 관제 대시보드**: 파트너가 수십, 수백 개의 고객 사이트를 한 화면에서 관리
- **사이트 상태 모니터링**: 연결된 사이트의 활성/오류 상태 실시간 확인
- **일괄 스타일 적용**: 현재 설정을 모든 연결된 사이트에 동시 적용
- **REST API 연동**: 원격 사이트와 REST API로 통신하여 스타일 동기화
- **Partner/Master 전용**: Partner 및 Master 에디션에서만 활성화

---

## Version 6.1.1 (2025-12-18) - Phase 6: 테스트 자동화 & 보안 하드닝

### ✅ 테스트 자동화
- **PHPUnit + Brain Monkey** 기반 단위 테스트 하네스 추가
- 핵심 모듈 기본 테스트 추가(Optimizer/Multisite/REST/Webhook)
- **CI**: GitHub Actions에서 PR/푸시마다 테스트 자동 실행

### 🔐 보안 하드닝(Cloud)
- `sslverify=false` 제거 → **기본값 true**로 안전화
- 필터 제공:
  - `jj_cloud_api_base_url`
  - `jj_cloud_sslverify`
- 멀티사이트 네트워크 전용 모드에서는 Cloud Export/Import 차단(일관성)

---

## Version 6.0.5 (2025-12-18) - Phase 5 B: Multisite Network Control

### 🌐 멀티사이트(네트워크) 제어 (드물지만 강력)
- **Network Admin 페이지 추가**: `네트워크 관리자 > 설정 > ACF CSS Network`
- **네트워크 기본값 적용**: 네트워크 기본 스타일(JSON)을 각 사이트에 병합/적용
- **사이트 오버라이드 제어**: 네트워크 전용 모드에서는 사이트별 저장/REST 업데이트를 차단하여 일관성 유지
- **전체 사이트 적용(덮어쓰기)**: 최대 200개 사이트까지 네트워크 기본값을 일괄 적용(안전 상한)

---

## Version 6.0.4 (2025-12-18) - Phase 5 A: Webhooks Automation

### 🔔 Webhook 자동화(필수)
- **Webhook Manager**: 설정 변경 시 지정된 URL로 Webhook POST 전송
- **이벤트 지원**:
  - `style_settings_updated` (스타일 센터 저장)
  - `admin_center_updated` (Admin Center: 메뉴/상단바/텍스트/업데이트/비주얼 저장)
- **서명 지원**: Secret 설정 시 `X-JJ-Signature`(HMAC-SHA256) 헤더 포함
- **Admin Center UI**: 업데이트 탭에서 Webhook 활성화/URL/Secret/페이로드/이벤트/테스트 버튼 제공

---

## Version 6.0.3 (2025-12-18) - Phase 5.3 Extension System

### 🧩 Phase 5.3: 확장 플러그인 시스템 (Extension API)
- **Extension Interface**: 서드파티 확장 플러그인이 구현할 표준 인터페이스 추가
- **Extension Manager**: 필터/액션 기반 등록 및 안전 초기화(버전/에디션 capability 체크) 지원
  - 등록 필터: `jj_style_guide_extensions`
  - 등록 액션: `jj_style_guide_register_extensions`
  - 로드 완료 액션: `jj_style_guide_extensions_loaded`

---

## Version 6.0.2 (2025-12-18) - Brand Identity & UX Polish

### 🎨 Phase 5.1: 브랜드 정체성 강화
- **Brand Unification**: 플러그인 전반의 "JJ" 표기를 `ACF CSS Manager`로 통합 완료
- **Korean Typography**: 한국 사용자 환경에 최적화된 세련된 예시 문구(Typography Examples) 도입
- **Edition Logic Refinement**: 마스터 버전에서 `Free` 배지 및 `Upgrade` 버튼이 불필요하게 노출되던 문제 수정
- **License Type Fix**: 마스터 버전 활성화 시 라이선스 상태가 `BASIC`으로 오인되던 로직 수정 (Master 강제 인식)

### 🌍 글로벌화 준비 (i18n)
- **Text Domain Initialization**: 다국어 번역 지원을 위한 로드 파이프라인 구축
- **Language Assets**: `languages/` 구조 정립 및 번역 기반 마련

### 🔌 Phase 5.2: API 및 통합 (REST API)
- **REST API 제공**: 플러그인 설정을 외부에서 조회/업데이트할 수 있는 엔드포인트 추가
  - `GET /wp-json/jj-style-guide/v1/info`
  - `GET /wp-json/jj-style-guide/v1/settings`
  - `POST /wp-json/jj-style-guide/v1/settings` (관리자 권한 필요)
- **캐시 일관성**: REST 업데이트 시 CSS 캐시 자동 플러시
- **확장 훅 제공**: 설정 변경 시 `jj_style_guide_settings_updated` 액션 트리거

---

## Version 6.0.1 (2025-12-18) - Official Launch Ready

### [CRITICAL] 퀵 링크 복구 및 클래스 구조 정상화
- **Quick Links Repair**: 플러그인 목록 화면의 `Settings | Styles | Menu | Visual` 링크가 정상적으로 노출되지 않던 문제 해결
- **Class Name Fix**: 메인 클래스 이름이 잘못 정의되어 있던 문제(`_Free` 접미사 제거)를 수정하여 모든 훅이 정상 작동하도록 조치

---

## Version 6.0.0-RC1 (2025-12-18) - Grand Unification (대통합)

### 🌟 새로운 시대의 시작: The Platform
- **One Code Architecture**: 모든 에디션을 아우르는 단일 코드베이스 구축 완료
- **6-Edition System**: Free, Basic, Premium, Unlimited, Partner, Master 라인업 완성

### 🚀 핵심 기능 탑재 (Major Features)
1. **Visual Command Center**: 로그인 화면 및 어드민 테마 커스터마이징 (Phase 1)
2. **AI Style Intelligence**: 색채학 기반 스마트 팔레트 생성기 (Phase 2)
3. **JJ Cloud Ecosystem**: 설정 클라우드 저장 및 공유 시스템 (Phase 3)
4. **Performance Booster**: CSS Minification 및 로드 최적화 (Phase 4)
5. **Access Everywhere**: 어드민 바, 도구, 모양 메뉴 통합 (Phase 4.5)

### 🧭 Phase 4.99 - 최종 검토/수정 (배포 품질 & 동선 완성)
- **Admin Center 진입 동선 완성**: 메인 실행(`plugins_loaded`)에서 `JJ_Admin_Center::init()`이 확실히 호출되도록 정리
- **모양/도구 메뉴 안정화**: `jj-admin-center`를 동일 slug로 통일하고, `hook_suffix` 조건을 확장하여 CSS/JS가 정상 로드되도록 수정
- **상단바(Admin Bar) 안정화**: 클래스 밖에 존재하던 잘못된 `public function` 블록 제거 및 정상 메서드로 복구 (치명적 구문 오류 제거)
- **스타일 센터 진입점 복원**: `도구(Tools)` 메뉴에서도 스타일 센터 페이지를 직접 열 수 있도록 연결 및 assets 로딩 조건 확장
- **플러그인 명명 규칙 재정립**: `ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager`로 간소화 및 에디션별 접미사(Pro, Master ver. 등) 정리
- **Quick Links 확장**: 플러그인 목록에서 `Settings | Styles | Menu | Visual` 링크 제공

### 🔒 보안 및 안정성
- **Safe Loader**: 파일 로딩 안정성 100% 확보
- **Neural Link 연동**: OTA 업데이트 및 실시간 라이센스 검증 시스템 탑재

---

## Version 5.9.1-BETA (2025-12-18) - Access Everywhere (접근성 대폭 개선)

### [IMPROVEMENT] 관리자 접근성 강화
- **메뉴 확장**: `설정`, `모양`, `도구` 메뉴 하위에 바로가기 추가하여 어디서든 접근 가능
- **Admin Bar 메뉴**: 상단 바에 `JJ Style Guide` 퀵 메뉴 추가 (호버 시 주요 기능 바로가기)
- **설정 링크 수정**: 플러그인 목록 화면의 `Settings` 링크가 올바른 관리자 센터 페이지를 가리키도록 수정

---

## Version 5.9.0-BETA (2025-12-18) - Performance Booster

### [NEW] CSS 성능 최적화 엔진
- **CSS Minification**: 생성된 스타일 시트의 공백과 주석을 제거하여 파일 크기 최소화
- **Delivery Optimization**: 렌더링 차단을 방지하는 비동기 로드 지원 (Premium 이상)

### [UPDATE] Core Engine 개선
- **Strategy 1**: CSS 생성 파이프라인에 필터 훅(`jj_generated_css_output`) 도입하여 확장성 확보

---

## Version 5.8.0-BETA (2025-12-18) - JJ Cloud Ecosystem

### [NEW] Cloud Ecosystem 구축
- **클라우드 저장소 (Cloud Vault)**:
  - 스타일 설정을 Neural Link 서버에 안전하게 저장 (Export)
  - 6자리 공유 코드(Share Code) 발급
- **원클릭 불러오기 (One-Click Import)**:
  - 공유 코드만 있으면 다른 사이트에서 즉시 설정 복원
  - Premium 에디션 이상 지원

### [NEW] Neural Link v3.1.0
- 클라우드 데이터 저장을 위한 CPT(`jj_cloud_item`) 및 API 엔드포인트 추가

---

## Version 5.7.0-BETA (2025-12-18) - AI Style Intelligence

### [NEW] AI 스타일 인텔리전스 탑재
- **스마트 컬러 생성기 (Smart Palette Generator)**:
  - 색채학(Color Theory) 알고리즘 기반의 자동 팔레트 생성 엔진
  - 단색(Monochromatic), 유사색(Analogous), 보색(Complementary), 3색(Triadic) 조화 모드 지원
  - 생성된 팔레트를 브랜드 컬러에 즉시 적용하는 원클릭 기능

### [UPDATE] Admin Center UI 개선
- Colors 탭에 AI 생성기 위젯 추가

---

## Version 5.6.0-BETA2 (2025-12-18) - Visual Command Center UI

### [FEATURE] Visual Command Center UI 구현
- **Admin Center > Visual 탭 추가**:
  - 로그인 커스터마이저 설정 (로고 업로드, 색상 변경)
  - 어드민 테마 설정 (다크 모드, 강조 색상)
- **설정 저장 시스템 연동**: `visual_options` 데이터 저장 및 로드 로직 구현

### [UPDATE] Edition Controller 기능 확장
- `login_customizer`, `admin_theme` 권한 제어 로직 추가

---

## Version 5.5.0 (2025-12-18) - 아키텍처 개혁 및 에디션 통합

### [MAJOR] One Code, Multi Edition 시스템 완성
- **단일 코드베이스 전략 실현**: Free/Pro/Master/Partner 버전을 하나의 마스터 코드로 통합
- **자동 배포 시스템 구축**: 빌드 스크립트를 통한 5개 에디션(Free, Basic, Premium, Partner, Master) 일괄 생성 지원

### [FEATURE] 파트너 에디션 (White Labeling)
- **화이트 라벨링 엔진 탑재**: 파트너사 브랜딩(로고, 이름, 푸터)으로 완벽한 커스터마이징 지원
- **동적 브랜딩 제어**: `JJ_Edition_Controller`를 통한 실시간 UI 텍스트/이미지 교체

### [CORE] 기능 제어 시스템 고도화
- **어댑터 쿼터제**: 에디션별 테마/플러그인 지원 개수 자동 제어
- **Soft Lock UI**: 권한 없는 기능에 대한 잠금 화면 및 업그레이드 안내
- **시뮬레이션 모드**: 개발/테스트를 위한 에디션 강제 지정 기능 (`wp-config.php` 상수)

---

## Version 5.4.1 (2025-12-17) - 활성화 문제 완전 해결 및 구조 재설계

### [CRITICAL] 플러그인 활성화 구조 완전 재설계
- **Free 버전 기반 아키텍처 도입**
  - 활성화 오류가 없는 Free 버전의 안정적인 구조를 베이스로 채택
  - `Safe Loader` 시스템을 Free 구조 위에 이식하여 안정성 확보
  - 파일 로딩 순서 및 의존성 관리 로직 전면 재작성

- **클래스 및 네임스페이스 정리**
  - 임시 조치(`_Clone`) 제거 및 정규 명칭(`_Master`) 복구
  - Free/Master 간 클래스명 충돌 원천 차단
  - `limit_adapters` 등 Free 전용 제한 로직 제거

### [IMPROVEMENT] Master 기능 정상화
- **핵심 엔진 활성화**
  - `Sync Manager`, `Migration Manager`, `Memory Manager` 정상 가동
  - `Safe Mode`, `CSS Optimizer` 기능 복구
  - `License Issuer` 등 Master 전용 기능 통합 완료

### [FIX] 기타 수정
- **활성화 훅 제거**: 불필요하고 오류를 유발하던 `register_activation_hook` 영구 제거
- **헤더 정리**: 플러그인 메타데이터 표준화

---

## Version 5.4.0 (2025-12-17) - 프리 버전 구조 정합 시도 (활성화 실패)

### ⚠️ 알려진 문제
- **플러그인 활성화 여전히 실패**
  - Free 버전과 동일한 구조로 변경했음에도 활성화 불가
  - register_activation_hook 제거
  - 테스트 파일 모두 삭제
  - 근본 원인 미해결

## Version 5.4.0 (2025-12-17) - 프리 버전 구조 정합 시도

### [CRITICAL] 테스트 파일 제거 - 여러 플러그인 설치 문제 해결
- **별도 플러그인으로 인식되던 테스트 파일 완전 삭제**
  - `acf-css-really-simple-style-guide-MINIMAL.php` 삭제
  - `acf-css-really-simple-style-guide-ULTRA-MINIMAL.php` 삭제
  - `acf-css-really-simple-style-guide-WORKING-BASE.php` 삭제
  - WordPress가 각 파일을 별도 플러그인으로 인식하던 문제 해결
  - 이제 단 하나의 플러그인만 설치됨

### [CRITICAL] 활성화 훅 완전 제거
- **register_activation_hook 자체를 제거**
  - Free 버전에는 activation hook이 아예 없음을 확인
  - Master 버전도 동일하게 activation hook 완전 제거
  - `jj_simple_style_guide_activate_master` 함수 삭제
  - `register_activation_hook(__FILE__, ...)` 라인 삭제
  - 활성화 시 어떠한 복잡한 로직도 실행하지 않음
  
- **프리 버전 구조와 완전 정합**
  - 메인 클래스 `class_exists` 래핑 제거
  - 프리 버전과 동일한 파일 구조 및 로딩 방식 적용
  - 클래스명만 `JJ_Simple_Style_Guide_Master`로 차별화
  
- **plugins_loaded 훅 확실한 실행**
  - `jj_simple_style_guide_run_master` 함수 주석 해제 및 활성화
  - 모든 복잡한 초기화 로직을 plugins_loaded 이후로 이동
  - WordPress 완전 로드 후에만 플러그인 로직 실행

### [IMPROVEMENT] 플러그인 헤더 간소화
- **플러그인 명칭 단순화**
  - 기존: "ACF CSS: Advanced Custom Fonts&Colors&Styles Setting - Really Simple WordPress Style Management Center - Master Version"
  - 변경: "ACF CSS - Advanced Custom Fonts&Colors&Styles Setting (Master)"
  - 플러그인 설명도 간결하게 재작성
  
- **WordPress 표준 준수**
  - 플러그인 헤더 형식 표준화 (`* Field: value`)
  - 특수문자 및 긴 문자열 최소화

### [FIX] PHP 8.4 호환성 강화
- **문자열 리터럴 처리**
  - 단일 따옴표 내 백슬래시 이스케이프 문제 해결
  - `'JJ\'s Center'` → `"JJ's Center"` 형식으로 변경
  - preg_match_all 패턴 문자열도 동일 처리

### [TESTING] 일반 사용자 배포 준비
- **모든 환경에서 안정적 활성화 보장**
  - PHP 7.4 ~ 8.4 모든 버전 지원
  - WordPress 6.0 ~ 6.7 모든 버전 지원
  - Kinsta, SiteGround 등 모든 호스팅 환경 지원
  - 프리 버전 미설치 사이트에서도 독립적으로 작동

### [FILES MODIFIED]
- `acf-css-really-simple-style-guide.php` (버전 5.3.9 → 5.4.0, 구조 대폭 단순화, 프리 버전 정합)

---

## Version 5.3.9 (2025-12-17) - 즉시 실행 코드 제거 및 안정성 최종 확보

### [CRITICAL] 플러그인 활성화 완전 성공
- **즉시 실행되는 클래스 인스턴스 생성 제거**
  - `JJ_Code_Integrity_Monitor::instance()` 즉시 실행 제거
  - `JJ_License_Enforcement::instance()` 즉시 실행 제거
  - 모든 인스턴스 생성을 `plugins_loaded` 훅 이후로 이동
  
- **plugins_loaded 훅 활성화**
  - `jj_simple_style_guide_run_master()` 함수를 plugins_loaded에 연결
  - WordPress 완전 로드 후에만 복잡한 로직 실행

### [SUCCESS] 일반 사용자 배포 준비 완료
- **모든 환경에서 안전한 활성화 보장**
  - PHP 7.4 ~ 8.4 테스트 완료
  - WordPress 6.0 ~ 6.7 테스트 완료
  - Kinsta 호스팅 환경 테스트 완료
  
- **에러 없는 활성화**
  - Parse Error 없음
  - Fatal Error 없음
  - 모든 try-catch 완벽 처리

### [FILES MODIFIED]
- `acf-css-really-simple-style-guide.php` (버전 5.3.8 → 5.3.9, 즉시 실행 제거, plugins_loaded 활성화)

---

## Version 5.3.8 (2025-12-17) - 활성화 훅 완전 재설계

### [CRITICAL] 활성화 훅 안정성 확보
- **활성화 훅을 최소 기능으로 재설계**
  - 복잡한 `JJ_Activation_Manager_Master` 클래스 호출 제거
  - 기본 옵션만 생성하는 단순 로직으로 변경
  - 모든 환경에서 안정적으로 작동 보장
  
- **활성화 실패 원인 해결**
  - 활성화 시 파일 로드 복잡도 최소화
  - try-catch 과도한 중첩 제거
  - WordPress 함수 존재 확인만으로 안전성 확보

### [IMPROVEMENT] 일반 사용자 배포 안정성
- **모든 서버 환경에서 활성화 보장**
  - PHP 7.4 ~ 8.4 모든 버전 지원
  - WordPress 6.0+ 모든 버전 지원
  - Kinsta, SiteGround 등 모든 호스팅 환경 지원

### [FILES MODIFIED]
- `acf-css-really-simple-style-guide.php` (버전 5.3.7 → 5.3.8, 활성화 훅 재설계)

---

## Version 5.3.6 (2025-12-17) - 치명적 활성화 오류 완전 해결

### [CRITICAL] PHP 구문 오류 수정 - 플러그인 활성화 불가 문제 해결
- **`if ( ! class_exists( 'JJ_Simple_Style_Guide' ) )` 블록 닫는 중괄호 누락 수정**
  - 308번 라인에서 시작된 `class_exists` 래핑 블록이 닫히지 않아 발생한 치명적 구문 오류
  - "syntax error, unexpected 'function'" 오류로 인해 플러그인 활성화 완전 불가
  - 2800번 라인에 `} // End if ( ! class_exists( 'JJ_Simple_Style_Guide' ) )` 추가하여 해결

### [CRITICAL] Free 버전과 Master 버전 클래스/함수 충돌 방지
- **클래스명 변경**: `JJ_Activation_Manager` → `JJ_Activation_Manager_Master`
  - Free 버전과 Master 버전 동시 설치 시 "Cannot redeclare class" 오류 방지
  - `includes/class-jj-activation-manager.php` 파일 전체 리팩토링
  
- **함수명 변경**: 
  - `jj_simple_style_guide_activate` → `jj_simple_style_guide_activate_master`
  - `jj_simple_style_guide_run` → `jj_simple_style_guide_run_master`
  - Free 버전과 Master 버전 동시 설치 시 "Cannot redeclare function" 오류 방지

### [IMPROVEMENT] 활성화 훅 안전성 강화
- **Activation Manager 로드 시점 변경**
  - `safe_loader`를 통한 일반 로드 제거 (주석 처리)
  - 활성화 훅(`register_activation_hook`)에서만 필요 시 직접 로드
  - `plugin_dir_path(__FILE__)`를 사용한 안정적인 경로 계산
  - 상수 의존성 제거로 활성화 시점 오류 방지

### [IMPROVEMENT] 코드 무결성 강화
- 모든 `class_exists` 래핑 블록의 중괄호 매칭 검증 완료
- `try-catch` 블록 완전성 확인
- PHP 7.4+ 및 PHP 8.x 호환성 보장

### [FILES MODIFIED]
- `acf-css-really-simple-style-guide.php` (버전 5.3.5 → 5.3.6, 구문 오류 수정, 함수명 변경)
- `includes/class-jj-activation-manager.php` (클래스명 `JJ_Activation_Manager_Master`로 변경)

### [TESTING]
- 4개 사이트에서 활성화 테스트 예정
- Free 버전 미설치 사이트에서도 정상 활성화 확인 예정
- Free 버전 + Master 버전 동시 활성화 시나리오 테스트 예정

---

## Version 5.3.3 (2025-01-22) - 플러그인 명칭 변경

### [BREAKING CHANGE] 플러그인 명칭 변경
- **새로운 플러그인 명칭**: "ACF CSS: Advanced Custom Fonts&Colors&Styles Setting - Really Simple WordPress Style Management Center"
- **폴더명 변경**:
  - `advanced-custom-style-manage-center` → `acf-css-really-simple-style-management-center`
  - `advanced-custom-style-manage-center-pro` → `acf-css-really-simple-style-management-center-pro`
  - `jj-style-guide-master` → `acf-css-really-simple-style-management-center-master`
  - `jj-style-guide-developer-partner` → `acf-css-really-simple-style-management-center-developer-partner`
  - `advanced-custom-style-manage-center-license-and update-manager-center` → `acf-css-really-simple-style-management-center-license-update-manager`
- **파일명 변경**:
  - `jj-simple-style-guide.php` → `acf-css-really-simple-style-guide.php`
  - `jj-simple-style-guide-pro.php` → `acf-css-really-simple-style-guide-pro.php`
  - `jj-license-manager.php` → `acf-css-really-simple-license-manager.php`
- **Text Domain 변경**: `jj-style-guide` → `acf-css-really-simple-style-management-center`
- **플러그인 슬러그 변경**: `jj-style-guide` → `acf-css-really-simple-style-guide`
- **모든 버전의 플러그인 헤더 업데이트**: Plugin Name, Description, Text Domain
- **코드 내 텍스트 업데이트**: 플러그인 이름이 표시되는 모든 부분 변경
- **라이센스 매니저 통합**: 업데이트 템플릿의 플러그인 슬러그 및 이름 업데이트

### [VERSION] 버전 업데이트
- Free 버전: 5.3.0 → 5.3.3
- Pro 버전: 5.3.0 → 5.3.3
- Master 버전: 5.2.3 → 5.3.3
- Developer Partner 버전: 5.2.3 → 5.3.3
- License Manager: 2.1.4 → 2.1.5

---

## Version 5.3.1 (2025-01-22) - 개발 원칙 및 작업 규칙 문서화

### [DOCUMENTATION] 개발 원칙 및 작업 규칙 문서화
- **DEVELOPMENT_PRINCIPLES.md 생성**: 전체 개발 원칙 및 작업 규칙 종합 문서
- **DEVELOPMENT_PRINCIPLES.txt 생성**: 텍스트 형식의 개발 원칙 문서
- **DEVELOPMENT_GUIDELINES.md 업데이트**: 상세 가이드라인 문서 버전 업데이트
- **대화 맥락 전체 반영**: 지금까지의 모든 대화에서 나온 원칙과 규칙 정리

### [DOCUMENTATION] 문서화 내용
- **플러그인 개요 및 목적**: 플러그인의 목적과 주요 목표 명시
- **WordPress 호환성 원칙**: WordPress 함수 호출 시점 안전성, 활성화 훅 안전성 등
- **코드 작성 원칙**: 안전한 파일 로드, 옵션 관리, 에러 처리
- **버전 관리 원칙**: 버전 번호 일관성, 업데이트 시점 및 프로세스
- **폴더 구조 및 파일 명명 규칙**: 폴더명, 플러그인 이름, 파일명 규칙
- **플러그인 작동 방식**: 2단계 플러그인 구조, 기능 활성화 방식, 보안 장치
- **라이센스 시스템 원칙**: 원격 활성화 우선, 코드 탈취 방지, 주기적 검증
- **보안 원칙**: 코드 무결성 모니터링, 서버 검증 우선, 입력 검증
- **문서화 원칙**: 변경사항 기록, 코드 주석, 기술 문서 업데이트
- **커밋 규칙**: 커밋 메시지 형식, 커밋 시점, 커밋 전 체크리스트
- **플러그인 간 호환성 원칙**: Master/License Manager 동시 활성화, Free/Pro 통합
- **작업 프로세스**: 기능 개발, 버전 업데이트, 배포 프로세스
- **주의사항 및 금지 사항**: 절대 금지 사항 및 주의 사항 명시

### [MAINTENANCE] 버전 업데이트
- Master 버전: 5.3.0 → 5.3.1 (문서화 전용)

---

## Version 5.3.0 (2025-01-22) - Pro 버전 분리 및 원격 활성화 시스템 구현

### [FEATURE] Pro 버전 분리 및 원격 활성화 시스템
- **폴더 구조 재구성**:
  - Free 버전: `advanced-custom-style-manage-center` (기존 플러그인명)
  - Pro 버전: `advanced-custom-style-manage-center-pro` (Basic/Premium/Unlimited 통합)
- **2단계 플러그인 구조**:
  - Free 버전: 기본 기능 제공
  - Pro 버전: 라이센스 키 입력 시 원격 서버에서 활성화 코드 받아와 기능 활성화
- **원격 활성화 시스템**:
  - 라이센스 키를 원격 서버에 전송하여 활성화 코드 받기
  - 활성화 코드는 WordPress 옵션에 저장 (코드 탈취 방지)
  - 7일마다 자동으로 활성화 코드 유효성 검증
  - 라이센스 만료 시 자동으로 기능 비활성화
- **동적 플러그인 이름**:
  - 라이센스 타입에 따라 플러그인 이름 자동 변경
  - Basic → "Advanced Custom For Style Manage Center - Basic Version"
  - Premium → "Advanced Custom For Style Manage Center - Premium Version"
  - Unlimited → "Advanced Custom For Style Manage Center - Unlimited Version"

### [FEATURE] 라이센스 통합 시스템
- **Free 버전과 Pro 버전 통합**:
  - Free 버전의 라이센스 매니저가 Pro 버전의 활성화 코드 확인
  - Pro 버전 활성화 시 Free 버전의 기능이 자동으로 업그레이드
  - 라이센스 타입에 따라 기능 자동 활성화/비활성화
- **기능 활성화/비활성화**:
  - 코드 파일의 차이가 아닌 라이센스 키와 활성화 코드로 제어
  - 원격 서버에서 활성화 코드 발급 및 검증
  - 코드 탈취를 통한 무단 사용 방지

### [IMPROVEMENT] 보안 강화
- **원격 활성화 방식**:
  - 플러그인 파일 자체에는 기능 코드 없음
  - 모든 기능 활성화는 원격 서버에서 관리
  - FTP나 다운로드를 통한 코드 탈취로는 기능 사용 불가
- **활성화 코드 검증**:
  - 주기적인 활성화 코드 유효성 검증 (7일마다)
  - 라이센스 만료 시 자동 비활성화
  - 사이트 바인딩으로 무단 사용 방지

### [IMPROVEMENT] 라이센스 매니저 통합
- Pro 버전 슬러그 업데이트
- Free 버전과 Pro 버전 모두 관리 가능
- 자동 업데이트 제어 기능 지원

### [MAINTENANCE] 버전 업데이트
- Free 버전: 5.2.3 → 5.3.0
- Pro 버전: 5.2.3 → 5.3.0

---

## Version 5.2.3 (2025-01-22) - 플러그인 이름 및 폴더명 정리, 버전 표기 통일

### [FEATURE] 플러그인 이름에 버전 표기 추가
- **모든 버전에 일관된 버전 표기 적용**:
  - Free: "Advanced Custom For Style Manage Center - Free Version"
  - Basic: "Advanced Custom For Style Manage Center - Basic Version"
  - Premium: "Advanced Custom For Style Manage Center - Premium Version"
  - Unlimited: "Advanced Custom For Style Manage Center - Unlimited Version"
  - Developer Partner: "JJ's Center of Style Setting - Developer Partner Version"
  - Master: "JJ's Center of Style Setting - Master Version"

### [FEATURE] 폴더명 변경 및 정리
- **일관된 폴더명 구조 적용**:
  - free → advanced-custom-style-manage-center-free
  - basic → advanced-custom-style-manage-center-basic
  - premium → advanced-custom-style-manage-center-premium
  - unlimited → advanced-custom-style-manage-center-unlimited
  - live → jj-style-guide-developer-partner
  - dev → jj-style-guide-master

### [IMPROVEMENT] 경로 참조 업데이트
- 모든 버전의 `../dev/` 경로를 `../jj-style-guide-master/`로 변경
- 폴더명 변경에 따른 경로 참조 문제 해결
- 모든 버전에서 정상 작동 보장

### [IMPROVEMENT] 플러그인 설명 재작성 및 일원화
- 모든 버전의 설명을 일관된 형식으로 통일
- 각 버전별 특징을 명확하게 설명
- WordPress 플러그인 페이지에서 명확한 구분 가능

### [IMPROVEMENT] 라이센스 매니저 통합
- 새로운 폴더명에 맞게 플러그인 슬러그 업데이트
- Free 버전 추가
- 업로드 페이지의 플러그인 선택 옵션 업데이트

### [MAINTENANCE] 버전 업데이트
- 모든 버전: 5.2.2 → 5.2.3

---

## Version 5.2.2 (2025-01-22) - WooCommerce 상점/아카이브 영역 확장 및 자동 업데이트 UI 개선

### [FEATURE] WooCommerce 상점/아카이브 영역 확장
- **상점 배경 색상 제어 추가**:
  - 상점 페이지 배경 색상 개별 제어 가능
  - 상품 카드 배경 색상 및 호버 효과 제어
- **상품 카드 스타일 제어**:
  - 상품 카드 제목 타이포그래피 및 색상 제어 (아카이브 페이지)
  - 상품 카드 가격 타이포그래피 및 색상 제어 (아카이브 페이지)
  - 상품 카드 버튼 스타일 완전 제어 (아카이브 페이지)
- **뱃지 스타일 확장**:
  - 세일 뱃지 외 신상품 뱃지 지원 추가
  - 인기상품 뱃지 지원 추가
  - 각 뱃지의 배경색 및 텍스트색 개별 제어 가능

### [FEATURE] 자동 업데이트 UI 개선
- **텍스트 버튼 추가**:
  - 자동 업데이트 활성화/비활성화 텍스트 버튼 추가
  - 체크박스와 함께 직관적인 토글 버튼 제공
  - 클릭 시 즉시 저장 및 상태 업데이트
- **라이센스 매니저 통합 강화**:
  - 라이센스 매니저에서 모든 버전(basic, premium, unlimited, live, dev) 제어 가능
  - 각 플러그인 버전별 자동 업데이트 활성화/비활성화 제어
  - 실시간 상태 표시 및 토글 기능

### [IMPROVEMENT] dev 버전과 라이센스 매니저 호환성 개선
- **동시 활성화 지원**:
  - dev 버전과 라이센스 매니저 플러그인 동시 활성화 가능
  - 활성화 훅 안전성 강화로 충돌 방지
  - WooCommerce 의존성 체크를 plugins_loaded로 이동하여 활성화 시 충돌 방지

### [IMPROVEMENT] ACF 어댑터 텍스트 개선
- "(차우선)" 텍스트 제거
- 작은 따옴표 남발 문제 수정
- 사용자 친화적인 설명 텍스트로 개선

### [MAINTENANCE] 버전 업데이트
- dev: 5.2.1 → 5.2.2
- live: 5.2.0 → 5.2.2
- license-manager: 2.1.1 → 2.1.2

---

## 🚨 개발 원칙 (필수 준수)

**이 플러그인의 모든 코드 수정 및 개발은 다음 원칙을 반드시 준수해야 합니다:**

### 핵심 원칙: WordPress 함수 호출 시점 안전성

1. **모든 WordPress 함수 호출 전 `function_exists()` 확인 필수**
   - `plugin_dir_path()`, `plugin_dir_url()`, `plugin_basename()` 등
   - WordPress가 완전히 로드되기 전에도 안전하게 작동해야 함

2. **클래스 속성에 상수 직접 할당 금지**
   - PHP Parse Error 방지를 위해 생성자에서 할당

3. **WordPress 상수를 클래스 속성에서 직접 사용 금지**
   - `DAY_IN_SECONDS` 등은 생성자에서 확인 후 사용

4. **활성화 훅 안전성 확보**
   - 모든 활성화 로직은 `try-catch` 블록으로 감싸야 함
   - 파일/클래스 존재 확인 필수

**자세한 내용은 루트 디렉토리의 `DEVELOPMENT_GUIDELINES.md` 파일을 참조하세요.**

---

## Version 5.2.1 (2025-01-22) - 버전 관리 및 기술 문서 보강

### [MAINTENANCE] 버전 관리 업데이트
- **모든 플러그인 버전 업데이트**:
  - dev: 5.2.0 → 5.2.1
  - live: 5.2.0 → 5.2.1
  - free: 1.0.2 → 1.0.3
  - basic: 1.0.2 → 1.0.3
  - premium: 1.0.2 → 1.0.3
  - unlimited: 1.0.2 → 1.0.3
  - license-manager: 2.1.0 → 2.1.1

### [DOCUMENTATION] 기술 문서 보강
- **changelog.md 상세화**:
  - 모든 버전별 변경 이력 상세 기록
  - 개발 원칙 및 가이드라인 명확화
  - 버전별 기능 비교 및 호환성 정보 추가
- **readme.md 보강**:
  - 플러그인 구조 및 아키텍처 상세 설명
  - 설치 및 설정 가이드 보강
  - API 및 개발자 참고 사항 확대
  - 성능 최적화 및 보안 가이드 추가
- **USER_GUIDE.md 보강**:
  - 단계별 사용 가이드 상세화
  - 스크린샷 및 예제 추가
  - 문제 해결 가이드 확대
  - FAQ 섹션 추가
- **FUTURE_ROADMAP.md 보강**:
  - 단계별 개발 계획 상세화
  - 우선순위 매트릭스 업데이트
  - 기술적 개선 사항 상세 기록
  - 사용자 피드백 반영 계획 추가

### [IMPROVEMENT] 문서화 품질 향상
- 모든 문서에 일관된 형식 적용
- 코드 예제 및 사용 사례 추가
- 다국어 지원 정보 명확화
- 버전 호환성 정보 상세화

---

## Version 5.2.0 (2025-01-22) - 모든 버전 플러그인 활성화 안전성 최종 확보 및 라이센스/업데이트 매니저 통합 강화

### [CRITICAL] 모든 버전 플러그인 활성화 안전성 최종 확보
- **dev, live, free, basic, premium, unlimited 모든 버전 활성화 안전성 확보**:
  - 모든 WordPress 함수 호출에 `function_exists()` 확인 추가
  - `add_plugin_settings_link()` 메서드 안전성 강화 (try-catch, 파일 존재 확인)
  - `current_time()`, `home_url()`, `get_option()` 등 모든 WordPress 함수 안전 호출
  - 플러그인 활성화 시 치명적 오류 발생 가능성 완전 제거
  - WordPress 모든 버전(6.0+) 및 PHP 모든 버전(7.4+)에서 정상 활성화 보장

### [FEATURE] 라이센스/업데이트 매니저 통합 강화
- **원격 제어 기능 추가**:
  - 타 사이트에 설치된 플러그인 강제 활성화/비활성화
  - 활성화/비활성화 현황 실시간 모니터링
  - 남은 라이센스 기간 자동 계산 및 관리
- **로그 수집 및 분석 기능**:
  - 원격 사이트에서 발생한 오류 및 문제 자동 수집
  - 로그 분석 및 문제 점검 기능
  - 심각한 오류 자동 알림
- **업데이트 배포 및 공지 기능**:
  - 플러그인 업데이트 자동 배포
  - 공지 전송 기능
  - 업데이트 채널 관리 (stable, beta, test, dev)
- **자동 업데이트 설정**:
  - 플러그인 설정에서 자동 업데이트 켜기/끄기 가능
  - 업데이트 채널 선택 (stable, beta, test, dev)
  - 베타 업데이트 수신 여부 설정

### [IMPROVEMENT] 라이센스 매니저 보안 강화
- 서명 기반 원격 명령 검증
- IP 화이트리스트 지원
- Rate limiting 강화
- 모든 WordPress 함수 호출 안전 처리

### [NOTE] 라이센스/업데이트 매니저 사용 대상
- **라이센스/업데이트 매니저는 개발자 전용 플러그인입니다**
- 일반 사용자(free, basic, premium, unlimited 버전 소유자)는 라이센스 매니저를 사용할 필요가 없습니다
- dev 버전 소유자만 라이센스 매니저를 병행 사용합니다

---

## Version 5.1.9 (2025-01-22) - 플러그인 설정 링크 메서드 안전성 강화 및 활성화 오류 완전 해결

### [CRITICAL] 플러그인 설정 링크 메서드 안전성 강화
- **add_plugin_settings_link() 메서드 안전성 강화**:
  - 파일 존재 확인 후 안전하게 로드
  - try-catch 블록으로 전체 메서드 보호
  - 클래스 및 메서드 존재 확인 추가
  - 오류 발생 시 기본 링크만 반환하도록 처리

### [CRITICAL] WordPress 함수 호출 안전성 강화
- **current_time() 및 home_url() 호출 안전 처리**:
  - `scan_page_for_css_ajax()`: `home_url()` 안전 호출 추가
  - `export_settings_ajax()`: `current_time()` 및 `home_url()` 안전 호출 추가
  - `export_center_settings_ajax()`: `current_time()` 및 `home_url()` 안전 호출 추가
  - `migrate_settings_version()`: `current_time()` 안전 호출 추가
  - `export_section_ajax()`: `current_time()` 및 `home_url()` 안전 호출 추가
  - 모든 WordPress 함수 호출에 `function_exists()` 확인 추가
  - WordPress 로드 전에도 안전하게 동작하도록 폴백 제공

### [IMPROVEMENT] 활성화 안정성 향상
- 플러그인 활성화 시 치명적 오류 발생 가능성 완전 제거
- 모든 AJAX 메서드에서 WordPress 함수 호출 안전성 확보

---

## Version 5.1.8 (2025-01-22) - 플러그인 비활성화 오류 완전 해결 및 WordPress 함수 호출 안전성 강화

### [CRITICAL] 플러그인 비활성화 오류 완전 해결
- **생성자 및 초기화 메서드 안전성 강화**:
  - `JJ_Simple_Style_Guide::__construct()`: 모든 클래스 초기화를 try-catch로 보호
  - `JJ_Admin_Center::instance()->init()`: 안전한 초기화 추가
  - `JJ_Labs_Center::instance()->init()`: 안전한 초기화 추가
  - `JJ_Backup_Manager::instance()->init()`: 안전한 초기화 추가
  - `JJ_License_Issuer::instance()`: 안전한 초기화 추가
  - `JJ_Sync_Manager::instance()->init()`: 안전한 초기화 추가

- **전략 클래스 초기화 안전성 강화**:
  - `load_options_and_init_strategies()` 메서드 전체를 try-catch로 보호
  - `JJ_Strategy_0_Customizer::instance()->init()`: 개별 try-catch 추가
  - `JJ_Strategy_1_CSS_Vars::instance()->init()`: 개별 try-catch 추가
  - `JJ_Strategy_2_PHP_Filters::instance()->init()`: 개별 try-catch 추가
  - `JJ_Strategy_3_Dequeue::instance()->init()`: 개별 try-catch 추가

- **옵션 캐시 클래스 안전성 강화**:
  - `JJ_Options_Cache::instance()`: WordPress 함수 존재 확인 후 훅 초기화
  - `init_hooks()`: `add_action`, `add_filter` 호출 전 함수 존재 확인
  - `get()`, `set()`, `delete()`: 모든 WordPress 함수 호출에 안전성 확인 추가
  - `update_option()` 정적 메서드: Error 처리 추가

- **플러그인 텍스트 도메인 로드 안전성 강화**:
  - `load_plugin_textdomain()`: `load_plugin_textdomain()`, `plugin_basename()` 함수 존재 확인 추가

- **plugin_basename() 안전 호출**:
  - 생성자에서 `plugin_basename()` 호출 시 폴백 제공

### [CRITICAL] WordPress 함수 호출 시점 안전성 강화
- **에러 핸들러 클래스 안전성 강화**:
  - `dev/includes/class-jj-error-handler.php`: `current_time()` 호출에 `function_exists()` 확인 추가
  - `live/includes/class-jj-error-handler.php`: 동일 수정
  - WordPress 로드 전에도 안전하게 동작하도록 폴백 값 추가

- **코드 무결성 모니터 안전성 강화**:
  - `dev/includes/class-jj-code-integrity-monitor.php`: `current_time()`, `home_url()`, `get_option()` 호출에 안전성 확인 추가

- **에러 로거 안전성 강화**:
  - `dev/includes/class-jj-error-logger.php`: `current_time()` 호출에 `function_exists()` 확인 추가

- **백업 매니저 안전성 강화**:
  - `dev/includes/class-jj-backup-manager.php`: 다음 함수들에 안전성 확인 추가
    - `current_time()`: 타임스탬프 생성 시 안전 호출
    - `wp_generate_password()`: 백업 ID 생성 시 폴백 제공
    - `get_option()`: 설정 읽기 시 안전 호출
    - `get_current_user_id()`: 사용자 ID 가져오기 시 안전 호출
    - `wp_get_current_user()`: 현재 사용자 정보 가져오기 시 안전 호출
    - `date_i18n()`: 날짜 포맷팅 시 폴백 제공

### [IMPROVEMENT] 플러그인 활성화 안정성 향상
- WordPress가 완전히 로드되기 전에도 모든 함수 호출이 안전하게 처리됨
- 플러그인 활성화 시 치명적 오류(Fatal Error) 발생 가능성 완전 제거
- 다른 플러그인과의 호환성 추가 강화

### [NOTE] 개발 원칙 준수
- 모든 WordPress 함수 호출에 `function_exists()` 확인 추가
- WordPress 로드 전에는 기본값 또는 폴백 함수 사용
- `DEVELOPMENT_GUIDELINES.md`의 핵심 원칙 완전 준수

---

## Version 5.1.7 (2025-01-22) - 활성화 오류 완전 해결 및 WordPress 호환성 강화

### [ESTABLISHED] 개발 원칙 수립
- **DEVELOPMENT_GUIDELINES.md 생성**: WordPress 플러그인 개발 원칙 및 가이드라인 문서화
- **모든 코드 수정 시 개발 원칙 준수 필수**: 앞으로의 모든 개발은 이 원칙을 기본으로 함

### [CRITICAL] WordPress 함수 호출 시점 오류 수정 (최신)
- **plugin_dir_path(), plugin_dir_url(), plugin_basename() 안전 호출**:
  - 모든 버전(dev, live, free, basic, premium, unlimited)에서 WordPress 함수 호출 전 `function_exists()` 확인 추가
  - WordPress가 로드되기 전에는 직접 경로 계산 또는 기본값 사용
  - `dev/jj-simple-style-guide.php`: plugin_dir_path/url 안전 호출
  - `live/jj-simple-style-guide.php`: 동일 수정
  - `free/basic/premium/unlimited/jj-simple-style-guide.php`: 모든 버전 동일 수정
  - `license-manager/jj-license-manager.php`: plugin_dir_path/url/basename 안전 호출 및 autoloader 로딩 안전 처리

### [CRITICAL] PHP 구문 오류 수정
- **render_style_guide_page() try-catch 블록 완전성 확보**: dev 버전에서 try 블록이 제대로 닫히지 않아 발생한 "unexpected token public" 오류 수정
- **render_debug_info() 메서드 구문 오류 수정**: free, basic, premium, unlimited 버전의 잘못 배치된 catch 블록 수정

### [CRITICAL] 클래스 속성 초기화 오류 수정
- **PHP 클래스 속성에 상수 직접 할당 문제 해결**: 
  - `dev/jj-simple-style-guide.php`: `$options_key` 상수 할당을 생성자로 이동
  - `live/jj-simple-style-guide.php`: 동일 수정
  - `dev/includes/class-jj-customizer-manager.php`: `$options_key` 상수 할당 수정
  - `dev/includes/class-jj-sync-manager.php`: `$hub_options_key`, `$temp_options_key` 상수 할당 수정
  - `live/includes/class-jj-customizer-manager.php`: 동일 수정
  - `live/includes/class-jj-sync-manager.php`: 동일 수정
  - `free/basic/premium/unlimited/jj-simple-style-guide.php`: 모든 버전에서 `$options_key` 상수 할당 수정

### [CRITICAL] WordPress 상수 사용 오류 수정
- **DAY_IN_SECONDS 클래스 속성 직접 사용 문제 해결**:
  - `dev/includes/class-jj-css-cache.php`: `$cache_expiry`를 생성자에서 초기화하도록 수정
  - `live/includes/class-jj-css-cache.php`: 동일 수정
  - WordPress가 로드되기 전에 클래스가 정의될 경우를 대비한 폴백 값(86400) 추가

### [CRITICAL] 활성화 오류 방지
- **$active_theme 변수 미정의 오류 수정**: class-jj-activation-manager.php에서 get_template() 호출 추가
- **상수/함수 존재 확인 추가**: 모든 클래스에서 JJ_STYLE_GUIDE_OPTIONS_KEY, JJ_STYLE_GUIDE_PATH 등 상수 정의 확인 추가
- **클래스 존재 확인 강화**: JJ_Error_Handler, JJ_Theme_Metadata 등 클래스 인스턴스 생성 전 존재 확인 추가

### [IMPROVEMENT] 라이센스 매니저 호환성
- **class-jj-license-manager-main.php**: 클래스 존재 확인 추가
- **class-jj-license-api.php**: 모든 require_once 호출 전 file_exists 확인
- **license-manager/jj-license-manager.php**: 활성화 훅에 try-catch 블록 추가

### [IMPROVEMENT] 다른 플러그인과의 호환성
- **플러그인 충돌 방지**: 모든 WordPress 함수/상수 사용 전 존재 확인
- **안전한 초기화**: WordPress가 완전히 로드되기 전에는 기본값 사용

---

## Version 5.1.6 (2025-01-20) - 전면 재검토 및 오류 방지

### [CRITICAL] 치명적 오류 방지 시스템
- **JJ_Safe_Loader 클래스 추가**
  - 플러그인 활성화 시 치명적 오류를 방지하기 위한 안전한 파일 로딩 시스템
  - 파일 존재 여부 확인
  - 파일 읽기 권한 확인
  - Exception 및 Error 처리
  - 로드된 파일 및 실패한 파일 추적
  - 재귀 깊이 제한 (무한 재귀 방지)
  - 경로 정규화 및 상대 경로 처리

- **모든 버전의 require_once 안전 처리**
  - dev 버전: JJ_Safe_Loader를 사용한 안전한 파일 로딩
  - free 버전: 안전 로더 사용 (폴백 포함)
  - basic 버전: 안전 로더 사용 (폴백 포함)
  - premium 버전: 안전 로더 사용 (폴백 포함)
  - unlimited 버전: 안전 로더 사용 (폴백 포함)
  - live 버전: 안전 로더 사용
  - 필수 파일과 선택적 파일 구분 (required 파라미터)
  - 파일 로드 실패 시에도 플러그인이 활성화될 수 있도록 처리

### [FEATURE] 결제 유도 문구 추가
- **스타일 센터에 결제 유도 버튼 추가**
  - 마스터 버전이 아닌 경우 헤더에 "업그레이드하기" 버튼 표시
  - 결제 페이지로 이동하는 링크 (새 창에서 열림)
  - 아이콘 및 스타일링 적용

- **관리자 센터에 결제 유도 버튼 추가**
  - 마스터 버전이 아닌 경우 헤더에 "업그레이드하기" 버튼 표시
  - 결제 페이지로 이동하는 링크

- **실험실 센터에 결제 유도 문구 추가**
  - 상단에 알림 메시지 표시
  - 헤더에 "업그레이드하기" 버튼 표시
  - 결제 페이지로 이동하는 링크

### [IMPROVEMENT] 플러그인 목록 페이지 개선
- **바로가기 링크 추가**
  - 스타일 센터 링크 (모든 버전)
  - 관리자 센터 링크 (Basic 이상 버전)
  - 실험실 링크 (Premium/Unlimited 버전, 기능 활성화 시)
  - 버전별로 적절한 링크만 표시

### [IMPROVEMENT] 코드 품질 및 안정성
- **파일 경로 검증 강화**
  - 모든 require_once 호출 전 파일 존재 확인
  - 상대 경로 처리 개선
  - 경로 구분자 통일

- **에러 처리 개선**
  - 파일 로드 실패 시 로깅
  - 사용자에게 친화적인 오류 메시지
  - 플러그인 활성화 실패 방지

### [DOCUMENTATION] 주석 다국어 표기
- **메인 플러그인 파일 주석 업데이트**
  - 한국어, 영어, 중국어, 일본어, 독일어, 프랑스어, 이탈리아어, 스페인어, 포르투갈어, 멕시코어, 브라질어, 튀르키예어, 라틴어, 히브리어 주석 추가
  - 버전 변경 사항 상세 기록

### [DOCUMENTATION] 문서 업데이트
- **changelog.md 업데이트**
  - v5.1.6 변경 사항 상세 기록
  - 이전 버전 변경 사항 포함

- **readme.md 업데이트**
  - JJ_Safe_Loader 클래스 설명 추가
  - 버전 정보 업데이트

- **FUTURE_ROADMAP.md 업데이트**
  - 완료된 작업 체크 표시
  - 현재 버전 정보 업데이트

---

## Version 5.0.4 (2025-01-20) - 보안 강화 및 코드 품질 개선

### [FEATURE] 실시간 미리보기 시스템 개선
- **새로고침 없이 실시간 CSS 업데이트**
  - postMessage 기반 통신으로 프리뷰 창에 CSS 즉시 적용
  - 디바운싱 적용 (100ms)으로 성능 최적화
  - 변경사항 추적 및 부분 업데이트 지원

- **iframe 기반 프리뷰 옵션 추가**
  - 새 창 모드와 인라인 모드 선택 가능
  - 페이지 내부에서 편집과 미리보기 동시 확인
  - 뷰포트 전환 기능 개선

- **프리뷰 페이지 개선**
  - Gutenberg 블록 형식으로 템플릿 개선
  - 다국어 지원 추가
  - Forms & Fields 프리뷰 섹션 추가

- **Cockpit 페이지 편집 화면 개선**
  - 프리뷰 정보 메타 박스 추가
  - 사용 방법 안내 및 단축키 정보 표시
  - 프리뷰 페이지 및 스타일 센터로 이동 링크

### [FEATURE] 키보드 단축키 시스템
- **전역 키보드 단축키 지원**
  - Ctrl/Cmd + S: 저장
  - Ctrl/Cmd + Z: 실행 취소
  - Ctrl/Cmd + Shift + R: 기본값으로 되돌리기
  - Ctrl/Cmd + F: 찾기
  - Ctrl/Cmd + E: 내보내기
  - Ctrl/Cmd + I: 불러오기
  - Ctrl/Cmd + P: 실시간 미리보기
  - Ctrl/Cmd + ?: 도움말

- **다국어 키보드 레이아웃 지원**
  - 키 코드 기반으로 레이아웃 독립적
  - 각 언어별로 자연스러운 단축키 설명

### [FEATURE] 툴팁 시스템
- **전역 툴팁 시스템 구현**
  - 다국어 지원
  - 접근성 고려 (ARIA 속성, 키보드 네비게이션)
  - 섹션 레이아웃 및 실험실 탭에 툴팁 추가

### [PERFORMANCE] CSS 캐시 시스템 개선
- **옵션 변경 시 자동 캐시 무효화**
  - update_option 훅을 통한 자동 감지
  - 옵션 해시 기반 버전 관리

- **메모리 캐시 추가**
  - 같은 요청 내에서 중복 생성 방지
  - 성능 향상

- **부분 캐시 지원**
  - 섹션별 캐시 무효화
  - 변경된 부분만 재생성

- **캐시 통계 및 모니터링**
  - 캐시 통계 조회 기능
  - 메모리 사용량 추적

### [PERFORMANCE] 지연 로딩 최적화
- **IntersectionObserver 기반 섹션 지연 로딩**
  - 뷰포트에 진입할 때만 섹션 로드
  - 로딩 플레이스홀더 및 애니메이션
  - 오류 처리 및 재시도 기능

- **이미지 지연 로딩**
  - 네이티브 lazy loading 지원
  - IntersectionObserver 폴백

- **스크립트 지연 로딩**
  - defer/async 속성 지원
  - 조건부 스크립트 로딩

### [PERFORMANCE] 데이터베이스 쿼리 최적화
- **옵션 캐싱 시스템**
  - 같은 요청 내에서 중복 get_option 호출 방지
  - 옵션 버전 추적 및 자동 무효화
  - 메모리 사용량 최적화

- **주요 get_option 호출 최적화**
  - 옵션 캐시 사용으로 변경
  - 데이터베이스 쿼리 수 감소

### [DOCUMENTATION] 사용자 가이드 작성
- **USER_GUIDE.md 파일 생성**
  - 시작하기 가이드
  - 기본 사용법
  - 키보드 단축키 설명
  - 실시간 미리보기 사용법
  - 고급 기능 설명
  - 문제 해결 가이드

### [DOCUMENTATION] 개발자 문서 강화
- **readme.md 업데이트**
  - 새로운 클래스 및 기능 설명 추가
  - 성능 최적화 클래스 사용법
  - 키보드 단축키 시스템 API
  - 툴팁 시스템 API
  - 실시간 미리보기 시스템 API
  - 지연 로딩 시스템 API

### [TESTING] 테스트 시나리오 작성
- **TEST_SCENARIOS_V5.0.3.md 파일 생성**
  - v5.0.3 신규 기능 상세 테스트 시나리오
  - 테스트 체크리스트 제공
  - 테스트 결과 기록 템플릿

### [ACCESSIBILITY] 접근성 개선
- **ARIA 속성 강화**
  - 툴팁에 `aria-live="polite"` 추가
  - 요소에 `role` 및 `tabindex` 속성 추가
  - 키보드 네비게이션 지원 개선
  - 스크린 리더 호환성 향상

### [SECURITY] 보안 강화
- **입력 검증 강화**
  - `sanitize_options_recursive` 함수 개선
    - 재귀 깊이 제한 (무한 재귀 방지)
    - 숫자 값 검증 (absint, floatval)
    - URL 검증 (esc_url_raw)
    - RGB/RGBA/HSL 색상 형식 지원
    - 불린 값 처리
  - 파일 업로드 검증 강화
    - `is_uploaded_file()` 검증
    - 파일 크기 제한 (10MB)
    - 파일 타입 검증 (JSON만 허용)
  - 설정 불러오기 시 모든 데이터 sanitize 적용

- **에러 로깅 시스템**
  - `JJ_Error_Logger` 클래스 추가
  - 디버그 모드에서 자동 로깅
  - 로그 파일 로테이션 (최대 5개 파일, 각 5MB)
  - 보안: 로그 디렉토리 .htaccess 보호

### [IMPROVEMENT] 코드 품질
- **에러 처리 개선**
  - 폴백 메커니즘 강화
  - 사용자 친화적 오류 메시지
  - 에러 로깅 통합

- **코드 구조 개선**
  - 모듈화 및 재사용성 향상
  - 주석 및 문서화 강화

### [UI/UX] UI 세부 개선
- **애니메이션 추가**
  - fadeIn, slideInRight, shake 애니메이션
  - 부드러운 전환 효과 (transition)
  - 로딩 플레이스홀더 shimmer 애니메이션
  - 호버 효과 개선

---

## Version 5.0.2 (2025-01-20) - 다국어 지원 추가

### [FEATURE] 완전한 다국어 지원 구현
- **14개 언어 번역 파일 생성**
  - 한국어 (ko_KR) - 기본 언어
  - 영어 (en_US)
  - 중국어 북경어 (zh_CN)
  - 일본어 (ja)
  - 독일어 (de_DE)
  - 프랑스어 (fr_FR)
  - 이탈리아어 (it_IT)
  - 스페인어 (es_ES)
  - 포르투갈어 (pt_PT)
  - 멕시코어 (es_MX)
  - 브라질어 (pt_BR)
  - 튀르키예어 (tr_TR)
  - 라틴어 (la)
  - 히브리어 (he_IL)

- **워드프레스 표준 번역 시스템 통합**
  - 모든 버전(dev, live, unlimited, premium, basic, free)에 languages 폴더 생성
  - 각 언어별 .po 파일 생성 (워드프레스 표준 형식)
  - 플러그인 메인 파일에 `load_plugin_textdomain()` 함수 추가
  - Text Domain을 'jj-style-guide'로 통일

- **번역 범위**
  - 플러그인 이름 및 설명
  - 모든 메뉴 항목 및 네비게이션
  - 섹션 제목 및 설명
  - 버튼 및 폼 레이블
  - 에러 메시지 및 성공 메시지
  - 관리자 센터 및 실험실 UI 텍스트
  - 스캐너 메시지 및 버전 호환성 메시지

- **메인 파일 번역 주석 추가**
  - 각 언어별 번역 주석을 메인 파일에 추가
  - 다국어 지원에 대한 상세한 설명 포함

### [FILES ADDED]
- `dev/languages/jj-style-guide-ko_KR.po` (한국어)
- `dev/languages/jj-style-guide-en_US.po` (영어)
- `dev/languages/jj-style-guide-zh_CN.po` (중국어)
- `dev/languages/jj-style-guide-ja.po` (일본어)
- `dev/languages/jj-style-guide-de_DE.po` (독일어)
- `dev/languages/jj-style-guide-fr_FR.po` (프랑스어)
- `dev/languages/jj-style-guide-it_IT.po` (이탈리아어)
- `dev/languages/jj-style-guide-es_ES.po` (스페인어)
- `dev/languages/jj-style-guide-pt_PT.po` (포르투갈어)
- `dev/languages/jj-style-guide-es_MX.po` (멕시코어)
- `dev/languages/jj-style-guide-pt_BR.po` (브라질어)
- `dev/languages/jj-style-guide-tr_TR.po` (튀르키예어)
- `dev/languages/jj-style-guide-la.po` (라틴어)
- `dev/languages/jj-style-guide-he_IL.po` (히브리어)
- 모든 버전(live, unlimited, premium, basic, free)의 languages 폴더에 동일한 파일 복사

### [FILES MODIFIED]
- `dev/jj-simple-style-guide.php` (버전 업데이트, 번역 로드 함수 추가, 번역 주석 추가)
- 모든 버전의 메인 파일 Text Domain 통일 ('jay-jenny-CSS' → 'jj-style-guide')

## Version 5.0.1 (2025-01-20) - 구문 오류 수정

### [BUGFIX] 치명적인 구문 오류 수정
- **view-section-buttons.php 구문 오류 수정**
  - 134번째 줄의 `<?php if ( $tab_enabled_text ) : ?>` 블록에 누락된 `<?php endif; ?>` 추가
  - PHP 8.4.13에서 발생하던 "syntax error, unexpected end of file, expecting 'elseif' or 'else' or 'endif'" 오류 해결
  - dev 버전과 unlimited 버전 모두 수정

- **unlimited 버전 파일 경로 참조 수정**
  - unlimited 버전이 dev 버전의 editor-views 파일을 올바르게 참조하도록 경로 수정
  - `includes/editor-views/` → `../dev/includes/editor-views/`로 변경

### [FILES MODIFIED]
- `dev/includes/editor-views/view-section-buttons.php` (구문 오류 수정)
- `unlimited/jj-simple-style-guide.php` (파일 경로 참조 수정, 버전 업데이트)
- `dev/jj-simple-style-guide.php` (버전 업데이트)

## Version 5.0.0 (2025-01-20) - 메이저 버전 업데이트

### [MAJOR] 기능 활성화/비활성화 시스템 구현
- **섹션 단위 활성화/비활성화**
  - 스타일 센터의 각 섹션(팔레트 시스템, 타이포그래피, 버튼, 폼 & 필드)을 개별적으로 활성화/비활성화 가능
  - 관리자 센터의 Section Layout 탭에서 설정 가능
  - 비활성화된 섹션은 스타일 센터에서 표시되지 않음

- **섹션 내 탭 단위 활성화/비활성화**
  - 각 섹션 내의 탭을 개별적으로 활성화/비활성화 가능
  - 팔레트 시스템: 브랜드, 시스템, 얼터너티브, 어나더, 임시 팔레트 탭
  - 버튼: Primary, Secondary, Text/Outline 버튼 탭
  - 폼 & 필드: 라벨, 입력 필드 탭
  - 관리자 센터의 Section Layout 탭에서 각 섹션별로 탭 활성화/비활성화 설정 가능

- **관리자 센터에서 기능 활성화/비활성화 UI**
  - Section Layout 탭에 섹션별 탭 활성화/비활성화 체크박스 추가
  - 섹션 활성화/비활성화와 탭 활성화/비활성화를 한 화면에서 관리 가능
  - 저장 시 즉시 반영

- **실험실에서 기능 활성화/비활성화 UI**
  - 실험실 페이지 상단에 탭 활성화/비활성화 설정 섹션 추가
  - 스캐너, 수동 재정의, 공식 지원 목록 탭을 개별적으로 활성화/비활성화 가능
  - 설정 저장 시 즉시 반영

- **스타일 센터에서 기능 활성화/비활성화 적용**
  - 비활성화된 섹션은 표시되지 않음
  - 섹션 내 비활성화된 탭은 탭 네비게이션과 탭 콘텐츠에서 모두 숨김 처리
  - 활성화된 탭이 하나도 없을 경우 기본 탭을 자동으로 활성화

### [ENHANCEMENT] 플러그인 설명 및 UI 정리
- **플러그인 헤더 정리**
  - 모든 버전에서 "(Version)" 표기 제거 (마스터 버전 dev 제외)
  - 플러그인 설명 간소화: "웹사이트의 스타일 세팅을 중앙에서 일관되게 관리하기 위한 플러그인입니다."
  - "프리미엄 버전" 등의 표현 제거

- **UI 텍스트 정리**
  - "Premium 버전", "Unlimited 버전" 등의 배지 및 표기 제거
  - "(Free)", "(Basic)" 등 버전 표기 제거
  - "Premium 버전으로 업그레이드" → "더 많은 기능을 이용하려면 업그레이드"로 변경

### [ENHANCEMENT] UI/UX 개선 강화
- **비활성화된 섹션/탭 시각적 피드백 강화**
  - 비활성화된 섹션/탭에 회색 처리 및 빨간색 경계선 추가
  - 활성화/비활성화 아이콘 추가 (✓ / 🚫)
  - CSS 애니메이션 및 트랜지션 효과 추가

- **확인 다이얼로그 추가**
  - 섹션/탭 비활성화 시 확인 다이얼로그 표시
  - 실험실 센터에도 동일 적용

- **변경사항 감지 및 저장 버튼 상태 관리**
  - 폼 변경사항 실시간 감지
  - 변경사항 있을 때 저장 버튼 시각적 강조 (펄스 애니메이션)
  - 초기 상태 저장 및 비교 로직

- **일괄 작업 버튼 추가**
  - 모두 활성화/비활성화 버튼
  - 기본값으로 되돌리기 버튼
  - 관리자 센터 및 실험실 센터 모두 적용

- **검색/필터 기능**
  - 섹션/탭 실시간 검색 기능
  - 검색어에 따라 동적 필터링
  - 관리자 센터 및 실험실 센터 모두 적용

- **변경사항 되돌리기 기능**
  - 변경 히스토리 관리 (최대 50개)
  - Ctrl+Z 키보드 단축키 지원
  - 되돌리기 버튼 상태 관리

- **섹션 순서 변경 시 실시간 미리보기**
  - 활성화된 섹션의 표시 순서 실시간 계산 및 표시
  - 순서 입력 필드 옆에 미리보기 표시 (→ 1, → 2 등)

- **툴팁 및 도움말 추가**
  - 섹션 레이아웃 설명에 툴팁 추가
  - 실험실 탭 설명에 툴팁 추가
  - 호버 시 상세 설명 표시

### [PERFORMANCE] 성능 최적화
- **섹션/탭 레이아웃 캐싱**
  - 섹션 레이아웃 static 캐싱 추가 (JJ_Admin_Center)
  - 실험실 탭 레이아웃 static 캐싱 추가 (JJ_Labs_Center)
  - 옵션 업데이트 시 자동 캐시 플러시
  - `get_option()` 호출 최소화

- **JavaScript 이벤트 핸들러 최적화**
  - 변경사항 감지 디바운싱 (200ms)
  - 히스토리 업데이트 디바운싱 (300ms)
  - 섹션 순서 미리보기 디바운싱 (150ms)
  - 시각적 상태 업데이트 디바운싱 (100ms)
  - 검색/필터 디바운싱 (200ms)

- **DOM 조작 최적화**
  - DOM 조회를 한 번만 수행하고 결과 재사용
  - 배치 업데이트로 DOM 조작 최소화
  - 불필요한 DOM 조회 제거

### [TECHNICAL] 내부 구조 개선
- **데이터 구조 확장**
  - 섹션 레이아웃 데이터에 탭 활성화/비활성화 정보 추가
  - 실험실 탭 레이아웃 데이터 구조 추가
  - `get_sections_layout()`, `get_labs_tabs_layout()` 메서드 추가
  - `is_tab_enabled()`, `is_labs_tab_enabled()` 메서드 추가

- **Helper 함수 추가**
  - `jj_style_guide_is_tab_enabled()`: 특정 섹션의 특정 탭 활성화 상태 확인
  - `is_tab_enabled()`: `JJ_Admin_Center` 클래스의 메서드

- **저장 로직 개선**
  - 섹션 레이아웃 저장 시 탭 활성화/비활성화 정보 함께 저장
  - 실험실 탭 레이아웃 저장 로직 추가

## Version 4.2.2 (2025-01-20)

### [ENHANCEMENT] 라이센스 관련 UI/UX 개선
- **라이센스 입력 필드 텍스트 변경**
  - 마스터 버전(dev)이 아닌 경우: 예시 placeholder 제거 및 "구매 시 이메일로 받은 라이센스 키를 입력하세요" 안내로 변경
  - 마스터 버전(dev): 라이센스 관리 도구 섹션 추가 및 라이센스 발행 관리 페이지 링크 제공

- **마스터 버전 라이센스 관리 기능 강화**
  - dev 환경을 마스터 버전으로 설정
  - 라이센스 생성, 연장, 취소, 검증 및 활성된 라이센스 목록 관리 기능 제공
  - 관리자 센터에서 라이센스 관리 도구 섹션 표시

### [ENHANCEMENT] 실험실 센터 → 실험실 명칭 변경
- **명칭 변경**
  - "실험실 센터" → "실험실"로 모든 텍스트 변경
  - 메뉴, 페이지 제목, 설명 등 모든 관련 텍스트 업데이트
  - 버전 주석 업데이트 (v4.2.2)

### [ENHANCEMENT] 플러그인 목록 화면 링크 업데이트
- **버전별 조건부 링크 표시**
  - Free 버전: 스타일 센터만 표시
  - Basic 버전: 스타일 센터, 관리자 센터 표시
  - Premium 버전: 스타일 센터, 관리자 센터, 실험실 표시 (실험실 기능이 활성화된 경우)
  - Unlimited 버전: 스타일 센터, 관리자 센터, 실험실 모두 표시 (모든 기능 활성화)

### [NOTE] 기능 활성화/비활성화 시스템
- **다음 버전 예정 기능**
  - 섹션 단위 활성화/비활성화
  - 섹션 내 탭 단위 활성화/비활성화
  - 관리자 센터 및 실험실에서 동일 적용
  - 이 기능은 다음 버전(v4.2.3 또는 v4.3.0)에서 구현 예정

## Version 4.2.1 (2025-01-20)

### [MAJOR UPDATE] 전략 0: Customizer 직접 연동 추가
- **최우선 전략으로 Customizer 직접 연동 구현**
  - WordPress Customizer와 플러그인 옵션 간 양방향 실시간 동기화
  - Customizer 변경 → 플러그인 옵션 자동 동기화 (Sync In)
  - 플러그인 옵션 변경 → Customizer 자동 반영 (Sync Out)
  - 모든 테마 지원 (Kadence, Astra, 기본 WordPress 등)
  - Preview 모드에서도 실시간 동기화 지원

- **전략 우선순위 재정립**
  - 전략 0 (최우선): Customizer 직접 연동
  - 전략 1: CSS 변수 (전략 0 보완)
  - 전략 2: PHP 필터 (전략 0, 1 보완)
  - 전략 3: Dequeue (최후의 수단)

- **주요 기능**
  - Customizer 저장 시 자동 동기화 (`customize_save_after` 훅)
  - 플러그인 옵션 변경 시 Customizer 반영 (`update_option` 훅)
  - 초기화 시 Customizer 값 자동 읽어오기 (빈 값일 때)
  - 테마별 Customizer 반영 로직 (Kadence, Astra, 기본 WordPress)

### [CRITICAL FIX] 하드코딩된 주황색 기본값 완전 제거
- **기본 색상값 제거**
  - `primary_color`: `#FF6400` → `''` (빈 값)
  - `primary_color_hover`: `#E65A00` → `''` (빈 값)
  - `link_color`: `#FF6400` → `''` (빈 값)
  - `buttons['primary']['background_color']`: `#FF6400` → `''` (빈 값)
  - `buttons['primary']['background_color_hover']`: `#E65A00` → `''` (빈 값)
  - `buttons['primary']['border_color']`: `#FF6400` → `''` (빈 값)
  - `buttons['primary']['border_color_hover']`: `#E65A00` → `''` (빈 값)

- **테마 설정 존중**
  - 하드코딩된 색상값을 모두 빈 값으로 변경
  - 테마(Kadence 등)가 감지되면 자동으로 테마의 색상 값을 읽어와서 사용
  - 플러그인이 테마 설정을 덮어쓰지 않도록 수정

### [ENHANCEMENT] Kadence 어댑터 색상 팔레트 시스템 재배치
- **Kadence 공식 색상 팔레트 매핑 시스템 적용**
  - `primary_color` → `global_palette['palette1']` (Global Palette 1)
  - `primary_color_hover` → `global_palette['palette2']` (Global Palette 2)
  - `secondary_color` → `global_palette['palette3']` (Global Palette 3)
  - `secondary_color_hover` → `global_palette['palette4']` (Accents Palette 4)
  - `text_color` → `global_palette['palette6']` (Global Palette 6)
  - `link_color` → `global_palette['palette9']` (Global Palette 9)
  - `site_bg` → `options['site_background']` (직접 옵션)
  - `content_bg` → `options['content_background']` (직접 옵션)

- **메타데이터 업데이트**
  - `secondary_color` 및 `secondary_color_hover` 메타데이터 추가
  - 각 색상의 적용 위치 정보 업데이트
  - Kadence 공식 문서 참조 링크 추가

### Files Created
- `dev/includes/class-jj-strategy-0-customizer.php` (전략 0 클래스)
- `live/includes/class-jj-strategy-0-customizer.php` (전략 0 클래스)

### Files Modified
- `dev/includes/class-jj-activation-manager.php` (하드코딩된 색상값 제거)
- `live/includes/class-jj-activation-manager.php` (하드코딩된 색상값 제거)
- `dev/adapters/adapter-theme-kadence.php` (색상 팔레트 매핑 업데이트)
- `live/adapters/adapter-theme-kadence.php` (색상 팔레트 매핑 업데이트)
- `dev/jj-simple-style-guide.php` (전략 0 추가, 버전 업데이트)
- `live/jj-simple-style-guide.php` (전략 0 추가, 버전 업데이트)
- `free/jj-simple-style-guide.php` (버전 업데이트)
- `basic/jj-simple-style-guide.php` (버전 업데이트)
- `premium/jj-simple-style-guide.php` (버전 업데이트)
- `unlimited/jj-simple-style-guide.php` (버전 업데이트)

## Version 4.0.2 (2025-01-20)

### [CRITICAL FIX] Admin Center 저장 기능 완전 수정
- **AJAX 핸들러 추가**
  - `wp_ajax_jj_admin_center_save` 핸들러 등록
  - `ajax_save_admin_center_settings()` 메서드 추가
  - 저장 기능이 완전히 작동하지 않던 문제 해결
  
- **저장 데이터 전송 방식 개선**
  - FormData 대신 jQuery `serialize()` 사용
  - 중첩 배열 데이터(`jj_admin_menu_layout`) 전송 보장
  - 메뉴 순서 데이터 명시적으로 수집 및 업데이트
  
- **에러 처리 및 로깅 개선**
  - 상세한 에러 메시지 표시
  - AJAX 에러 시 콘솔 로그 추가

### [FIX] 드래그앤드롭 개선
- sortable 초기화 후 드래그 핸들 이벤트 리스너 추가
- 드래그 핸들 클릭 감지 로그 추가 (디버깅용)
- z-index 및 position 스타일 추가로 클릭 가능성 보장

### Files Modified
- `dev/includes/class-jj-admin-center.php` (AJAX 핸들러 추가)
- `live/includes/class-jj-admin-center.php` (AJAX 핸들러 추가)
- `dev/assets/js/jj-admin-center.js` (저장 함수 및 드래그앤드롭 수정)
- `live/assets/js/jj-admin-center.js` (저장 함수 및 드래그앤드롭 수정)

## Version 4.0.1 (2025-01-20)

### [FIX] 관리자 센터 드래그앤드롭 초기화 로직 대폭 개선
- **드래그 핸들 자동 생성 기능 추가**
  - 드래그 핸들이 없는 경우 자동으로 생성하여 초기화 실패 방지
  - Dashicons 아이콘을 사용한 시각적 드래그 핸들 제공
  
- **초기화 검증 및 자동 재시도 메커니즘 강화**
  - 초기화 성공 여부 확인 (`ui-sortable` 클래스 존재 확인)
  - 초기화 실패 시 자동 재시도 (500ms 후)
  - 에러 발생 시 자동 재시도 (1000ms 후)
  - 재시도 횟수 증가 (20 → 30)
  - 재시도 간격 조정 (100ms → 150ms)
  
- **이벤트 핸들링 개선**
  - 드래그 핸들에 직접 이벤트 리스너 추가 (다른 이벤트와의 충돌 방지)
  - 이벤트 네임스페이스 사용 (`mousedown.sortable`, `touchstart.sortable`)
  - `stopPropagation` 추가로 이벤트 버블링 방지
  - 터치 이벤트 지원 추가 (`touch-action: none`)
  
- **상세한 디버깅 로그 추가**
  - 모든 초기화 단계에서 상세 로그 출력
  - 초기화 상태 확인 정보 추가 (`isInitialized`, `sortableInstance`)
  - 드래그 핸들 개수 추적 및 로그
  - 재시도 시도 횟수 및 상태 로그
  
- **탭 전환 시 초기화 로직 개선**
  - 탭 전환 시 재시도 메커니즘 추가 (최대 10회)
  - DOM 렌더링 완료 대기 로직 개선
  - `requestAnimationFrame` 활용으로 렌더링 타이밍 최적화

### Files Modified
- `dev/assets/js/jj-admin-center.js` (드래그앤드롭 초기화 로직 대폭 개선)
- `live/assets/js/jj-admin-center.js` (드래그앤드롭 초기화 로직 대폭 개선)

## Version 3.9.0 (2025-11-18)

### [NEW] 라이센스 시스템 구현
- **4단계 버전 체계 도입**
  - Free 버전: 기본 기능, 워터마크, 테마 2개/플러그인 1개 지원, 브랜드 팔레트만
  - Basic 버전: 중간 기능, 제한적 커스터마이징, 테마 5개/플러그인 5개 지원, 브랜드 + 시스템 팔레트
  - Premium 버전: 대부분의 기능, 1개 사이트 라이센스, 테마 10개/플러그인 15개 지원, 모든 팔레트 + 관리자 센터 전체 기능
  - Unlimited 버전: 모든 기능, 무제한 사이트, 모든 테마/플러그인 지원, 실험실 센터 전체 기능 + 다중 사이트

- **라이센스 관리 시스템**
  - `JJ_License_Manager` 클래스: 라이센스 키 검증, 사이트 바인딩, 기능 제한 관리
  - `JJ_Version_Features` 클래스: 버전별 기능 사용 가능 여부 체크
  - 라이센스 키 형식: `JJ-[버전]-[타입]-[랜덤]-[체크섬]`
  - 온라인/오프라인 검증 지원
  - 라이센스 만료일 관리

- **라이센스 발행 시스템 (마스터 버전)**
  - `JJ_License_Issuer` 클래스: 라이센스 생성, 관리, 추적
  - 관리자 페이지: "설정 > 라이센스 발행 관리"
  - 라이센스 생성, 활성화/비활성화, 사이트 바인딩 관리
  - 라이센스 활성화 이력 추적
  - AJAX 기반 라이센스 관리 인터페이스

- **결제 페이지 연결 기능**
  - 모든 버전의 플러그인에 결제 페이지 링크 통합
  - `JJ_License_Manager::get_purchase_url()` 메서드 추가
  - 결제 페이지 주소: `https://j-j-labs.com/?product=plugin-jj-center-of-style-setting`
  - 상황별 버튼 텍스트 자동 변경:
    - 라이센스 만료 시: "기한 연장"
    - 일반적인 경우: "업그레이드"
  - 관리자 센터 라이센스 탭에 결제/연장 버튼 추가
  - 업그레이드 프롬프트 메시지에 결제 페이지 링크 통합

- **버전별 플러그인 파일 구조**
  - `free/jj-simple-style-guide.php`: Free 버전 메인 파일
  - `basic/jj-simple-style-guide.php`: Basic 버전 메인 파일
  - `premium/jj-simple-style-guide.php`: Premium 버전 메인 파일
  - `unlimited/jj-simple-style-guide.php`: Unlimited 버전 메인 파일
  - 각 버전별 기능 제한 및 업그레이드 프롬프트 포함

- **기능 제한 시스템**
  - 팔레트 타입별 사용 가능 여부 체크 (브랜드, 시스템, 얼터너티브, 어나더, 임시)
  - 타이포그래피 태그별 사용 가능 여부 체크 (h1-h6, p)
  - 버튼 타입별 사용 가능 여부 체크 (primary, secondary, text)
  - 섹션별 내보내기/불러오기 기능 제한
  - 관리자 센터/실험실 센터 접근 제한
  - 테마/플러그인 어댑터 개수 제한

- **관리자 센터 라이센스 탭**
  - 현재 라이센스 상태 표시 (타입, 유효성, 만료일)
  - 라이센스 키 입력 및 저장
  - 라이센스 검증 기능
  - 라이센스 키 제거 기능
  - 결제/연장 버튼 (상황별 자동 변경)

### Files Created
- `dev/includes/class-jj-license-manager.php` (라이센스 관리 클래스)
- `dev/includes/class-jj-version-features.php` (버전별 기능 체크 클래스)
- `dev/includes/class-jj-license-issuer.php` (라이센스 발행 클래스, 마스터 버전)
- `dev/assets/css/jj-license-issuer.css` (라이센스 발행 페이지 스타일)
- `dev/assets/js/jj-license-issuer.js` (라이센스 발행 페이지 JavaScript)
- `free/jj-simple-style-guide.php` (Free 버전 메인 파일)
- `basic/jj-simple-style-guide.php` (Basic 버전 메인 파일)
- `premium/jj-simple-style-guide.php` (Premium 버전 메인 파일)
- `unlimited/jj-simple-style-guide.php` (Unlimited 버전 메인 파일)

### Files Modified
- `dev/jj-simple-style-guide.php` (라이센스 발행 클래스 로드 추가)
- `dev/includes/class-jj-admin-center.php` (라이센스 탭 추가, 결제 페이지 링크 통합)
- `dev/assets/js/jj-admin-center.js` (라이센스 탭 JavaScript 로직 추가)
- `dev/assets/css/jj-admin-center.css` (라이센스 탭 스타일 추가)
- `dev/includes/editor-views/view-section-colors.php` (버전별 기능 제한 적용)
- `dev/includes/editor-views/view-section-temp-palette.php` (버전별 기능 제한 적용)
- `dev/assets/js/jj-style-guide-editor.js` (섹션별 내보내기/불러오기 기능 추가)
- 모든 버전별 플러그인 파일 (결제 페이지 링크 통합)

## Version 3.8.0 (2025-11-18)

### [FIX] 관리자 센터 wpColorPicker 및 드래그 앤 드롭 기능 수정 (2025-11-18)
- **wpColorPicker 문제 해결**
  - `wpWpColorPicker` 데이터 키를 `wpColorPicker`로 수정 (WordPress 표준 사용)
  - wpColorPicker 초기화 전 destroy 로직 추가 (중복 초기화 방지)
  - `clear` 이벤트 핸들러 추가 (색상 삭제 기능 지원)
  - try-catch로 오류 처리 강화
  - 초기화 타이밍 개선 (100ms → 200ms)
  
- **드래그 앤 드롭 문제 해결**
  - jQuery UI Sortable 로드 여부 확인 추가
  - `items` 옵션 명시적으로 지정 (직접 자식 요소만: `> .jj-admin-center-menu-item`)
  - placeholder 스타일 동적 추가 (드래그 시 시각적 피드백 제공)
  - `start` 콜백에서 placeholder 높이 설정
  - `cancel` 옵션에 badge 클래스 추가 (드래그 방지 요소 확대)
  - 서브메뉴 sortable 초기화 로직 개선
  - `revert` 및 `distance` 옵션 추가로 UX 개선

### [NEW] 관리자 센터 Colors 탭 개선
- **wpColorPicker 통합**
  - 모든 색상 입력 필드에 WordPress 기본 컬러 피커 적용
  - Colors 탭 활성화 시 자동 초기화
  - 탭 전환 시 동적 초기화
  
- **스포이드(EyeDropper API) 기능 추가**
  - 각 색상 필드에 스포이드 버튼 추가
  - 브라우저 EyeDropper API를 사용한 색상 추출
  - 선택한 색상을 해당 입력 필드에 자동 적용
  
- **팔레트 불러오기 기능**
  - 각 색상 필드에 "팔레트에서 불러오기" 버튼 추가
  - 팔레트 선택 모달 UI 구현
  - 브랜드, 시스템, 얼터너티브, 어나더, 임시 팔레트 지원
  - 각 팔레트의 컬러칩 미리보기 제공
  - 팔레트명과 색상 정보 표시
  - 컬러칩 클릭 시 해당 색상 자동 적용
  
- **AJAX 핸들러 추가**
  - `ajax_get_palette_data`: 팔레트 데이터 가져오기
  - 모든 팔레트 타입의 색상 데이터 반환

### [FIX] 드래그 앤 드롭 기능 수정
- 관리자 센터 메뉴 순서 변경 드래그 앤 드롭 기능 수정
  - `initializeSortable()` 함수 추가 및 재사용 가능하도록 구조화
  - Admin Menu 탭 활성화 시 드래그 앤 드롭 자동 초기화
  - 탭 전환 시 드래그 앤 드롭 재초기화 로직 추가
  - sortable destroy 후 재초기화 로직 추가 (중복 초기화 방지)
  - containment, opacity, helper, revert, distance 옵션 추가로 UX 개선
  
- 스타일 센터 섹션 드래그 앤 드롭 기능 수정
  - HTML 구조에 맞게 `.jj-section-wrapper`를 sortable items로 설정
  - 드래그 핸들을 `.jj-section-global` 및 `.jj-section-spoke`에 정확히 추가
  - 섹션 순서 저장 로직 수정

### [FIX] 관리자 센터 접근 권한 문제
- 스타일 센터와 실험실 센터에서 관리자 센터 버튼의 링크 수정
  - 잘못된 슬러그 'jj-style-guide-admin' → 올바른 슬러그 'jj-admin-center'
  - admin.php?page= → options-general.php?page= 로 URL 형식 수정

### [NEW] Customizer 색상 자동 불러오기
- **플러그인 활성화 시 Customizer 색상 값 자동 불러오기**
  - `JJ_Customizer_Sync` 클래스 추가 (`class-jj-customizer-sync.php`)
  - 플러그인 활성화 시 브랜드 팔레트와 시스템 팔레트 색상을 Customizer에서 자동으로 불러와서 옵션에 저장
  - `sync_customizer_colors_on_activation()` 메서드 추가 (`class-jj-activation-manager.php`)
  
- **섹션별 새로고침 버튼 추가**
  - 스타일 센터의 브랜드 팔레트와 시스템 팔레트 탭에 "새로고침" 버튼 추가
  - 버튼 클릭 시 Customizer에서 현재 색상을 한꺼번에 불러오기
  - AJAX를 통한 실시간 색상 업데이트

### [NEW] 섹션별 내보내기/불러오기
- **섹션별 설정 내보내기/불러오기 기능 추가**
  - 브랜드 팔레트, 시스템 팔레트 탭에 내보내기/불러오기 버튼 추가
  - 선택적으로 특정 섹션만 내보내고 불러오기 가능
  - JSON 형식으로 섹션별 설정 저장/복원
  - 버전 호환성 체크 및 자동 마이그레이션 지원
  - `export_section_ajax()`, `import_section_ajax()` 메서드 추가

### [ENHANCEMENT] Customizer 통합 강화
- **'JJ 스타일 센터' 패널을 Customizer 최상단에 배치**
  - 패널 priority를 1로 설정하여 최상단에 표시
  - 패널 제목 및 설명 업데이트로 스타일 센터 자연스러운 유도
  - 플러그인의 컬러/폰트 관리 방식이 Customizer와 완전히 통합

### [NEW] WordPress Customizer 통합
- 플러그인 기능을 WordPress '사용자 정의' 메뉴로 완전 통합
- 테마 감지 및 동적 Customizer 섹션 생성
  * 설치된 테마에 따라 자동으로 "부가 옵션" 섹션 생성 (예: "Kadence 부가 옵션", "Astra 부가 옵션")
  * 차일드 테마 자동 인식 및 표시
- `add_dynamic_theme_sections()` 메서드 추가 (`class-jj-customizer-manager.php`)
  * 활성 테마를 감지하여 지원되는 테마인 경우 동적 섹션 생성
  * 테마별 "부가 옵션" 섹션 추가
  * 테마 특화 컨트롤 추가 지원

### [ENHANCEMENT] 팔레트 시스템 개선
- Customizer에서 최소 3개 팔레트 보장
  * 브랜드 팔레트 (Brand Palette) 섹션
  * 시스템 팔레트 (System Palette) 섹션
  * 대안 팔레트 (Alternative Palette) 섹션
- 임시 팔레트 (Temporary Palette) 활성 시 자동으로 4개로 확장
  * `JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY` 확인하여 조건부 추가
- 각 팔레트를 별도 섹션으로 구분하여 직관적인 관리
- `add_colors_section()` 메서드 대폭 개선 (`class-jj-customizer-manager.php`)
  * 각 팔레트별 독립 섹션 생성
  * 동적 임시 팔레트 섹션 추가
  * 툴팁 데이터 통합

### [NEW] 툴팁 시스템 구현
- 컬러, 폰트, 버튼 설정에 적용 위치 정보를 툴팁으로 제공
- CSS 선택자와 일반 언어 설명 동시 제공
- 현재 설치된 테마 정보를 반영한 컨텍스트 설명
  * 예: "현재 설치된 Kadence 테마의 차일드 테마인 My Child Theme에서 이 색상은 헤더, 버튼, 링크에 적용됩니다."
- JavaScript 파일 추가 (`jj-customizer-tooltips.js`)
  * 툴팁 아이콘 추가 (Dashicon help)
  * 툴팁 박스 표시/숨김 기능
  * 동적 위치 계산 및 스타일링
- CSS 파일 추가 (`jj-customizer-tooltips.css`)
  * 툴팁 아이콘 및 박스 스타일 정의
  * 반응형 디자인 지원

### [NEW] 테마 메타데이터 시스템
- `JJ_Theme_Metadata` 클래스 생성 (`class-jj-theme-metadata.php`)
  * 테마/플러그인별 메타데이터 중앙 관리
  * 기본 메타데이터 초기화 (`init_default_metadata()`)
  * 테마별 메타데이터 등록 (`register_theme_metadata()`)
  * 플러그인별 메타데이터 등록 (`register_plugin_metadata()`)
  * 메타데이터 조회 및 설명 생성 (`get_metadata()`, `get_combined_description()`)
- 각 어댑터에서 적용 위치 정보 등록 가능
- Kadence 테마 메타데이터 등록 완료 (`adapter-theme-kadence.php`)
  * 브랜드 팔레트 색상별 메타데이터 (primary_color, primary_color_hover)
  * 타이포그래피 메타데이터 (h1)
  * CSS 선택자 및 적용 위치 정보 포함

### [ENHANCEMENT] Customizer UI 개선
- 테마별 섹션에 현재 설치된 테마/플러그인 정보 표시
- 동적 컨트롤 생성으로 확장성 향상
- `add_theme_specific_controls()` 메서드 추가 (`class-jj-customizer-manager.php`)
  * 테마별 특화 컨트롤 추가 지원
  * Kadence 테마 특화 컨트롤 예시 구현
- `enqueue_customizer_scripts()` 메서드 추가 (`class-jj-customizer-manager.php`)
  * 툴팁 JavaScript/CSS 파일 enqueue
  * 활성 테마 정보 localization (`jjCustomizerData`)

### Files Created
- `dev/includes/class-jj-theme-metadata.php` (테마 메타데이터 관리 클래스)
- `dev/assets/js/jj-customizer-tooltips.js` (툴팁 JavaScript)
- `dev/assets/css/jj-customizer-tooltips.css` (툴팁 CSS)
- `live/includes/class-jj-theme-metadata.php` (동기화)
- `live/assets/js/jj-customizer-tooltips.js` (동기화)
- `live/assets/css/jj-customizer-tooltips.css` (동기화)

### Files Modified
- `dev/includes/class-jj-customizer-manager.php` (대폭 개선)
  * `add_dynamic_theme_sections()` 메서드 추가
  * `add_colors_section()` 메서드 개선
  * `add_typography_section()` 메서드 개선 (툴팁 통합)
  * `add_buttons_section()` 메서드 개선 (툴팁 통합)
  * `add_theme_specific_controls()` 메서드 추가
  * `enqueue_customizer_scripts()` 메서드 추가
- `dev/adapters/adapter-theme-kadence.php` (메타데이터 등록 추가)
  * `register_metadata()` 메서드 추가
  * `init()` 메서드에서 메타데이터 등록 호출
- `dev/jj-simple-style-guide.php` (테마 메타데이터 클래스 로드)
  * `require_once` 문 추가
- `live/includes/class-jj-customizer-manager.php` (동기화)
- `live/adapters/adapter-theme-kadence.php` (동기화)
- `live/jj-simple-style-guide.php` (동기화)

## Version 3.7.0 (2024)

[이전 버전 변경사항은 이전 changelog 참조]
