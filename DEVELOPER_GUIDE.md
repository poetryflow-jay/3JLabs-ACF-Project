# ACF CSS - 개발자 가이드

**버전**: 9.5.0  
**작성일**: 2026년 1월 1일

---

## 목차

1. [아키텍처 개요](#아키텍처-개요)
2. [REST API](#rest-api)
3. [후크 및 필터](#후크-및-필터)
4. [확장 개발](#확장-개발)
5. [코드 품질 표준](#코드-품질-표준)

---

## 아키텍처 개요

### 주요 클래스

- `JJ_Simple_Style_Guide_Master`: 메인 플러그인 클래스
- `JJ_Admin_Center`: 관리자 센터
- `JJ_Options_Cache`: 옵션 캐시 시스템
- `JJ_CSS_Cache`: CSS 캐시 시스템
- `JJ_Asset_Optimizer`: Asset 최적화
- `JJ_Security_Hardener`: 보안 강화

### Phase 9 추가 클래스

- `JJ_I18n_Manager`: 다국어 지원
- `JJ_Localization`: 현지화
- `JJ_Dashboard_Widgets`: 대시보드 위젯
- `JJ_Global_Search`: 전역 검색
- `JJ_Style_Guide_Generator`: 스타일 가이드 생성
- `JJ_Version_Control`: 버전 관리
- `JJ_AI_Suggestions`: AI 제안
- `JJ_AI_Optimizer`: AI 최적화
- `JJ_AI_Debugger`: AI 디버깅
- `JJ_Batch_Processor`: 배치 처리
- `JJ_Performance_Monitor`: 성능 모니터링
- `JJ_Error_Tracker`: 에러 추적
- `JJ_Code_Optimizer`: 코드 최적화

---

## REST API

### 엔드포인트

**기본 URL**: `/wp-json/jj-style-guide/v1/`

#### 정보 조회
```
GET /info
```

#### 설정 조회/업데이트
```
GET /settings
POST /settings
```

#### 팔레트
```
GET /palettes
POST /palettes
```

#### 스냅샷
```
GET /snapshots
POST /snapshots
GET /snapshots/{id}
POST /snapshots/{id} (복원)
```

#### 스타일 가이드 생성
```
POST /style-guide/generate
GET /style-guide/analyze
```

### 인증

모든 API는 관리자 권한(`manage_options`)이 필요합니다.

---

## 후크 및 필터

### 액션

- `jj_style_guide_settings_updated`: 설정 업데이트 시
- `jj_snapshot_created`: 스냅샷 생성 시
- `jj_snapshot_restored`: 스냅샷 복원 시

### 필터

- `jj_style_guide_options`: 옵션 필터링
- `jj_css_output`: CSS 출력 필터링

---

## 확장 개발

### Extension 인터페이스

```php
interface JJ_Style_Guide_Extension {
    public function get_name();
    public function get_version();
    public function init();
}
```

### 예제

```php
class My_Custom_Extension implements JJ_Style_Guide_Extension {
    public function get_name() {
        return 'My Custom Extension';
    }
    
    public function get_version() {
        return '1.0.0';
    }
    
    public function init() {
        // 초기화 코드
    }
}
```

---

## 코드 품질 표준

### PHP

- PSR-12 코딩 표준 준수
- 모든 입력값 sanitize
- 모든 출력값 escape
- Nonce 검증 필수

### JavaScript

- ES6+ 문법 사용
- jQuery 의존성 최소화
- 에러 처리 필수

---

**기여 가이드**: https://3j-labs.com/contribute
