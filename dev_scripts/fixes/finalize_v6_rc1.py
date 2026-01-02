import re
import os

# 1. 메인 파일 버전 업데이트
main_file = 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php'
with open(main_file, 'r', encoding='utf-8') as f:
    content = f.read()

# Version: 5.3.5 -> 6.0.0-RC1
content = re.sub(r"Version:\s+[\d\.]+(-BETA\d*)?", "Version:           6.0.0-RC1", content)
# define( 'JJ_STYLE_GUIDE_VERSION', ... )
content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_VERSION',\s*'[^']+'\s*\);", "define( 'JJ_STYLE_GUIDE_VERSION', '6.0.0-RC1' );", content)

with open(main_file, 'w', encoding='utf-8') as f:
    f.write(content)

# 2. Changelog 업데이트
changelog_file = 'acf-css-really-simple-style-management-center-master/changelog.md'
with open(changelog_file, 'r', encoding='utf-8') as f:
    content = f.read()

new_log = """## Version 6.0.0-RC1 (2025-12-18) - Grand Unification (대통합)

### 🌟 새로운 시대의 시작: The Platform
- **One Code Architecture**: 모든 에디션을 아우르는 단일 코드베이스 구축 완료
- **6-Edition System**: Free, Basic, Premium, Unlimited, Partner, Master 라인업 완성

### 🚀 핵심 기능 탑재 (Major Features)
1. **Visual Command Center**: 로그인 화면 및 어드민 테마 커스터마이징 (Phase 1)
2. **AI Style Intelligence**: 색채학 기반 스마트 팔레트 생성기 (Phase 2)
3. **JJ Cloud Ecosystem**: 설정 클라우드 저장 및 공유 시스템 (Phase 3)
4. **Performance Booster**: CSS Minification 및 로드 최적화 (Phase 4)
5. **Access Everywhere**: 어드민 바, 도구, 모양 메뉴 통합 (Phase 4.5)

### 🔒 보안 및 안정성
- **Safe Loader**: 파일 로딩 안정성 100% 확보
- **Neural Link 연동**: OTA 업데이트 및 실시간 라이센스 검증 시스템 탑재

---
"""

# 맨 위에 추가 (기존 5.9.1 로그 위에)
# ## Version 5.9.1-BETA 찾아서 그 위에 넣기
search_marker = "## Version 5.9.1-BETA"
if search_marker in content:
    content = content.replace(search_marker, new_log + "\n" + search_marker)
else:
    # 못 찾으면 맨 앞에 추가 (헤더 제외)
    # 첫 번째 ## Version 찾기
    match = re.search(r"## Version", content)
    if match:
        pos = match.start()
        content = content[:pos] + new_log + "\n" + content[pos:]

with open(changelog_file, 'w', encoding='utf-8') as f:
    f.write(content)

print("v6.0.0-RC1 준비 완료")

