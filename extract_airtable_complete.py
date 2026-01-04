#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Airtable 완전 데이터 추출 스크립트
Playwright를 사용하여 동적 콘텐츠를 로드하고 모든 데이터를 추출
"""

import json
import re
from typing import List, Dict, Optional

def parse_airtable_from_text(text: str) -> List[Dict]:
    """
    Airtable 페이지에서 추출한 텍스트를 파싱
    
    컬럼 구조:
    1. Name (s)
    2. ModuleName
    3. Impact on Search Ranking
    4. Estimated Weight (1-10)
    5. SEO Factor/Focus Area
    6. Impact on Search Ranking Explanation
    """
    
    records = []
    lines = [line.strip() for line in text.split('\n') if line.strip()]
    
    # 컬럼 헤더 찾기
    headers = []
    header_indices = []
    for i, line in enumerate(lines[:20]):
        if any(keyword in line.lower() for keyword in ['name', 'module', 'impact', 'weight', 'seo', 'explanation']):
            headers.append(line)
            header_indices.append(i)
    
    # 데이터 행 파싱
    i = 0
    current_record = {}
    
    while i < len(lines):
        line = lines[i]
        
        # 새 레코드 시작 패턴 (보통 이름으로 시작)
        if re.match(r'^[A-Z][a-zA-Z\s\-_]+$', line) and len(line) > 2 and len(line) < 100:
            # 이전 레코드 저장
            if current_record and 'name' in current_record:
                records.append(current_record.copy())
            
            # 새 레코드 시작
            current_record = {
                'name': line,
                'module_name': '',
                'impact': '',
                'weight': None,
                'seo_factor': '',
                'explanation': ''
            }
            i += 1
            continue
        
        # 가중치 추출 (1-10)
        weight_match = re.search(r'\b([1-9]|10)\b', line)
        if weight_match and current_record and current_record.get('weight') is None:
            try:
                weight = int(weight_match.group(1))
                if 1 <= weight <= 10:
                    current_record['weight'] = weight
            except:
                pass
        
        # Impact 패턴
        impact_keywords = ['high', 'medium', 'low', 'positive', 'negative', 'significant', 'moderate', 'minimal']
        if any(keyword in line.lower() for keyword in impact_keywords):
            if current_record and not current_record.get('impact'):
                current_record['impact'] = line
        
        # Module Name 패턴
        if re.search(r'(Module|Component|Feature|System)', line, re.IGNORECASE):
            if current_record and not current_record.get('module_name'):
                current_record['module_name'] = line
        
        # SEO Factor 패턴
        seo_keywords = ['technical', 'content', 'link', 'user', 'mobile', 'speed', 'security', 'social']
        if any(keyword in line.lower() for keyword in seo_keywords):
            if current_record and not current_record.get('seo_factor'):
                current_record['seo_factor'] = line
        
        # Explanation (긴 텍스트)
        if len(line) > 50:
            if current_record:
                if not current_record.get('explanation'):
                    current_record['explanation'] = line
                else:
                    current_record['explanation'] += ' ' + line
        
        i += 1
    
    # 마지막 레코드 저장
    if current_record and 'name' in current_record:
        records.append(current_record)
    
    return records

def categorize_seo_factors(records: List[Dict]) -> Dict[str, List[Dict]]:
    """SEO 요소를 카테고리별로 분류"""
    
    categories = {
        'Technical SEO': [],
        'Content SEO': [],
        'Link SEO': [],
        'User Experience': [],
        'Mobile SEO': [],
        'Security': [],
        'Social Signals': [],
        'Domain Authority': [],
        'Other': []
    }
    
    for record in records:
        seo_factor = record.get('seo_factor', '').lower()
        name = record.get('name', '').lower()
        
        categorized = False
        
        # Technical SEO
        if any(keyword in seo_factor or keyword in name for keyword in 
               ['speed', 'performance', 'technical', 'https', 'ssl', 'server', 'hosting', 'caching', 'cdn']):
            categories['Technical SEO'].append(record)
            categorized = True
        
        # Content SEO
        if any(keyword in seo_factor or keyword in name for keyword in 
               ['content', 'keyword', 'title', 'meta', 'heading', 'text', 'readability']):
            categories['Content SEO'].append(record)
            categorized = True
        
        # Link SEO
        if any(keyword in seo_factor or keyword in name for keyword in 
               ['link', 'backlink', 'domain', 'authority', 'pagerank']):
            categories['Link SEO'].append(record)
            categorized = True
        
        # User Experience
        if any(keyword in seo_factor or keyword in name for keyword in 
               ['user', 'engagement', 'click', 'dwell', 'bounce', 'ctr']):
            categories['User Experience'].append(record)
            categorized = True
        
        # Mobile SEO
        if any(keyword in seo_factor or keyword in name for keyword in 
               ['mobile', 'responsive', 'touch', 'viewport']):
            categories['Mobile SEO'].append(record)
            categorized = True
        
        # Security
        if any(keyword in seo_factor or keyword in name for keyword in 
               ['security', 'https', 'ssl', 'encryption', 'secure']):
            categories['Security'].append(record)
            categorized = True
        
        # Social Signals
        if any(keyword in seo_factor or keyword in name for keyword in 
               ['social', 'facebook', 'twitter', 'share', 'like']):
            categories['Social Signals'].append(record)
            categorized = True
        
        # Domain Authority
        if any(keyword in seo_factor or keyword in name for keyword in 
               ['domain', 'authority', 'age', 'trust', 'reputation']):
            categories['Domain Authority'].append(record)
            categorized = True
        
        if not categorized:
            categories['Other'].append(record)
    
    return categories

if __name__ == "__main__":
    print("Airtable 데이터 파서 준비 완료")
    print("브라우저에서 추출한 텍스트를 이 스크립트로 파싱하세요")
