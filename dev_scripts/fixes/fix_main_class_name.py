file_path = 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. 클래스 이름 변경: JJ_Simple_Style_Guide_Master_Free -> JJ_Simple_Style_Guide_Master
content = content.replace('class JJ_Simple_Style_Guide_Master_Free', 'class JJ_Simple_Style_Guide_Master')

# 2. 버전 업데이트: 6.0.0-RC1 -> 6.0.1
content = content.replace("Version:           6.0.0-RC1", "Version:           6.0.1")
content = content.replace("define( 'JJ_STYLE_GUIDE_VERSION', '6.0.0-RC1' );", "define( 'JJ_STYLE_GUIDE_VERSION', '6.0.1' );")

# 3. 퀵 링크 로직은 이미 fix_quick_links_syntax.py에서 수정되었으므로, 클래스 이름만 바뀌면 동작함.

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Main class name fixed and version bumped to 6.0.1.")

