# 3J Labs - Local WordPress Development Environment

> **Kinsta Production Mirror**  
> PHP 8.4 + nginx 1.29 + MariaDB 11.4

## 🚀 빠른 시작

### 1. 환경 변수 설정

```bash
cp env.example .env
# 필요한 경우 .env 파일 수정
```

### 2. Docker 환경 시작

```bash
# 기본 환경 시작 (WordPress + nginx + MariaDB)
docker-compose up -d

# 도구 포함 시작 (phpMyAdmin, WP-CLI)
docker-compose --profile tools up -d
```

### 3. 접속

| 서비스 | URL | 비고 |
|--------|-----|------|
| **WordPress** | http://localhost:8080 | 프론트엔드 |
| **관리자** | http://localhost:8080/wp-admin | 관리자 패널 |
| **phpMyAdmin** | http://localhost:8081 | 데이터베이스 관리 |

### 4. 기본 관리자 계정

- **ID**: admin
- **Password**: admin123!

---

## 📁 폴더 구조

```
local-wordpress/
├── config/
│   ├── mariadb/my.cnf        # MariaDB 설정
│   ├── nginx/
│   │   ├── nginx.conf        # nginx 메인 설정
│   │   └── default.conf      # 가상 호스트 설정
│   └── php/
│       ├── php.ini           # PHP 설정
│       └── php-fpm.conf      # PHP-FPM 설정
├── logs/
│   ├── nginx/                # nginx 로그
│   └── php/                  # PHP 로그
├── plugins/                  # 개발 중인 플러그인 (마운트)
├── scripts/
│   ├── deploy-plugin.ps1     # 플러그인 배포 (PowerShell)
│   ├── deploy-plugin.sh      # 플러그인 배포 (Bash)
│   ├── docker-entrypoint.sh  # Docker 엔트리포인트
│   └── init-wordpress.sh     # WordPress 초기화
├── docker-compose.yml        # Docker Compose 설정
├── Dockerfile.php            # PHP 이미지 빌드
├── env.example               # 환경 변수 템플릿
└── README.md                 # 이 파일
```

---

## 🔧 플러그인 개발

### 플러그인 배포

```powershell
# PowerShell (Windows)
.\scripts\deploy-plugin.ps1

# 파일 변경 감시 모드
.\scripts\deploy-plugin.ps1 -Watch

# 기존 삭제 후 재배포
.\scripts\deploy-plugin.ps1 -Clean

# 배포 후 활성화
.\scripts\deploy-plugin.ps1 -Activate
```

```bash
# Bash (Linux/macOS)
./scripts/deploy-plugin.sh

# 파일 변경 감시 모드
./scripts/deploy-plugin.sh --watch

# 기존 삭제 후 재배포
./scripts/deploy-plugin.sh --clean

# 배포 후 활성화
./scripts/deploy-plugin.sh --activate
```

### 실시간 개발

`plugins/` 폴더는 WordPress 컨테이너에 마운트되어 있어, 파일 변경이 즉시 반영됩니다.

```bash
# 플러그인 폴더를 직접 수정하면 자동으로 반영됨
local-wordpress/plugins/ → /var/www/html/wp-content/plugins/acf-css-dev
```

---

## 📊 Kinsta 사양 비교

| 항목 | Kinsta 프로덕션 | 로컬 환경 |
|------|-----------------|-----------|
| **PHP** | 8.4.13 | 8.4 |
| **nginx** | 1.29.2 | 1.27 |
| **MariaDB** | 11.4.7 | 11.4 |
| **메모리** | 256M | 256M |
| **업로드** | 128M | 128M |
| **시간대** | Asia/Seoul | Asia/Seoul |
| **max_input_vars** | 10000 | 10000 |
| **max_execution_time** | 300 | 300 |

---

## 🛠 유용한 명령어

### Docker 관리

```bash
# 로그 확인
docker-compose logs -f

# 특정 서비스 로그
docker-compose logs -f php

# 컨테이너 재시작
docker-compose restart

# 환경 중지
docker-compose down

# 환경 중지 + 볼륨 삭제 (데이터 초기화)
docker-compose down -v
```

### WP-CLI

```bash
# WP-CLI 컨테이너 접속
docker exec -it 3j_wpcli bash

# 플러그인 목록
docker exec 3j_wpcli wp plugin list --path=/var/www/html

# 플러그인 활성화
docker exec 3j_wpcli wp plugin activate acf-css-dev --path=/var/www/html

# 캐시 삭제
docker exec 3j_wpcli wp cache flush --path=/var/www/html
```

### PHP 컨테이너

```bash
# PHP 컨테이너 접속
docker exec -it 3j_php sh

# PHP 버전 확인
docker exec 3j_php php -v

# 설치된 확장 모듈
docker exec 3j_php php -m
```

---

## 🐛 트러블슈팅

### 포트 충돌

다른 서비스가 8080 포트를 사용 중인 경우:

```bash
# .env 파일에서 포트 변경
HTTP_PORT=8888
```

### 권한 문제

```bash
# WordPress 파일 권한 재설정
docker exec 3j_php chown -R www-data:www-data /var/www/html
docker exec 3j_php chmod -R 755 /var/www/html
```

### 데이터베이스 연결 실패

```bash
# 데이터베이스 상태 확인
docker exec 3j_mariadb mysqladmin ping -uroot -p3j_root_secret

# 데이터베이스 재시작
docker-compose restart db
```

### 이미지 재빌드

```bash
# PHP 이미지 재빌드
docker-compose build --no-cache php

# 전체 재시작
docker-compose down && docker-compose up -d --build
```

---

## 📝 라이센스

3J Labs - MIT License
