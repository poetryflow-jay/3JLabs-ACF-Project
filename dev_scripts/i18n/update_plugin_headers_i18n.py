#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 20] 플러그인 헤더 및 다국어 번역 업데이트 스크립트

22개 언어에 대한 플러그인 헤더 번역 및 PO 파일 업데이트
"""

import os
import re
from pathlib import Path
from typing import Dict, List

# 22개 언어 정의
LANGUAGES = {
    'ko_KR': {
        'name': '한국어',
        'plugin_name': 'ACF CSS - 고급 커스텀 폰트 & 색상 & 스타일 설정 관리자',
        'description': 'WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.',
        'author': '3J 랩스 (제이×제니×제이슨 연구소)',
    },
    'en_US': {
        'name': 'English (US)',
        'plugin_name': 'ACF CSS - Advanced Custom Fonts & Colors & Styles Setting Manager',
        'description': 'A comprehensive style management plugin that centrally and consistently manages all style elements of your WordPress website, including color palettes, typography, buttons, and forms. The Free version provides basic style management features for maintaining brand consistency and efficient design system operation. Install the Pro version plugin alongside to access Basic, Premium, and Unlimited features. Fully integrated with WordPress Customizer for intuitive style management with real-time preview.',
        'author': '3J Labs (Jay × Jenny × Jason Research Lab)',
    },
    'en_GB': {
        'name': 'English (UK)',
        'plugin_name': 'ACF CSS - Advanced Custom Fonts & Colours & Styles Setting Manager',
        'description': 'A comprehensive style management plugin that centrally and consistently manages all style elements of your WordPress website, including colour palettes, typography, buttons, and forms. The Free version provides basic style management features for maintaining brand consistency and efficient design system operation. Install the Pro version plugin alongside to access Basic, Premium, and Unlimited features. Fully integrated with WordPress Customizer for intuitive style management with real-time preview.',
        'author': '3J Labs (Jay × Jenny × Jason Research Lab)',
    },
    'zh_CN': {
        'name': '中文 (简体)',
        'plugin_name': 'ACF CSS - 高级自定义字体、颜色和样式设置管理器',
        'description': '一个全面的样式管理插件，可集中一致地管理WordPress网站的所有样式元素，包括调色板、排版、按钮和表单。免费版本提供基本的样式管理功能，用于维护品牌一致性和高效的设计系统运营。同时安装Pro版本插件可访问Basic、Premium和Unlimited功能。与WordPress Customizer完全集成，提供实时预览的直观样式管理。',
        'author': '3J实验室 (杰伊×珍妮×杰森研究实验室)',
    },
    'zh_TW': {
        'name': '中文 (繁體)',
        'plugin_name': 'ACF CSS - 進階自訂字體、顏色與樣式設定管理員',
        'description': '一個全面的樣式管理外掛，可集中一致地管理WordPress網站的所有樣式元素，包括調色板、排版、按鈕和表單。免費版本提供基本的樣式管理功能，用於維護品牌一致性和高效的設計系統運營。同時安裝Pro版本外掛可存取Basic、Premium和Unlimited功能。與WordPress Customizer完全整合，提供即時預覽的直觀樣式管理。',
        'author': '3J實驗室 (傑伊×珍妮×傑森研究實驗室)',
    },
    'zh_HK': {
        'name': '中文 (香港)',
        'plugin_name': 'ACF CSS - 進階自訂字體、顏色與樣式設定管理員',
        'description': '一個全面的樣式管理外掛，可集中一致地管理WordPress網站的所有樣式元素，包括調色板、排版、按鈕和表單。免費版本提供基本的樣式管理功能，用於維護品牌一致性和高效的設計系統運營。同時安裝Pro版本外掛可存取Basic、Premium和Unlimited功能。與WordPress Customizer完全整合，提供即時預覽的直觀樣式管理。',
        'author': '3J實驗室 (傑伊×珍妮×傑森研究實驗室)',
    },
    'ja': {
        'name': '日本語',
        'plugin_name': 'ACF CSS - 高度なカスタムフォント・カラー・スタイル設定マネージャー',
        'description': 'WordPressウェブサイトのすべてのスタイル要素（カラーパレット、タイポグラフィ、ボタン、フォーム）を中央で一貫して管理する包括的なスタイル管理プラグインです。無料版は基本的なスタイル管理機能を提供し、ブランドの一貫性を維持し、デザインシステムを効率的に運用できます。Pro版プラグインを併用すると、Basic、Premium、Unlimited機能にアクセスできます。WordPress Customizerと完全に統合され、リアルタイムプレビューとともに直感的なスタイル管理が可能です。',
        'author': '3Jラボ (ジェイ×ジェニー×ジェイソン研究所)',
    },
    'es_ES': {
        'name': 'Español (España)',
        'plugin_name': 'ACF CSS - Administrador Avanzado de Fuentes, Colores y Estilos Personalizados',
        'description': 'Un plugin completo de gestión de estilos que gestiona de forma centralizada y consistente todos los elementos de estilo de su sitio web WordPress, incluyendo paletas de colores, tipografía, botones y formularios. La versión gratuita proporciona funciones básicas de gestión de estilos para mantener la consistencia de la marca y operar el sistema de diseño de manera eficiente. Instale el plugin de la versión Pro junto con él para acceder a las funciones Basic, Premium y Unlimited. Totalmente integrado con WordPress Customizer para una gestión de estilos intuitiva con vista previa en tiempo real.',
        'author': '3J Labs (Laboratorio de Investigación Jay × Jenny × Jason)',
    },
    'pt_BR': {
        'name': 'Português (Brasil)',
        'plugin_name': 'ACF CSS - Gerenciador Avançado de Fontes, Cores e Estilos Personalizados',
        'description': 'Um plugin completo de gerenciamento de estilos que gerencia de forma centralizada e consistente todos os elementos de estilo do seu site WordPress, incluindo paletas de cores, tipografia, botões e formulários. A versão gratuita fornece recursos básicos de gerenciamento de estilos para manter a consistência da marca e operar o sistema de design de forma eficiente. Instale o plugin da versão Pro junto com ele para acessar os recursos Basic, Premium e Unlimited. Totalmente integrado com o WordPress Customizer para gerenciamento intuitivo de estilos com visualização em tempo real.',
        'author': '3J Labs (Laboratório de Pesquisa Jay × Jenny × Jason)',
    },
    'fr_FR': {
        'name': 'Français (France)',
        'plugin_name': 'ACF CSS - Gestionnaire Avancé de Polices, Couleurs et Styles Personnalisés',
        'description': 'Un plugin complet de gestion de styles qui gère de manière centralisée et cohérente tous les éléments de style de votre site WordPress, y compris les palettes de couleurs, la typographie, les boutons et les formulaires. La version gratuite fournit des fonctionnalités de base de gestion de styles pour maintenir la cohérence de la marque et exploiter efficacement le système de conception. Installez le plugin de la version Pro en même temps pour accéder aux fonctionnalités Basic, Premium et Unlimited. Entièrement intégré avec WordPress Customizer pour une gestion intuitive des styles avec aperçu en temps réel.',
        'author': '3J Labs (Laboratoire de Recherche Jay × Jenny × Jason)',
    },
    'fr_CA': {
        'name': 'Français (Canada)',
        'plugin_name': 'ACF CSS - Gestionnaire Avancé de Polices, Couleurs et Styles Personnalisés',
        'description': 'Un plugin complet de gestion de styles qui gère de manière centralisée et cohérente tous les éléments de style de votre site WordPress, y compris les palettes de couleurs, la typographie, les boutons et les formulaires. La version gratuite fournit des fonctionnalités de base de gestion de styles pour maintenir la cohérence de la marque et exploiter efficacement le système de conception. Installez le plugin de la version Pro en même temps pour accéder aux fonctionnalités Basic, Premium et Unlimited. Entièrement intégré avec WordPress Customizer pour une gestion intuitive des styles avec aperçu en temps réel.',
        'author': '3J Labs (Laboratoire de Recherche Jay × Jenny × Jason)',
    },
    'de_DE': {
        'name': 'Deutsch (Deutschland)',
        'plugin_name': 'ACF CSS - Erweiterter Manager für Benutzerdefinierte Schriftarten, Farben und Stile',
        'description': 'Ein umfassendes Stilverwaltungs-Plugin, das alle Stilelemente Ihrer WordPress-Website zentral und konsistent verwaltet, einschließlich Farbpaletten, Typografie, Schaltflächen und Formularen. Die kostenlose Version bietet grundlegende Stilverwaltungsfunktionen zur Aufrechterhaltung der Markenkonsistenz und zum effizienten Betrieb des Designsystems. Installieren Sie das Pro-Version-Plugin parallel, um auf Basic-, Premium- und Unlimited-Funktionen zuzugreifen. Vollständig in WordPress Customizer integriert für intuitive Stilverwaltung mit Echtzeitvorschau.',
        'author': '3J Labs (Jay × Jenny × Jason Forschungsinstitut)',
    },
    'de_CH': {
        'name': 'Deutsch (Schweiz)',
        'plugin_name': 'ACF CSS - Erweiterter Manager für Benutzerdefinierte Schriftarten, Farben und Stile',
        'description': 'Ein umfassendes Stilverwaltungs-Plugin, das alle Stilelemente Ihrer WordPress-Website zentral und konsistent verwaltet, einschließlich Farbpaletten, Typografie, Schaltflächen und Formularen. Die kostenlose Version bietet grundlegende Stilverwaltungsfunktionen zur Aufrechterhaltung der Markenkonsistenz und zum effizienten Betrieb des Designsystems. Installieren Sie das Pro-Version-Plugin parallel, um auf Basic-, Premium- und Unlimited-Funktionen zuzugreifen. Vollständig in WordPress Customizer integriert für intuitive Stilverwaltung mit Echtzeitvorschau.',
        'author': '3J Labs (Jay × Jenny × Jason Forschungsinstitut)',
    },
    'nl_NL': {
        'name': 'Nederlands (Nederland)',
        'plugin_name': 'ACF CSS - Geavanceerde Aangepaste Lettertypen, Kleuren en Stijlen Beheerder',
        'description': 'Een uitgebreide stijlbeheerplugin die alle stylelementen van uw WordPress-website centraal en consistent beheert, inclusief kleurenpaletten, typografie, knoppen en formulieren. De gratis versie biedt basisfuncties voor stijlbeheer om merkkonsistentie te behouden en het ontwerpsysteem efficiënt te bedienen. Installeer de Pro-versie plugin naast elkaar om toegang te krijgen tot Basic-, Premium- en Unlimited-functies. Volledig geïntegreerd met WordPress Customizer voor intuïtief stijlbeheer met real-time preview.',
        'author': '3J Labs (Jay × Jenny × Jason Onderzoekslaboratorium)',
    },
    'nl_BE': {
        'name': 'Nederlands (België)',
        'plugin_name': 'ACF CSS - Geavanceerde Aangepaste Lettertypen, Kleuren en Stijlen Beheerder',
        'description': 'Een uitgebreide stijlbeheerplugin die alle stylelementen van uw WordPress-website centraal en consistent beheert, inclusief kleurenpaletten, typografie, knoppen en formulieren. De gratis versie biedt basisfuncties voor stijlbeheer om merkkonsistentie te behouden en het ontwerpsysteem efficiënt te bedienen. Installeer de Pro-versie plugin naast elkaar om toegang te krijgen tot Basic-, Premium- en Unlimited-functies. Volledig geïntegreerd met WordPress Customizer voor intuïtief stijlbeheer met real-time preview.',
        'author': '3J Labs (Jay × Jenny × Jason Onderzoekslaboratorium)',
    },
    'it_IT': {
        'name': 'Italiano',
        'plugin_name': 'ACF CSS - Gestore Avanzato di Font, Colori e Stili Personalizzati',
        'description': 'Un plugin completo di gestione degli stili che gestisce in modo centralizzato e coerente tutti gli elementi di stile del tuo sito WordPress, inclusi palette di colori, tipografia, pulsanti e moduli. La versione gratuita fornisce funzionalità di base di gestione degli stili per mantenere la coerenza del brand e operare il sistema di design in modo efficiente. Installa il plugin della versione Pro insieme per accedere alle funzionalità Basic, Premium e Unlimited. Completamente integrato con WordPress Customizer per una gestione intuitiva degli stili con anteprima in tempo reale.',
        'author': '3J Labs (Laboratorio di Ricerca Jay × Jenny × Jason)',
    },
    'vi': {
        'name': 'Tiếng Việt',
        'plugin_name': 'ACF CSS - Trình Quản Lý Phông Chữ, Màu Sắc và Kiểu Dáng Tùy Chỉnh Nâng Cao',
        'description': 'Một plugin quản lý kiểu dáng toàn diện quản lý tập trung và nhất quán tất cả các yếu tố kiểu dáng của trang web WordPress của bạn, bao gồm bảng màu, kiểu chữ, nút và biểu mẫu. Phiên bản miễn phí cung cấp các tính năng quản lý kiểu dáng cơ bản để duy trì tính nhất quán của thương hiệu và vận hành hệ thống thiết kế hiệu quả. Cài đặt plugin phiên bản Pro cùng với nó để truy cập các tính năng Basic, Premium và Unlimited. Tích hợp hoàn toàn với WordPress Customizer để quản lý kiểu dáng trực quan với xem trước thời gian thực.',
        'author': '3J Labs (Phòng Nghiên Cứu Jay × Jenny × Jason)',
    },
    'hi_IN': {
        'name': 'हिन्दी',
        'plugin_name': 'ACF CSS - उन्नत कस्टम फ़ॉन्ट, रंग और स्टाइल सेटिंग प्रबंधक',
        'description': 'एक व्यापक स्टाइल प्रबंधन प्लगइन जो आपकी WordPress वेबसाइट के सभी स्टाइल तत्वों को केंद्रीय रूप से और लगातार प्रबंधित करता है, जिसमें रंग पैलेट, टाइपोग्राफी, बटन और फॉर्म शामिल हैं। मुफ्त संस्करण ब्रांड स्थिरता बनाए रखने और डिज़ाइन सिस्टम को कुशलतापूर्वक संचालित करने के लिए बुनियादी स्टाइल प्रबंधन सुविधाएं प्रदान करता है। Basic, Premium और Unlimited सुविधाओं तक पहुंचने के लिए Pro संस्करण प्लगइन को साथ में स्थापित करें। वास्तविक समय पूर्वावलोकन के साथ सहज स्टाइल प्रबंधन के लिए WordPress Customizer के साथ पूरी तरह से एकीकृत।',
        'author': '3J लैब्स (जे × जेनी × जेसन अनुसंधान प्रयोगशाला)',
    },
    'th': {
        'name': 'ไทย',
        'plugin_name': 'ACF CSS - ตัวจัดการฟอนต์ สี และสไตล์ที่กำหนดเองขั้นสูง',
        'description': 'ปลั๊กอินจัดการสไตล์ที่ครอบคลุมซึ่งจัดการองค์ประกอบสไตล์ทั้งหมดของเว็บไซต์ WordPress ของคุณอย่างเป็นศูนย์กลางและสม่ำเสมอ รวมถึงจานสี การจัดรูปแบบตัวอักษร ปุ่ม และแบบฟอร์ม เวอร์ชันฟรีให้คุณสมบัติการจัดการสไตล์พื้นฐานเพื่อรักษาความสอดคล้องของแบรนด์และดำเนินการระบบการออกแบบอย่างมีประสิทธิภาพ ติดตั้งปลั๊กอินเวอร์ชัน Pro พร้อมกันเพื่อเข้าถึงคุณสมบัติ Basic, Premium และ Unlimited รวมเข้ากับ WordPress Customizer อย่างสมบูรณ์สำหรับการจัดการสไตล์ที่ใช้งานง่ายพร้อมตัวอย่างแบบเรียลไทม์',
        'author': '3J Labs (ห้องปฏิบัติการวิจัย Jay × Jenny × Jason)',
    },
    'tr_TR': {
        'name': 'Türkçe',
        'plugin_name': 'ACF CSS - Gelişmiş Özel Yazı Tipleri, Renkler ve Stil Ayarları Yöneticisi',
        'description': 'WordPress web sitenizin renk paletleri, tipografi, düğmeler ve formlar dahil olmak üzere tüm stil öğelerini merkezi ve tutarlı bir şekilde yöneten kapsamlı bir stil yönetim eklentisi. Ücretsiz sürüm, marka tutarlılığını korumak ve tasarım sistemini verimli bir şekilde çalıştırmak için temel stil yönetim özellikleri sağlar. Basic, Premium ve Unlimited özelliklerine erişmek için Pro sürüm eklentisini yan yana yükleyin. Gerçek zamanlı önizleme ile sezgisel stil yönetimi için WordPress Customizer ile tamamen entegre edilmiştir.',
        'author': '3J Labs (Jay × Jenny × Jason Araştırma Laboratuvarı)',
    },
    'ru_RU': {
        'name': 'Русский',
        'plugin_name': 'ACF CSS - Продвинутый Менеджер Настроек Пользовательских Шрифтов, Цветов и Стилей',
        'description': 'Комплексный плагин управления стилями, который централизованно и последовательно управляет всеми элементами стиля вашего сайта WordPress, включая цветовые палитры, типографику, кнопки и формы. Бесплатная версия предоставляет базовые функции управления стилями для поддержания согласованности бренда и эффективной работы системы дизайна. Установите плагин версии Pro вместе с ним, чтобы получить доступ к функциям Basic, Premium и Unlimited. Полностью интегрирован с WordPress Customizer для интуитивного управления стилями с предварительным просмотром в реальном времени.',
        'author': '3J Labs (Исследовательская лаборатория Jay × Jenny × Jason)',
    },
    'uk': {
        'name': 'Українська',
        'plugin_name': 'ACF CSS - Розширений Менеджер Налаштувань Користувацьких Шрифтів, Кольорів та Стилів',
        'description': 'Комплексний плагін управління стилями, який централізовано та послідовно керує всіма елементами стилю вашого сайту WordPress, включаючи кольорові палітри, типографіку, кнопки та форми. Безкоштовна версія надає базові функції управління стилями для підтримки узгодженості бренду та ефективної роботи системи дизайну. Встановіть плагін версії Pro разом з ним, щоб отримати доступ до функцій Basic, Premium та Unlimited. Повністю інтегрований з WordPress Customizer для інтуїтивного управління стилями з попереднім переглядом у реальному часі.',
        'author': '3J Labs (Дослідницька лабораторія Jay × Jenny × Jason)',
    },
}

def update_po_file(po_path: Path, lang_code: str, lang_data: Dict) -> bool:
    """PO 파일 업데이트"""
    if not po_path.exists():
        print(f"⚠️ PO 파일이 없습니다: {po_path}")
        return False
    
    try:
        with open(po_path, 'r', encoding='utf-8') as f:
            lines = f.readlines()
        
        updated_lines = []
        i = 0
        
        while i < len(lines):
            line = lines[i]
            
            # Plugin Name 찾기 및 업데이트
            if line.strip() == '#. Plugin Name of the plugin':
                updated_lines.append(line)
                i += 1
                # 다음 줄들 건너뛰기 (#: 로 시작하는 줄)
                while i < len(lines) and lines[i].startswith('#'):
                    updated_lines.append(lines[i])
                    i += 1
                # msgid 줄
                if i < len(lines) and lines[i].startswith('msgid'):
                    updated_lines.append(lines[i])
                    i += 1
                # msgstr 줄 찾기 및 교체
                if i < len(lines) and lines[i].startswith('msgstr'):
                    updated_lines.append(f'msgstr "{lang_data["plugin_name"]}"\n')
                    i += 1
                else:
                    updated_lines.append(line)
                    i += 1
                continue
            
            # Description 찾기 및 업데이트
            if line.strip() == '#. Description of the plugin':
                updated_lines.append(line)
                i += 1
                # 다음 줄들 건너뛰기 (#: 로 시작하는 줄)
                while i < len(lines) and lines[i].startswith('#'):
                    updated_lines.append(lines[i])
                    i += 1
                # msgid 줄들 (여러 줄일 수 있음)
                msgid_lines = []
                while i < len(lines) and (lines[i].startswith('msgid') or (lines[i].startswith('"') and not lines[i].startswith('msgstr'))):
                    msgid_lines.append(lines[i])
                    i += 1
                # msgstr 줄 찾기 및 교체
                if i < len(lines) and lines[i].startswith('msgstr'):
                    # 여러 줄 msgstr 처리
                    msgstr_lines = []
                    msgstr_lines.append(lines[i])
                    i += 1
                    while i < len(lines) and lines[i].startswith('"'):
                        msgstr_lines.append(lines[i])
                        i += 1
                    # msgstr 교체 (여러 줄로 나눠서)
                    desc = lang_data["description"]
                    # 긴 설명은 여러 줄로 나눔
                    if len(desc) > 77:  # PO 파일 한 줄 최대 길이 고려
                        parts = []
                        current = desc
                        while len(current) > 77:
                            parts.append(current[:77])
                            current = current[77:]
                        if current:
                            parts.append(current)
                        updated_lines.append('msgstr ""\n')
                        for part in parts:
                            updated_lines.append(f'"{part}"\n')
                    else:
                        updated_lines.append(f'msgstr "{desc}"\n')
                else:
                    updated_lines.extend(msgid_lines)
                    updated_lines.append(line)
                    i += 1
                continue
            
            # Author 찾기 및 업데이트
            if line.strip() == '#. Author of the plugin':
                updated_lines.append(line)
                i += 1
                # 다음 줄들 건너뛰기 (#: 로 시작하는 줄)
                while i < len(lines) and lines[i].startswith('#'):
                    updated_lines.append(lines[i])
                    i += 1
                # msgid 줄
                if i < len(lines) and lines[i].startswith('msgid'):
                    updated_lines.append(lines[i])
                    i += 1
                # msgstr 줄 찾기 및 교체
                if i < len(lines) and lines[i].startswith('msgstr'):
                    updated_lines.append(f'msgstr "{lang_data["author"]}"\n')
                    i += 1
                else:
                    updated_lines.append(line)
                    i += 1
                continue
            
            updated_lines.append(line)
            i += 1
        
        with open(po_path, 'w', encoding='utf-8') as f:
            f.writelines(updated_lines)
        
        return True
    except Exception as e:
        print(f"❌ PO 파일 업데이트 실패 ({lang_code}): {e}")
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
    print("Phase 20: 플러그인 헤더 다국어 번역 업데이트")
    print("=" * 60)
    print()
    
    success_count = 0
    fail_count = 0
    
    for lang_code, lang_data in LANGUAGES.items():
        po_filename = f'acf-css-really-simple-style-management-center-{lang_code}.po'
        po_path = languages_path / po_filename
        
        print(f"📝 [{lang_code}] {lang_data['name']} 업데이트 중...")
        
        if update_po_file(po_path, lang_code, lang_data):
            print(f"   ✅ 완료: {po_filename}")
            success_count += 1
        else:
            print(f"   ❌ 실패: {po_filename}")
            fail_count += 1
    
    print()
    print("=" * 60)
    print(f"✅ 완료: {success_count}개")
    if fail_count > 0:
        print(f"❌ 실패: {fail_count}개")
    print("=" * 60)
    print()
    print("📋 다음 단계:")
    print("  1. PO 파일을 MO 파일로 컴파일 (msgfmt 또는 WP-CLI)")
    print("  2. 플러그인 재빌드")
    print("  3. 테스트")

if __name__ == '__main__':
    main()
