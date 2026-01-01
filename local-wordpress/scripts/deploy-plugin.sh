#!/bin/bash
# 3J Labs - 플러그인 자동 배포 스크립트 (Bash)
# ACF CSS 플러그인을 로컬 WordPress에 배포합니다.
#
# 사용법:
#   ./scripts/deploy-plugin.sh                # 전체 동기화
#   ./scripts/deploy-plugin.sh --watch        # 파일 변경 감시 및 자동 배포
#   ./scripts/deploy-plugin.sh --clean        # 기존 플러그인 삭제 후 재배포
#   ./scripts/deploy-plugin.sh --activate     # 배포 후 플러그인 활성화

set -e

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

# 경로 설정
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"
PLUGIN_SOURCE_DIR="$(dirname "$ROOT_DIR")/acf-css-really-simple-style-management-center-master"
PLUGIN_DEST_DIR="$ROOT_DIR/plugins"

# 옵션 파싱
WATCH=false
CLEAN=false
ACTIVATE=false
DEACTIVATE=false

while [[ "$#" -gt 0 ]]; do
    case $1 in
        --watch|-w) WATCH=true ;;
        --clean|-c) CLEAN=true ;;
        --activate|-a) ACTIVATE=true ;;
        --deactivate|-d) DEACTIVATE=true ;;
        *) echo "Unknown option: $1"; exit 1 ;;
    esac
    shift
done

# 출력 함수
success() { echo -e "${GREEN}[OK]${NC} $1"; }
info() { echo -e "${CYAN}[INFO]${NC} $1"; }
warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; }

# 배너 출력
echo ""
echo -e "${MAGENTA}============================================${NC}"
echo -e "${MAGENTA}  3J Labs - Plugin Auto-Deploy Script${NC}"
echo -e "${MAGENTA}============================================${NC}"
echo ""

# 소스 경로 확인
if [ ! -d "$PLUGIN_SOURCE_DIR" ]; then
    error "플러그인 소스 경로를 찾을 수 없습니다: $PLUGIN_SOURCE_DIR"
    exit 1
fi
info "소스 경로: $PLUGIN_SOURCE_DIR"

# 대상 경로 생성
mkdir -p "$PLUGIN_DEST_DIR"

# 배포 함수
deploy_plugin() {
    info "플러그인 배포 시작..."
    
    start_time=$(date +%s.%N)
    
    # Clean 옵션: 기존 파일 삭제
    if [ "$CLEAN" = true ]; then
        warn "기존 플러그인 파일 삭제 중..."
        rm -rf "$PLUGIN_DEST_DIR"/*
    fi
    
    # rsync로 동기화 (변경된 파일만)
    rsync -av --delete \
        --exclude='.git' \
        --exclude='node_modules' \
        --exclude='tests' \
        --exclude='.github' \
        --exclude='vendor' \
        --exclude='*.swp' \
        --exclude='*.tmp' \
        "$PLUGIN_SOURCE_DIR/" "$PLUGIN_DEST_DIR/"
    
    end_time=$(date +%s.%N)
    elapsed=$(echo "$end_time - $start_time" | bc)
    
    success "배포 완료! (소요시간: ${elapsed}초)"
    
    # 플러그인 활성화
    if [ "$ACTIVATE" = true ]; then
        info "플러그인 활성화 중..."
        docker exec 3j_wpcli wp plugin activate acf-css-dev --path=/var/www/html 2>/dev/null && \
            success "플러그인 활성화 완료!" || \
            warn "플러그인 활성화 실패 (수동 활성화 필요)"
    fi
    
    # 플러그인 비활성화
    if [ "$DEACTIVATE" = true ]; then
        info "플러그인 비활성화 중..."
        docker exec 3j_wpcli wp plugin deactivate acf-css-dev --path=/var/www/html 2>/dev/null && \
            success "플러그인 비활성화 완료!" || \
            warn "플러그인 비활성화 실패"
    fi
}

# Watch 모드
watch_plugin() {
    info "파일 변경 감시 모드 시작..."
    info "종료하려면 Ctrl+C를 누르세요."
    echo ""
    
    # 초기 배포
    deploy_plugin
    
    # inotifywait 또는 fswatch 사용
    if command -v fswatch &> /dev/null; then
        fswatch -o "$PLUGIN_SOURCE_DIR" | while read -r; do
            echo ""
            info "[$(date +%H:%M:%S)] 변경 감지!"
            deploy_plugin
        done
    elif command -v inotifywait &> /dev/null; then
        while true; do
            inotifywait -r -e modify,create,delete "$PLUGIN_SOURCE_DIR" 2>/dev/null
            echo ""
            info "[$(date +%H:%M:%S)] 변경 감지!"
            deploy_plugin
        done
    else
        warn "fswatch 또는 inotifywait가 설치되어 있지 않습니다."
        warn "수동 배포 모드로 전환합니다."
        
        while true; do
            read -p "Enter를 눌러 배포하거나 q를 입력하여 종료: " input
            if [ "$input" = "q" ]; then
                break
            fi
            deploy_plugin
        done
    fi
}

# 메인 실행
if [ "$WATCH" = true ]; then
    watch_plugin
else
    deploy_plugin
fi

echo ""
