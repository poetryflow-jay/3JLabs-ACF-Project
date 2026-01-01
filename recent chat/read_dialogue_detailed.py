#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
대화만 추출해서 상세히 읽기 (특히 File 01은 더 자세히)
"""
import re
import sys
sys.stdout.reconfigure(encoding='utf-8')

def clean_text(text):
    """텍스트 정리"""
    # 코드 블록 제거
    text = re.sub(r'```[\s\S]*?```', '', text)
    # 인라인 코드 제거
    text = re.sub(r'`[^`]+`', '', text)
    # 연속 공백 정리
    text = re.sub(r'\s+', ' ', text)
    # 앞뒤 공백 제거
    return text.strip()

def extract_detailed_dialogue(filepath, max_length=None):
    """상세 대화 추출"""
    print(f"\n{'='*90}")
    print(f"{filepath}")
    print('='*90)
    
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # 파일 제목 추출
    title_match = re.search(r'^# (.+)$', content, re.MULTILINE)
    title = title_match.group(1) if title_match else "제목 없음"
    print(f"제목: {title}\n")
    
    # 대화 세션 추출 (--- 로 구분된 블록)
    sessions = []
    pattern = r'^---\s*\n\*\*(User|Cursor)\*\*\s*\n\n(.*?)(?=\n---|\Z)'
    matches = re.finditer(pattern, content, re.MULTILINE | re.DOTALL)
    
    current_session = None
    for match in matches:
        speaker = match.group(1)
        message = match.group(2)
        
        if speaker == 'User':
            if current_session:
                sessions.append(current_session)
            current_session = {'user': clean_text(message), 'cursor': ''}
        elif speaker == 'Cursor' and current_session:
            current_session['cursor'] = clean_text(message)
    
    if current_session:
        sessions.append(current_session)
    
    print(f"총 대화 세션 수: {len(sessions)}\n")
    
    # 대화 출력 (File 01은 전체, 다른 파일은 요약)
    display_count = len(sessions) if max_length is None else min(max_length, len(sessions))
    
    for i, session in enumerate(sessions[:display_count], 1):
        print(f"\n{'─'*90}")
        print(f"[세션 {i}/{len(sessions)}]")
        print(f"{'─'*90}")
        print(f"👤 User:")
        user_msg = session['user'][:800] if len(session['user']) > 800 else session['user']
        print(f"   {user_msg}")
        if len(session['user']) > 800:
            print(f"   ... (총 {len(session['user'])}자, {len(session['user'])-800}자 생략)")
        
        print(f"\n🤖 Cursor (Jason):")
        cursor_msg = session['cursor'][:1200] if len(session['cursor']) > 1200 else session['cursor']
        print(f"   {cursor_msg}")
        if len(session['cursor']) > 1200:
            print(f"   ... (총 {len(session['cursor'])}자, {len(session['cursor'])-1200}자 생략)")
        print()
    
    return sessions

if __name__ == '__main__':
    # File 03: 요약만 (처음 15개 세션)
    print("\n" + "█"*90)
    print("FILE 03 분석 (오래된 대화 - 요약)")
    print("█"*90)
    sessions_03 = extract_detailed_dialogue('cursor_Jason_chat 03_.md', max_length=15)
    
    # File 02: 중간 정도 (처음 20개 세션)
    print("\n\n" + "█"*90)
    print("FILE 02 분석 (중간 대화)")
    print("█"*90)
    sessions_02 = extract_detailed_dialogue('cursor_Jason_chat 02_.md', max_length=20)
    
    # File 01: 최근 대화 - 전체 상세히
    print("\n\n" + "█"*90)
    print("FILE 01 분석 (최근 대화 - 전체 상세)")
    print("█"*90)
    sessions_01 = extract_detailed_dialogue('cursor_Jason_chat 01_.md', max_length=None)  # 전체
    
    print("\n\n" + "="*90)
    print("전체 분석 완료!")
    print(f"File 03: {len(sessions_03)}개 세션")
    print(f"File 02: {len(sessions_02)}개 세션")
    print(f"File 01: {len(sessions_01)}개 세션")
    print("="*90)
