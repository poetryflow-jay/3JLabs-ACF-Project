/**
 * J&J Lazy Loading System - v5.0.3
 * 
 * 고급 지연 로딩 시스템
 * - IntersectionObserver를 사용한 섹션별 지연 로딩
 * - 이미지 지연 로딩
 * - 스크립트 지연 로딩
 * - 성능 최적화
 * 
 * @since 5.0.3
 * @version 5.0.3
 */
(function($) {
    'use strict';

    /**
     * 지연 로딩 매니저
     */
    const LazyLoading = {
        // IntersectionObserver 인스턴스
        observer: null,
        
        // 로드된 섹션 추적
        loadedSections: new Set(),
        
        // 로딩 중인 섹션 추적
        loadingSections: new Set(),
        
        // 설정
        config: {
            // 뷰포트에 진입하기 전에 미리 로드할 거리 (px)
            rootMargin: '200px',
            // 섹션이 얼마나 보여야 로드할지 (0.1 = 10%)
            threshold: 0.1,
            // 최대 동시 로딩 수
            maxConcurrentLoads: 3,
            // 로딩 타임아웃 (ms)
            loadTimeout: 10000,
        },
        
        /**
         * 초기화
         */
        init: function() {
            // IntersectionObserver 지원 확인
            if (!('IntersectionObserver' in window)) {
                // 폴백: 모든 섹션 즉시 로드
                this.loadAllSections();
                return;
            }
            
            // IntersectionObserver 생성
            this.createObserver();
            
            // 지연 로딩 대상 섹션 등록
            this.registerLazySections();
            
            // 이미지 지연 로딩 초기화
            this.initImageLazyLoading();
            
            // 스크립트 지연 로딩 초기화
            this.initScriptLazyLoading();
        },
        
        /**
         * IntersectionObserver 생성
         */
        createObserver: function() {
            const self = this;
            
            this.observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const section = entry.target;
                        const sectionSlug = section.dataset.sectionSlug;
                        
                        if (sectionSlug && !self.loadedSections.has(sectionSlug) && !self.loadingSections.has(sectionSlug)) {
                            self.loadSection(sectionSlug, section);
                        }
                    }
                });
            }, {
                rootMargin: this.config.rootMargin,
                threshold: this.config.threshold
            });
        },
        
        /**
         * 지연 로딩 대상 섹션 등록
         */
        registerLazySections: function() {
            const self = this;
            
            // 모든 섹션 래퍼 찾기
            $('.jj-section-wrapper[data-section-slug]').each(function() {
                const $section = $(this);
                const sectionSlug = $section.data('section-slug');
                
                // 이미 로드된 섹션은 제외
                if (self.loadedSections.has(sectionSlug)) {
                    return;
                }
                
                // 지연 로딩 플래그 확인
                if ($section.data('lazy-load') !== false) {
                    // 관찰 대상으로 등록
                    self.observer.observe(this);
                    
                    // 로딩 플레이스홀더 추가
                    self.addLoadingPlaceholder($section, sectionSlug);
                }
            });
        },
        
        /**
         * 로딩 플레이스홀더 추가
         */
        addLoadingPlaceholder: function($section, sectionSlug) {
            // 이미 플레이스홀더가 있으면 스킵
            if ($section.find('.jj-lazy-loading-placeholder').length) {
                return;
            }
            
            const placeholder = $('<div class="jj-lazy-loading-placeholder" style="min-height: 200px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: jj-shimmer 1.5s infinite; border-radius: 4px; margin: 20px 0;"></div>');
            
            // 섹션 내용을 숨기고 플레이스홀더 표시
            $section.find('.jj-section-content').hide();
            $section.prepend(placeholder);
        },
        
        /**
         * 섹션 로드
         */
        loadSection: function(sectionSlug, sectionElement) {
            const self = this;
            
            // 동시 로딩 수 제한 확인
            if (this.loadingSections.size >= this.config.maxConcurrentLoads) {
                // 대기열에 추가
                setTimeout(function() {
                    self.loadSection(sectionSlug, sectionElement);
                }, 100);
                return;
            }
            
            // 로딩 중 플래그 설정
            this.loadingSections.add(sectionSlug);
            
            const $section = $(sectionElement);
            
            // 섹션별 로딩 로직
            if (sectionSlug === 'labs') {
                this.loadLabsSection($section);
            } else if (sectionSlug === 'spokes' || sectionSlug === 'themes') {
                this.loadAdapterSection($section, sectionSlug);
            } else {
                // 기본 섹션은 이미 로드되어 있으므로 플레이스홀더만 제거
                this.removeLoadingPlaceholder($section);
                this.loadingSections.delete(sectionSlug);
                this.loadedSections.add(sectionSlug);
            }
        },
        
        /**
         * Labs 섹션 로드
         */
        loadLabsSection: function($section) {
            const self = this;
            const $container = $section.find('#jj-lazy-load-labs');
            
            if (!$container.length) {
                this.removeLoadingPlaceholder($section);
                this.loadingSections.delete('labs');
                this.loadedSections.add('labs');
                return;
            }
            
            // 타임아웃 설정
            const timeout = setTimeout(function() {
                self.handleLoadError($section, 'labs', '로딩 시간 초과');
            }, this.config.loadTimeout);
            
            $.ajax({
                url: typeof jj_admin_params !== 'undefined' ? jj_admin_params.ajax_url : ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_load_labs_section',
                    security: typeof jj_admin_params !== 'undefined' ? jj_admin_params.nonce : ''
                },
                success: function(response) {
                    clearTimeout(timeout);
                    
                    if (response.success && response.data.html) {
                        $container.html(response.data.html);
                        
                        // 필드 초기화
                        if (typeof initializeAllFields === 'function') {
                            initializeAllFields($container);
                        }
                        
                        self.removeLoadingPlaceholder($section);
                        self.loadingSections.delete('labs');
                        self.loadedSections.add('labs');
                    } else {
                        self.handleLoadError($section, 'labs', response.data?.message || '로딩 실패');
                    }
                },
                error: function(xhr, status, error) {
                    clearTimeout(timeout);
                    self.handleLoadError($section, 'labs', error || '네트워크 오류');
                }
            });
        },
        
        /**
         * 어댑터 섹션 로드 (Spokes/Themes)
         */
        loadAdapterSection: function($section, sectionSlug) {
            const self = this;
            const $container = $section.find('#jj-lazy-load-' + sectionSlug);
            
            if (!$container.length) {
                this.removeLoadingPlaceholder($section);
                this.loadingSections.delete(sectionSlug);
                this.loadedSections.add(sectionSlug);
                return;
            }
            
            // 타임아웃 설정
            const timeout = setTimeout(function() {
                self.handleLoadError($section, sectionSlug, '로딩 시간 초과');
            }, this.config.loadTimeout);
            
            $.ajax({
                url: typeof jj_admin_params !== 'undefined' ? jj_admin_params.ajax_url : ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_load_adapters_ui',
                    security: typeof jj_admin_params !== 'undefined' ? jj_admin_params.nonce : '',
                    section: sectionSlug
                },
                success: function(response) {
                    clearTimeout(timeout);
                    
                    if (response.success && response.data.html) {
                        $container.html(response.data.html);
                        
                        // 필드 초기화
                        if (typeof initializeAllFields === 'function') {
                            initializeAllFields($container);
                        }
                        
                        self.removeLoadingPlaceholder($section);
                        self.loadingSections.delete(sectionSlug);
                        self.loadedSections.add(sectionSlug);
                    } else {
                        self.handleLoadError($section, sectionSlug, response.data?.message || '로딩 실패');
                    }
                },
                error: function(xhr, status, error) {
                    clearTimeout(timeout);
                    self.handleLoadError($section, sectionSlug, error || '네트워크 오류');
                }
            });
        },
        
        /**
         * 로딩 플레이스홀더 제거
         */
        removeLoadingPlaceholder: function($section) {
            $section.find('.jj-lazy-loading-placeholder').fadeOut(200, function() {
                $(this).remove();
            });
            $section.find('.jj-section-content').fadeIn(200);
        },
        
        /**
         * 로딩 오류 처리
         */
        handleLoadError: function($section, sectionSlug, errorMessage) {
            const $placeholder = $section.find('.jj-lazy-loading-placeholder');
            const errorHtml = '<div class="jj-lazy-load-error" style="padding: 20px; text-align: center; color: #d63638; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; margin: 20px 0;">' +
                '<p style="margin: 0;"><strong>로딩 오류:</strong> ' + errorMessage + '</p>' +
                '<button type="button" class="button button-secondary jj-retry-load" data-section="' + sectionSlug + '" style="margin-top: 10px;">다시 시도</button>' +
                '</div>';
            
            $placeholder.html(errorHtml);
            
            // 다시 시도 버튼 이벤트
            $placeholder.find('.jj-retry-load').on('click', function() {
                const retrySection = $(this).data('section');
                LazyLoading.loadingSections.delete(retrySection);
                LazyLoading.loadSection(retrySection, $section[0]);
            });
            
            this.loadingSections.delete(sectionSlug);
        },
        
        /**
         * 모든 섹션 즉시 로드 (폴백)
         */
        loadAllSections: function() {
            // 기존 로딩 함수들 호출
            if (typeof loadLabsSection === 'function') {
                loadLabsSection();
            }
            if (typeof loadSpokesAndThemesUI === 'function') {
                loadSpokesAndThemesUI();
            }
        },
        
        /**
         * 이미지 지연 로딩 초기화
         */
        initImageLazyLoading: function() {
            // 네이티브 lazy loading 지원 확인
            if ('loading' in HTMLImageElement.prototype) {
                // 네이티브 lazy loading 사용
                $('img[data-src]').each(function() {
                    const $img = $(this);
                    $img.attr('src', $img.data('src'));
                    $img.attr('loading', 'lazy');
                    $img.removeAttr('data-src');
                });
            } else {
                // IntersectionObserver를 사용한 폴백
                const imageObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                $('img[data-src]').each(function() {
                    imageObserver.observe(this);
                });
            }
        },
        
        /**
         * 스크립트 지연 로딩 초기화
         */
        initScriptLazyLoading: function() {
            // data-lazy-script 속성을 가진 스크립트 태그 처리
            $('script[data-lazy-script]').each(function() {
                const $script = $(this);
                const src = $script.data('lazy-script');
                
                if (src) {
                    const script = document.createElement('script');
                    script.src = src;
                    script.async = true;
                    script.defer = true;
                    $script.replaceWith(script);
                }
            });
        },
        
        /**
         * 섹션 강제 로드 (사용자가 탭을 클릭한 경우 등)
         */
        forceLoadSection: function(sectionSlug) {
            const $section = $('.jj-section-wrapper[data-section-slug="' + sectionSlug + '"]');
            
            if ($section.length && !this.loadedSections.has(sectionSlug) && !this.loadingSections.has(sectionSlug)) {
                this.loadSection(sectionSlug, $section[0]);
            }
        }
    };
    
    // DOM 준비 시 초기화
    $(document).ready(function() {
        // 약간의 지연 후 초기화 (다른 스크립트가 먼저 로드되도록)
        setTimeout(function() {
            LazyLoading.init();
        }, 100);
    });
    
    // 섹션 탭 클릭 시 강제 로드
    $(document).on('click', '.jj-section-tab, .jj-tab-button', function() {
        const $tab = $(this);
        const $section = $tab.closest('.jj-section-wrapper');
        const sectionSlug = $section.data('section-slug');
        
        if (sectionSlug) {
            LazyLoading.forceLoadSection(sectionSlug);
        }
    });
    
    // 전역으로 노출
    window.JJLazyLoading = LazyLoading;
    
    // CSS 애니메이션 추가 (shimmer 효과)
    if (!$('#jj-lazy-loading-styles').length) {
        $('<style id="jj-lazy-loading-styles">' +
            '@keyframes jj-shimmer {' +
            '  0% { background-position: -200% 0; }' +
            '  100% { background-position: 200% 0; }' +
            '}' +
            '</style>').appendTo('head');
    }
    
})(jQuery);

