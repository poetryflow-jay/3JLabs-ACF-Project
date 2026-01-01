<?php
/**
 * 3J Labs - Local WordPress Configuration
 * 제이x제니x제이슨 연구소 (3J Labs)
 * 
 * 로컬 Docker 환경용 설정 파일
 * 사용법: 이 파일을 wp-config.php로 복사하거나 이름 변경
 */

// ** Docker 환경 데이터베이스 설정 ** //
define( 'DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'wordpress' );
define( 'DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wordpress' );
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'wordpress_secret' );
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'db:3306' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', 'utf8mb4_unicode_ci' );

/**
 * Authentication Keys and Salts
 * Kinsta 백업에서 가져온 키 유지
 */
define( 'AUTH_KEY',          '{Y;mq<$l?$XF4VY`y@=i(4$ B6`Iq2D:4Sv+!EH}+:~XO@|Zk@p5;7ycN4Um@4:e' );
define( 'SECURE_AUTH_KEY',   '.c%@Iw2HC,EPwFnE7*w}AODTF1e-k=EH4Wh0.O/P_F_Ss }D~7MW^d2e.sAmM-$v' );
define( 'LOGGED_IN_KEY',     'Q5>6NNo&[~O#6; MBnkM$gRgqZHLeW*^PgQ>yHV~UyB=n`$mB%=.-DaPy7X-mn7T' );
define( 'NONCE_KEY',         'l:o^AaU#?^BnF^q{.%zVc>$1{!EH!pYa&PI(/S[Oa>Et?&qsT`c]SdOXNs qGJ@b' );
define( 'AUTH_SALT',         'eLW^yPk-|p+]M_?0R`R/Au(N$q/]hL.5J+>}m<$/X`RDMn)4agx);#V?M3Gy!!/q' );
define( 'SECURE_AUTH_SALT',  'p4E+y#_MKDKR|t5$Xv)8LgjxA!A9.V/3Z {Bg?io<Rm:M<U9$@&3W}gnH[Qw|,fU' );
define( 'LOGGED_IN_SALT',    ',(2:ihY^qsDdoTDCIp^CV9?H<~_%Zn$F.0jb<:[g&}_bp 2S1nVR,1F`Zj+o!_AG' );
define( 'NONCE_SALT',        'qhV<#=g@v(PrVw=THO* Caa$9/YI*7c6]RPusn9|A)%,Dr!PMapXi&wX)z;Xk`=^' );
define( 'WP_CACHE_KEY_SALT', ',{1#8^7g4?WP$@gXu;^5S$z~(T{?.CUEt5a/R,+KG0},Gc=6d [ggSZO/~3Wfl.X' );

/**
 * WordPress 데이터베이스 테이블 접두사
 */
$table_prefix = 'wp_';

/**
 * 로컬 개발 환경 설정
 */
// 사이트 URL (로컬)
define( 'WP_HOME', getenv('WP_HOME') ?: 'http://localhost:8080' );
define( 'WP_SITEURL', getenv('WP_SITEURL') ?: 'http://localhost:8080' );

// 디버그 모드 (개발 환경)
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'SCRIPT_DEBUG', true );

// 메모리 제한
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '256M' );

// 파일 편집 허용 (개발 환경)
define( 'DISALLOW_FILE_EDIT', false );

// 자동 업데이트 비활성화 (개발 환경)
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );

// 환경 타입
define( 'WP_ENVIRONMENT_TYPE', 'local' );

// 캐시 비활성화 (개발 환경)
define( 'WP_CACHE', false );

// SSL 강제 비활성화 (로컬)
define( 'FORCE_SSL_ADMIN', false );

/* 편집 종료 */

/** WordPress 디렉토리 절대 경로 */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** WordPress 설정 포함 */
require_once ABSPATH . 'wp-settings.php';
