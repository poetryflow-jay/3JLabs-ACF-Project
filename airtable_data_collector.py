#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Airtable 데이터 수집 및 파싱 스크립트
웹 페이지에서 직접 데이터를 추출하여 구조화된 형태로 저장
"""

import requests
from bs4 import BeautifulSoup
import json
import csv
import re
import time
from typing import List, Dict, Optional
from datetime import datetime

class AirtableDataCollector:
    """Airtable 데이터 수집기"""
    
    def __init__(self, url: str):
        self.url = url
        self.headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        }
        self.records = []
    
    def fetch_page(self) -> str:
        """페이지 HTML 가져오기"""
        try:
            response = requests.get(self.url, headers=self.headers, timeout=30)
            response.raise_for_status()
            return response.text
        except Exception as e:
            print(f"페이지 가져오기 실패: {e}")
            return ""
    
    def extract_data_from_html(self, html: str) -> List[Dict]:
        """HTML에서 데이터 추출"""
        soup = BeautifulSoup(html, 'html.parser')
        records = []
        
        # Airtable의 데이터는 보통 JavaScript에 포함되어 있음
        # script 태그에서 데이터 찾기
        scripts = soup.find_all('script')
        for script in scripts:
            if script.string:
                # JSON 데이터 패턴 찾기
                json_matches = re.findall(r'\{[^{}]*"fields"[^{}]*\}', script.string)
                for match in json_matches:
                    try:
                        data = json.loads(match)
                        if 'fields' in data:
                            records.append(self.parse_record(data))
                    except:
                        continue
        
        # 테이블 구조에서도 데이터 추출 시도
        tables = soup.find_all('table')
        for table in tables:
            rows = table.find_all('tr')
            for row in rows[1:]:  # 헤더 제외
                cells = row.find_all(['td', 'th'])
                if len(cells) >= 4:
                    record = self.parse_table_row(cells)
                    if record:
                        records.append(record)
        
        return records
    
    def parse_record(self, data: Dict) -> Dict:
        """레코드 파싱"""
        fields = data.get('fields', {})
        return {
            'name': fields.get('Name (s)', ''),
            'module_name': fields.get('ModuleName', ''),
            'impact': fields.get('Impact on Search Ranking', ''),
            'weight': self.extract_weight(fields.get('Estimated Weight (1-10)', '')),
            'seo_factor': fields.get('SEO Factor/Focus Area', ''),
            'explanation': fields.get('Impact on Search Ranking Explanation', '')
        }
    
    def parse_table_row(self, cells: List) -> Optional[Dict]:
        """테이블 행 파싱"""
        if len(cells) < 4:
            return None
        
        texts = [cell.get_text(strip=True) for cell in cells]
        
        return {
            'name': texts[0] if len(texts) > 0 else '',
            'module_name': texts[1] if len(texts) > 1 else '',
            'impact': texts[2] if len(texts) > 2 else '',
            'weight': self.extract_weight(texts[3] if len(texts) > 3 else ''),
            'seo_factor': texts[4] if len(texts) > 4 else '',
            'explanation': texts[5] if len(texts) > 5 else ''
        }
    
    def extract_weight(self, text: str) -> Optional[int]:
        """가중치 추출 (1-10)"""
        if not text:
            return None
        
        # 숫자 패턴 찾기
        match = re.search(r'\b([1-9]|10)\b', str(text))
        if match:
            weight = int(match.group(1))
            if 1 <= weight <= 10:
                return weight
        return None
    
    def categorize_seo_factor(self, seo_factor: str) -> str:
        """SEO Factor 카테고리화"""
        if not seo_factor:
            return 'Other'
        
        seo_lower = seo_factor.lower()
        
        if any(kw in seo_lower for kw in ['technical', 'speed', 'performance', 'mobile', 'https', 'ssl', 'server']):
            return 'Technical SEO'
        elif any(kw in seo_lower for kw in ['content', 'keyword', 'title', 'meta', 'text']):
            return 'Content SEO'
        elif any(kw in seo_lower for kw in ['link', 'backlink', 'domain', 'authority', 'pagerank']):
            return 'Link SEO'
        elif any(kw in seo_lower for kw in ['user', 'engagement', 'click', 'dwell', 'experience']):
            return 'User Experience'
        elif any(kw in seo_lower for kw in ['social', 'brand', 'media']):
            return 'Social Signals'
        elif any(kw in seo_lower for kw in ['local', 'geographic', 'location']):
            return 'Local SEO'
        elif any(kw in seo_lower for kw in ['international', 'multilingual', 'language']):
            return 'International SEO'
        else:
            return 'Other'
    
    def collect_data(self) -> List[Dict]:
        """데이터 수집 메인 함수"""
        print("Airtable 데이터 수집 시작...")
        
        html = self.fetch_page()
        if not html:
            print("HTML을 가져올 수 없습니다.")
            return []
        
        print(f"HTML 길이: {len(html)} 문자")
        
        # HTML에서 데이터 추출
        records = self.extract_data_from_html(html)
        
        # 카테고리 추가
        for record in records:
            record['category'] = self.categorize_seo_factor(record.get('seo_factor', ''))
        
        print(f"수집된 레코드 수: {len(records)}")
        return records
    
    def save_to_csv(self, records: List[Dict], filename: str = 'airtable_seo_data.csv'):
        """CSV 파일로 저장"""
        if not records:
            print("저장할 데이터가 없습니다.")
            return
        
        # 팩터별로 정렬
        sorted_records = sorted(records, key=lambda x: (
            x.get('category', 'Other'),
            -(x.get('weight') or 0)  # 가중치 내림차순
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
        
        print(f"CSV 파일 저장 완료: {filename}")
        print(f"총 {len(sorted_records)}개 레코드 저장됨")
    
    def save_to_json(self, records: List[Dict], filename: str = 'airtable_seo_data.json'):
        """JSON 파일로 저장"""
        if not records:
            print("저장할 데이터가 없습니다.")
            return
        
        # 팩터별로 그룹화
        grouped = {}
        for record in records:
            category = record.get('category', 'Other')
            if category not in grouped:
                grouped[category] = []
            
            # 가중치 내림차순 정렬
            grouped[category].append(record)
        
        for category in grouped:
            grouped[category].sort(key=lambda x: -(x.get('weight') or 0))
        
        data = {
            'metadata': {
                'source': 'Airtable',
                'url': self.url,
                'collected_at': datetime.now().isoformat(),
                'total_records': len(records),
                'categories': list(grouped.keys())
            },
            'data': grouped
        }
        
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        
        print(f"JSON 파일 저장 완료: {filename}")
        print(f"카테고리별 레코드 수:")
        for category, items in grouped.items():
            print(f"  - {category}: {len(items)}개")

if __name__ == "__main__":
    url = "https://airtable.com/applEPk7fCvm7MghM/shrR2eOWItDCSW76O/tblRX4GHpE79ePSdI?viewControls=on"
    
    collector = AirtableDataCollector(url)
    records = collector.collect_data()
    
    if records:
        collector.save_to_csv(records)
        collector.save_to_json(records)
    else:
        print("데이터를 수집할 수 없습니다. 브라우저 자동화가 필요할 수 있습니다.")
