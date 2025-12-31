# 🔧 문제 해결 가이드

ACF CSS Manager 사용 중 발생할 수 있는 문제와 해결 방법을 안내합니다.

---

## 🚨 일반적인 문제

### 1. 플러그인 활성화 실패

**증상:** 플러그인 활성화 버튼 클릭 후 오류 발생

**원인 및 해결:**

```
✅ PHP 버전 확인
   - 최소 PHP 7.4 필요
   - 호스팅 패널에서 PHP 버전 업그레이드

✅ WordPress 버전 확인
   - 최소 WordPress 5.8 필요
   - 대시보드 > 업데이트에서 최신 버전으로 업데이트

✅ 메모리 부족
   - wp-config.php에 다음 추가:
   define('WP_MEMORY_LIMIT', '256M');

✅ 플러그인 충돌
   - 다른 플러그인 모두 비활성화 후 하나씩 활성화
   - 문제되는 플러그인 찾아서 지원팀에 보고
```

---

### 2. 스타일이 적용되지 않음

**증상:** 색상/폰트를 변경해도 프론트엔드에 반영되지 않음

**해결 순서:**

#### Step 1: 캐시 삭제

```
1. WordPress 캐시 플러그인
   - WP Rocket: 설정 > 캐시 삭제
   - LiteSpeed Cache: LiteSpeed Cache > 도구 > 전체 삭제
   - W3 Total Cache: 성능 > 모든 캐시 삭제

2. 서버 캐시
   - Kinsta: MyKinsta > 도구 > 캐시 삭제
   - Cloudflare: 캐시 > 전체 삭제

3. 브라우저 캐시
   - Chrome: Ctrl+Shift+R (강력 새로고침)
   - 또는 개발자 도구 > Network > Disable cache 체크
```

#### Step 2: CSS 우선순위 확인

```
ACF CSS Manager > 고급 설정 > CSS 우선순위

- 기본: 10
- 테마 CSS보다 높게: 100
- 모든 것보다 높게: 9999
```

#### Step 3: CSS 검사

```
1. 브라우저 개발자 도구 열기 (F12)
2. Elements 탭에서 요소 선택
3. Styles 패널에서 취소선 확인
4. 어떤 CSS가 덮어쓰고 있는지 확인
```

---

### 3. 라이센스 활성화 실패

**증상:** "라이센스 활성화에 실패했습니다" 오류

**해결:**

```
✅ 라이센스 키 확인
   - 복사-붙여넣기로 입력 (오타 방지)
   - 앞뒤 공백 제거

✅ 인터넷 연결 확인
   - curl https://j-j-labs.com -I 테스트

✅ 방화벽 확인
   - 호스팅 방화벽에서 j-j-labs.com 허용
   - 보안 플러그인에서 외부 연결 허용

✅ SSL 확인
   - HTTPS 사이트인지 확인
   - SSL 인증서 유효성 확인

✅ 사이트 수 제한
   - 이미 다른 사이트에서 사용 중인지 확인
   - 기존 사이트에서 비활성화 후 재시도
```

---

### 4. AI 스타일 생성 오류

**증상:** "AI 생성 오류" 메시지 표시

**원인별 해결:**

```
❌ "API Key가 설정되지 않았습니다"
   → AI 스타일 > 설정에서 OpenAI API Key 입력

❌ "API Key가 유효하지 않습니다"
   → OpenAI 대시보드에서 API Key 재확인
   → 사용량 한도 초과 여부 확인

❌ "Rate limit exceeded"
   → 잠시 후 다시 시도 (1분 대기)
   → OpenAI 계정 사용량 확인

❌ "Invalid JSON response"
   → 프롬프트 수정 후 재시도
   → 특수문자 제거
```

---

### 5. 클라우드 동기화 오류

**증상:** 클라우드 내보내기/가져오기 실패

**해결:**

```
✅ 라이센스 상태 확인
   - PRO 이상 활성 라이센스 필요

✅ 네트워크 확인
   - API 서버 연결 테스트
   - System Status에서 Cloud 상태 확인

✅ 데이터 크기 확인
   - 너무 큰 커스텀 CSS는 분할 저장

✅ 권한 확인
   - 관리자 권한 필요
```

---

### 6. 관리자 메뉴 표시 안 됨

**증상:** ACF CSS Manager 메뉴가 WordPress 관리자에 없음

**해결:**

```
✅ 로그인 계정 확인
   - 관리자(Administrator) 역할로 로그인

✅ 플러그인 상태 확인
   - 플러그인 > 설치된 플러그인에서 "활성" 상태 확인

✅ 역할 편집기 플러그인 확인
   - User Role Editor 등에서 권한 제한 여부 확인

✅ 다른 플러그인 충돌
   - 관리 메뉴 관련 플러그인 일시 비활성화
```

---

### 7. 성능 저하

**증상:** 사이트 로딩 속도 저하

**진단 및 해결:**

```
Step 1: 원인 파악
   - Query Monitor 플러그인으로 느린 쿼리 확인
   - GTmetrix 또는 PageSpeed Insights 분석

Step 2: 최적화 설정
   - CSS Tree Shaking 활성화
   - CSS Minify 활성화
   - 불필요한 어댑터 비활성화

Step 3: 캐싱 활용
   - 페이지 캐싱 플러그인 사용
   - CDN 활성화
   - 브라우저 캐싱 설정

Step 4: 리소스 최적화
   - 사용하지 않는 기능 비활성화
   - 관리자에서만 로드되는 스크립트 확인
```

---

## 🛠️ 고급 문제 해결

### 디버그 모드 활성화

```php
// wp-config.php에 추가
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

로그 확인: `wp-content/debug.log`

### ACF CSS 디버그 모드

```php
// wp-config.php에 추가
define('JJ_STYLE_GUIDE_DEBUG', true);
```

### System Status 확인

```
설정 > ACF CSS Manager > System Status

확인 항목:
- PHP 버전
- WordPress 버전
- 메모리 한도
- 활성 플러그인
- 테마 정보
- API 연결 상태
```

---

## 📋 지원 요청 시 포함할 정보

문제 해결이 어려울 경우 support@j-j-labs.com으로 다음 정보와 함께 연락주세요:

```
□ WordPress 버전
□ PHP 버전
□ ACF CSS Manager 버전
□ 활성 테마 이름
□ 활성 플러그인 목록
□ 오류 메시지 전문
□ 문제 재현 단계
□ System Status 스크린샷
□ 브라우저 콘솔 오류 (F12 > Console)
```

---

## 🔄 복구 방법

### 설정 초기화

```
설정 > ACF CSS Manager > 백업 > 설정 초기화
```

### 이전 버전 복원

```
1. 설정 > ACF CSS Manager > 백업
2. 자동 백업 목록에서 원하는 시점 선택
3. "이 백업 복원" 클릭
```

### 완전 재설치

```
1. 플러그인 비활성화 및 삭제
2. 옵션 테이블에서 데이터 삭제 (선택):
   DELETE FROM wp_options WHERE option_name LIKE 'jj_style_guide%';
3. 플러그인 재설치 및 활성화
```

---

문제가 지속되면 [지원 요청](/support/)을 해주세요!

