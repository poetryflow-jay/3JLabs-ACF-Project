# 라이센스 보안 강화 시스템 v25.0.0

## 📋 개요

가짜 라이센스 키 방지 및 권한 속임 방지를 위한 종합 보안 시스템입니다.

---

## 🎯 주요 기능

### 1. 가짜 라이센스 키 감지

#### 형식 검증
- **라이센스 키 형식**: `XXXXX-XXXXX-XXXXX-XXXXX` (대문자, 숫자)
- **체크섬 검증**: 마지막 4자리 체크섬으로 변조 감지
- **정규식 검증**: 형식이 맞지 않으면 즉시 차단

#### 서버 측 검증
- **실시간 검증**: 라이센스 키 저장 시 즉시 서버 검증
- **캐시 검증**: 1시간 캐시로 성능 최적화
- **오프라인 모드**: 서버 연결 실패 시 캐시된 결과 사용

### 2. 권한 속임 방지

#### 라이센스 타입 검증
- **서버 타입 우선**: 로컬 타입과 서버 타입이 다르면 서버 결과 우선
- **자동 무효화**: 타입 불일치 시 자동으로 FREE로 강제
- **위반 로그**: 모든 타입 불일치 기록

#### 라이센스 에디션 검증
- **마스터/파트너 사기 방지**: 서버에서 실제 에디션 확인
- **에디션 불일치 감지**: 로컬 에디션과 서버 에디션이 다르면 차단
- **자동 강등**: 위반 시 자동으로 free 에디션으로 강제

### 3. 라이센스 키 변조 감지

#### 해시 기반 검증
- **SHA-512 해시**: 라이센스 키의 SHA-512 해시 저장
- **변조 감지**: 해시가 변경되면 즉시 서버 재검증
- **자동 무효화**: 변조 감지 시 자동으로 라이센스 무효화

### 4. 실시간 모니터링

#### 주기적 검증
- **1시간마다 검증**: 관리자 페이지 접속 시 자동 검증
- **일일 서버 검증**: 매일 서버에 검증 요청
- **위반 알림**: 위반 감지 시 즉시 관리자 알림

#### 위반 로그
- **상세 로그**: 모든 보안 위반 기록
- **IP 주소 추적**: 위반 시 IP 주소 기록
- **사용자 ID 추적**: 위반 시 사용자 ID 기록

---

## 🔧 기술 구현

### 라이센스 키 형식

```
XXXXX-XXXXX-XXXXX-XXXXX
```

- 각 섹션: 5자리 (대문자, 숫자)
- 마지막 4자리: 체크섬
- 총 길이: 23자 (하이픈 포함)

### 체크섬 알고리즘

```php
$checksum = substr($key_without_dashes, -4);
$data = substr($key_without_dashes, 0, -4);
$calculated_checksum = hash('sha256', $data . wp_salt('auth'));
```

### 서버 검증 API

**엔드포인트**: `https://license.3j-labs.com/api/v1/verify`

**요청 형식**:
```json
{
  "license_key": "XXXXX-XXXXX-XXXXX-XXXXX",
  "site_url": "https://example.com",
  "site_name": "Example Site",
  "plugin_version": "25.0.0",
  "wp_version": "6.4",
  "timestamp": 1234567890,
  "signature": "hmac_sha256_signature"
}
```

**응답 형식**:
```json
{
  "success": true,
  "is_valid": true,
  "data": {
    "type": "PREMIUM",
    "edition": "premium",
    "expires_at": "2026-12-31",
    "sites_allowed": 5,
    "sites_used": 2
  },
  "signature": "server_response_signature"
}
```

---

## 🛡️ 보안 계층

### 1단계: 형식 검증
- 정규식으로 기본 형식 검증
- 체크섬으로 변조 감지

### 2단계: 로컬 해시 검증
- 저장된 해시와 비교
- 해시 불일치 시 서버 재검증

### 3단계: 서버 검증
- 실시간 서버 검증
- 서버 응답 서명 검증

### 4단계: 타입/에디션 검증
- 서버에서 받은 타입/에디션과 로컬 값 비교
- 불일치 시 자동 무효화

---

## 📊 위반 처리

### 위반 유형

1. **invalid_format**: 잘못된 형식의 라이센스 키
2. **key_changed**: 라이센스 키가 변경됨
3. **server_verification_failed**: 서버 검증 실패
4. **type_mismatch**: 라이센스 타입 불일치
5. **edition_mismatch**: 라이센스 에디션 불일치
6. **server_response_signature_invalid**: 서버 응답 서명 검증 실패
7. **license_revoked**: 라이센스 무효화

### 위반 시 조치

1. **즉시 무효화**: 라이센스 키 제거
2. **FREE로 강제**: 에디션을 FREE로 강제 변경
3. **알림 표시**: 관리자에게 위반 알림
4. **로그 기록**: 모든 위반 사항 기록
5. **서버 보고**: 서버에 위반 사항 보고 (선택적)

---

## 🔐 설정 옵션

### Strict Mode
- **기본값**: `true`
- **설명**: 위반 시 즉시 라이센스 무효화
- **비활성화 시**: 위반 로그만 기록, 라이센스 유지

### Server Verification
- **기본값**: `true`
- **설명**: 서버 검증 활성화
- **비활성화 시**: 로컬 검증만 수행

### Cache Verification
- **기본값**: `true`
- **설명**: 검증 결과 캐싱 (1시간)
- **비활성화 시**: 매번 서버 검증

### Auto Revoke on Violation
- **기본값**: `true`
- **설명**: 위반 시 자동 무효화
- **비활성화 시**: 수동 무효화만 가능

---

## 📝 사용 예시

### 라이센스 키 검증

```php
// 필터를 통한 검증
$is_valid = apply_filters('jj_license_is_valid', false, $license_key);

// 직접 검증
$license_security = JJ_License_Security_V25::instance();
$is_valid = $license_security->verify_with_server($license_key);
```

### 라이센스 타입 검증

```php
// 필터를 통한 타입 검증
$license_type = apply_filters('jj_license_type', 'FREE', $license_key);

// 서버에서 실제 타입 가져오기
$server_data = $license_security->get_license_data_from_server($license_key);
$server_type = $server_data['type'] ?? 'FREE';
```

### 위반 로그 조회

```php
$violations = get_option('jj_license_violations_v25', array());
foreach ($violations as $violation) {
    echo $violation['type'] . ': ' . $violation['timestamp'];
}
```

---

## 🚨 주의사항

1. **서버 연결 필수**: 서버 검증을 위해서는 라이센스 서버에 연결 가능해야 함
2. **캐시 주의**: 캐시된 검증 결과는 1시간 동안 유효
3. **오프라인 모드**: 서버 연결 실패 시 캐시된 결과 사용
4. **Strict Mode**: 활성화 시 위반 시 즉시 무효화되므로 주의

---

**작성일**: 2026-01-03  
**작성자**: AI Assistant  
**버전**: v25.0.0
