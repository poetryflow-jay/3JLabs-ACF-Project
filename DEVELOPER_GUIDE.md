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
6. [작업 원칙](#작업-원칙)

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

## 작업 원칙

### 터미널 및 빌드 작업 원칙

#### 1. Python REPL 상태 감지 및 대응
터미널 프롬프트가 `>>>`로 표시되면 Python REPL 상태입니다. 이 상태에서는 모든 명령이 Python 코드로 해석되어 `SyntaxError`가 발생할 수 있습니다.

**해결 방법**: `exit()` 명령으로 Python을 종료한 후 정상 셸로 복구합니다.

#### 2. 타임아웃 및 재시도 전략
터미널 작업이 40초 이상 응답이 없거나, 유의미한 진행이 없으면:
- 작업을 중지하고 다른 방법으로 재시도합니다.
- 복잡한 한 줄 명령 대신 `.ps1` 스크립트 파일을 생성하여 실행합니다.

#### 3. PowerShell 빌드 스크립트 원칙
- 복잡한 PowerShell 명령은 한 줄로 작성하지 않고, 별도의 `.ps1` 스크립트 파일로 분리합니다.
- 이렇게 하면 Python 문자열 이스케이프 문제를 피하고, 재사용성과 가독성이 향상됩니다.

#### 4. ZIP 빌드 주의사항
WordPress 플러그인 ZIP 파일은 **플러그인 폴더가 포함**되어야 합니다.

- ❌ 잘못된 방법: `Compress-Archive -Path "$folder\*"` (ZIP 루트에 파일이 풀림)
- ✅ 올바른 방법: `Compress-Archive -Path $folder` (ZIP 안에 폴더가 포함됨)

이렇게 해야 WordPress 업로드 설치 시 올바르게 인식됩니다.

---

**기여 가이드**: https://3j-labs.com/contribute
