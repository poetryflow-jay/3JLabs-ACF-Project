# 3J Labs - PHP 8.5 FPM Docker Image
# 제이x제니x제이슨 연구소 (3J Labs)
# Latest PHP for Early Adopters
#
# PHP 8.5 + FPM + 필수 확장 모듈

FROM php:8.4-fpm-alpine
# Note: PHP 8.5는 2025년 11월 출시 예정. 현재는 8.4 사용
# PHP 8.5 공식 이미지 출시 시 아래로 변경:
# FROM php:8.5-fpm-alpine

LABEL maintainer="3J Labs <support@3j-labs.com>"
LABEL description="PHP 8.5 FPM for WordPress - 3J Labs Development (제이x제니x제이슨 연구소)"

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

# WordPress CLI 설치
COPY --from=wordpress:cli-php8.3 /usr/local/bin/wp /usr/local/bin/wp

# 작업 디렉토리 설정
WORKDIR /var/www/html

# PHP-FPM 설정 (호스트에서 마운트)
# COPY config/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# 로그 디렉토리 생성
RUN mkdir -p /var/log/php && chown -R www-data:www-data /var/log/php

# 기본 PHP-FPM 실행
CMD ["php-fpm"]

EXPOSE 9000
