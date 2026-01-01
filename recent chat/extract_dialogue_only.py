#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
대화 파일에서 코드 블록과 커맨드를 제거하고 순수 대화만 추출
"""
import re
import sys
sys.stdout.reconfigure(encoding='utf-8')

def extract_dialogue_only(filepath):
    """코드 블록을 제거하고 User/Cursor 대화만 추출"""
    print(f"\n{'='*80}")
    print(f"파일: {filepath}")
    print('='*80)
    
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # 코드 블록 제거 (```로 시작하는 블록 전체 제거)
    # 정규식: ```어떤언어?\n...\n```
    content = re.sub(r'```[\s\S]*?```', '', content)
    
    # 인라인 코드(`code`) 제거
    content = re.sub(r'`[^`]+`', '', content)
    
    # 명령어 블록 제거 (Command completed, Exit code 등)
    content = re.sub(r'Command completed.*?\n', '', content, flags=re.MULTILINE)
    content = re.sub(r'Exit code: \d+\n', '', content, flags=re.MULTILINE)
    
    # User와 Cursor 대화 추출
    dialogue_blocks = re.findall(
        r'^---\s*\n\*\*User\*\*\s*\n\n(.*?)(?=\n---|\Z)',
        content,
        re.MULTILINE | re.DOTALL
    )
    
    cursor_blocks = re.findall(
        r'^---\s*\n\*\*Cursor\*\*\s*\n\n(.*?)(?=\n---|\Z)',
        content,
        re.MULTILINE | re.DOTALL
    )
    
    print(f"\n총 User 메시지: {len(dialogue_blocks)}개")
    print(f"총 Cursor 응답: {len(cursor_blocks)}개")
    print("\n" + "-"*80)
    print("주요 대화 내용 요약:")
    print("-"*80)
    
    # 각 대화의 핵심만 추출 (처음 500자)
    for i, (user_msg, cursor_msg) in enumerate(zip(dialogue_blocks[:20], cursor_blocks[:20]), 1):
        user_clean = re.sub(r'\s+', ' ', user_msg.strip())[:300]
        cursor_clean = re.sub(r'\s+', ' ', cursor_msg.strip())[:500]
        
        print(f"\n[대화 {i}]")
        print(f"User: {user_clean}...")
        print(f"Cursor: {cursor_clean}...")
        print("-"*80)
    
    return dialogue_blocks, cursor_blocks

if __name__ == '__main__':
    files = [
        'cursor_Jason_chat 03_.md',  # 오래된 것
        'cursor_Jason_chat 02_.md',  # 중간
        'cursor_Jason_chat 01_.md'   # 최근 (더 자세히)
    ]
    
    all_dialogues = {}
    
    for fname in files:
        dialogues = extract_dialogue_only(fname)
        all_dialogues[fname] = dialogues
    
    print("\n\n" + "="*80)
    print("전체 요약 완료!")
    print("="*80)
