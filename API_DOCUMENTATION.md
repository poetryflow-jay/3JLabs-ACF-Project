# 3J Labs ACF CSS Plugin Family - API 문서

**버전**: v23.0.3  
**작성일**: 2026년 1월 5일  
**최종 업데이트**: Phase 42

---

## 목차

1. [개요](#개요)
2. [REST API](#rest-api)
3. [AJAX 핸들러](#ajax-핸들러)
4. [필터 및 액션 훅](#필터-및-액션-훅)
5. [공유 유틸리티 클래스](#공유-유틸리티-클래스)
6. [보안 모듈 API](#보안-모듈-api)
7. [라이센스 관리 API](#라이센스-관리-api)

---

## 개요

3J Labs ACF CSS Plugin Family는 WordPress 플러그인 생태계로, REST API, AJAX 핸들러, 그리고 다양한 필터/액션 훅을 제공합니다.

### 기본 정보

- **기본 네임스페이스**: `jj-style-guide/v1`
- **인증 방식**: WordPress Nonce + Capability Check
- **응답 형식**: JSON
- **에러 처리**: `WP_Error` 객체

---

## REST API

### 기본 URL

```
/wp-json/jj-style-guide/v1/
```

### 인증

모든 REST API 엔드포인트는 관리자 권한(`manage_options`)이 필요합니다.

```php
// 권한 확인 예제
if ( ! current_user_can( 'manage_options' ) ) {
    return new WP_Error( 'forbidden', '권한이 없습니다.', array( 'status' => 403 ) );
}
```

### 엔드포인트

#### 1. 정보 조회

**GET** `/info`

플러그인 기본 정보를 반환합니다.

**응답 예시**:
```json
{
    "name": "ACF CSS Manager",
    "version": "23.0.2",
    "author": "3J Labs",
    "description": "WordPress 스타일 통합 관리 시스템"
}
```

#### 2. 설정 조회/업데이트

**GET** `/settings`

현재 설정을 조회합니다.

**응답 예시**:
```json
{
    "color_palette": {...},
    "typography": {...},
    "spacing": {...}
}
```

**POST** `/settings`

설정을 업데이트합니다.

**요청 본문**:
```json
{
    "color_palette": {
        "primary": "#007AFF",
        "secondary": "#5856D6"
    }
}
```

**응답 예시**:
```json
{
    "success": true,
    "message": "설정이 업데이트되었습니다."
}
```

#### 3. 팔레트 관리

**GET** `/palettes`

모든 색상 팔레트를 조회합니다.

**POST** `/palettes`

새 팔레트를 생성합니다.

**요청 본문**:
```json
{
    "name": "My Custom Palette",
    "colors": {
        "primary": "#007AFF",
        "secondary": "#5856D6"
    }
}
```

#### 4. 스냅샷 관리

**GET** `/snapshots`

모든 스냅샷 목록을 조회합니다.

**POST** `/snapshots`

새 스냅샷을 생성합니다.

**요청 본문**:
```json
{
    "name": "Backup 2026-01-05",
    "description": "월간 백업"
}
```

**GET** `/snapshots/{id}`

특정 스냅샷을 조회합니다.

**POST** `/snapshots/{id}`

스냅샷을 복원합니다.

**요청 본문**:
```json
{
    "action": "restore"
}
```

#### 5. 스타일 가이드 생성

**POST** `/style-guide/generate`

스타일 가이드를 생성합니다.

**요청 본문**:
```json
{
    "format": "html",
    "include_css": true,
    "include_js": false
}
```

**GET** `/style-guide/analyze`

현재 사이트의 스타일을 분석합니다.

---

## AJAX 핸들러

### 기본 구조

모든 AJAX 핸들러는 다음 구조를 따릅니다:

```php
public function ajax_my_action() {
    // 1. Nonce 검증
    check_ajax_referer( 'my_nonce_action', 'nonce' );
    
    // 2. 권한 확인
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        return;
    }
    
    // 3. 파라미터 검증
    $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    
    // 4. 작업 수행
    $result = $this->do_something( $id );
    
    // 5. 응답
    if ( is_wp_error( $result ) ) {
        wp_send_json_error( array( 'message' => $result->get_error_message() ) );
    } else {
        wp_send_json_success( array( 'data' => $result ) );
    }
}
```

### 공유 AJAX 헬퍼 사용

`JJ_Ajax_Helper`를 사용하면 코드를 간소화할 수 있습니다:

```php
public function ajax_my_action() {
    $ajax = JJ_Shared_Loader::ajax();
    
    // Nonce + 권한 한 번에 검증
    if ( ! $ajax->verify_request( 'my_nonce_action', 'nonce' ) ) {
        return; // 자동으로 wp_send_json_error 호출됨
    }
    
    // 안전한 파라미터 가져오기
    $id = $ajax->get_post_param( 'id', 0, 'int' );
    $email = $ajax->get_post_param( 'email', '', 'email' );
    $items = $ajax->get_post_param( 'items', array(), 'array' );
    
    // 작업 수행
    $result = $this->do_something( $id );
    
    // 응답
    $ajax->send_success( '완료!', array( 'id' => $id ) );
}
```

### 주요 AJAX 액션

#### ACF CSS Manager

- `jj_save_settings`: 설정 저장
- `jj_save_palette`: 팔레트 저장
- `jj_create_snapshot`: 스냅샷 생성
- `jj_restore_snapshot`: 스냅샷 복원
- `jj_generate_style_guide`: 스타일 가이드 생성

#### WP Bulk Manager

- `jj_bulk_install_plugins`: 플러그인 대량 설치
- `jj_bulk_install_themes`: 테마 대량 설치
- `jj_bulk_activate`: 대량 활성화
- `jj_bulk_deactivate`: 대량 비활성화
- `jj_bulk_update`: 대량 업데이트

#### ACF Nudge Flow

- `jj_nf_save_workflow`: 워크플로우 저장
- `jj_nf_trigger_workflow`: 워크플로우 트리거
- `jj_nf_get_analytics`: 분석 데이터 조회

#### ACF Mail SMTP

- `acf_mail_smtp_save_form`: 폼 저장
- `acf_mail_smtp_save_smtp`: SMTP 설정 저장
- `acf_mail_smtp_test_smtp`: SMTP 테스트
- `acf_mail_smtp_save_automation`: 자동화 규칙 저장

---

## 필터 및 액션 훅

### 액션 (Actions)

#### `jj_style_guide_settings_updated`

설정이 업데이트될 때 실행됩니다.

```php
add_action( 'jj_style_guide_settings_updated', function( $old_settings, $new_settings ) {
    // 설정 변경 시 실행할 코드
}, 10, 2 );
```

#### `jj_snapshot_created`

스냅샷이 생성될 때 실행됩니다.

```php
add_action( 'jj_snapshot_created', function( $snapshot_id, $snapshot_data ) {
    // 스냅샷 생성 시 실행할 코드
}, 10, 2 );
```

#### `jj_snapshot_restored`

스냅샷이 복원될 때 실행됩니다.

```php
add_action( 'jj_snapshot_restored', function( $snapshot_id ) {
    // 스냅샷 복원 시 실행할 코드
}, 10, 1 );
```

#### `jj_build_complete`

빌드가 완료될 때 실행됩니다 (빌드 매니저).

```php
add_action( 'jj_build_complete', function( $plugin_id, $version, $zip_path ) {
    // 빌드 완료 시 실행할 코드
}, 10, 3 );
```

### 필터 (Filters)

#### `jj_style_guide_options`

옵션 값을 필터링합니다.

```php
add_filter( 'jj_style_guide_options', function( $options ) {
    // 옵션 수정
    $options['custom_option'] = 'custom_value';
    return $options;
} );
```

#### `jj_css_output`

CSS 출력을 필터링합니다.

```php
add_filter( 'jj_css_output', function( $css, $context ) {
    // CSS 수정
    $css .= "\n/* Custom CSS */\n.custom { color: red; }";
    return $css;
}, 10, 2 );
```

#### `jj_build_exclude_patterns`

빌드 시 제외할 파일 패턴을 필터링합니다.

```php
add_filter( 'jj_build_exclude_patterns', function( $patterns ) {
    $patterns[] = '^custom-exclude/';
    return $patterns;
} );
```

#### `jj_license_check`

라이센스 검증 로직을 필터링합니다.

```php
add_filter( 'jj_license_check', function( $is_valid, $license_key, $plugin_id ) {
    // 커스텀 라이센스 검증 로직
    return $is_valid;
}, 10, 3 );
```

---

## 공유 유틸리티 클래스

### JJ_Ajax_Helper

AJAX 핸들러를 간소화하는 헬퍼 클래스입니다.

#### 사용 방법

```php
$ajax = JJ_Shared_Loader::ajax();

// 요청 검증
if ( ! $ajax->verify_request( 'action_name', 'nonce' ) ) {
    return; // 자동으로 wp_send_json_error 호출
}

// 파라미터 가져오기
$id = $ajax->get_post_param( 'id', 0, 'int' );
$name = $ajax->get_post_param( 'name', '', 'text' );
$email = $ajax->get_post_param( 'email', '', 'email' );
$items = $ajax->get_post_param( 'items', array(), 'array' );

// 성공 응답
$ajax->send_success( '완료!', array( 'id' => $id ) );

// 에러 응답
$ajax->send_error( '오류 발생!' );
```

#### 메서드

- `verify_request( $action, $nonce_key = 'nonce' )`: Nonce 및 권한 검증
- `get_post_param( $key, $default, $type = 'text' )`: 안전한 POST 파라미터 가져오기
- `get_get_param( $key, $default, $type = 'text' )`: 안전한 GET 파라미터 가져오기
- `send_success( $message, $data = array() )`: 성공 응답 전송
- `send_error( $message, $data = array() )`: 에러 응답 전송

### JJ_File_Validator

파일 업로드 검증을 간소화하는 클래스입니다.

#### 사용 방법

```php
$validator = JJ_Shared_Loader::file_validator();

// ZIP 파일 검증
$result = $validator->validate_zip( $_FILES['plugin_file'] );

if ( is_wp_error( $result ) ) {
    wp_send_json_error( $result->get_error_message() );
}

// 패키지 타입 감지
$type = $validator->detect_package_type( $result['tmp_name'] );
// 반환값: 'plugin' 또는 'theme'
```

#### 메서드

- `validate_zip( $file )`: ZIP 파일 검증
- `detect_package_type( $zip_path )`: 플러그인/테마 타입 감지
- `extract_zip( $zip_path, $dest_dir )`: ZIP 파일 압축 해제

### JJ_Singleton_Trait

싱글톤 패턴을 간단하게 구현하는 트레이트입니다.

#### 사용 방법

```php
class My_Plugin_Class {
    use JJ_Singleton_Trait;
    
    protected function __construct() {
        // 초기화 코드
    }
    
    public function do_something() {
        // 메서드 구현
    }
}

// 인스턴스 가져오기
$instance = My_Plugin_Class::instance();
$instance->do_something();
```

---

## 보안 모듈 API

### JJ_Security_Module_V25_Loader

통합 보안 모듈 로더입니다.

#### 사용 방법

```php
// 보안 모듈 로드
$security = JJ_Security_Module_V25_Loader::instance();

// 파일 무결성 모니터링 활성화
$security->enable_file_integrity_monitoring();

// 업데이트 보안 활성화
$security->enable_update_security();

// 라이센스 보안 활성화
$security->enable_license_security();
```

### 파일 무결성 모니터링

```php
$integrity = JJ_File_Integrity_Shared::instance();

// 파일 해시 생성
$hash = $integrity->generate_file_hash( $file_path );

// 파일 변경 감지
$is_changed = $integrity->check_file_changed( $file_path, $stored_hash );

// 자동 복구
$integrity->auto_recover_file( $file_path );
```

### 업데이트 보안

```php
$update_security = JJ_Update_Security_Shared::instance();

// 패키지 서명 검증
$is_valid = $update_security->verify_package_signature( $zip_path, $signature );

// 채널 검증
$is_valid_channel = $update_security->verify_update_channel( $channel );

// 롤백 보호
$update_security->enable_rollback_protection();
```

---

## 라이센스 관리 API

### JJ_License_Manager_Shared

공유 라이센스 관리자입니다.

#### 사용 방법

```php
$license = JJ_License_Manager_Shared::instance();

// 라이센스 키 검증
$is_valid = $license->verify_license_key( $license_key, $plugin_id );

// 라이센스 상태 조회
$status = $license->get_license_status( $license_key );

// 라이센스 활성화
$result = $license->activate_license( $license_key, $plugin_id, $domain );

// 라이센스 비활성화
$result = $license->deactivate_license( $license_key, $plugin_id, $domain );
```

### 라이센스 보안

```php
$license_security = JJ_License_Security_Shared::instance();

// 라이센스 형식 검증
$is_valid_format = $license_security->validate_license_format( $license_key );

// 라이센스 변조 감지
$is_tampered = $license_security->detect_tampering( $license_key );

// 비정상 사용 패턴 감지
$is_abnormal = $license_security->detect_abnormal_usage( $license_key );
```

---

## 에러 처리

### WP_Error 사용

모든 API는 `WP_Error` 객체를 사용하여 에러를 처리합니다.

```php
// 에러 생성
$error = new WP_Error( 'error_code', '에러 메시지', array( 'additional_data' => 'value' ) );

// 에러 확인
if ( is_wp_error( $result ) ) {
    $error_code = $result->get_error_code();
    $error_message = $result->get_error_message();
    $error_data = $result->get_error_data();
}
```

### 표준 에러 코드

- `forbidden`: 권한 없음 (403)
- `not_found`: 리소스를 찾을 수 없음 (404)
- `invalid_request`: 잘못된 요청 (400)
- `server_error`: 서버 오류 (500)

---

## 예제 코드

### REST API 클라이언트 (JavaScript)

```javascript
// 설정 조회
fetch('/wp-json/jj-style-guide/v1/settings', {
    method: 'GET',
    headers: {
        'X-WP-Nonce': wpApiSettings.nonce
    }
})
.then(response => response.json())
.then(data => {
    console.log('Settings:', data);
});

// 설정 업데이트
fetch('/wp-json/jj-style-guide/v1/settings', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': wpApiSettings.nonce
    },
    body: JSON.stringify({
        color_palette: {
            primary: '#007AFF',
            secondary: '#5856D6'
        }
    })
})
.then(response => response.json())
.then(data => {
    console.log('Update result:', data);
});
```

### AJAX 클라이언트 (jQuery)

```javascript
// 설정 저장
jQuery.ajax({
    url: ajaxurl,
    type: 'POST',
    data: {
        action: 'jj_save_settings',
        nonce: jjSettings.nonce,
        settings: {
            color_palette: {
                primary: '#007AFF'
            }
        }
    },
    success: function(response) {
        if (response.success) {
            console.log('Settings saved:', response.data);
        } else {
            console.error('Error:', response.data.message);
        }
    }
});
```

---

## 버전 정보

- **v23.0.3**: Phase 42 - 실시간 업데이트, 빌드 시스템 개선
- **v23.0.2**: Phase 41 - 플러그인 목록 UI/UX 개선
- **v23.0.1**: Phase 40.1 - Code Snippets Box & Nudge Flow 개선
- **v22.5.1**: Phase 39.3 - 보안 강화

---

**작성자**: 3J Labs Development Team  
**문서 버전**: 1.0.0  
**최종 업데이트**: 2026년 1월 5일
