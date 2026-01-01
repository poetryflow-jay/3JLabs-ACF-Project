#!/bin/sh
# 3J Labs - Docker Entrypoint Script
# WordPress 초기화 및 PHP-FPM 시작

set -e

# 로그 디렉토리 생성
mkdir -p /var/log/php
chown -R www-data:www-data /var/log/php

# WordPress 설치 확인
if [ ! -f /var/www/html/wp-config.php ]; then
    echo "[3J Labs] WordPress 초기화 중..."
    
    # WordPress 파일 복사
    if [ -d /tmp/wordpress ]; then
        cp -r /tmp/wordpress/* /var/www/html/
        rm -rf /tmp/wordpress
    fi
    
    # wp-config.php 생성
    cat > /var/www/html/wp-config.php << 'WPCONFIG'
<?php
/**
 * WordPress 설정 파일
 * 3J Labs - Local Development Environment
 */

// 데이터베이스 설정
define('DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'wordpress');
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wordpress');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'wordpress_secret');
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'db:3306');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// 인증 고유 키
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

// 테이블 접두사
$table_prefix = 'wp_';

// 디버그 모드
define('WP_DEBUG', filter_var(getenv('WORDPRESS_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN));
define('WP_DEBUG_LOG', filter_var(getenv('WORDPRESS_DEBUG_LOG') ?: 'false', FILTER_VALIDATE_BOOLEAN));
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', WP_DEBUG);

// 메모리 제한
define('WP_MEMORY_LIMIT', getenv('WP_MEMORY_LIMIT') ?: '256M');
define('WP_MAX_MEMORY_LIMIT', getenv('WP_MAX_MEMORY_LIMIT') ?: '256M');

// 파일 편집 (개발 환경에서 활성화)
define('DISALLOW_FILE_EDIT', false);

// 자동 업데이트 비활성화 (개발 환경)
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);

// 환경 타입
define('WP_ENVIRONMENT_TYPE', 'local');

// 사이트 URL (로컬)
if (!defined('WP_HOME')) {
    define('WP_HOME', 'http://localhost:8080');
}
if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', 'http://localhost:8080');
}

// 절대 경로
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// WordPress 설정 포함
require_once ABSPATH . 'wp-settings.php';
WPCONFIG

    # 권한 설정
    chown -R www-data:www-data /var/www/html
    chmod -R 755 /var/www/html
    chmod 640 /var/www/html/wp-config.php
    
    echo "[3J Labs] WordPress 초기화 완료!"
fi

# 플러그인 디렉토리 확인
if [ ! -d /var/www/html/wp-content/plugins ]; then
    mkdir -p /var/www/html/wp-content/plugins
    chown -R www-data:www-data /var/www/html/wp-content/plugins
fi

# 심볼릭 링크 또는 마운트 확인 (ACF CSS 개발 플러그인)
if [ -d /var/www/html/wp-content/plugins/acf-css-dev ]; then
    echo "[3J Labs] ACF CSS 개발 플러그인 마운트 감지됨"
fi

echo "[3J Labs] PHP-FPM 시작..."
exec "$@"
