# Phase 45: TGMPA 플러그인 파일명 매핑 수정 보고서

## 버전 정보
- **수정 버전**: v23.0.7
- **수정일**: 2026-01-05
- **작성자**: Claude Code AI Assistant

---

## 문제 요약

### 증상
1. **ACF CSS v23.0.2 중복 설치**: WP Bulk Manager로 플러그인 설치 시 ACF CSS가 매번 중복 설치됨
2. **다른 플러그인 설치/활성화 실패**: ACF CSS, 벌크 매니저, 코드 박스 외 다른 플러그인들이 설치/활성화되지 않음

### 영향 범위
- ACF CSS 마스터 에디션 사용자
- WP Bulk Manager를 통한 일괄 플러그인 설치 기능

---

## 근본 원인 분석

### 1. 플러그인 메인 파일명 불일치 (핵심 원인)

**파일**: `includes/class-jj-custom-tgmpa.php`, `includes/class-jj-required-plugins.php`

```php
// 기존 잘못된 코드
$plugin_files = array(
    'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-installer.php', // ← 잘못된 파일명
);

// 실제 메인 파일
wp-bulk-manager/wp-bulk-manager.php  // ← 올바른 파일명
```

**결과**:
- `is_plugin_active()` 함수가 항상 `false` 반환
- 플러그인이 이미 설치/활성화되어도 "설치 안됨"으로 인식
- TGMPA가 반복적으로 설치/활성화 시도

### 2. 대체 경로 탐색 로직 불완전

**기존 문제**:
```php
// SEO 플러그인에만 대체 경로 확인 로직 존재
if ( $slug === 'wp-bulk-seo-aeo' ) {
    $alt_path = 'SEO/wp-bulk-seo-aeo/wp-bulk-seo-aeo.php';
    // ...
}
```

다른 플러그인들의 파일명 변경 시 대응 불가능

### 3. TGMPA 인스턴스 충돌

- `JJ_Required_Plugins` 싱글톤에서 TGMPA 중복 로드 가능성
- 벌크 매니저의 `activate_plugin()` 직접 호출과 TGMPA 활성화 로직 충돌

---

## 수정 내용

### 1. class-jj-custom-tgmpa.php

**수정 위치**: Line 339-436

```php
// [v23.0.7] 플러그인 파일 매핑 수정 - 올바른 메인 파일명 사용
$plugin_files = array(
    'acf-css-woo-license' => 'acf-css-woo-license/acf-css-woo-license.php',
    'acf-code-snippets-box' => 'acf-code-snippets-box/acf-code-snippets-box.php',
    'jj-marketing-automation-dashboard' => 'jj-marketing-automation-dashboard/jj-marketing-dashboard.php',
    'wp-bulk-seo-aeo' => 'wp-bulk-seo-aeo/wp-bulk-seo-aeo.php',
    'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-manager.php', // [v23.0.7] 수정
);

// [v23.0.7] 대체 파일명 매핑 (호환성 유지)
$alt_files = array(
    'acf-css-woo-license' => array(
        'acf-css-woo-license/acf-css-neural-link.php',
    ),
    'wp-bulk-manager' => array(
        'wp-bulk-manager/wp-bulk-installer.php', // 레거시 호환
    ),
    'wp-bulk-seo-aeo' => array(
        'SEO/wp-bulk-seo-aeo/wp-bulk-seo-aeo.php',
    ),
    'jj-marketing-automation-dashboard' => array(
        'jj-marketing-automation-dashboard/jj-marketing-automation-dashboard.php',
    ),
);
```

**새로운 기능**: `find_main_plugin_file()` 메서드 추가
- 플러그인 폴더에서 메인 파일 자동 감지
- `Plugin Name:` 헤더가 있는 PHP 파일 검색

### 2. class-jj-required-plugins.php

**수정 위치**: Line 297-325

```php
// [v23.0.7] 올바른 플러그인 파일 경로
$plugin_files = array(
    'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-manager.php', // 수정됨
);

// [v23.0.7] 대체 파일명 (레거시 호환)
$alt_files = array(
    'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-installer.php',
    'wp-bulk-seo-aeo' => 'SEO/wp-bulk-seo-aeo/wp-bulk-seo-aeo.php',
);
```

---

## 수정된 파일 목록

| 파일 | 변경 유형 | 설명 |
|------|----------|------|
| `acf-css-really-simple-style-guide.php` | 버전 업데이트 | 23.0.6 → 23.0.7 |
| `includes/class-jj-custom-tgmpa.php` | 버그 수정 | 플러그인 파일명 매핑 + 동적 탐색 |
| `includes/class-jj-required-plugins.php` | 버그 수정 | 플러그인 파일명 매핑 + 대체 경로 |

---

## 테스트 권장사항

### 설치 테스트
1. WP Bulk Manager를 통해 모든 3J Labs 플러그인 일괄 설치
2. 중복 설치 발생 여부 확인
3. 모든 플러그인 정상 활성화 확인

### 확인 항목
- [ ] ACF CSS v23.0.2 중복 설치 문제 해결
- [ ] WP Bulk Manager 정상 설치/활성화
- [ ] Marketing Dashboard 정상 설치/활성화
- [ ] WP Bulk SEO & AEO 정상 설치/활성화
- [ ] ACF Code Snippets Box 정상 설치/활성화
- [ ] ACF CSS Woo License 정상 설치/활성화

---

## 향후 권장사항

1. **플러그인 파일명 표준화**: 모든 3J Labs 플러그인의 메인 파일명을 슬러그와 동일하게 유지
2. **자동 감지 우선**: 하드코딩된 매핑보다 동적 탐색 우선 적용 검토
3. **통합 테스트 자동화**: CI/CD 파이프라인에 플러그인 설치/활성화 테스트 추가

---

## 버전 히스토리

| 버전 | 날짜 | 변경 내용 |
|------|------|----------|
| 23.0.7 | 2026-01-05 | TGMPA 플러그인 파일명 매핑 수정, 동적 탐색 추가 |
| 23.0.6 | 2026-01-05 | 조건부 플러그인 탭 추가, 빠른 이동 상단 배치 |
| 23.0.5 | 2026-01-04 | 스타일 센터 탭 전환 및 저장 버튼 UI 수정 |
