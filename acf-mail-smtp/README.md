# ACF Mail SMTP - Advanced Custom Form & Mail & SMTP

강력한 폼 빌더, SMTP 이메일 발송, Gmail API OAuth2 연동, 자동화 기능을 제공하는 올인원 폼 솔루션입니다.

## Version: 2.1.0

## Features

### Form Builder
- 드래그 앤 드롭 폼 빌더
- 다양한 필드 타입 지원 (텍스트, 이메일, 전화, 체크박스, 라디오 등)
- 조건부 로직
- 폼 제출 데이터 저장 및 관리

### SMTP / Gmail API
- 표준 SMTP 설정 (Host, Port, TLS/SSL, 인증)
- **[v2.1.0] Gmail API OAuth2 연동**
  - Google Cloud Console 연동
  - OAuth2 토큰 자동 갱신
  - XOAUTH2 인증 방식 지원
- 비밀번호 AES-256-CBC 암호화
- 테스트 이메일 발송

### Email Features
- HTML 이메일 템플릿
- 변수 치환 (폼 제목, 데이터, 사이트명 등)
- 첨부파일 지원
- 이메일 발송 로그

### Automation
- 조건부 트리거 (8가지 연산자)
- 이메일 자동 발송
- 웹훅 (JSON POST)
- 제출 후 리다이렉션

## Requirements

- WordPress 6.0+
- PHP 7.4+
- OpenSSL extension (암호화용)

## Installation

1. 플러그인 zip 파일 업로드
2. 플러그인 활성화
3. ACF Mail SMTP 메뉴에서 설정

## Gmail API 설정 방법

1. [Google Cloud Console](https://console.cloud.google.com) 접속
2. 새 프로젝트 생성 또는 기존 프로젝트 선택
3. API 및 서비스 > OAuth 동의 화면 설정
4. API 및 서비스 > 사용자 인증 정보 > OAuth 2.0 클라이언트 ID 생성
5. 승인된 리디렉션 URI에 `https://yourdomain.com/wp-admin/admin.php?page=acf-mail-smtp-smtp&gmail_oauth=callback` 추가
6. 클라이언트 ID와 시크릿을 플러그인 SMTP 설정에 입력
7. "Gmail 연결" 버튼 클릭하여 OAuth 인증

## Changelog

### v2.1.0 (2026-01-05)
- Gmail API OAuth2 직접 연동 추가
- OAuth2 토큰 자동 갱신
- XOAUTH2 SMTP 인증 지원
- 보안 강화 (다중 salt 기반 암호화)

### v1.0.2
- 초기 릴리스
- SMTP 설정 및 이메일 발송
- 폼 빌더 및 제출 관리
- 자동화 규칙

## Author

3J Labs (제이x제니x제이슨 연구소)
Created by Jay & Jason & Jenny

## License

GPLv2 or later
