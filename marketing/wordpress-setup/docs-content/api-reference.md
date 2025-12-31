# 🛠️ REST API 레퍼런스

ACF CSS Manager REST API를 사용하여 외부 시스템과 통합할 수 있습니다.

> ⚠️ REST API는 Partner 버전 이상에서 사용 가능합니다.

---

## 🔐 인증

모든 API 요청에는 인증이 필요합니다.

### 방법 1: WordPress Application Password

```bash
curl -X GET "https://your-site.com/wp-json/jj-style-guide/v1/settings" \
  -u "username:application_password"
```

### 방법 2: JWT 토큰 (플러그인 필요)

```bash
curl -X GET "https://your-site.com/wp-json/jj-style-guide/v1/settings" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## 📍 엔드포인트

### Base URL

```
https://your-site.com/wp-json/jj-style-guide/v1/
```

---

## 📖 설정 API

### GET /settings

현재 플러그인 설정을 조회합니다.

**요청:**

```bash
curl -X GET "https://your-site.com/wp-json/jj-style-guide/v1/settings" \
  -u "admin:xxxx xxxx xxxx xxxx"
```

**응답:**

```json
{
  "colors": {
    "primary_color": "#2563eb",
    "secondary_color": "#f59e0b",
    "accent_color": "#06b6d4",
    "background_color": "#ffffff",
    "text_color": "#1e293b"
  },
  "typography": {
    "heading_font_family": "Space Grotesk",
    "body_font_family": "Noto Sans KR",
    "base_font_size": "16px"
  },
  "buttons": {
    "border_radius": "12px",
    "padding": "12px 24px"
  }
}
```

---

### POST /settings

플러그인 설정을 업데이트합니다.

**요청:**

```bash
curl -X POST "https://your-site.com/wp-json/jj-style-guide/v1/settings" \
  -u "admin:xxxx xxxx xxxx xxxx" \
  -H "Content-Type: application/json" \
  -d '{
    "settings": {
      "colors": {
        "primary_color": "#1d4ed8"
      }
    }
  }'
```

**응답:**

```json
{
  "message": "Settings updated successfully."
}
```

---

## 🔑 라이센스 API (Neural Link)

Neural Link 서버의 라이센스 관리 API입니다.

### Base URL

```
https://neural-link-server.com/wp-json/acf-neural-link/v1/
```

---

### GET /ping

서버 상태를 확인합니다.

**요청:**

```bash
curl -X GET "https://neural-link.com/wp-json/acf-neural-link/v1/ping" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

**응답:**

```json
{
  "success": true,
  "message": "Neural Link Server is running",
  "version": "3.2.0",
  "time": "2025-12-19 15:30:00"
}
```

---

### POST /license/issue

새 라이센스를 발행합니다.

**요청:**

```bash
curl -X POST "https://neural-link.com/wp-json/acf-neural-link/v1/license/issue" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@example.com",
    "edition": "premium",
    "duration": 365,
    "site_limit": 0,
    "order_id": "WC-12345",
    "source": "woocommerce"
  }'
```

**파라미터:**

| 파라미터 | 타입 | 필수 | 설명 |
|---------|------|------|------|
| email | string | ✅ | 고객 이메일 |
| edition | string | ✅ | free, basic, premium, unlimited, partner, master |
| duration | int | ❌ | 라이센스 기간 (일), 0=영구, 기본값: 365 |
| site_limit | int | ❌ | 사이트 수 제한, 0=무제한, 기본값: 0 |
| order_id | string | ❌ | 주문 번호 |
| source | string | ❌ | 발행 소스 (woocommerce, manual, api) |

**응답:**

```json
{
  "success": true,
  "license_key": "PRE-ABCDE-FGHIJ-KLMNO-PQRST",
  "edition": "premium",
  "expires_at": "2026-12-19 15:30:00",
  "site_limit": 0
}
```

---

### POST /license/verify

라이센스 유효성을 검증합니다.

**요청:**

```bash
curl -X POST "https://neural-link.com/wp-json/acf-neural-link/v1/license/verify" \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "PRE-ABCDE-FGHIJ-KLMNO-PQRST",
    "site_url": "https://customer-site.com"
  }'
```

**응답 (유효):**

```json
{
  "valid": true,
  "activated": true,
  "edition": "premium",
  "expires_at": "2026-12-19 15:30:00",
  "site_limit": 0,
  "sites_count": 1,
  "activated_sites": ["https://customer-site.com"]
}
```

**응답 (만료):**

```json
{
  "valid": false,
  "message": "만료된 라이센스입니다.",
  "expired_at": "2025-12-19 15:30:00"
}
```

---

### POST /license/activate

사이트에 라이센스를 활성화합니다.

**요청:**

```bash
curl -X POST "https://neural-link.com/wp-json/acf-neural-link/v1/license/activate" \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "PRE-ABCDE-FGHIJ-KLMNO-PQRST",
    "site_url": "https://new-site.com"
  }'
```

**응답 (성공):**

```json
{
  "success": true,
  "message": "라이센스가 활성화되었습니다.",
  "edition": "premium",
  "expires_at": "2026-12-19 15:30:00",
  "sites_count": 2
}
```

**응답 (한도 초과):**

```json
{
  "success": false,
  "message": "사이트 활성화 한도에 도달했습니다.",
  "site_limit": 1,
  "sites_count": 1
}
```

---

### POST /license/deactivate

사이트에서 라이센스를 비활성화합니다.

**요청:**

```bash
curl -X POST "https://neural-link.com/wp-json/acf-neural-link/v1/license/deactivate" \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "PRE-ABCDE-FGHIJ-KLMNO-PQRST",
    "site_url": "https://old-site.com"
  }'
```

**응답:**

```json
{
  "success": true,
  "message": "라이센스가 비활성화되었습니다.",
  "sites_count": 1
}
```

---

### GET /license/info

라이센스 상세 정보를 조회합니다. (관리자 전용)

**요청:**

```bash
curl -X GET "https://neural-link.com/wp-json/acf-neural-link/v1/license/info?license_key=PRE-ABCDE-FGHIJ-KLMNO-PQRST" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

**응답:**

```json
{
  "success": true,
  "license_key": "PRE-ABCDE-FGHIJ-KLMNO-PQRST",
  "email": "customer@example.com",
  "edition": "premium",
  "status": "active",
  "site_limit": 0,
  "sites_count": 2,
  "activated_sites": [
    "https://site1.com",
    "https://site2.com"
  ],
  "order_id": "WC-12345",
  "source": "woocommerce",
  "created_at": "2025-12-19 15:30:00",
  "expires_at": "2026-12-19 15:30:00"
}
```

---

### GET /licenses

라이센스 목록을 조회합니다. (관리자 전용)

**요청:**

```bash
curl -X GET "https://neural-link.com/wp-json/acf-neural-link/v1/licenses?page=1&per_page=20&edition=premium" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

**파라미터:**

| 파라미터 | 타입 | 기본값 | 설명 |
|---------|------|--------|------|
| page | int | 1 | 페이지 번호 |
| per_page | int | 20 | 페이지당 항목 수 (최대 100) |
| email | string | - | 이메일로 필터 |
| edition | string | - | 에디션으로 필터 |
| status | string | - | 상태로 필터 (active, expired, revoked) |

**응답:**

```json
{
  "success": true,
  "total": 150,
  "page": 1,
  "per_page": 20,
  "total_pages": 8,
  "licenses": [
    {
      "id": 1,
      "license_key": "PRE-ABCDE-...",
      "email": "customer@example.com",
      "edition": "premium",
      "status": "active",
      ...
    }
  ]
}
```

---

## 🔗 Webhook

스타일 변경 시 외부 서비스에 알림을 보냅니다.

### 이벤트 종류

| 이벤트 | 설명 |
|--------|------|
| `jj_style_guide_settings_updated` | 스타일 설정 변경 |
| `jj_style_guide_admin_center_updated` | 관리자 센터 설정 변경 |

### Webhook Payload

```json
{
  "event": "jj_style_guide_settings_updated",
  "site_url": "https://your-site.com",
  "timestamp": 1703001234,
  "data": {
    "changed_keys": ["primary_color", "heading_font"],
    "user_id": 1
  }
}
```

### 서명 검증 (PHP)

```php
function verify_webhook_signature( $payload, $signature, $secret ) {
    $timestamp = $_SERVER['HTTP_X_JJ_TIMESTAMP'];
    $expected = hash_hmac( 'sha256', $timestamp . '.' . $payload, $secret );
    return hash_equals( $expected, $signature );
}

// 사용
$payload = file_get_contents( 'php://input' );
$signature = $_SERVER['HTTP_X_JJ_SIGNATURE'];
$secret = 'your_webhook_secret';

if ( verify_webhook_signature( $payload, $signature, $secret ) ) {
    // 유효한 요청
    $data = json_decode( $payload, true );
    // 처리...
} else {
    http_response_code( 401 );
    exit( 'Invalid signature' );
}
```

---

## 📊 응답 코드

| 코드 | 설명 |
|------|------|
| 200 | 성공 |
| 400 | 잘못된 요청 (파라미터 오류) |
| 401 | 인증 필요 |
| 403 | 권한 없음 |
| 404 | 리소스 없음 |
| 429 | 요청 한도 초과 |
| 500 | 서버 오류 |

---

## 💻 SDK / 라이브러리

### PHP

```php
// WordPress 환경
$response = wp_remote_post( 'https://neural-link.com/wp-json/acf-neural-link/v1/license/issue', array(
    'headers' => array(
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type'  => 'application/json',
    ),
    'body' => wp_json_encode( array(
        'email'   => 'customer@example.com',
        'edition' => 'premium',
    ) ),
) );

$body = json_decode( wp_remote_retrieve_body( $response ), true );
```

### JavaScript

```javascript
const response = await fetch('https://neural-link.com/wp-json/acf-neural-link/v1/license/verify', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        license_key: 'PRE-ABCDE-FGHIJ-KLMNO-PQRST',
        site_url: 'https://customer-site.com'
    })
});

const data = await response.json();
console.log(data.valid ? 'Valid license' : 'Invalid license');
```

### Python

```python
import requests

response = requests.post(
    'https://neural-link.com/wp-json/acf-neural-link/v1/license/issue',
    headers={
        'Authorization': f'Bearer {api_key}',
        'Content-Type': 'application/json',
    },
    json={
        'email': 'customer@example.com',
        'edition': 'premium',
        'duration': 365,
    }
)

data = response.json()
print(f"License Key: {data['license_key']}")
```

---

## 📞 지원

API 관련 문의: api@j-j-labs.com

