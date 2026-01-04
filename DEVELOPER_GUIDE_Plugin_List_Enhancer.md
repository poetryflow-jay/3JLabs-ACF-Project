# 개발자 가이드: 플러그인 목록 페이지 향상 시스템

**버전**: 23.0.2  
**최종 업데이트**: 2026년 1월 3일

---

## 📖 개요

플러그인 목록 페이지 향상 시스템은 WordPress 플러그인 목록 페이지(`plugins.php`)의 UI/UX를 대폭 개선하는 통합 시스템입니다. 모든 플러그인에 자동으로 적용되며, 플러그인별 맞춤 기능 링크를 제공합니다.

---

## 🏗️ 아키텍처

### 클래스 구조

```
JJ_Global_Plugin_List_Enhancer (전역)
├── 모든 플러그인에 자동 적용
├── 자동 업데이트 버튼 추가
└── 기본 메타 링크 추가

JJ_Plugin_List_Enhancer (플러그인별)
├── 플러그인별 맞춤 기능 링크
├── 설정 페이지 바로가기
├── 롤백 기능
└── 업그레이드 유도
```

---

## 🚀 빠른 시작

### 1. 기본 초기화

```php
// 플러그인 메인 파일에 추가
add_action( 'plugins_loaded', function () {
    // 클래스 파일 로드
    $plugin_list_enhancer_file = dirname( dirname( __FILE__ ) ) . 
        '/acf-css-really-simple-style-management-center-master/includes/class-jj-plugin-list-enhancer.php';
    
    if ( file_exists( $plugin_list_enhancer_file ) ) {
        require_once $plugin_list_enhancer_file;
    }
    
    // 초기화
    if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
        $enhancer = new JJ_Plugin_List_Enhancer();
        $enhancer->init( array(
            'plugin_file' => __FILE__,
            'plugin_name' => 'Your Plugin Name',
            'settings_url' => admin_url( 'admin.php?page=your-settings' ),
            'text_domain' => 'your-text-domain',
            'version_constant' => 'YOUR_PLUGIN_VERSION',
            'license_constant' => 'YOUR_PLUGIN_LICENSE_TYPE',
            'upgrade_url' => 'https://3j-labs.com/',
            'docs_url' => admin_url( 'admin.php?page=your-docs' ),
            'support_url' => 'https://3j-labs.com/support',
        ) );
    }
}, 25 );
```

### 2. 설정 파라미터

| 파라미터 | 필수 | 설명 |
|---------|------|------|
| `plugin_file` | ✅ | 플러그인 메인 파일 경로 (`__FILE__`) |
| `plugin_name` | ✅ | 플러그인 표시 이름 |
| `settings_url` | ✅ | 설정 페이지 URL |
| `text_domain` | ✅ | 텍스트 도메인 |
| `version_constant` | ✅ | 버전 상수명 |
| `license_constant` | ✅ | 라이센스 타입 상수명 |
| `upgrade_url` | ❌ | 업그레이드 URL (기본: https://3j-labs.com/) |
| `docs_url` | ❌ | 문서 URL |
| `support_url` | ❌ | 지원 URL (기본: https://3j-labs.com/support) |

---

## 🎨 플러그인별 기능 링크 추가

### 위치

`acf-css-really-simple-style-management-center-master/includes/class-jj-plugin-list-enhancer.php`  
→ `get_feature_links()` 메서드

### 예제

```php
// 코드 박스 플러그인 예제
elseif ( strpos( $this->plugin_basename, 'acf-code-snippets-box' ) !== false ) {
    $links['snippets'] = array(
        'url' => admin_url( 'admin.php?page=acf-code-snippets' ),
        'label' => __( '코드 박스', $text_domain ),
        'icon' => '📦',
        'color' => '#4facfe',
        'tooltip' => __( '코드 스니펫 관리 페이지로 이동', $text_domain ),
    );
    $links['presets'] = array(
        'url' => admin_url( 'admin.php?page=acf-code-snippets-presets' ),
        'label' => __( '프리셋 라이브러리', $text_domain ),
        'icon' => '📚',
        'color' => '#00f2fe',
        'tooltip' => __( '프리셋 라이브러리 열기', $text_domain ),
    );
}
```

### 링크 속성

| 속성 | 필수 | 설명 |
|------|------|------|
| `url` | ✅ | 링크 URL |
| `label` | ✅ | 링크 텍스트 |
| `icon` | ❌ | 이모지 아이콘 |
| `color` | ❌ | 링크 색상 (HEX) |
| `tooltip` | ❌ | 툴팁 텍스트 |

---

## 🔧 고급 사용법

### 커스텀 필터 추가

```php
// 플러그인별 메타 링크 추가
add_filter( 'plugin_row_meta', function( $plugin_meta, $plugin_file ) {
    if ( $plugin_file !== plugin_basename( __FILE__ ) ) {
        return $plugin_meta;
    }
    
    $plugin_meta[] = sprintf(
        '<a href="%s" style="font-weight: 800; color: #2271b1;">%s</a>',
        esc_url( 'https://example.com' ),
        esc_html__( '커스텀 링크', 'your-text-domain' )
    );
    
    return $plugin_meta;
}, 10, 2 );
```

### 자동 업데이트 상태 확인

```php
$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
$is_enabled = in_array( plugin_basename( __FILE__ ), $auto_updates, true );

if ( $is_enabled ) {
    // 자동 업데이트 활성화됨
}
```

---

## 🎯 WordPress 훅 활용

### `plugin_action_links_{$plugin_file}`

플러그인 제목 아래 액션 링크 추가

```php
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
    $links['custom'] = '<a href="' . admin_url( 'admin.php?page=custom' ) . '">커스텀</a>';
    return $links;
} );
```

### `plugin_row_meta`

플러그인 설명 아래 메타 링크 추가

```php
add_filter( 'plugin_row_meta', function( $plugin_meta, $plugin_file ) {
    if ( $plugin_file !== plugin_basename( __FILE__ ) ) {
        return $plugin_meta;
    }
    
    $plugin_meta[] = '<a href="https://example.com">커스텀 메타</a>';
    return $plugin_meta;
}, 10, 2 );
```

---

## 🐛 문제 해결

### 자동 업데이트 버튼이 표시되지 않음

1. `JJ_Global_Plugin_List_Enhancer` 클래스가 로드되었는지 확인
2. `plugins_loaded` 훅이 올바르게 실행되는지 확인
3. WordPress 코어 플러그인 제외 로직 확인

### 플러그인별 링크가 표시되지 않음

1. `JJ_Plugin_List_Enhancer::init()` 호출 확인
2. `get_feature_links()` 메서드에 플러그인 슬러그가 올바르게 매칭되는지 확인
3. 플러그인 basename 확인: `plugin_basename( __FILE__ )`

### 스타일이 적용되지 않음

1. `enqueue_assets()` 메서드가 `plugins.php` 페이지에서만 실행되는지 확인
2. CSS 우선순위 확인 (필요시 `!important` 사용)
3. 브라우저 캐시 클리어

---

## 📚 참고 자료

### WordPress 공식 문서
- [Plugin API - plugin_action_links](https://developer.wordpress.org/reference/hooks/plugin_action_links/)
- [Plugin API - plugin_row_meta](https://developer.wordpress.org/reference/hooks/plugin_row_meta/)
- [Auto Updates](https://wordpress.org/support/article/configuring-automatic-background-updates/)

### 내부 문서
- `class-jj-plugin-list-enhancer.php` - 메인 클래스 파일
- `RELEASE_NOTES_v23.0.2.md` - 릴리즈 노트

---

## 🤝 기여하기

새로운 플러그인에 기능 링크를 추가하거나 개선사항을 제안하려면:

1. `get_feature_links()` 메서드에 플러그인별 링크 추가
2. 테스트 후 PR 제출
3. 릴리즈 노트 업데이트

---

**문의**: https://3j-labs.com/support  
**개발팀**: 3J Labs (제이x제니x제이슨 연구소)
