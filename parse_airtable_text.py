#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Airtable 텍스트 데이터 파싱 스크립트
브라우저에서 추출한 텍스트를 구조화된 데이터로 변환
"""

import re
import json
from typing import List, Dict, Optional

def parse_airtable_text(text: str) -> List[Dict]:
    """
    Airtable 페이지에서 추출한 텍스트를 파싱하여 구조화된 데이터로 변환
    
    예상 형식:
    - Name (s)
    - ModuleName
    - Impact on Search Ranking
    - Estimated Weight (1-10)
    - SEO Factor/Focus Area
    - Impact on Search Ranking Explanation
    """
    
    lines = text.split('\n')
    records = []
    current_record = {}
    
    # 컬럼 헤더 패턴
    column_patterns = {
        'name': re.compile(r'^(Name|Module|Factor|Element)', re.IGNORECASE),
        'module': re.compile(r'ModuleName|Module', re.IGNORECASE),
        'impact': re.compile(r'Impact on Search Ranking|Impact', re.IGNORECASE),
        'weight': re.compile(r'Estimated Weight|Weight|\(1-10\)', re.IGNORECASE),
        'seo_factor': re.compile(r'SEO Factor|Focus Area', re.IGNORECASE),
        'explanation': re.compile(r'Explanation|Description', re.IGNORECASE)
    }
    
    # 숫자 패턴 (가중치)
    weight_pattern = re.compile(r'\b([1-9]|10)\b')
    
    # 텍스트를 라인별로 처리
    i = 0
    while i < len(lines):
        line = lines[i].strip()
        
        if not line:
            i += 1
            continue
        
        # 새 레코드 시작 패턴 감지
        # 보통 이름이나 모듈명으로 시작
        if re.match(r'^[A-Z][a-zA-Z\s]+$', line) and len(line) > 3:
            # 이전 레코드 저장
            if current_record and 'name' in current_record:
                records.append(current_record.copy())
            
            # 새 레코드 시작
            current_record = {'name': line}
            i += 1
            continue
        
        # 가중치 추출 (1-10)
        weight_match = weight_pattern.search(line)
        if weight_match and 'weight' not in current_record:
            weight = int(weight_match.group(1))
            if 1 <= weight <= 10:
                current_record['weight'] = weight
        
        # 긴 설명 텍스트 (Explanation)
        if len(line) > 50 and 'explanation' not in current_record:
            current_record['explanation'] = line
        elif len(line) > 50 and 'explanation' in current_record:
            current_record['explanation'] += ' ' + line
        
        # Impact 패턴
        if re.search(r'(High|Medium|Low|Positive|Negative)', line, re.IGNORECASE):
            if 'impact' not in current_record:
                current_record['impact'] = line
        
        # SEO Factor 패턴
        if re.search(r'(Technical|Content|Links|User|Mobile|Speed)', line, re.IGNORECASE):
            if 'seo_factor' not in current_record:
                current_record['seo_factor'] = line
        
        i += 1
    
    # 마지막 레코드 저장
    if current_record and 'name' in current_record:
        records.append(current_record)
    
    return records

def extract_from_browser_text():
    """
    브라우저에서 추출한 전체 텍스트를 파싱
    """
    # 실제로는 브라우저에서 추출한 텍스트를 여기에 넣어야 함
    # 일단 샘플 구조 반환
    return []

if __name__ == "__main__":
    print("Airtable 텍스트 파서 준비 완료")
    print("브라우저에서 텍스트를 추출한 후 이 스크립트로 파싱하세요")
