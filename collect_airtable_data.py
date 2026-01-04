#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Airtable 데이터 수집 스크립트
Playwright를 사용하여 동적 콘텐츠 로드 및 데이터 추출
"""

import json
import csv
import re
from typing import List, Dict, Optional
from datetime import datetime

def parse_airtable_data_from_text(text: str) -> List[Dict]:
    """텍스트에서 Airtable 데이터 파싱"""
    records = []
    lines = [line.strip() for line in text.split('\n') if line.strip()]
    
    i = 0
    current_record = {}
    
    while i < len(lines):
        line = lines[i]
        
        # 새 레코드 시작 (이름으로 시작하는 패턴)
        if re.match(r'^[A-Z][a-zA-Z\s\-_()]+$', line) and 3 < len(line) < 100:
            if current_record and current_record.get('name'):
                records.append(current_record.copy())
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
        
        # 가중치 추출
        weight_match = re.search(r'\b([1-9]|10)\b', line)
        if weight_match and current_record and current_record.get('weight') is None:
            try:
                weight = int(weight_match.group(1))
                if 1 <= weight <= 10:
                    current_record['weight'] = weight
            except:
                pass
        
        # Impact 패턴
        impact_keywords = ['high', 'medium', 'low', 'positive', 'negative', 'significant']
        if any(kw in line.lower() for kw in impact_keywords) and not current_record.get('impact'):
            current_record['impact'] = line
        
        # Module Name
        if re.search(r'(Module|Component|System|Feature)', line, re.IGNORECASE) and not current_record.get('module_name'):
            current_record['module_name'] = line
        
        # SEO Factor
        seo_keywords = ['technical', 'content', 'link', 'user', 'mobile', 'social', 'local']
        if any(kw in line.lower() for kw in seo_keywords) and not current_record.get('seo_factor'):
            current_record['seo_factor'] = line
        
        # Explanation (긴 텍스트)
        if len(line) > 50:
            if not current_record.get('explanation'):
                current_record['explanation'] = line
            else:
                current_record['explanation'] += ' ' + line
        
        i += 1
    
    if current_record and current_record.get('name'):
        records.append(current_record)
    
    return records

def categorize_seo_factor(seo_factor: str) -> str:
    """SEO Factor 카테고리화"""
    if not seo_factor:
        return 'Other'
    
    seo_lower = seo_factor.lower()
    
    categories = {
        'Technical SEO': ['technical', 'speed', 'performance', 'mobile', 'https', 'ssl', 'server', 'hosting', 'caching'],
        'Content SEO': ['content', 'keyword', 'title', 'meta', 'text', 'readability', 'freshness'],
        'Link SEO': ['link', 'backlink', 'domain', 'authority', 'pagerank', 'internal'],
        'User Experience': ['user', 'engagement', 'click', 'dwell', 'bounce', 'ctr', 'experience'],
        'Social Signals': ['social', 'brand', 'media', 'share', 'facebook', 'twitter'],
        'Local SEO': ['local', 'geographic', 'location', 'business'],
        'International SEO': ['international', 'multilingual', 'language', 'hreflang']
    }
    
    for category, keywords in categories.items():
        if any(kw in seo_lower for kw in keywords):
            return category
    
    return 'Other'

def save_to_csv(records: List[Dict], filename: str = 'airtable_seo_data.csv'):
    """CSV로 저장"""
    if not records:
        return
    
    # 카테고리 및 가중치로 정렬
    for record in records:
        record['category'] = categorize_seo_factor(record.get('seo_factor', ''))
    
    sorted_records = sorted(records, key=lambda x: (
        x.get('category', 'Other'),
        -(x.get('weight') or 0)
    ))
    
    fieldnames = ['name', 'module_name', 'category', 'seo_factor', 'weight', 'impact', 'explanation']
    
    with open(filename, 'w', newline='', encoding='utf-8-sig') as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        for record in sorted_records:
            writer.writerow({
                'name': record.get('name', ''),
                'module_name': record.get('module_name', ''),
                'category': record.get('category', 'Other'),
                'seo_factor': record.get('seo_factor', ''),
                'weight': record.get('weight', ''),
                'impact': record.get('impact', ''),
                'explanation': record.get('explanation', '')
            })
    
    print(f"CSV 저장 완료: {filename} ({len(sorted_records)}개 레코드)")

if __name__ == "__main__":
    print("Airtable 데이터 파서 준비 완료")
    print("브라우저에서 추출한 텍스트를 이 스크립트로 파싱하세요")
