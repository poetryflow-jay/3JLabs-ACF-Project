#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 20] PO 파일 Description 부분 수정

Description 부분에 msgid가 누락된 경우를 수정합니다.
"""

from pathlib import Path

# POT 파일에서 msgid 가져오기
POT_MSGID = "A comprehensive style management plugin that centrally and consistently manages all style elements of your WordPress website, including color palettes, typography, buttons, and forms. The Free version provides basic style management features for maintaining brand consistency and efficient design system operation. Install the Pro version plugin alongside to access Basic, Premium, and Unlimited features. Fully integrated with WordPress Customizer for intuitive style management with real-time preview."

def fix_po_description(po_path: Path) -> bool:
    """PO 파일의 Description 부분 수정"""
    try:
        with open(po_path, 'r', encoding='utf-8') as f:
            lines = f.readlines()
        
        fixed_lines = []
        i = 0
        
        while i < len(lines):
            line = lines[i]
            
            # Description 부분 찾기
            if line.strip() == '#. Description of the plugin':
                fixed_lines.append(line)
                i += 1
                
                # 다음 줄들 건너뛰기 (#: 로 시작하는 줄)
                while i < len(lines) and lines[i].startswith('#'):
                    fixed_lines.append(lines[i])
                    i += 1
                
                # msgid가 없으면 추가
                if i >= len(lines) or not lines[i].startswith('msgid'):
                    # msgid 추가
                    fixed_lines.append(f'msgid "{POT_MSGID}"\n')
                
                # msgid 줄들 읽기
                while i < len(lines) and (lines[i].startswith('msgid') or (lines[i].startswith('"') and not lines[i].startswith('msgstr'))):
                    if lines[i].startswith('msgid'):
                        fixed_lines.append(lines[i])
                    i += 1
                
                # msgstr 줄 찾기 및 수정
                if i < len(lines) and lines[i].startswith('msgstr'):
                    # 여러 줄 msgstr 처리
                    msgstr_lines = []
                    msgstr_lines.append(lines[i])
                    i += 1
                    while i < len(lines) and lines[i].startswith('"') and not lines[i].startswith('msgid') and not lines[i].startswith('#.'):
                        # "msgstr" 텍스트가 포함된 경우 제거
                        cleaned = lines[i].strip().strip('"')
                        if cleaned.startswith('msgstr'):
                            cleaned = cleaned[6:].strip().strip('"')
                        msgstr_lines.append(f'"{cleaned}"\n' if cleaned else lines[i])
                        i += 1
                    
                    # 여러 줄의 따옴표를 하나로 합치기
                    combined = ''.join([line.strip().strip('"') for line in msgstr_lines if line.strip() and not line.strip().startswith('msgstr')])
                    
                    # PO 파일 형식에 맞게 여러 줄로 나누기
                    if len(combined) > 77:
                        fixed_lines.append('msgstr ""\n')
                        current = combined
                        while len(current) > 77:
                            fixed_lines.append(f'"{current[:77]}"\n')
                            current = current[77:]
                        if current:
                            fixed_lines.append(f'"{current}"\n')
                    else:
                        fixed_lines.append(f'msgstr "{combined}"\n')
                else:
                    # msgstr이 없으면 추가
                    fixed_lines.append('msgstr ""\n')
                continue
            
            fixed_lines.append(line)
            i += 1
        
        with open(po_path, 'w', encoding='utf-8') as f:
            f.writelines(fixed_lines)
        
        return True
    except Exception as e:
        print(f"   ❌ 오류: {e}")
        import traceback
        traceback.print_exc()
        return False

def main():
    """메인 함수"""
    base_path = Path(__file__).parent
    plugin_path = base_path / 'acf-css-really-simple-style-management-center-master'
    languages_path = plugin_path / 'languages'
    
    if not languages_path.exists():
        print(f"❌ languages 폴더가 없습니다: {languages_path}")
        return
    
    print("=" * 60)
    print("Phase 20: PO 파일 Description 부분 수정")
    print("=" * 60)
    print()
    
    po_files = list(languages_path.glob('*.po'))
    
    success_count = 0
    fail_count = 0
    
    for po_file in po_files:
        lang_code = po_file.stem.replace('acf-css-really-simple-style-management-center-', '')
        print(f"📝 [{lang_code}] 수정 중...")
        
        if fix_po_description(po_file):
            print(f"   ✅ 완료: {po_file.name}")
            success_count += 1
        else:
            print(f"   ❌ 실패: {po_file.name}")
            fail_count += 1
    
    print()
    print("=" * 60)
    print(f"✅ 완료: {success_count}개")
    if fail_count > 0:
        print(f"❌ 실패: {fail_count}개")
    print("=" * 60)

if __name__ == '__main__':
    main()
