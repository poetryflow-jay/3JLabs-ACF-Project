# ACF CSS AI Extension

> AI 기반 스타일 자동 생성 확장 플러그인 - OpenAI GPT로 당신의 아이디어를 CSS 스타일로 변환

**버전**: 1.0.0  
**호환 버전**: ACF CSS Manager 6.0.3+  
**최소 PHP**: 7.4  
**최소 WordPress**: 5.8

---

## 📌 개요

ACF CSS AI Extension은 자연어 프롬프트를 통해 웹사이트 스타일을 자동으로 생성해주는 확장 플러그인입니다. 복잡한 CSS 지식 없이도 "고급스러운 블랙&골드, 법률사무소 느낌"과 같은 간단한 설명만으로 전문가 수준의 색상 팔레트와 타이포그래피를 얻을 수 있습니다.

### ✨ 주요 기능

| 기능 | 설명 |
|------|------|
| 🤖 **AI 스타일 생성** | OpenAI GPT 기반 스타일 자동 생성 |
| 🔍 **스마트 프리뷰** | Before/After 시각적 Diff 비교 |
| ☁️ **Cloud 저장** | 생성된 스타일을 클라우드에 즉시 저장 |
| 🔐 **보안** | API Key 별도 관리, 네트워크 잠금 지원 |

---

## 🚀 설치

### 요구사항

- ACF CSS Manager (메인 플러그인) **v6.0.3 이상** 필수
- OpenAI API Key (https://platform.openai.com/api-keys)
- PHP 7.4+
- WordPress 5.8+

### 설치 방법

1. `acf-css-ai-extension.zip` 파일을 다운로드합니다.
2. WordPress 관리자 > 플러그인 > 새로 추가 > 플러그인 업로드
3. ZIP 파일을 업로드하고 "지금 설치"를 클릭합니다.
4. "플러그인 활성화"를 클릭합니다.

---

## ⚙️ 설정

### 1. API Key 설정

1. 관리자 메뉴 > 설정 > **ACF CSS AI** 로 이동
2. **AI 설정** 섹션에서 Provider 선택 (OpenAI)
3. OpenAI API Key 입력 (`sk-...` 형식)
4. **설정 저장** 클릭

> ⚠️ **주의**: API Key는 암호화되지 않고 저장됩니다. 보안이 중요한 환경에서는 환경 변수 사용을 권장합니다.

### 2. 환경 변수로 API Key 설정 (권장)

`wp-config.php`에 다음을 추가:

```php
define( 'JJ_AI_OPENAI_API_KEY', 'sk-your-api-key-here' );
```

---

## 📖 사용법

### 기본 사용

1. **설정 > ACF CSS AI** 페이지로 이동
2. 프롬프트 입력창에 원하는 스타일 설명 입력
   - 예: "미니멀하고 모던한 블랙&화이트, 테크 스타트업 느낌"
   - 예: "따뜻한 파스텔 색상, 카페 웹사이트에 어울리는 부드러운 분위기"
3. **AI 제안 생성** 버튼 클릭
4. 생성된 결과 확인 (Before/After Diff 표시)
5. 마음에 들면 **적용(저장)** 클릭

### 스마트 프리뷰 (Phase 7.3)

AI가 제안한 변경사항은 JSON 텍스트가 아닌 **시각적 Diff**로 표시됩니다:

```
브랜드 Primary Color
#3498db → #1a1a1a
[🔵]     [⬛]

타이포그래피 Heading Font
Open Sans → Playfair Display
```

### Cloud 저장 (Phase 7.4)

생성된 스타일이 마음에 들면:

1. **☁️ Cloud 저장** 버튼 클릭
2. 공유 코드가 발급됩니다 (예: `ABC123XYZ`)
3. 이 코드로 다른 사이트에 스타일을 복제하거나 템플릿 마켓에 등록할 수 있습니다.

---

## 🔒 보안 및 제한

### API 사용량

- OpenAI API 호출당 비용이 발생합니다
- 기본 모델: `gpt-3.5-turbo` (비용 효율적)
- 평균 응답 토큰: 200~500 tokens

### 네트워크 잠금

멀티사이트 환경에서 네트워크 전용 모드가 활성화된 경우:
- AI 스타일 적용이 차단됩니다
- 네트워크 관리자에서만 스타일 변경 가능

### JSON 검증

AI 응답은 다음 필수 필드를 포함해야 합니다:
- `explanation`: 변경 설명
- `settings_patch`: 적용할 설정 패치

잘못된 응답은 자동으로 필터링됩니다.

---

## 🛠️ 개발자 정보

### 필터/액션 훅

```php
// AI 생성 전 프롬프트 수정
add_filter( 'jj_ai_modify_prompt', function( $prompt, $context ) {
    return $prompt . ' 한국 웹사이트에 적합하게';
}, 10, 2 );

// AI 결과 적용 후 후처리
add_action( 'jj_ai_style_applied', function( $patch, $source ) {
    // 로깅, 알림 등
}, 10, 2 );
```

### 파일 구조

```
acf-css-ai-extension/
├── acf-css-ai-extension.php      # 메인 플러그인 파일
├── README.md                     # 이 문서
├── assets/
│   ├── ai-extension.css          # 관리자 UI 스타일
│   ├── ai-extension.js           # 관리자 UI 스크립트
│   └── jsondiffpatch-lite.js     # Diff 시각화 라이브러리
└── includes/
    ├── class-jj-acf-css-ai-extension.php  # 메인 클래스
    └── providers/
        ├── interface-ai-provider.php      # Provider 인터페이스
        └── class-jj-ai-provider-openai.php # OpenAI Provider
```

---

## 📝 Changelog

### 1.0.0 (2025-12-18)
- 🎉 최초 릴리스
- OpenAI GPT 통합
- Before/After Diff 시각화 (Phase 7.3)
- Cloud 저장 기능 (Phase 7.4)
- 네트워크 잠금 지원

---

## 📄 라이선스

이 플러그인은 ACF CSS Manager의 확장이며, 동일한 상업용 라이선스가 적용됩니다.

**© 2025 J&J Labs. All rights reserved.**
