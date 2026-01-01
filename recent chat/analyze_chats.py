#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
대화 파일을 분석하고 구조를 파악하는 스크립트
"""
import re
import sys
from pathlib import Path

def analyze_chat_file(filepath):
    """대화 파일의 구조를 분석"""
    print(f"\n{'='*60}")
    print(f"분석 중: {filepath.name}")
    print('='*60)
    
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
        lines = content.split('\n')
    
    # 메타데이터 추출
    title_match = re.search(r'^# (.+)$', content, re.MULTILINE)
    date_match = re.search(r'_Exported on (.+?) from', content)
    
    print(f"총 줄 수: {len(lines):,}")
    print(f"제목: {title_match.group(1) if title_match else 'N/A'}")
    print(f"내보낸 날짜: {date_match.group(1) if date_match else 'N/A'}")
    
    # 대화 세션 추출
    user_sessions = list(re.finditer(r'^---\s*\n\*\*User\*\*\s*\n\n(.+?)(?=\n---|\Z)', content, re.MULTILINE | re.DOTALL))
    cursor_sessions = list(re.finditer(r'^---\s*\n\*\*Cursor\*\*\s*\n\n(.+?)(?=\n---|\Z)', content, re.MULTILINE | re.DOTALL))
    
    print(f"User 메시지 수: {len(user_sessions)}")
    print(f"Cursor 응답 수: {len(cursor_sessions)}")
    
    # 주요 키워드 추출
    keywords = ['모델', '설치', 'FLUX', 'Gemma', 'Llama', '다운로드', '워크스페이스', '프로젝트', '코드', '에러', '오류', '완료']
    keyword_counts = {}
    content_lower = content.lower()
    for keyword in keywords:
        count = content_lower.count(keyword.lower())
        if count > 0:
            keyword_counts[keyword] = count
    
    print(f"\n주요 키워드 빈도:")
    for keyword, count in sorted(keyword_counts.items(), key=lambda x: x[1], reverse=True)[:10]:
        print(f"  {keyword}: {count}회")
    
    return {
        'total_lines': len(lines),
        'user_sessions': len(user_sessions),
        'cursor_sessions': len(cursor_sessions),
        'content': content
    }

if __name__ == '__main__':
    files = [
        Path('cursor_Jason_chat 03_.md'),
        Path('cursor_Jason_chat 02_.md'),
        Path('cursor_Jason_chat 01_.md')
    ]
    
    results = []
    for filepath in files:
        if filepath.exists():
            result = analyze_chat_file(filepath)
            results.append((filepath.name, result))
        else:
            print(f"파일을 찾을 수 없습니다: {filepath}")
    
    print(f"\n{'='*60}")
    print("전체 요약")
    print('='*60)
    total_lines = sum(r[1]['total_lines'] for r in results)
    total_sessions = sum(r[1]['user_sessions'] for r in results)
    print(f"총 줄 수: {total_lines:,}")
    print(f"총 대화 세션 수: {total_sessions}")
