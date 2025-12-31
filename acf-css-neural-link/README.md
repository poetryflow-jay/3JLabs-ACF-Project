# JJ License Manager

**버전**: 2.1.1  
**마지막 업데이트**: 2025년 1월 22일

JJ's Center of Style Setting 플러그인 전용 라이센스 관리 및 업데이트 시스템입니다. **개발자 전용 플러그인**으로, WordPress 기반 플러그인 판매 웹사이트를 위한 완전한 라이센스 관리 솔루션을 제공합니다.

## ⚠️ 중요: 사용 대상

**라이센스/업데이트 매니저는 개발자 전용 플러그인입니다.**
- 일반 사용자(free, basic, premium, unlimited 버전 소유자)는 라이센스 매니저를 사용할 필요가 없습니다
- dev 버전 소유자만 라이센스 매니저를 병행 사용합니다

## 최근 변경 사항

### v2.1.1 (2025-01-22)
- 버전 관리 및 기술 문서 보강
- 모든 문서화 개선 및 상세화

### v2.1.0 (2025-01-22)

### [FEATURE] 원격 제어 기능
- 타 사이트 플러그인 강제 활성화/비활성화
- 활성화/비활성화 현황 실시간 모니터링
- 남은 라이센스 기간 자동 계산 및 관리

### [FEATURE] 로그 수집 및 분석
- 원격 사이트 오류 자동 수집
- 로그 분석 및 문제 점검
- 심각한 오류 자동 알림

### [FEATURE] 업데이트 배포 및 공지
- 플러그인 업데이트 자동 배포
- 공지 전송 기능
- 업데이트 채널 관리

### [IMPROVEMENT] 보안 강화
- 서명 기반 원격 명령 검증
- IP 화이트리스트 지원
- Rate limiting 강화

## 주요 기능

- **자동 라이센스 생성**: WooCommerce 주문 완료 시 자동으로 라이센스 키 생성
- **라이센스 검증 API**: REST API를 통한 라이센스 검증 및 활성화 관리
- **활성화 관리**: 사이트별 라이센스 활성화 추적 및 제한
- **만료 관리**: 구독 기간에 따른 자동 만료 및 비활성화
- **히스토리 추적**: 모든 라이센스 활동의 상세 기록
- **회원 연동**: WordPress 사용자 및 WooCommerce 주문과 완전 연동

## 요구사항

- WordPress 6.0 이상
- PHP 7.4 이상
- WooCommerce (필수)

## 설치

1. 플러그인 파일을 `wp-content/plugins/` 디렉터리에 업로드
2. WordPress 관리자에서 플러그인 활성화
3. WooCommerce가 설치 및 활성화되어 있어야 합니다

## WooCommerce 상품 설정

라이센스를 자동으로 생성하려면 WooCommerce 상품에 다음 메타 필드를 설정하세요:

- `_jj_license_type`: 라이센스 타입 (FREE, BASIC, PREM, UNLIM)
- `_jj_subscription_period`: 구독 기간 단위 (day, week, month, year)
- `_jj_subscription_length`: 구독 기간 길이 (숫자 또는 'lifetime' 평생 라이센스)

또는 상품 이름이나 SKU에 라이센스 타입이 포함되어 있으면 자동으로 감지됩니다.

## API 엔드포인트

플러그인은 다음 REST API 엔드포인트를 제공합니다:

### 라이센스 검증
```
POST /wp-json/jj-license/v1/verify
```

**파라미터:**
- `license_key` (필수): 라이센스 키
- `site_id` (필수): 사이트 ID
- `site_url` (필수): 사이트 URL
- `plugin_version` (선택): 플러그인 버전

**응답:**
```json
{
  "valid": true,
  "type": "PREM",
  "message": "라이센스가 활성화되었습니다.",
  "status": "active",
  "expires_timestamp": 1735689600,
  "valid_until": 1735689600,
  "days_until_expiry": 30,
  "expiring_soon": false
}
```

### 라이센스 활성화
```
POST /wp-json/jj-license/v1/activate
```

**파라미터:**
- `license_key` (필수): 라이센스 키
- `site_id` (필수): 사이트 ID
- `site_url` (필수): 사이트 URL

**응답:**
```json
{
  "success": true,
  "message": "라이센스가 활성화되었습니다.",
  "activation_id": 123
}
```

### 라이센스 비활성화
```
POST /wp-json/jj-license/v1/deactivate
```

**파라미터:**
- `license_key` (필수): 라이센스 키
- `site_id` (필수): 사이트 ID
- `site_url` (필수): 사이트 URL

**응답:**
```json
{
  "success": true,
  "message": "라이센스가 비활성화되었습니다."
}
```

### 원격 제어 (v2.1.0+)
```
POST /wp-json/jj-license/v1/remote-command
```

**파라미터:**
- `action` (필수): 명령 타입 (force_activate, force_deactivate)
- `license_key` (필수): 라이센스 키
- `site_id` (필수): 사이트 ID
- `signature` (필수): HMAC SHA256 서명
- `timestamp` (필수): 타임스탬프

**보안:**
- 모든 원격 명령은 HMAC SHA256 서명으로 검증됩니다
- IP 화이트리스트 지원
- Rate limiting 적용

### 로그 수집 (v2.1.0+)
```
POST /wp-json/jj-license/v1/collect-log
```

**파라미터:**
- `license_key` (필수): 라이센스 키
- `site_id` (필수): 사이트 ID
- `site_url` (필수): 사이트 URL
- `log_data` (필수): 로그 데이터 객체
  - `type`: 로그 타입 (error, warning, info)
  - `level`: 로그 레벨 (critical, fatal, error, warning, info)
  - `message`: 로그 메시지
  - `context`: 컨텍스트 정보 (JSON)
  - `stack_trace`: 스택 트레이스 (선택)
  - `plugin_version`: 플러그인 버전
  - `wordpress_version`: WordPress 버전
  - `php_version`: PHP 버전

### 업데이트 알림 수신 (v2.1.0+)
```
POST /wp-json/jj-license/v1/update-notification
```

**파라미터:**
- `license_key` (필수): 라이센스 키
- `site_id` (필수): 사이트 ID
- `plugin_slug` (필수): 플러그인 슬러그
- `version` (필수): 새 버전
- `update_channel` (필수): 업데이트 채널 (stable, beta, test, dev)
- `signature` (필수): HMAC SHA256 서명

### 공지 수신 (v2.1.0+)
```
POST /wp-json/jj-license/v1/announcement
```

**파라미터:**
- `license_key` (필수): 라이센스 키
- `site_id` (필수): 사이트 ID
- `announcement_type` (필수): 공지 타입 (info, warning, error, success)
- `title` (필수): 공지 제목
- `message` (필수): 공지 메시지
- `signature` (필수): HMAC SHA256 서명

## 데이터베이스 구조

플러그인은 다음 테이블을 생성합니다:

### 기본 테이블
- `wp_jj_licenses`: 라이센스 정보
  - `id`: 라이센스 ID (Primary Key)
  - `license_key`: 라이센스 키 (Unique)
  - `license_type`: 라이센스 타입 (FREE, BASIC, PREM, UNLIM)
  - `order_id`: WooCommerce 주문 ID
  - `user_id`: WordPress 사용자 ID
  - `created_at`: 생성 일시
  - `expires_at`: 만료 일시 (NULL = 평생 라이센스)
  - `status`: 상태 (active, expired, cancelled)

- `wp_jj_license_activations`: 라이센스 활성화 기록
  - `id`: 활성화 ID (Primary Key)
  - `license_id`: 라이센스 ID (Foreign Key)
  - `site_id`: 사이트 ID
  - `site_url`: 사이트 URL
  - `is_active`: 활성화 여부 (1 = 활성, 0 = 비활성)
  - `activated_at`: 활성화 일시
  - `deactivated_at`: 비활성화 일시
  - `last_check`: 마지막 검증 일시

- `wp_jj_license_history`: 라이센스 활동 히스토리
  - `id`: 히스토리 ID (Primary Key)
  - `license_id`: 라이센스 ID (Foreign Key)
  - `action`: 활동 타입 (activated, deactivated, verified, expired, etc.)
  - `description`: 활동 설명
  - `created_at`: 생성 일시

### v2.1.0+ 추가 테이블
- `wp_jj_license_logs`: 원격 사이트 로그 수집
  - `id`: 로그 ID (Primary Key)
  - `license_id`: 라이센스 ID (Foreign Key)
  - `site_id`: 사이트 ID
  - `site_url`: 사이트 URL
  - `log_type`: 로그 타입 (error, warning, info)
  - `log_level`: 로그 레벨 (critical, fatal, error, warning, info)
  - `message`: 로그 메시지
  - `context`: 컨텍스트 정보 (JSON)
  - `stack_trace`: 스택 트레이스
  - `plugin_version`: 플러그인 버전
  - `wordpress_version`: WordPress 버전
  - `php_version`: PHP 버전
  - `created_at`: 생성 일시

- `wp_jj_license_updates`: 업데이트 배포 기록
  - `id`: 업데이트 ID (Primary Key)
  - `plugin_slug`: 플러그인 슬러그
  - `version`: 버전
  - `update_channel`: 업데이트 채널 (stable, beta, test, dev)
  - `distributed_at`: 배포 일시
  - `status`: 상태 (distributed, pending, cancelled)

- `wp_jj_license_announcements`: 공지 기록
  - `id`: 공지 ID (Primary Key)
  - `announcement_type`: 공지 타입 (info, warning, error, success)
  - `title`: 공지 제목
  - `message`: 공지 메시지
  - `sent_at`: 전송 일시
  - `status`: 상태 (sent, pending, cancelled)

## 라이센스 타입

- **FREE**: 무료 버전 (1개 사이트)
- **BASIC**: 기본 버전 (1개 사이트)
- **PREM**: 프리미엄 버전 (1개 사이트)
- **UNLIM**: 언리미티드 버전 (무제한 사이트)

## 개발자 정보

- **개발자**: Jay & Jenny Labs
- **웹사이트**: https://j-j-labs.com
- **라이센스**: GPLv2 or later

## 보안

### 서명 기반 검증
모든 원격 명령 및 업데이트 알림은 HMAC SHA256 서명으로 검증됩니다:

```php
$signature = hash_hmac('sha256', $data_string, $secret_key);
```

### IP 화이트리스트
원격 명령을 받는 사이트에서 IP 화이트리스트를 설정할 수 있습니다:

```php
// wp-config.php 또는 플러그인 설정에서
define('JJ_LICENSE_MANAGER_ALLOWED_IPS', '192.168.1.1,10.0.0.1');
```

### Rate Limiting
API 엔드포인트는 Rate Limiting을 적용하여 남용을 방지합니다:
- 기본 제한: 분당 60회 요청
- 라이센스 검증: 분당 10회
- 원격 명령: 분당 5회

## 개발자 가이드

### 라이센스 생성
```php
$license_manager = new JJ_License_Manager();
$license_key = $license_manager->generate_license_key('PREM', 365); // Premium, 1년
```

### 라이센스 검증
```php
$validator = new JJ_License_Validator();
$result = $validator->verify($license_key, $site_id, $site_url);
if ($result['valid']) {
    // 라이센스 유효
}
```

### 원격 제어
```php
$remote_control = new JJ_License_Remote_Control();
$result = $remote_control->force_activate_plugin($license_key, $site_id, $site_url);
```

### 로그 수집
```php
$log_collector = new JJ_License_Log_Collector();
$result = $log_collector->collect_log($license_key, $site_id, $site_url, $log_data);
```

### 업데이트 배포
```php
$update_distributor = new JJ_License_Update_Distributor();
$result = $update_distributor->distribute_update('jj-simple-style-guide', '5.2.1', 'stable');
```

## 문제 해결

### 라이센스가 활성화되지 않을 때
1. WooCommerce 상품 메타 필드 확인
2. 라이센스 타입이 올바르게 설정되었는지 확인
3. 데이터베이스 테이블이 생성되었는지 확인

### 원격 제어가 작동하지 않을 때
1. 서명 검증 확인
2. IP 화이트리스트 확인
3. Rate limiting 확인
4. 네트워크 연결 확인

### 로그가 수집되지 않을 때
1. 로그 수집 API 엔드포인트 확인
2. 라이센스 검증 확인
3. 데이터베이스 테이블 확인

## 지원

문제가 발생하거나 질문이 있으시면 웹사이트를 방문해주세요:
- **웹사이트**: https://j-j-labs.com
- **이메일**: support@j-j-labs.com

## 라이센스

이 플러그인은 GPLv2 또는 이후 버전의 라이센스를 따릅니다.

