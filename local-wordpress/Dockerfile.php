# 3J Labs - PHP 8.4 FPM Docker Image
# Kinsta Production Mirror
#
# PHP 8.4.13 + FPM + 필수 확장 모듈

FROM php:8.4-fpm-alpine

LABEL maintainer="3J Labs <support@3j-labs.com>"
LABEL description="PHP 8.4 FPM for WordPress - Kinsta Production Mirror"

# 시스템 패키지 설치
RUN apk add --no-cache \
    # 이미지 처리
    imagemagick \
    imagemagick-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libavif-dev \
    # 데이터베이스
    mariadb-client \
    # 기타 의존성
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    curl-dev \
    # 빌드 도구
    $PHPIZE_DEPS \
    linux-headers

# PHP 확장 모듈 설정 및 설치
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    --with-avif

RUN docker-php-ext-install -j$(nproc) \
    # 데이터베이스
    mysqli \
    pdo \
    pdo_mysql \
    # 이미지 처리
    gd \
    exif \
    # 문자열/국제화
    mbstring \
    intl \
    # XML 처리
    xml \
    dom \
    simplexml \
    xmlreader \
    xmlwriter \
    # 기타
    zip \
    opcache \
    bcmath \
    calendar \
    sockets

# ImageMagick (imagick) 설치
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Redis 확장 설치 (캐싱용)
RUN pecl install redis \
    && docker-php-ext-enable redis

# APCu 확장 설치 (오브젝트 캐싱)
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# 임시 빌드 도구 정리
RUN apk del $PHPIZE_DEPS linux-headers

# WordPress 설치 (wp-cli 사용)
COPY --from=wordpress:cli-php8.3 /usr/local/bin/wp /usr/local/bin/wp

# WordPress 다운로드 및 설치
ENV WORDPRESS_VERSION 6.9
RUN curl -o /tmp/wordpress.tar.gz -fSL "https://wordpress.org/wordpress-${WORDPRESS_VERSION}.tar.gz" \
    && tar -xzf /tmp/wordpress.tar.gz -C /tmp \
    && rm /tmp/wordpress.tar.gz

# 작업 디렉토리 설정
WORKDIR /var/www/html

# WordPress 파일 복사 스크립트
COPY scripts/init-wordpress.sh /usr/local/bin/init-wordpress.sh
RUN chmod +x /usr/local/bin/init-wordpress.sh

# PHP-FPM 설정
COPY config/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# 권한 설정
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 엔트리포인트
COPY scripts/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]

EXPOSE 9000
