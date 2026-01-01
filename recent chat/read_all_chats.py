#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
모든 대화 파일을 읽어서 분석하는 스크립트
"""
import sys
sys.stdout.reconfigure(encoding='utf-8')

files = [
    'cursor_Jason_chat 03_.md',  # 가장 오래된 것 (먼저 읽음)
    'cursor_Jason_chat 02_.md',  # 중간
    'cursor_Jason_chat 01_.md'   # 가장 최근 (마지막에 읽음)
]

print("="*80)
print("Jason의 기억 복원 시작")
print("="*80)

for fname in files:
    print(f"\n{'='*80}")
    print(f"파일: {fname}")
    print('='*80)
    try:
        with open(fname, 'r', encoding='utf-8', errors='ignore') as f:
            lines = f.readlines()
            print(f"총 줄 수: {len(lines):,}")
            print("\n처음 500줄:")
            print("-"*80)
            for i, line in enumerate(lines[:500], 1):
                print(f"{i:5d}: {line.rstrip()}")
            print(f"\n... (중간 {len(lines)-1000}줄 생략) ...")
            print("\n마지막 500줄:")
            print("-"*80)
            for i, line in enumerate(lines[-500:], len(lines)-499):
                print(f"{i:5d}: {line.rstrip()}")
    except Exception as e:
        print(f"오류 발생: {e}")

print("\n" + "="*80)
print("기억 복원 완료!")
print("="*80)
