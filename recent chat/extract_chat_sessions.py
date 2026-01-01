#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
대화 파일에서 각 세션을 추출하여 저장하는 스크립트
"""
import re
from pathlib import Path

def extract_sessions(filepath, output_dir):
    """대화 파일에서 각 세션을 추출하여 별도 파일로 저장"""
    output_dir = Path(output_dir)
    output_dir.mkdir(exist_ok=True)
    
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # 각 대화 세션 추출 (--- 로 구분된 블록)
    pattern = r'(^---\s*\n\*\*(?:User|Cursor)\*\*.*?)(?=\n---|\Z)'
    sessions = re.finditer(pattern, content, re.MULTILINE | re.DOTALL)
    
    sessions_list = []
    current_session = None
    
    for match in sessions:
        block = match.group(1)
        if '**User**' in block:
            if current_session:
                sessions_list.append(current_session)
            current_session = {'user': block, 'cursor': None}
        elif '**Cursor**' in block and current_session:
            current_session['cursor'] = block
    
    if current_session:
        sessions_list.append(current_session)
    
    # 각 세션을 파일로 저장 (첫 20개만)
    for i, session in enumerate(sessions_list[:20], 1):
        session_content = f"{session['user']}\n\n{session['cursor'] if session['cursor'] else ''}"
        output_file = output_dir / f"{filepath.stem}_session_{i:03d}.md"
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(session_content)
    
    print(f"{filepath.name}: {len(sessions_list)}개 세션 추출 완료 (처음 20개 저장됨)")
    return sessions_list

if __name__ == '__main__':
    files = [
        Path('cursor_Jason_chat 03_.md'),
        Path('cursor_Jason_chat 02_.md'),
        Path('cursor_Jason_chat 01_.md')
    ]
    
    for filepath in files:
        if filepath.exists():
            extract_sessions(filepath, 'extracted_sessions')
