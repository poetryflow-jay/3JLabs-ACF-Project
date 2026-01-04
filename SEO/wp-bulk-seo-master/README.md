# WP 1-Click SEO Master

SEO 에코시스템 중앙 관리 플러그인 - 라이선스, 원격 사이트, 순위 추적

## 개요

WP 1-Click SEO Master는 3J SEO 에코시스템의 중앙 허브입니다. 여러 워드프레스 사이트와 비워드프레스 플랫폼(Webflow, Framer, Ghost, 아임웹, 카페24 등)을 하나의 대시보드에서 관리합니다.

## 핵심 기능

### 1. 라이선스 관리
- **4단계 라이선스**: Starter(1), Professional(5), Agency(25), Enterprise(무제한)
- **자동 활성화**: API 기반 원격 라이선스 검증
- **만료 관리**: 자동 만료 및 갱신 알림
- **사용량 추적**: 활성화된 사이트 수 실시간 모니터링

### 2. 원격 사이트 관리
- **플랫폼 자동 감지**: WordPress, Webflow, Framer, Ghost, 아임웹, 카페24
- **원격 크롤링**: 사이트 SEO 상태 주기적 수집
- **건강도 점수**: 각 사이트 SEO 점수 대시보드 표시
- **문제 알림**: 점수 하락 시 이메일 알림

### 3. Universal Snippet (GTM 스타일)
- **한 줄 설치**: 비워드프레스 사이트에 코드 한 줄로 연동
- **자동 분석**: 메타 태그, 헤딩, 이미지, 링크 수집
- **원격 제어**: 마스터에서 SEO 설정 원격 주입
- **실시간 동기화**: 변경 사항 즉시 반영

```html
<!-- Universal Snippet 예시 -->
<script src="https://your-master-site.com/seo-snippet.min.js" 
        data-site-key="YOUR_SITE_KEY"></script>
```

### 4. 순위 추적 (Rank Tracker)
- **유연한 빈도**: 1일 2~12회 체크 가능
- **키워드 관리**: 무제한 키워드 등록
- **히스토리 차트**: 순위 변동 그래프 시각화
- **국가별 추적**: Google 지역별 순위 추적
- **경쟁사 비교**: 동일 키워드 경쟁사 순위 모니터링

### 5. 통합 대시보드
- **전체 통계**: 관리 사이트 수, 평균 점수, 순위 변동
- **사이트 건강도**: 한눈에 보는 사이트 상태
- **최근 활동**: 라이선스 활성화, 순위 변동 로그
- **차트 시각화**: Chart.js 기반 그래프

## 설치 방법

1. 메인 관리 사이트에 플러그인 설치
2. WordPress 관리자 > 플러그인 > 새로 추가
3. `wp-1-click-seo-master-v1.0.0.zip` 업로드
4. 플러그인 활성화
5. SEO Master > 설정에서 기본 옵션 구성

## 시스템 요구사항

- WordPress 5.8+
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.3+
- SSL 인증서 (HTTPS 필수)
- WP Cron 또는 서버 Cron 활성화

## 파일 구조

```
wp-bulk-seo-master/
├── wp-bulk-seo-master.php       # 메인 플러그인 파일
├── admin/
│   ├── class-admin.php          # 관리자 클래스
│   └── views/
│       ├── dashboard.php        # 메인 대시보드
│       ├── licenses.php         # 라이선스 관리
│       ├── sites.php            # 원격 사이트 관리
│       ├── ranks.php            # 순위 추적
│       └── settings.php         # 설정
├── includes/
│   ├── api/
│   │   └── class-rest-api.php   # REST API 엔드포인트
│   ├── class-database.php       # DB 스키마 관리
│   ├── class-license-manager.php    # 라이선스 CRUD
│   ├── class-rank-tracker.php       # 순위 추적 엔진
│   ├── class-remote-site-manager.php # 원격 사이트 관리
│   └── class-universal-snippet.php  # Universal Snippet 생성
└── assets/
    ├── css/admin.css            # 관리자 스타일
    ├── js/admin.js              # 관리자 스크립트
    └── js/seo-snippet.min.js    # Universal Snippet (minified)
```

## 데이터베이스 테이블

| 테이블명 | 설명 |
|----------|------|
| `{prefix}seo_master_licenses` | 라이선스 정보 |
| `{prefix}seo_master_activations` | 라이선스 활성화 기록 |
| `{prefix}seo_master_remote_sites` | 원격 사이트 목록 |
| `{prefix}seo_master_keywords` | 추적 키워드 |
| `{prefix}seo_master_rank_history` | 순위 변동 히스토리 |

## REST API 엔드포인트

| 엔드포인트 | 메서드 | 설명 |
|-----------|--------|------|
| `/wp-json/seo-master/v1/license/validate` | POST | 라이선스 검증 |
| `/wp-json/seo-master/v1/license/activate` | POST | 라이선스 활성화 |
| `/wp-json/seo-master/v1/license/deactivate` | POST | 라이선스 비활성화 |
| `/wp-json/seo-master/v1/site/register` | POST | 사이트 등록 |
| `/wp-json/seo-master/v1/site/report` | POST | SEO 데이터 리포트 |
| `/wp-json/seo-master/v1/ranks/check` | POST | 순위 체크 |

## 라이선스 타입

| 타입 | 최대 사이트 | 가격 (예시) |
|------|------------|------------|
| Starter | 1 | $49/년 |
| Professional | 5 | $149/년 |
| Agency | 25 | $349/년 |
| Enterprise | 무제한 | $749/년 |

## 순위 추적 빈도

| 빈도 | 체크 시간 |
|------|----------|
| 2회/일 | 00:00, 12:00 |
| 4회/일 | 00:00, 06:00, 12:00, 18:00 |
| 6회/일 | 00:00, 04:00, 08:00, 12:00, 16:00, 20:00 |
| 12회/일 | 매 2시간마다 |

## 설정 옵션

```php
// 기본 설정
'rank_check_frequency' => 4,        // 1일 체크 횟수
'default_country' => 'kr',          // 기본 국가 코드
'email_notifications' => true,      // 이메일 알림
'notification_threshold' => 5,      // 순위 변동 알림 기준

// API 설정
'google_api_key' => '',             // Google API 키
'serp_api_key' => '',               // SERP API 키

// 크론 설정
'enable_cron' => true,
'cron_interval' => 'hourly',
```

## 사용 예시

### 라이선스 생성
```php
$license_manager = new WP_Bulk_SEO_Master_License_Manager();
$license = $license_manager->create_license([
    'type' => 'professional',
    'email' => 'customer@example.com',
    'expires_at' => date('Y-m-d', strtotime('+1 year'))
]);
// 결과: 3J-XXXX-XXXX-XXXX-XXXX
```

### Universal Snippet 생성
```php
$snippet_manager = new WP_Bulk_SEO_Master_Universal_Snippet();
$code = $snippet_manager->generate_snippet($site_id);
// <script src="..."></script> 반환
```

## 라이선스

GPL-2.0+

## 지원

- 웹사이트: https://3jlabs.com
- 이메일: support@3jlabs.com

## 버전 히스토리

### v1.0.0 (2026-01-04)
- 초기 릴리스
- 라이선스 관리 시스템
- 원격 사이트 관리
- Universal Snippet (GTM 스타일)
- 순위 추적 (1일 2~12회)
- REST API
