# Release Notes v23.0.3

**Release Date**: 2026-01-05
**Phase**: 42.2 - Security, Performance, Rollback

---

## Summary

v23.0.3은 보안 강화, 성능 최적화, 롤백 기능 완성에 초점을 맞춘 릴리즈입니다. PHP 문법 오류 3개를 수정하고, AJAX 보안을 강화했으며, 캐싱 시스템을 도입했습니다.

---

## New Features

### Rollback System Complete (Plugin List Enhancer)
- 완전한 플러그인 롤백 기능 구현
- WordPress Core `Plugin_Upgrader` 활용
- 3J Labs 플러그인 및 WordPress.org 플러그인 지원
- 자동 백업 및 복원 기능
- 롤백 후 활성화 상태 자동 복원
- 롤백 버전 선택 모달 UI

### Transient Caching System (User Journey Analytics)
- Traffic Reporter에 캐싱 레이어 추가
- 실시간 데이터: 1분 TTL
- 요약 데이터: 5분 TTL
- 상세 분석: 10분 TTL
- 일일 데이터: 1시간 TTL
- 캐시 무효화 메서드 제공

---

## Security Fixes

### AJAX Nonce Verification (User Journey Analytics)
- `ajax_track_visit()` 메서드에 nonce 검증 추가
- CSRF 공격 방지
- 실패 시 에러 응답 반환

```php
if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'acf_uja_nonce' ) ) {
    wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
    return;
}
```

---

## Bug Fixes

### PHP Syntax Errors (3 files fixed)

1. **class-jj-rollback-shared.php**
   - 생성자 중복 선언 수정
   - 닫히지 않은 함수 블록 수정
   - 싱글톤 패턴 재구현

2. **class-jj-plugin-list-enhancer.php**
   - PHP 문자열 내 JavaScript `$` 변수 이스케이프 누락 수정
   - 모든 `$modal`, `$trigger`, `$confirmBtn` 변수 이스케이프

3. **acf-nudge-flow/admin/class-admin.php**
   - `else` 다음 `elseif` 사용 문법 오류 수정
   - `else`를 `elseif ( $active_tab === 'nudge' )`로 변경

---

## Performance Improvements

### Database Query Optimization
- Transient 캐싱으로 반복적인 데이터베이스 쿼리 감소
- 대시보드 로딩 시간 개선

### Cache Invalidation
- 선택적 캐시 무효화 지원
- 데이터 변경 시 자동 캐시 갱신

---

## Technical Details

### Files Modified
| File | Changes |
|------|---------|
| `acf-user-journey-analytics/acf-user-journey-analytics.php` | AJAX nonce 검증 |
| `acf-user-journey-analytics/includes/class-traffic-reporter.php` | 캐싱 시스템 |
| `acf-css-really-simple-style-management-center-master/includes/class-jj-plugin-list-enhancer.php` | 롤백 기능, JS 이스케이프 |
| `acf-nudge-flow/admin/class-admin.php` | if/elseif 문법 수정 |
| `shared-ui-assets/php/class-jj-rollback-shared.php` | 클래스 구조 수정 |

### Lines Changed
- Total additions: ~350 lines
- Total modifications: ~50 lines
- Bug fixes: 3 critical PHP syntax errors

### PHP Syntax Check Results
```
Total files checked: 283
Passed: 283
Errors: 0
```

---

## Compatibility

- WordPress: 6.0+
- PHP: 7.4+
- MySQL: 5.7+ / MariaDB 10.3+

---

## Upgrade Notes

1. 이 업데이트는 자동으로 적용됩니다
2. 캐시 시스템은 기본 활성화됩니다
3. 롤백 기능은 플러그인 목록 페이지에서 사용할 수 있습니다
4. 기존 데이터 및 설정은 유지됩니다

---

## Known Issues

- None

---

## Credits

- Development: 3J Labs (Jay, Jenny, Jason)
- AI Assistant: Claude Code (Opus 4.5)
