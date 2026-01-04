# 3J SEO Analyzer - Chrome Extension

Google SERP 실시간 분석 및 WordPress 연동 Chrome 확장 프로그램

## 개요

3J SEO Analyzer는 Google 검색 결과 페이지(SERP)를 실시간으로 분석하고, WP 1-Click SEO 플러그인과 연동하여 SEO 인사이트를 제공합니다.

## 주요 기능

### 1. SERP 실시간 분석
- **검색 결과 오버레이**: 각 결과에 SEO 점수 배지 표시
- **타이틀 분석**: 길이, 키워드 포함 여부
- **메타 설명 분석**: 길이, 최적화 상태
- **URL 구조 분석**: SEO 친화적 URL 평가

### 2. 경쟁사 분석
- **상위 10개 결과 비교**: 타이틀, 설명 패턴 분석
- **평균 콘텐츠 길이**: 상위 결과 평균 워드 수
- **공통 키워드 추출**: 자주 사용되는 키워드 파악
- **백링크 추정**: 도메인 권한 표시

### 3. WordPress 연동
- **사이트 연결**: WP 1-Click SEO 설치 사이트와 연동
- **데이터 동기화**: 분석 결과 자동 전송
- **순위 추적 연동**: 검색 시 자동 순위 기록
- **원클릭 최적화**: 분석 결과 바로 적용

### 4. 순위 추적
- **자동 기록**: 검색 시 내 사이트 순위 자동 저장
- **히스토리 조회**: 팝업에서 순위 변동 확인
- **알림**: 순위 변동 시 브라우저 알림

## 설치 방법

### 개발자 모드 설치
1. Chrome 브라우저에서 `chrome://extensions` 접속
2. 우측 상단 "개발자 모드" 활성화
3. "압축해제된 확장 프로그램을 로드합니다" 클릭
4. `chrome-extension` 폴더 선택

### CRX 파일 설치
1. `wp-1-click-seo-chrome-extension-v1.0.0.zip` 압축 해제
2. Chrome 확장 프로그램 페이지에서 로드

## 파일 구조

```
chrome-extension/
├── manifest.json           # Manifest V3 설정
├── js/
│   ├── background.js       # Service Worker (백그라운드)
│   └── serp-analyzer.js    # Content Script (SERP 분석)
├── css/
│   └── serp-overlay.css    # SERP 오버레이 스타일
├── popup/
│   ├── popup.html          # 팝업 UI
│   ├── popup.css           # 팝업 스타일
│   └── popup.js            # 팝업 로직
├── options/
│   ├── options.html        # 설정 페이지 UI
│   ├── options.css         # 설정 스타일
│   └── options.js          # 설정 로직
└── icons/                  # 확장 프로그램 아이콘
    ├── icon16.png
    ├── icon48.png
    └── icon128.png
```

## Manifest V3 권한

```json
{
  "permissions": [
    "activeTab",     // 현재 탭 접근
    "storage",       // 로컬 저장소
    "alarms",        // 주기적 작업
    "scripting"      // 스크립트 주입
  ],
  "host_permissions": [
    "https://www.google.com/*",
    "https://www.google.co.kr/*"
  ]
}
```

## 사용 방법

### 1. WordPress 사이트 연결
1. 확장 프로그램 아이콘 클릭 > 설정
2. WP 1-Click SEO가 설치된 사이트 URL 입력
3. API 키 입력 (플러그인 설정에서 확인)
4. 연결 테스트 > 저장

### 2. SERP 분석
1. Google에서 키워드 검색
2. 검색 결과에 SEO 점수 배지 자동 표시
3. 배지 클릭 시 상세 분석 보기

### 3. 순위 추적
1. 설정에서 추적할 도메인 등록
2. 검색 시 자동으로 순위 기록
3. 팝업에서 순위 히스토리 확인

## 설정 옵션

| 옵션 | 설명 | 기본값 |
|------|------|--------|
| WordPress URL | 연동할 WP 사이트 주소 | - |
| API Key | WP 1-Click SEO API 키 | - |
| Auto Sync | 자동 데이터 동기화 | true |
| Show Overlay | SERP 오버레이 표시 | true |
| Track Domains | 순위 추적 도메인 목록 | [] |
| Notification | 순위 변동 알림 | true |

## 오버레이 점수 기준

| 점수 | 등급 | 색상 | 의미 |
|------|------|------|------|
| 90-100 | A | 초록 | 최적화 우수 |
| 80-89 | B | 청록 | 양호 |
| 70-79 | C | 노랑 | 개선 필요 |
| 60-69 | D | 주황 | 문제 있음 |
| 0-59 | F | 빨강 | 심각한 문제 |

## 분석 항목

### 타이틀 분석
- 길이 (30-60자 권장)
- 키워드 포함 여부
- 브랜드 포함 여부
- 특수문자 사용

### 메타 설명 분석
- 길이 (120-160자 권장)
- 키워드 포함 여부
- 행동 유도 문구(CTA)
- 잘림 여부

### URL 분석
- 길이 (75자 이하 권장)
- 키워드 포함
- 하이픈 사용
- 깊이 (3단계 이하 권장)

## 개발자 정보

### 빌드
```bash
# 개발 모드
npm run dev

# 프로덕션 빌드
npm run build
```

### 테스트
```bash
npm test
```

## 알려진 이슈

- Google 검색 결과 페이지 구조 변경 시 오버레이 위치 조정 필요
- 일부 국가별 Google 도메인에서 미지원

## 라이선스

GPL-2.0+

## 지원

- 웹사이트: https://3jlabs.com
- 이메일: support@3jlabs.com

## 버전 히스토리

### v1.0.0 (2026-01-04)
- 초기 릴리스
- Manifest V3 지원
- SERP 실시간 분석
- WordPress 연동
- 순위 자동 추적
- 팝업 UI
- 설정 페이지
