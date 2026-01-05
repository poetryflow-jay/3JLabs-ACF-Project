# Phase 45: 버전 추출 및 대시보드 연동 개선 완료 보고서

**작성일**: 2026년 1월 5일  
**버전**: v23.0.1  
**상태**: ✅ 완료

---

## 작업 요약

파이썬 빌드 프로그램, HTML 대시보드, 그리고 버전 업데이트가 완벽하게 연동되도록 개선 작업을 완료했습니다.

---

## 개선 사항

### 1. 버전 추출 함수 개선 (`get_version_from_file`)

**이전 문제점**:
- 플러그인 헤더의 `Version:` 필드만 확인
- 상수 정의(`define('VERSION', ...)`)를 확인하지 않음
- 일부 플러그인에서 버전을 찾지 못함

**개선 내용**:
- ✅ 플러그인 헤더 `Version:` 필드 확인 (우선순위 1)
- ✅ 상수 정의 확인 (우선순위 2)
  - `define('PLUGIN_VERSION', '...')`
  - `define('JJ_STYLE_GUIDE_VERSION', '...')`
  - `define('VERSION', '...')`
- ✅ 읽기 범위 확대 (2000자 → 5000자)
- ✅ 오류 처리 개선

**수정된 코드**:
```python
def get_version_from_file(file_path):
    """PHP 파일에서 버전 추출 (플러그인 헤더 및 상수 정의 모두 확인)"""
    if not file_path.exists():
        return "N/A"
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read(5000)  # 처음 5000자 읽기
            
            # 1. 플러그인 헤더에서 Version 추출 (우선순위 1)
            match = re.search(r'\*\s*Version:\s*([\d.]+)', content)
            if match:
                return match.group(1)
            
            # 2. 상수 정의에서 버전 추출 (우선순위 2)
            patterns = [
                r"define\s*\(\s*['\"]\w*VERSION\w*['\"]\s*,\s*['\"]([\d.]+)['\"]",
                r"define\s*\(\s*['\"]\w*_VERSION['\"]\s*,\s*['\"]([\d.]+)['\"]",
                r"define\s*\(\s*['\"]VERSION['\"]\s*,\s*['\"]([\d.]+)['\"]",
            ]
            for pattern in patterns:
                match = re.search(pattern, content, re.IGNORECASE)
                if match:
                    return match.group(1)
            
    except Exception as e:
        print(f"Warning: Failed to extract version from {file_path}: {e}")
    return "N/A"
```

### 2. 빌드 완료 후 자동 대시보드 업데이트

**개선 내용**:
- ✅ GUI 모드: 빌드 완료 시 자동으로 대시보드 업데이트 (`_auto_update_dashboard`)
- ✅ CLI 모드: 빌드 완료 시 자동으로 대시보드 업데이트 (`_update_dashboard_simple`)
- ✅ 대시보드 경로 자동 확인 (설정된 경로 또는 기본 경로)
- ✅ 오류 처리 개선

**자동 업데이트 흐름**:
1. 빌드 완료 후 `build_all()` 함수 종료
2. GUI 모드: `_auto_update_dashboard()` 자동 호출
3. CLI 모드: `_update_dashboard_simple()` 자동 호출
4. 모든 플러그인의 최신 버전 정보 수집
5. 대시보드 HTML 파일 업데이트

### 3. 대시보드 업데이트 로직 개선

**이전 문제점**:
- 정규식 패턴이 정확하지 않음
- 플러그인 카드를 정확히 찾지 못함
- 다운로드 링크 업데이트 실패

**개선 내용**:
- ✅ `plugin-fullname`으로 플러그인 카드 정확히 찾기
- ✅ 카드 블록 전체를 추출하여 버전 태그 및 링크 업데이트
- ✅ 폴더 경로에 슬래시가 있는 경우 처리 (예: `SEO/wp-bulk-seo-aeo`)
- ✅ 여러 패턴으로 폴백 처리

**개선된 업데이트 로직**:
```python
# plugin-fullname으로 플러그인 카드 찾기
card_pattern = f'<div class="plugin-fullname">{folder_escaped}</div>'
card_match = re.search(card_pattern, content)

if card_match:
    # 카드 시작/끝 위치 찾기
    card_start = content.rfind('<div class="plugin-card', 0, card_match.start())
    card_end = content.find('</div>\n            </div>', card_match.end())
    
    # 카드 내용 추출 및 업데이트
    card_content = content[card_start:card_end]
    # version-tag 업데이트
    # 다운로드 링크 업데이트
    # 업데이트된 내용으로 교체
```

### 4. Windows 콘솔 인코딩 문제 해결

**문제점**:
- CLI 모드에서 유니코드 문자(이모지) 출력 시 `UnicodeEncodeError` 발생
- Windows 콘솔의 cp949 인코딩 문제

**해결 방법**:
- ✅ 스크립트 시작 시 UTF-8 인코딩 설정
- ✅ 오류 메시지에서 이모지 제거 또는 ASCII로 대체
- ✅ `errors='replace'` 옵션으로 안전하게 처리

**추가된 코드**:
```python
# Windows 콘솔 인코딩 설정 (UTF-8)
import sys
import io
if sys.platform == 'win32':
    try:
        sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
        sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8', errors='replace')
    except:
        pass
```

### 5. 대시보드 경로 자동 확인

**개선 내용**:
- ✅ 설정된 대시보드 경로가 없으면 기본 경로 사용 (`BASE_DIR/dashboard.html`)
- ✅ 대시보드 파일 존재 여부 확인
- ✅ 오류 메시지 개선

---

## 수정된 파일 목록

1. ✅ `3j_build_manager.py` (v23.0.0 → v23.0.1)
   - 버전 추출 함수 개선
   - 대시보드 자동 업데이트 로직 개선
   - Windows 콘솔 인코딩 문제 해결
   - CLI 모드 대시보드 업데이트 추가

---

## 테스트 결과

### 빌드 테스트
- ✅ 모든 14개 플러그인 빌드 성공
- ✅ ZIP 파일 생성 성공
- ✅ 서명 생성 성공
- ✅ 이전 버전 아카이브 성공

### 대시보드 업데이트 테스트
- ✅ CLI 모드에서 자동 업데이트 성공
- ✅ 버전 정보 정확히 추출
- ✅ 대시보드 HTML 파일 업데이트 성공

### 버전 추출 테스트
- ✅ 플러그인 헤더에서 버전 추출 성공
- ✅ 상수 정의에서 버전 추출 성공
- ✅ 모든 플러그인 버전 정확히 추출

---

## 연동 흐름

### 빌드 프로세스
```
1. 플러그인 빌드 시작
   ↓
2. 각 플러그인 버전 추출 (get_version_from_file)
   - 플러그인 헤더 확인
   - 상수 정의 확인
   ↓
3. ZIP 파일 생성 (버전 포함)
   - 예: plugin-name-master-v23.0.10.zip
   ↓
4. 빌드 완료
   ↓
5. 대시보드 자동 업데이트
   - 모든 플러그인 버전 정보 수집
   - 대시보드 HTML 업데이트
   - version-tag 업데이트
   - 다운로드 링크 업데이트
```

### 대시보드 업데이트 프로세스
```
1. 빌드 완료 후 자동 트리거
   ↓
2. 모든 플러그인 버전 정보 수집
   - get_version_from_file() 호출
   - 최신 버전 정보 수집
   ↓
3. 대시보드 HTML 파일 읽기
   ↓
4. 버전 정보 업데이트
   - 헤더 버전 업데이트
   - 개별 플러그인 카드 버전 업데이트
   - 다운로드 링크 업데이트
   - 빌드 정보 테이블 업데이트
   ↓
5. 파일 저장
```

---

## 사용 방법

### GUI 모드
1. 빌드 매니저 실행
2. "빌드 완료 시 대시보드 자동 업데이트" 체크박스 확인
3. 빌드 시작
4. 빌드 완료 시 자동으로 대시보드 업데이트

### CLI 모드
```bash
# 모든 플러그인 빌드 및 대시보드 자동 업데이트
python 3j_build_manager.py --cli --all

# 특정 플러그인만 빌드
python 3j_build_manager.py --cli --plugins acf-css-manager wp-bulk-manager
```

---

## 개선 효과

### 이전
- ❌ 버전 추출 실패 (상수 정의 확인 안 함)
- ❌ 대시보드 수동 업데이트 필요
- ❌ 버전 정보 불일치
- ❌ CLI 모드에서 유니코드 오류

### 개선 후
- ✅ 모든 플러그인 버전 정확히 추출
- ✅ 빌드 완료 시 자동 대시보드 업데이트
- ✅ 버전 정보 완벽 동기화
- ✅ CLI 모드 안정적 작동

---

## 다음 단계

1. **테스트**
   - 실제 WordPress 환경에서 플러그인 설치 테스트
   - 대시보드 버전 정보 확인

2. **모니터링**
   - 빌드 로그 확인
   - 대시보드 업데이트 로그 확인

3. **문서 업데이트**
   - 사용자 매뉴얼 업데이트
   - 개발자 가이드 업데이트

---

## 결론

파이썬 빌드 프로그램, HTML 대시보드, 그리고 버전 업데이트가 완벽하게 연동되도록 개선했습니다. 이제 빌드만 하면 자동으로 대시보드가 최신 버전 정보로 업데이트됩니다.

**주요 성과**:
- ✅ 버전 추출 정확도 향상 (플러그인 헤더 + 상수 정의)
- ✅ 빌드 완료 시 자동 대시보드 업데이트
- ✅ GUI/CLI 모드 모두 지원
- ✅ Windows 콘솔 인코딩 문제 해결
- ✅ 대시보드 업데이트 로직 개선

---

**작성자**: Auto (AI Assistant)  
**검토 필요**: 사용자 테스트 후 최종 확인
