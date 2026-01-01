#!/bin/sh
# 3J Labs - WordPress 초기화 스크립트
# WP-CLI를 사용한 WordPress 설치 및 설정

set -e

WP_PATH="/var/www/html"

echo "[3J Labs] WordPress 설치 확인 중..."

# 데이터베이스 연결 대기
echo "[3J Labs] 데이터베이스 연결 대기 중..."
while ! mysqladmin ping -h"db" --silent; do
    sleep 1
done
echo "[3J Labs] 데이터베이스 연결 완료!"

# WordPress 설치 확인
if ! wp core is-installed --path="$WP_PATH" 2>/dev/null; then
    echo "[3J Labs] WordPress 설치 시작..."
    
    wp core install \
        --path="$WP_PATH" \
        --url="http://localhost:8080" \
        --title="3J Labs Development Site" \
        --admin_user="admin" \
        --admin_password="admin123!" \
        --admin_email="admin@3j-labs.com" \
        --locale="ko_KR" \
        --skip-email
    
    echo "[3J Labs] WordPress 설치 완료!"
    
    # 기본 설정
    echo "[3J Labs] 기본 설정 적용 중..."
    
    # 시간대 설정
    wp option update timezone_string "Asia/Seoul" --path="$WP_PATH"
    
    # 퍼마링크 설정
    wp rewrite structure "/%postname%/" --path="$WP_PATH"
    
    # 언어 설정
    wp language core install ko_KR --path="$WP_PATH"
    wp site switch-language ko_KR --path="$WP_PATH"
    
    # 기본 플러그인 삭제
    wp plugin delete hello --path="$WP_PATH" 2>/dev/null || true
    wp plugin delete akismet --path="$WP_PATH" 2>/dev/null || true
    
    # 기본 테마 삭제 (하나만 남김)
    # wp theme delete twentytwentythree --path="$WP_PATH" 2>/dev/null || true
    
    echo "[3J Labs] 기본 설정 완료!"
fi

# ACF CSS 플러그인 활성화
if [ -d "$WP_PATH/wp-content/plugins/acf-css-dev" ]; then
    echo "[3J Labs] ACF CSS 개발 플러그인 활성화 중..."
    wp plugin activate acf-css-dev --path="$WP_PATH" 2>/dev/null || true
fi

echo "[3J Labs] WordPress 초기화 완료!"
echo ""
echo "=========================================="
echo "  3J Labs Local WordPress Environment"
echo "=========================================="
echo ""
echo "  사이트 URL:     http://localhost:8080"
echo "  관리자 URL:     http://localhost:8080/wp-admin"
echo "  phpMyAdmin:     http://localhost:8081"
echo ""
echo "  관리자 계정:"
echo "    ID:       admin"
echo "    Password: admin123!"
echo ""
echo "=========================================="
