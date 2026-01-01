/**
 * J&J Tooltips System - v5.0.3
 * 
 * 포괄적인 툴팁 시스템
 * - 다국어 지원
 * - 접근성 고려 (ARIA 속성)
 * - 키보드 네비게이션 지원
 * - 반응형 디자인
 * 
 * @since 5.0.3
 * @version 5.0.3
 */
(function($) {
    'use strict';

    /**
     * 툴팁 매니저
     */
    const Tooltips = {
        // 현재 언어 코드
        currentLocale: 'en_US',
        
        // 툴팁 데이터 (다국어)
        tooltips: {
            // 섹션 레이아웃 관련
            'section-layout-description': {
                ko_KR: '스타일 센터에 표시될 섹션의 순서와 표시 여부를 관리합니다. 드래그 앤 드롭으로 순서를 변경할 수 있습니다.',
                en_US: 'Manage the order and visibility of sections displayed in the Style Center. You can change the order by dragging and dropping.',
                zh_CN: '管理样式中心中显示的部分的顺序和可见性。您可以通过拖放来更改顺序。',
                ja: 'スタイルセンターに表示されるセクションの順序と表示を管理します。ドラッグアンドドロップで順序を変更できます。',
                de_DE: 'Verwalten Sie die Reihenfolge und Sichtbarkeit der im Style Center angezeigten Abschnitte. Sie können die Reihenfolge durch Ziehen und Ablegen ändern.',
                fr_FR: 'Gérez l\'ordre et la visibilité des sections affichées dans le Centre de style. Vous pouvez modifier l\'ordre par glisser-déposer.',
                it_IT: 'Gestisci l\'ordine e la visibilità delle sezioni visualizzate nel Centro stile. Puoi modificare l\'ordine trascinando e rilasciando.',
                es_ES: 'Administra el orden y la visibilidad de las secciones mostradas en el Centro de estilo. Puedes cambiar el orden arrastrando y soltando.',
                pt_PT: 'Gerir a ordem e a visibilidade das secções exibidas no Centro de estilo. Pode alterar a ordem arrastando e largando.',
                es_MX: 'Administra el orden y la visibilidad de las secciones mostradas en el Centro de estilo. Puedes cambiar el orden arrastrando y soltando.',
                pt_BR: 'Gerencie a ordem e a visibilidade das seções exibidas no Centro de estilo. Você pode alterar a ordem arrastando e soltando.',
                tr_TR: 'Stil Merkezinde görüntülenen bölümlerin sırasını ve görünürlüğünü yönetin. Sürükleyip bırakarak sırayı değiştirebilirsiniz.',
                la: 'Ordinem et visibilitatem sectionum in Centro Stili ostensarum administra. Ordinem trahendo et deponendo mutare potes.',
                he_IL: 'נהל את הסדר והנראות של הסעיפים המוצגים במרכז הסגנון. תוכל לשנות את הסדר על ידי גרירה ושחרור.'
            },
            
            'section-enabled': {
                ko_KR: '이 섹션을 스타일 센터에 표시할지 여부를 결정합니다. 비활성화하면 해당 섹션이 완전히 숨겨집니다.',
                en_US: 'Determines whether this section is displayed in the Style Center. When disabled, the section will be completely hidden.',
                zh_CN: '确定此部分是否显示在样式中心。禁用时，该部分将完全隐藏。',
                ja: 'このセクションをスタイルセンターに表示するかどうかを決定します。無効にすると、セクションは完全に非表示になります。',
                de_DE: 'Bestimmt, ob dieser Abschnitt im Style Center angezeigt wird. Wenn deaktiviert, wird der Abschnitt vollständig ausgeblendet.',
                fr_FR: 'Détermine si cette section est affichée dans le Centre de style. Lorsqu\'elle est désactivée, la section sera complètement masquée.',
                it_IT: 'Determina se questa sezione viene visualizzata nel Centro stile. Quando disabilitata, la sezione sarà completamente nascosta.',
                es_ES: 'Determina si esta sección se muestra en el Centro de estilo. Cuando está deshabilitada, la sección estará completamente oculta.',
                pt_PT: 'Determina se esta secção é exibida no Centro de estilo. Quando desativada, a secção ficará completamente oculta.',
                es_MX: 'Determina si esta sección se muestra en el Centro de estilo. Cuando está deshabilitada, la sección estará completamente oculta.',
                pt_BR: 'Determina se esta seção é exibida no Centro de estilo. Quando desativada, a seção ficará completamente oculta.',
                tr_TR: 'Bu bölümün Stil Merkezinde görüntülenip görüntülenmeyeceğini belirler. Devre dışı bırakıldığında, bölüm tamamen gizlenecektir.',
                la: 'Determinat utrum haec sectio in Centro Stili ostendatur. Cum inutilis est, sectio omnino abscondetur.',
                he_IL: 'קובע האם סעיף זה מוצג במרכז הסגנון. כאשר מושבת, הסעיף יהיה מוסתר לחלוטין.'
            },
            
            'tab-enabled': {
                ko_KR: '이 탭을 해당 섹션 내에서 표시할지 여부를 결정합니다. 비활성화하면 탭 버튼과 내용이 모두 숨겨집니다.',
                en_US: 'Determines whether this tab is displayed within the section. When disabled, both the tab button and content will be hidden.',
                zh_CN: '确定此选项卡是否在部分内显示。禁用时，选项卡按钮和内容都将隐藏。',
                ja: 'このタブをセクション内に表示するかどうかを決定します。無効にすると、タブボタンとコンテンツの両方が非表示になります。',
                de_DE: 'Bestimmt, ob dieser Tab im Abschnitt angezeigt wird. Wenn deaktiviert, werden sowohl die Tab-Schaltfläche als auch der Inhalt ausgeblendet.',
                fr_FR: 'Détermine si cet onglet est affiché dans la section. Lorsqu\'il est désactivé, le bouton d\'onglet et le contenu seront masqués.',
                it_IT: 'Determina se questa scheda viene visualizzata all\'interno della sezione. Quando disabilitata, sia il pulsante della scheda che il contenuto saranno nascosti.',
                es_ES: 'Determina si esta pestaña se muestra dentro de la sección. Cuando está deshabilitada, tanto el botón de la pestaña como el contenido estarán ocultos.',
                pt_PT: 'Determina se este separador é exibido dentro da secção. Quando desativado, tanto o botão do separador como o conteúdo ficarão ocultos.',
                es_MX: 'Determina si esta pestaña se muestra dentro de la sección. Cuando está deshabilitada, tanto el botón de la pestaña como el contenido estarán ocultos.',
                pt_BR: 'Determina se esta aba é exibida dentro da seção. Quando desativada, tanto o botão da aba quanto o conteúdo ficarão ocultos.',
                tr_TR: 'Bu sekmenin bölüm içinde görüntülenip görüntülenmeyeceğini belirler. Devre dışı bırakıldığında, hem sekme düğmesi hem de içerik gizlenecektir.',
                la: 'Determinat utrum haec tabula intra sectionem ostendatur. Cum inutilis est, tam tabula quam contentum abscondentur.',
                he_IL: 'קובע האם כרטיסייה זו מוצגת בתוך הסעיף. כאשר מושבת, גם כפתור הכרטיסייה וגם התוכן יהיו מוסתרים.'
            },
            
            // 실험실 탭 관련
            'labs-tab-scanner': {
                ko_KR: 'CSS, HTML, JavaScript 코드를 스캔하여 스타일 가이드에 적용할 수 있는 색상과 폰트를 자동으로 추출합니다.',
                en_US: 'Scans CSS, HTML, and JavaScript code to automatically extract colors and fonts that can be applied to the style guide.',
                zh_CN: '扫描CSS、HTML和JavaScript代码，自动提取可应用于样式指南的颜色和字体。',
                ja: 'CSS、HTML、JavaScriptコードをスキャンして、スタイルガイドに適用できる色とフォントを自動的に抽出します。',
                de_DE: 'Scannt CSS-, HTML- und JavaScript-Code, um automatisch Farben und Schriftarten zu extrahieren, die auf den Style Guide angewendet werden können.',
                fr_FR: 'Analyse le code CSS, HTML et JavaScript pour extraire automatiquement les couleurs et les polices qui peuvent être appliquées au guide de style.',
                it_IT: 'Scansiona il codice CSS, HTML e JavaScript per estrarre automaticamente colori e caratteri che possono essere applicati alla guida di stile.',
                es_ES: 'Escanea código CSS, HTML y JavaScript para extraer automáticamente colores y fuentes que se pueden aplicar a la guía de estilo.',
                pt_PT: 'Digitaliza código CSS, HTML e JavaScript para extrair automaticamente cores e fontes que podem ser aplicadas ao guia de estilo.',
                es_MX: 'Escanea código CSS, HTML y JavaScript para extraer automáticamente colores y fuentes que se pueden aplicar a la guía de estilo.',
                pt_BR: 'Escaneia código CSS, HTML e JavaScript para extrair automaticamente cores e fontes que podem ser aplicadas ao guia de estilo.',
                tr_TR: 'Stil kılavuzuna uygulanabilecek renkleri ve yazı tiplerini otomatik olarak çıkarmak için CSS, HTML ve JavaScript kodunu tarar.',
                la: 'CSS, HTML, JavaScript codicem scrutat ut colores et fontes quae ad ducem stili applicari possunt automatice extrahat.',
                he_IL: 'סורק קוד CSS, HTML ו-JavaScript כדי לחלץ אוטומטית צבעים וגופנים שניתן להחיל על מדריך הסגנון.'
            },
            
            'labs-tab-overrides': {
                ko_KR: '테마나 플러그인의 기본 스타일을 재정의하는 사용자 정의 CSS를 작성할 수 있습니다. 코드 하이라이팅과 자동 완성이 지원됩니다.',
                en_US: 'Write custom CSS to override the default styles of themes or plugins. Code highlighting and auto-completion are supported.',
                zh_CN: '编写自定义CSS以覆盖主题或插件的默认样式。支持代码高亮和自动完成。',
                ja: 'テーマやプラグインのデフォルトスタイルを上書きするカスタムCSSを記述できます。コードハイライトと自動補完がサポートされています。',
                de_DE: 'Schreiben Sie benutzerdefiniertes CSS, um die Standardstile von Themes oder Plugins zu überschreiben. Code-Hervorhebung und Auto-Vervollständigung werden unterstützt.',
                fr_FR: 'Écrivez du CSS personnalisé pour remplacer les styles par défaut des thèmes ou des plugins. La coloration syntaxique et la saisie semi-automatique sont prises en charge.',
                it_IT: 'Scrivi CSS personalizzato per sovrascrivere gli stili predefiniti di temi o plugin. Evidenziazione del codice e completamento automatico sono supportati.',
                es_ES: 'Escribe CSS personalizado para anular los estilos predeterminados de temas o complementos. Se admite resaltado de código y autocompletado.',
                pt_PT: 'Escreva CSS personalizado para substituir os estilos predefinidos de temas ou plugins. Destacamento de código e preenchimento automático são suportados.',
                es_MX: 'Escribe CSS personalizado para anular los estilos predeterminados de temas o complementos. Se admite resaltado de código y autocompletado.',
                pt_BR: 'Escreva CSS personalizado para substituir os estilos padrão de temas ou plugins. Destaque de código e preenchimento automático são suportados.',
                tr_TR: 'Temaların veya eklentilerin varsayılan stillerini geçersiz kılmak için özel CSS yazın. Kod vurgulama ve otomatik tamamlama desteklenir.',
                la: 'Scribe CSS personalisatum ut stilos praedefinitos thematum vel additorum rescindas. Code highlighting et auto-completio sustinentur.',
                he_IL: 'כתוב CSS מותאם אישית כדי לעקוף את סגנונות ברירת המחדל של תבניות או תוספים. הדגשת קוד והשלמה אוטומטית נתמכות.'
            },
            
            'labs-tab-supported-list': {
                ko_KR: '공식적으로 지원되는 테마와 플러그인 목록을 확인할 수 있습니다. 각 어댑터의 지원 기능과 호환성 레벨을 확인하세요.',
                en_US: 'View a list of officially supported themes and plugins. Check the supported features and compatibility level of each adapter.',
                zh_CN: '查看正式支持的主题和插件列表。检查每个适配器的支持功能和兼容性级别。',
                ja: '公式にサポートされているテーマとプラグインのリストを表示できます。各アダプターのサポート機能と互換性レベルを確認してください。',
                de_DE: 'Zeigen Sie eine Liste der offiziell unterstützten Themes und Plugins an. Überprüfen Sie die unterstützten Funktionen und Kompatibilitätsstufe jedes Adapters.',
                fr_FR: 'Affichez une liste des thèmes et plugins officiellement pris en charge. Vérifiez les fonctionnalités prises en charge et le niveau de compatibilité de chaque adaptateur.',
                it_IT: 'Visualizza un elenco di temi e plugin ufficialmente supportati. Controlla le funzionalità supportate e il livello di compatibilità di ciascun adattatore.',
                es_ES: 'Vea una lista de temas y complementos oficialmente compatibles. Verifique las funciones compatibles y el nivel de compatibilidad de cada adaptador.',
                pt_PT: 'Veja uma lista de temas e plugins oficialmente suportados. Verifique as funcionalidades suportadas e o nível de compatibilidade de cada adaptador.',
                es_MX: 'Vea una lista de temas y complementos oficialmente compatibles. Verifique las funciones compatibles y el nivel de compatibilidad de cada adaptador.',
                pt_BR: 'Veja uma lista de temas e plugins oficialmente suportados. Verifique as funcionalidades suportadas e o nível de compatibilidade de cada adaptador.',
                tr_TR: 'Resmi olarak desteklenen temalar ve eklentilerin listesini görüntüleyin. Her adaptörün desteklenen özelliklerini ve uyumluluk seviyesini kontrol edin.',
                la: 'Vide indicem thematum et additorum publice sustentatorum. Controlla functiones sustentatas et gradum compatibilitatis cuiusque adaptatoris.',
                he_IL: 'הצג רשימה של תבניות ותוספים הנתמכים רשמית. בדוק את התכונות הנתמכות ורמת התאימות של כל מתאם.'
            },

            // [Phase 13.2] 색상 관련 툴팁
            'color-palette-brand': {
                ko_KR: '브랜드 색상 팔레트입니다. 기본, 보조, 강조 색상을 포함합니다. 사이트 전체에서 일관된 브랜드 이미지를 위해 사용됩니다.',
                en_US: 'Brand color palette. Includes primary, secondary, and accent colors. Used for consistent brand image across the site.',
                ja: 'ブランドカラーパレット。プライマリ、セカンダリ、アクセントカラーが含まれます。サイト全体で一貫したブランドイメージのために使用されます。',
                zh_CN: '品牌色彩调色板。包括主色、辅助色和强调色。用于在整个网站上保持一致的品牌形象。'
            },
            'color-palette-system': {
                ko_KR: '시스템 색상 팔레트입니다. 성공, 경고, 오류, 정보 등 상태 표시에 사용되는 색상입니다.',
                en_US: 'System color palette. Colors used for status indicators like success, warning, error, and info.',
                ja: 'システムカラーパレット。成功、警告、エラー、情報などの状態表示に使用される色です。',
                zh_CN: '系统色彩调色板。用于成功、警告、错误、信息等状态指示的颜色。'
            },
            'color-preset': {
                ko_KR: '프리셋을 선택하면 전문가가 디자인한 색상 조합을 한 번에 적용할 수 있습니다.',
                en_US: 'Select a preset to apply a professionally designed color combination in one click.',
                ja: 'プリセットを選択すると、プロがデザインした色の組み合わせを一度に適用できます。',
                zh_CN: '选择预设可以一键应用专业设计的颜色组合。'
            },

            // 타이포그래피 관련 툴팁
            'typography-font-family': {
                ko_KR: '사이트에서 사용할 기본 폰트 패밀리를 설정합니다. 한국어와 영문 폰트를 각각 지정할 수 있습니다.',
                en_US: 'Set the default font family for your site. You can specify different fonts for Korean and English.',
                ja: 'サイトで使用するデフォルトのフォントファミリーを設定します。韓国語と英語のフォントを個別に指定できます。',
                zh_CN: '设置网站使用的默认字体系列。您可以分别指定韩语和英语字体。'
            },
            'typography-base-px': {
                ko_KR: '기준 픽셀 크기입니다. 이 값을 기준으로 rem/em 단위가 계산됩니다. 일반적으로 16px을 권장합니다.',
                en_US: 'Base pixel size. rem/em units are calculated based on this value. 16px is generally recommended.',
                ja: '基準ピクセルサイズです。この値を基準にrem/em単位が計算されます。一般的に16pxが推奨されます。',
                zh_CN: '基准像素大小。rem/em单位基于此值计算。通常建议使用16px。'
            },
            'typography-responsive': {
                ko_KR: '반응형 타이포그래피 설정입니다. 다양한 기기 크기에 따라 폰트 크기를 자동으로 조정합니다.',
                en_US: 'Responsive typography settings. Font sizes are automatically adjusted for different device sizes.',
                ja: 'レスポンシブタイポグラフィ設定です。様々なデバイスサイズに応じてフォントサイズを自動的に調整します。',
                zh_CN: '响应式排版设置。字体大小根据不同设备尺寸自动调整。'
            },

            // Figma 관련 툴팁
            'figma-api-token': {
                ko_KR: 'Figma Personal Access Token을 입력합니다. Figma 계정 설정에서 생성할 수 있으며, 이 플러그인에 암호화되어 저장됩니다.',
                en_US: 'Enter your Figma Personal Access Token. It can be generated from Figma account settings and is stored encrypted.',
                ja: 'Figma Personal Access Tokenを入力します。Figmaアカウント設定で生成でき、暗号化して保存されます。',
                zh_CN: '输入Figma个人访问令牌。可以从Figma帐户设置中生成，并以加密方式存储。'
            },
            'figma-file-key': {
                ko_KR: 'Figma 파일 URL에서 파일 키를 추출하여 입력합니다. URL의 /file/ 다음에 오는 부분입니다.',
                en_US: 'Enter the file key extracted from the Figma file URL. It is the part that comes after /file/ in the URL.',
                ja: 'FigmaファイルURLからファイルキーを抽出して入力します。URLの/file/の後に来る部分です。',
                zh_CN: '输入从Figma文件URL中提取的文件密钥。它是URL中/file/后面的部分。'
            },

            // 내보내기 관련 툴팁
            'export-pdf': {
                ko_KR: '스타일 가이드를 PDF로 내보냅니다. 브라우저의 인쇄 기능을 사용하여 깔끔한 문서를 생성합니다.',
                en_US: 'Export style guide as PDF. Creates a clean document using the browser print function.',
                ja: 'スタイルガイドをPDFとしてエクスポートします。ブラウザの印刷機能を使用してきれいなドキュメントを作成します。',
                zh_CN: '将样式指南导出为PDF。使用浏览器打印功能创建整洁的文档。'
            },
            'export-png': {
                ko_KR: '스타일 가이드를 PNG 이미지로 캡처합니다. 2배 해상도로 선명한 이미지를 생성합니다.',
                en_US: 'Capture style guide as PNG image. Creates a sharp image at 2x resolution.',
                ja: 'スタイルガイドをPNG画像としてキャプチャします。2倍の解像度でシャープな画像を生成します。',
                zh_CN: '将样式指南捕获为PNG图像。以2倍分辨率创建清晰的图像。'
            },

            // 백업 관련 툴팁
            'backup-auto': {
                ko_KR: '자동 백업을 활성화하면 설정 변경 시 자동으로 백업이 생성됩니다. 최대 10개까지 유지됩니다.',
                en_US: 'Enable auto-backup to automatically create backups when settings change. Up to 10 backups are retained.',
                ja: '自動バックアップを有効にすると、設定変更時に自動的にバックアップが作成されます。最大10個まで保持されます。',
                zh_CN: '启用自动备份后，设置更改时会自动创建备份。最多保留10个备份。'
            },
            'backup-restore': {
                ko_KR: '이 백업으로 복원합니다. 현재 설정은 새 백업으로 저장된 후 복원됩니다.',
                en_US: 'Restore to this backup. Current settings will be saved as a new backup before restoration.',
                ja: 'このバックアップに復元します。現在の設定は復元前に新しいバックアップとして保存されます。',
                zh_CN: '恢复到此备份。当前设置将在恢复前保存为新备份。'
            }
        },
        
        /**
         * 초기화
         */
        init: function() {
            // WordPress 로케일 가져오기
            if (typeof jj_admin_params !== 'undefined' && jj_admin_params.locale) {
                this.currentLocale = jj_admin_params.locale;
            } else if (typeof jjAdminCenter !== 'undefined' && jjAdminCenter.locale) {
                this.currentLocale = jjAdminCenter.locale;
            } else if (typeof wp !== 'undefined' && wp.i18n && wp.i18n.getLocale) {
                this.currentLocale = wp.i18n.getLocale();
            }
            
            // 툴팁 이벤트 바인딩
            this.bindEvents();
            
            // 기존 툴팁 속성이 있는 요소에 툴팁 적용
            this.initializeExistingTooltips();
        },
        
        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;
            
            // data-tooltip 속성이 있는 요소에 툴팁 추가
            $(document).on('mouseenter', '[data-tooltip]', function(e) {
                self.showTooltip($(this), $(this).data('tooltip'));
            });
            
            $(document).on('mouseleave', '[data-tooltip]', function() {
                self.hideTooltip();
            });
            
            // 키보드 포커스 시 툴팁 표시 (접근성)
            $(document).on('focusin', '[data-tooltip]', function() {
                self.showTooltip($(this), $(this).data('tooltip'));
            });
            
            $(document).on('focusout', '[data-tooltip]', function() {
                // 약간의 지연을 두어 키보드 네비게이션 시 툴팁이 유지되도록
                setTimeout(function() {
                    if (!$('[data-tooltip]:focus').length) {
                        self.hideTooltip();
                    }
                }, 100);
            });
            
            // ESC 키로 툴팁 닫기
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('.jj-tooltip').length) {
                    self.hideTooltip();
                }
            });
        },
        
        /**
         * 기존 툴팁 초기화
         */
        initializeExistingTooltips: function() {
            const self = this;
            
            // 섹션 레이아웃 설명에 툴팁 추가
            $('.jj-section-layout-description').each(function() {
                if (!$(this).attr('data-tooltip')) {
                    $(this).attr('data-tooltip', 'section-layout-description');
                }
            });
            
            // 실험실 탭 설명에 툴팁 추가
            $('.jj-labs-tab-description').each(function() {
                const tabType = $(this).data('tab-type');
                if (tabType && !$(this).attr('data-tooltip')) {
                    $(this).attr('data-tooltip', 'labs-tab-' + tabType);
                }
            });
        },
        
        /**
         * 툴팁 표시
         */
        showTooltip: function($element, tooltipKey) {
            // 기존 툴팁 제거
            this.hideTooltip();
            
            // 툴팁 텍스트 가져오기
            let tooltipText = '';
            
            if (typeof tooltipKey === 'string' && this.tooltips[tooltipKey]) {
                tooltipText = this.tooltips[tooltipKey][this.currentLocale] || 
                             this.tooltips[tooltipKey]['en_US'] || 
                             tooltipKey;
            } else if (typeof tooltipKey === 'string') {
                tooltipText = tooltipKey; // 직접 텍스트
            } else {
                return; // 유효하지 않은 툴팁 키
            }
            
            if (!tooltipText) {
                return;
            }
            
            // 툴팁 요소 생성
            const $tooltip = $('<div class="jj-tooltip" role="tooltip" aria-live="polite">' + 
                              '<div class="jj-tooltip-content">' + tooltipText + '</div>' +
                              '<div class="jj-tooltip-arrow"></div>' +
                              '</div>');
            
            $('body').append($tooltip);
            
            // 위치 계산
            this.positionTooltip($element, $tooltip);
            
            // 애니메이션으로 표시
            $tooltip.fadeIn(200);
        },
        
        /**
         * 툴팁 위치 계산
         */
        positionTooltip: function($element, $tooltip) {
            const elementOffset = $element.offset();
            const elementWidth = $element.outerWidth();
            const elementHeight = $element.outerHeight();
            const tooltipWidth = $tooltip.outerWidth();
            const tooltipHeight = $tooltip.outerHeight();
            const scrollTop = $(window).scrollTop();
            const scrollLeft = $(window).scrollLeft();
            const windowWidth = $(window).width();
            const windowHeight = $(window).height();
            
            // 기본 위치 (요소 위쪽 중앙)
            let top = elementOffset.top - tooltipHeight - 10;
            let left = elementOffset.left + (elementWidth / 2) - (tooltipWidth / 2);
            let arrowPosition = 'bottom';
            
            // 화면 밖으로 나가는지 확인하고 위치 조정
            if (top < scrollTop) {
                // 위쪽 공간이 부족하면 아래쪽에 표시
                top = elementOffset.top + elementHeight + 10;
                arrowPosition = 'top';
            }
            
            if (left < scrollLeft) {
                left = scrollLeft + 10;
            } else if (left + tooltipWidth > scrollLeft + windowWidth) {
                left = scrollLeft + windowWidth - tooltipWidth - 10;
            }
            
            // 툴팁 위치 설정
            $tooltip.css({
                top: top + 'px',
                left: left + 'px'
            });
            
            // 화살표 위치 설정
            $tooltip.find('.jj-tooltip-arrow').removeClass('top bottom left right');
            $tooltip.find('.jj-tooltip-arrow').addClass(arrowPosition);
        },
        
        /**
         * 툴팁 숨기기
         */
        hideTooltip: function() {
            $('.jj-tooltip').fadeOut(150, function() {
                $(this).remove();
            });
        },
        
        /**
         * 툴팁 텍스트 가져오기
         */
        getTooltipText: function(tooltipKey) {
            if (this.tooltips[tooltipKey]) {
                return this.tooltips[tooltipKey][this.currentLocale] || 
                       this.tooltips[tooltipKey]['en_US'] || 
                       tooltipKey;
            }
            return tooltipKey;
        }
    };
    
    // DOM 준비 시 초기화
    $(document).ready(function() {
        Tooltips.init();
    });
    
    // 전역으로 노출
    window.JJTooltips = Tooltips;
    
    // 스타일 적용
    if (!$('#jj-tooltips-styles').length) {
        const styles = `
            <style id="jj-tooltips-styles">
            .jj-tooltip {
                position: absolute;
                z-index: 100000;
                display: none;
                max-width: 300px;
                font-size: 13px;
                line-height: 1.5;
                pointer-events: none;
            }
            .jj-tooltip-content {
                background: #1d2327;
                color: #fff;
                padding: 8px 12px;
                border-radius: 4px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }
            .jj-tooltip-arrow {
                position: absolute;
                width: 0;
                height: 0;
                border-style: solid;
            }
            .jj-tooltip-arrow.top {
                bottom: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px 5px 0 5px;
                border-color: #1d2327 transparent transparent transparent;
            }
            .jj-tooltip-arrow.bottom {
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 0 5px 5px 5px;
                border-color: transparent transparent #1d2327 transparent;
            }
            .jj-tooltip-arrow.left {
                right: 100%;
                top: 50%;
                margin-top: -5px;
                border-width: 5px 0 5px 5px;
                border-color: transparent transparent transparent #1d2327;
            }
            .jj-tooltip-arrow.right {
                left: 100%;
                top: 50%;
                margin-top: -5px;
                border-width: 5px 5px 5px 0;
                border-color: transparent #1d2327 transparent transparent;
            }
            @media (max-width: 782px) {
                .jj-tooltip {
                    max-width: 250px;
                    font-size: 12px;
                }
            }
            </style>
        `;
        $('head').append(styles);
    }
    
})(jQuery);

