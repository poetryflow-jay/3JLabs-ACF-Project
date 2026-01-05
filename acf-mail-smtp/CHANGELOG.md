# Changelog

All notable changes to ACF Mail SMTP will be documented in this file.

## [2.1.0] - 2026-01-05

### Added
- Gmail API OAuth2 직접 연동 (`class-gmail-api.php`)
- Google OAuth2 인증 플로우
- OAuth2 Access Token 자동 갱신
- XOAUTH2 SMTP 인증 방식 지원
- Gmail API를 통한 직접 이메일 발송
- 다중 WordPress salt 기반 암호화 강화

### Changed
- 버전 업데이트 1.0.2 → 2.1.0
- 보안 모듈 암호화 키 분리 (auth salt + secure_auth salt)

### Technical Details
- `ACF_Mail_SMTP_Gmail_API` 클래스 추가
- Google OAuth2 엔드포인트 연동
- MIME 메시지 생성 및 Base64 URL-safe 인코딩
- 첨부파일 지원 (multipart/mixed)
- 한글 헤더 인코딩 (RFC 2047)

## [1.0.2] - 2025-12-xx

### Added
- 초기 릴리스
- SMTP 설정 및 이메일 발송
- 폼 빌더 및 제출 관리
- 자동화 규칙 (조건부 트리거)
- 이메일 로그
- HTML 이메일 템플릿
