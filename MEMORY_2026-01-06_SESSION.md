# 3J Labs ACF CSS 프로젝트 세션 메모리
> 작성일: 2026-01-06
> 세션: Phase 47 - 스타일 센터 탭 시스템 완전 수정

---

## 1. 프로젝트 개요

### 1.1 프로젝트 구조
```
C:/Users/computer/Desktop/3J-Labs-Projects/3J-ACF-CSS/
├── acf-css-really-simple-style-management-center-master/  # 메인 플러그인 (v25.1.0)
├── acf-css-neural-link/                                    # 라이센스/업데이트 서버 (v8.0.1)
├── acf-css-woocommerce-toolkit/                           # 우커머스 통합 (v2.4.3)
├── acf-css-ai-extension/                                  # AI 스타일 생성 (v3.3.3)
├── acf-css-woo-license/                                   # WooCommerce 라이센스 브릿지 (v23.0.2)
├── acf-code-snippets-box/                                 # 코드 스니펫 관리 (v4.0.2)
├── acf-nudge-flow/                                        # 넛지 워크플로우 (v23.0.0)
├── acf-mail-smtp/                                         # 메일 SMTP + Gmail API (v2.1.0)
├── acf-user-journey-analytics/                            # 사용자 여정 분석 (v1.0.3)
├── admin-menu-editor-pro/                                 # 관리자 메뉴 에디터 (v2.0.4)
├── jj-analytics-dashboard/                                # 분석 대시보드 (v1.0.3)
├── jj-marketing-automation-dashboard/                     # 마케팅 자동화 (v2.0.2)
├── wp-bulk-manager/                                       # 대량 작업 관리 (v23.1.3)
├── SEO/oneclick-seo-pro/                                  # SEO 플러그인 (v2.1.0)
├── shared-ui-assets/                                      # 공유 UI/보안 모듈
├── dist/                                                  # 빌드 출력 폴더
├── builds/                                                # 빌드 아카이브
├── 3j_build_manager.py                                    # Python 빌드 매니저 (v23.0.1)
└── dashboard.html                                         # 배포 대시보드 (v25)
```

### 1.2 핵심 기술 스택
- **백엔드**: PHP 7.4+, WordPress 6.0+
- **프론트엔드**: JavaScript (jQuery), CSS3, Spectrum.js (컬러피커)
- **빌드 시스템**: Python 3.x, tkinter GUI
- **버전 관리**: Git, GitHub
- **라이센스**: WooCommerce 연동, Neural Link 서버

---

## 2. 이번 세션에서 해결한 문제

### 2.1 ACF CSS 스타일 센터 탭 문제 (핵심 버그)

#### 문제 상황
- 스타일 센터에서 "팔레트" 탭은 정상 작동
- "타이포그래피", "폰트", "폼", "필드" 탭 클릭 시 아무것도 표시되지 않음

#### 근본 원인
`jj-style-guide-editor.js`의 탭 셀렉터 불일치:

```javascript
// 문제의 코드 (수정 전)
var $tabContents = $('.jj-section-wrapper.jj-section-tab-content');

// 탭 클릭 시 숨기기 (수정 전)
$('.jj-section-wrapper.jj-section-tab-content').hide()...
```

PHP에서 생성되는 섹션 래퍼에는 `jj-section-tab-content` 클래스가 조건부로만 추가되어, 일부 섹션이 셀렉터에 매칭되지 않았음.

#### 해결 방법
```javascript
// 수정된 코드
var $tabContents = $('.jj-section-wrapper[data-section]');

// 탭 클릭 시 숨기기 (수정 후)
$('.jj-section-wrapper[data-section]').hide()...

// 추가: 동적으로 클래스 추가
$tabContents.each(function() {
    $(this).addClass('jj-section-tab-content jj-section-hidden')
           .removeClass('jj-section-visible')
           .css('display', 'none');
});
```

#### 수정된 파일
- `acf-css-really-simple-style-management-center-master/assets/js/jj-style-guide-editor.js`
  - 라인 1892: `initSectionTabs()` 함수의 `$tabContents` 셀렉터
  - 라인 2019: 탭 클릭 이벤트의 숨기기 셀렉터

#### 버전 업데이트
- `acf-css-really-simple-style-guide.php`: 23.0.10 → 25.1.0

---

## 3. 시스템 아키텍처 상세

### 3.1 탭 시스템 구조

#### PHP 측 (class-jj-simple-style-guide.php)
```php
// 탭 버튼 생성 (라인 397-418)
echo '<button type="button" class="jj-section-tab-button' . $active_class . '"
      data-tab-section="' . esc_attr($slug) . '">' . $label . '</button>';

// 섹션 콘텐츠 생성 (라인 427-461)
echo '<div class="jj-section-wrapper jj-card' . $tab_class . $hidden_class . $visible_class . '"
      data-section="' . esc_attr($slug) . '"
      data-section-slug="' . esc_attr($slug) . '">';
```

#### JavaScript 측 (jj-style-guide-editor.js)
```javascript
// 탭 초기화 (라인 1888-1969)
function initSectionTabs() {
    var $tabButtons = $('.jj-section-tab-button');
    var $tabContents = $('.jj-section-wrapper[data-section]');
    // ...
}

// 탭 클릭 이벤트 (라인 1992-2062)
$(document).on('click', '.jj-section-tab-button', function(e) {
    var targetSection = $button.data('tab-section');
    var $targetSection = $('.jj-section-wrapper[data-section="' + targetSection + '"]');
    // ...
});
```

### 3.2 보안/라이센스 시스템

#### Neural Link (서버 역할)
```
acf-css-neural-link/
├── acf-css-neural-link.php          # 메인 파일 (v8.0.1)
├── includes/
│   ├── class-jj-license-manager-main.php    # 라이센스 관리 메인
│   ├── class-jj-license-validator.php       # 라이센스 검증
│   ├── class-jj-license-generator.php       # 라이센스 생성
│   ├── class-jj-plugin-updater.php          # 플러그인 업데이트
│   ├── class-jj-license-update-distributor.php  # 업데이트 배포
│   └── class-jj-pattern-learner.php         # 패턴 학습 (AI)
```

#### shared-ui-assets (클라이언트 역할)
```
shared-ui-assets/
├── class-jj-security-module-v25.php         # 보안 모듈 로더
├── class-jj-license-manager-shared.php      # 공유 라이센스 UI
└── ...
```

#### 연동 흐름
1. 각 플러그인 → shared-ui-assets 로드
2. shared-ui-assets → Neural Link 서버에 라이센스 확인 요청
3. Neural Link → WooCommerce 주문 데이터 확인
4. 라이센스 유효성 응답 → 플러그인 기능 활성화

### 3.3 빌드 시스템

#### 3j_build_manager.py 주요 기능
```python
# 플러그인 정의 (PLUGINS 딕셔너리)
PLUGINS = {
    'acf-css-master': {
        'id': 'acf-css-master',
        'name': 'ACF CSS 설정 관리자',
        'folder': 'acf-css-really-simple-style-management-center-master',
        'main_file': 'acf-css-really-simple-style-guide.php',
        # ...
    },
    # ... 14개 플러그인 정의
}

# 빌드 프로세스
def build_plugin(self, plugin_id, editions=None):
    1. 버전 추출 (PHP 헤더에서)
    2. 기존 ZIP 파일 old 폴더로 이동
    3. 새 ZIP 파일 생성
    4. 패키지 서명 생성 (HMAC-SHA256)
    5. 대시보드 HTML 업데이트
```

#### CLI 사용법
```bash
# 전체 빌드
python 3j_build_manager.py --cli --all

# 특정 플러그인만
python 3j_build_manager.py --cli --plugins acf-css-master acf-css-neural-link

# 특정 에디션만
python 3j_build_manager.py --cli --all --editions master
```

#### 출력 구조
```
dist/
├── acf-css-really-simple-style-management-center-master-master-v25.1.0.zip
├── acf-css-neural-link-master-v8.0.1.zip
├── package_signatures.json
├── old/
│   └── 2026-01-06/
│       ├── acf-css-...-v23.0.10_080333.zip
│       └── ...
└── SEO/
    └── oneclick-seo-pro-master-v2.1.0.zip
```

---

## 4. 코드 수정 기록

### 4.1 jj-style-guide-editor.js 변경사항

#### 변경 1: initSectionTabs() 셀렉터 (라인 1892)
```javascript
// Before
var $tabContents = $('.jj-section-wrapper.jj-section-tab-content');

// After
var $tabContents = $('.jj-section-wrapper[data-section]');
```

#### 변경 2: 숨기기 로직 (라인 1900-1902)
```javascript
// Before
$tabContents.hide().removeClass('jj-section-visible').addClass('jj-section-hidden').css('display', 'none');

// After
$tabContents.each(function() {
    $(this).addClass('jj-section-tab-content jj-section-hidden')
           .removeClass('jj-section-visible')
           .css('display', 'none');
});
```

#### 변경 3: 탭 클릭 숨기기 셀렉터 (라인 2019)
```javascript
// Before
$('.jj-section-wrapper.jj-section-tab-content').hide()...

// After
$('.jj-section-wrapper[data-section]').hide()...
```

### 4.2 acf-css-really-simple-style-guide.php 변경사항

```php
// Before (라인 ~15)
* Version:           23.0.10

// After
* Version:           25.1.0

// Before (라인 ~56)
define( 'JJ_STYLE_GUIDE_VERSION', '23.0.10' );

// After
define( 'JJ_STYLE_GUIDE_VERSION', '25.1.0' ); // [v25.1.0] 탭 시스템 완전 수정 - data-section 속성 기반 선택으로 변경
```

---

## 5. 이전 세션 작업 이력 (컨텍스트)

### 5.1 ACF Mail SMTP Gmail API 추가 (v2.1.0)
- `class-gmail-api.php` 생성 (~500줄)
- Gmail API OAuth2 직접 연동
- XOAUTH2 SMTP 인증 지원

### 5.2 JJ Analytics Dashboard 초기화 수정 (v1.0.3)
```php
// 추가된 코드
add_action( "plugins_loaded", function() {
    JJ_Analytics_Dashboard::instance();
} );
```

### 5.3 플러그인 분석 결과 (이번 세션 전)
| 플러그인 | 상태 | 비고 |
|---------|------|------|
| ACF CSS WooCommerce Toolkit | ✅ 양호 | v2.4.3 |
| ACF Admin Menu Editor Pro | ✅ 양호 | v2.0.4 |
| ACF CSS AI Extension | ✅ 양호 | v3.3.3 |
| JJ Analytics Dashboard | ✅ 수정됨 | 초기화 코드 추가 |
| ACF CSS License Bridge | ✅ 양호 | v23.0.2 |

---

## 6. 알려진 이슈 및 주의사항

### 6.1 파일 수정 시 주의
- **Edit 도구 오류**: 파일이 외부에서 수정되면 "file unexpectedly modified" 오류 발생
- **해결책**: Node.js 스크립트로 파일 수정하거나 git checkout 후 재시도

### 6.2 PowerShell 인코딩 문제
- PowerShell로 파일 수정 시 한국어가 깨질 수 있음
- **해결책**: Node.js 사용 또는 git checkout으로 복구

### 6.3 Git GC 오류
```
error: Could not read 180fc455798388eb4fdf7a34a75a2a46d302d2d5
fatal: Failed to traverse parents of commit...
```
- 커밋/푸시에는 영향 없음, 무시해도 됨

### 6.4 CRLF 경고
```
warning: LF will be replaced by CRLF...
```
- Windows 환경에서 정상, 무시해도 됨

---

## 7. 다음 세션을 위한 권장 작업

### 7.1 단기 과제
- [ ] 스타일 센터 탭 수정 후 실제 WordPress에서 테스트
- [ ] Neural Link 서버 업데이트 배포 테스트
- [ ] TGMPA 추천 플러그인 기능 재활성화 (Neural Link 서버 준비 후)

### 7.2 중기 과제
- [ ] Phase 19: Figma 통합 심화
- [ ] Phase 20: 테스트 자동화 및 CI/CD
- [ ] 모든 플러그인 버전 일괄 +1 업데이트

### 7.3 장기 과제
- [ ] AI 기반 스타일 추천 고도화
- [ ] 다국어 지원 확대
- [ ] 성능 최적화

---

## 8. 유용한 명령어 모음

### 8.1 빌드
```bash
# 전체 빌드
cd "C:/Users/computer/Desktop/3J-Labs-Projects/3J-ACF-CSS"
python 3j_build_manager.py --cli --all

# GUI 모드
python 3j_build_manager.py
```

### 8.2 PHP 문법 검사
```bash
# 단일 파일
php -l file.php

# 폴더 전체
find /path/to/folder -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"
```

### 8.3 Git 작업
```bash
# 상태 확인
git status --short

# 커밋
git add -A && git commit -m "메시지"

# 푸시
git push origin main
```

### 8.4 파일 수정 (Node.js 사용)
```bash
node -e "
const fs = require('fs');
let content = fs.readFileSync('file.php', 'utf8');
content = content.replace('old', 'new');
fs.writeFileSync('file.php', content, 'utf8');
"
```

---

## 9. 연락처 및 리소스

- **GitHub**: https://github.com/poetryflow-jay/3JLabs-ACF-Project
- **3J Labs**: https://3j-labs.com/
- **대시보드**: `dashboard.html` (로컬)

---

*이 메모리는 다음 AI 세션의 컨텍스트로 사용됩니다.*
*작성: Claude Opus 4.5 (Sisyphus Mode v4.0)*
*최종 업데이트: 2026-01-06 08:10 KST*
