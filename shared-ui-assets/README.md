# 3J Labs Shared UI Assets

**버전**: 1.1.0
**마지막 업데이트**: 2026년 1월 4일
**유지보수자**: Jason (CTO, 3J Labs)

---

## 개요

Shared UI Assets 폴더는 3J Labs 플러그인 패밀리 전체에 공통으로 사용되는 UI/UX 자산과 PHP 유틸리티를 관리합니다.

이 폴더의 CSS, JS, PHP 파일들은 모든 플러그인에서 공통으로 로드되어 일관된 디자인 언어를 유지하고, 중복 코드를 제거하며, 유지보수 효율성을 높입니다.

---

## 파일 구조

```
shared-ui-assets/
├── README.md (본 파일)
├── css/
│   ├── jj-ui-system-2026.css (메인 UI 시스템)
│   └── jj-section-enhancements-2026.css (섹션 강화)
├── js/
│   └── jj-ui-system-2026.js (UI 인터랙션)
└── php/
    ├── index.php (보안 파일)
    ├── class-jj-shared-loader.php (공통 로더)
    ├── class-jj-ajax-helper.php (AJAX 보안 헬퍼)
    ├── class-jj-file-validator.php (파일 검증 유틸리티)
    └── trait-jj-singleton.php (싱글톤 트레이트)
```

---

## 사용 방법

### WordPress 플러그인에서 로드

```php
// PHP 파일에서 공통 자산 로드
add_action( 'admin_enqueue_scripts', 'load_shared_ui_assets' );

function load_shared_ui_assets( $hook ) {
    // 특정 페이지에서만 로드 (선택 사항)
    if ( strpos( $hook, 'your-plugin-page' ) === false ) {
        return;
    }

    // 공통 CSS 로드
    $shared_css_url = plugin_dir_url( __DIR__ ) . '../shared-ui-assets/css/jj-ui-system-2026.css';
    $shared_css_path = plugin_dir_path( __DIR__ ) . '../shared-ui-assets/css/jj-ui-system-2026.css';

    if ( file_exists( $shared_css_path ) ) {
        $version = file_exists( $shared_css_path ) ? filemtime( $shared_css_path ) : '1.0.0';
        wp_enqueue_style( 'jj-ui-system-shared', $shared_css_url, array(), $version );
    }

    // 섹션 강화 CSS 로드 (선택 사항)
    $section_css_url = plugin_dir_url( __DIR__ ) . '../shared-ui-assets/css/jj-section-enhancements-2026.css';
    $section_css_path = plugin_dir_path( __DIR__ ) . '../shared-ui-assets/css/jj-section-enhancements-2026.css';

    if ( file_exists( $section_css_path ) ) {
        wp_enqueue_style( 'jj-section-enhancements-shared', $section_css_url, array( 'jj-ui-system-shared' ), $version );
    }

    // 공통 JS 로드 (선택 사항)
    $shared_js_url = plugin_dir_url( __DIR__ ) . '../shared-ui-assets/js/jj-ui-system-2026.js';
    $shared_js_path = plugin_dir_path( __DIR__ ) . '../shared-ui-assets/js/jj-ui-system-2026.js';

    if ( file_exists( $shared_js_path ) ) {
        wp_enqueue_script( 'jj-ui-system-shared-js', $shared_js_url, array( 'jquery' ), $version, true );
    }
}
```

---

## 디자인 토큰 (jj-ui-system-2026.css)

### 색상 시스템

```css
--jj-primary: #FF6B35;           /* Primary Orange */
--jj-primary-dark: #E85A2A;      /* Darker Orange */
--jj-accent-blue: #3B82F6;       /* Blue Accent */
--jj-accent-green: #10B981;      /* Green Accent */
--jj-accent-purple: #8B5CF6;     /* Purple Accent */
--jj-accent-amber: #F59E0B;      /* Amber Accent */
```

### 스페이스 토큰

```css
--jj-radius-sm: 6px;               /* Small Radius */
--jj-radius-md: 10px;              /* Medium Radius */
--jj-radius-lg: 12px;              /* Large Radius */
--jj-shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
--jj-shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
--jj-transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
```

---

## 컴포넌트 라이브러리

### Cards

```html
<div class="jj-card">
  <div class="jj-card-header">
    <h3>카드 제목</h3>
  </div>
  <div class="jj-card-body">
    <p>카드 내용</p>
  </div>
</div>
```

### Buttons

```html
<button class="jj-btn-primary">Primary Button</button>
<button class="jj-btn-secondary">Secondary Button</button>
<button class="jj-btn-success">Success Button</button>
<button class="jj-btn-danger">Danger Button</button>
```

### Badges

```html
<span class="jj-badge-success">Active</span>
<span class="jj-badge-warning">Warning</span>
<span class="jj-badge-error">Error</span>
```

---

## JavaScript API

### Tabs

```javascript
JJ_UI.Tabs.init({
  container: '.jj-tabs',
  activeClass: 'active',
  storageKey: 'jj-preferred-tab'
});
```

### Toast Notifications

```javascript
JJ_UI.Toast.show({
  message: '작업이 완료되었습니다.',
  type: 'success',
  duration: 3000
});
```

### Modal Dialog

```javascript
JJ_UI.Modal.open({
  title: '확인',
  content: '정말로 삭제하시겠습니까?',
  onConfirm: function() {
    // 삭제 로직
  }
});
```

---

## 버전 관리

- 파일이 수정될 때마다 `filemtime()`을 사용하여 자동으로 버전을 관리합니다.
- WordPress 브라우저 캐시를 자동으로 무효화합니다.

---

## 지원 플러그인

현재 다음 플러그인이 Shared UI Assets을 사용합니다:

1. ACF CSS Manager (v22.2.0+)
2. WP Bulk Manager (v22.3.0+)
3. ACF Code Snippets Box (v2.2.0+)
4. ACF CSS Neural Link (v6.2.0+)
5. ACF CSS WooCommerce Toolkit (v2.3.0+)
6. ACF CSS AI Extension (v3.2.0+)
7. ACF MBA Nudge Flow (v22.3.0+)

---

## 경고

이 폴더의 파일은 모든 3J Labs 플러그인에 영향을 미치므로, 수정 시 다음 사항을 고려하세요:

1. **하위 호환성**: 기존 스타일을 깨지 않도록 주의하세요.
2. **선택자 특이성**: 너무 일반적인 선택자를 피하고, 플러그인 고유 클래스를 사용하세요.
3. **성능**: 불필요한 CSS/JS를 제거하여 파일 크기를 줄이세요.

---

---

## PHP 유틸리티 (v1.1.0 추가)

### 공통 로더 사용법

```php
// 플러그인 초기화 시 공통 유틸리티 로드
$shared_path = plugin_dir_path( __FILE__ ) . '../shared-ui-assets/php/';
if ( file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
    require_once $shared_path . 'class-jj-shared-loader.php';
    JJ_Shared_Loader::load_all();
}
```

### AJAX 헬퍼 (JJ_Ajax_Helper)

AJAX 요청의 보안 검증을 간소화합니다.

```php
// AJAX 핸들러에서 사용
public function ajax_my_action() {
    $ajax = JJ_Shared_Loader::ajax();

    // nonce + 권한 한 번에 검증
    if ( ! $ajax->verify_request( 'my_nonce_action', 'nonce' ) ) {
        return; // 자동으로 wp_send_json_error 호출됨
    }

    // 파라미터 안전하게 가져오기
    $id = $ajax->get_post_param( 'id', 0, 'int' );
    $email = $ajax->get_post_param( 'email', '', 'email' );

    // 성공 응답
    $ajax->send_success( '작업이 완료되었습니다.', array( 'id' => $id ) );
}
```

### 파일 검증기 (JJ_File_Validator)

ZIP 파일 업로드 검증을 간소화합니다.

```php
// 파일 업로드 핸들러에서 사용
$validator = JJ_Shared_Loader::file_validator();

$result = $validator->validate_zip( $_FILES['plugin_file'] );
if ( is_wp_error( $result ) ) {
    wp_send_json_error( $result->get_error_message() );
}

// 플러그인/테마 타입 감지
$type = $validator->detect_package_type( $result['tmp_name'] );
// 'plugin', 'theme', 또는 'unknown'
```

### 싱글톤 트레이트 (JJ_Singleton_Trait)

클래스에 싱글톤 패턴을 쉽게 적용합니다.

```php
class My_Plugin_Class {
    use JJ_Singleton_Trait;

    protected function __construct() {
        // 초기화 로직
    }
}

// 사용
$instance = My_Plugin_Class::instance();
```

---

## 라이센스

GPLv2 또는 이후 버전

---

*3J Labs 연구소 - 제이x제니x제이슨*
*2026년 1월 4일*
