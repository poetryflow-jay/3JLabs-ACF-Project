# 3J Labs 코드 품질 검사 표준

## 개요

모든 코드 파일에 대해 문법 오류 검사, 경로 참조 확인, 클래스명/함수명 일관성 검사 등을 의무적으로 수행합니다.

## 검사 규칙

### 1. PHP 파일

#### 문법 검사
- **검사 도구**: `php -l` (PHP Lint)
- **검사 시점**: 
  - 개발 시작 시: 빠른 검사 (php -l 파일명)
  - 코드 저장 전: 중간 검사
  - 배포/커밋 전: 종합 검사

#### 경로 참조 검사
- 상대 경로 `../` 사용 금지 (3단계 이상)
- `require`, `include` 사용 시 상수 기반 경로 권장
- WordPress 함수 사용 권장: `plugin_dir_path()`, `plugin_dir_url()`

#### 명명 규칙
- 클래스명: `JJ_` 접두사 필수 (예: `JJ_Deployment_Engine`)
- 함수명: `snake_case` 사용 (예: `get_plugin_version()`)
- 상수명: `JJ_` 접두사 + `UPPER_SNAKE_CASE` (예: `JJ_STYLE_GUIDE_VERSION`)

### 2. JavaScript 파일

#### 문법 검사
- **검사 시점**: 코드 저장 전, 배포 전
- ESLint 또는 기본 문법 검사 수행

#### 경로 참조 검사
- WordPress 함수 사용: `wp_localize_script()`, `plugin_dir_url()`
- 직접 경로 참조 최소화

#### 명명 규칙
- 변수명: `camelCase` (WordPress 컨벤션)
- 함수명: `camelCase`
- 상수명: `UPPER_SNAKE_CASE`

### 3. Python 파일

#### 문법 검사
- **검사 도구**: `python -m py_compile`
- **검사 시점**: 실행 전, 배포 전

#### 코드 스타일
- PEP 8 스타일 가이드 준수
- 함수명: `snake_case`
- 클래스명: `PascalCase`

## 검사 도구 사용법

### 빠른 검사 (주요 파일만)
```bash
python code_quality_checker.py --quick
```

### 전체 검사
```bash
python code_quality_checker.py
```

### 특정 파일 검사
```bash
python code_quality_checker.py --file=경로/파일명.php
```

### JSON 형식 출력
```bash
python code_quality_checker.py --json > report.json
```

## 파일 헤더

모든 새로 생성되거나 수정된 파일에는 다음 헤더를 추가해야 합니다:

### PHP 파일 헤더
```php
<?php
/**
 * 3J Labs Code Quality Assurance
 * =================================
 * 
 * [자동 품질 검사 헤더]
 * 
 * 이 파일은 다음 규칙을 준수합니다:
 * 1. 문법 검사: 배포/저장/커밋 전 PHP 문법 오류 검사 필수 (php -l)
 * 2. 경로 참조: 모든 경로 참조는 절대 경로 또는 상수 기반 상대 경로 사용
 * 3. 클래스/함수명: 클래스는 JJ_ 접두사 필수, 함수는 snake_case
 * 4. 변경 사항: 모든 코드 변경은 변경 로그에 기록
 */
```

### JavaScript 파일 헤더
```javascript
/**
 * 3J Labs Code Quality Assurance
 * =================================
 * 
 * [자동 품질 검사 헤더]
 * 
 * 이 파일은 다음 규칙을 준수합니다:
 * 1. 문법 검사: 배포/저장/커밋 전 JavaScript 문법 오류 검사 필수
 * 2. 경로 참조: 모든 경로 참조는 WordPress 함수 또는 상대 경로 사용
 * 3. 변수명: camelCase 사용 (WordPress 컨벤션 준수)
 * 4. 변경 사항: 모든 코드 변경은 변경 로그에 기록
 */
```

## 자동 헤더 추가

헤더 추가 도구를 사용하여 새 파일에 헤더를 자동 추가할 수 있습니다:

```bash
python add_quality_headers.py 파일경로1.php 파일경로2.js
```

## 배포 시스템 통합

`jj_deployment_system.py`는 자동으로 다음을 수행합니다:

1. **빌드 전 PHP 문법 검사**: 모든 PHP 파일에 대해 `php -l` 실행
2. **오류 발생 시 빌드 중단**: 문법 오류가 있으면 빌드를 중단하고 오류 메시지 출력
3. **검사 결과 로깅**: 검사 결과를 로그에 기록

## 변경 사항 기록

모든 코드 변경은 다음에 기록해야 합니다:

1. **커밋 메시지**: 변경 내용과 검사 결과 포함
2. **변경 로그**: `CHANGELOG.md` 또는 `changelog.md` 업데이트
3. **배포 로그**: 배포 시스템이 자동 생성하는 로그

## 문제 해결

### PHP CLI를 찾을 수 없는 경우

1. **환경 변수 설정**:
   ```bash
   set PHP_BIN=C:\path\to\php.exe
   ```

2. **Scoop 설치** (Windows):
   ```bash
   scoop install php
   ```

3. **수동 설치**: PHP 공식 사이트에서 다운로드

### 검사가 너무 느린 경우

- `--quick` 옵션 사용: 주요 파일만 검사
- 특정 파일만 검사: `--file` 옵션 사용
- 백그라운드 실행: 대용량 프로젝트의 경우

## 예외 상황

다음 경우에는 검사를 건너뛸 수 있습니다:

1. **테스트 파일**: `tests/` 디렉토리의 파일
2. **외부 라이브러리**: `vendor/`, `node_modules/` 디렉토리
3. **자동 생성 파일**: 빌드 시스템이 자동 생성하는 파일

단, 이러한 예외도 주석으로 명시해야 합니다.

## 검사 체크리스트

코드 작성 시 다음을 확인하세요:

- [ ] PHP 문법 검사 통과 (`php -l`)
- [ ] 경로 참조 확인 (상대 경로 남용 없음)
- [ ] 클래스명/함수명 규칙 준수
- [ ] 파일 헤더 포함
- [ ] 변경 사항 기록
- [ ] 배포 시스템 통합 검사 통과

## 추가 정보

- 코드 품질 검사 도구: `code_quality_checker.py`
- 헤더 추가 도구: `add_quality_headers.py`
- 배포 시스템: `jj_deployment_system.py`
