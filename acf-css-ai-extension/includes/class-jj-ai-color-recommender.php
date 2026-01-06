<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * [v3.4.0] AI Color Palette Recommender
 *
 * 업종별/분위기별 AI 컬러 팔레트 추천 시스템
 * - 색상 심리학 기반 추천
 * - 색상 조화 분석 (보색, 유사색, 삼원색)
 * - 업종별 최적 컬러 팔레트 제안
 *
 * @since 3.4.0
 */
class JJ_AI_Color_Recommender {

    private static $instance = null;

    /**
     * 업종별 추천 색상 데이터
     */
    private $industry_palettes = array(
        'law' => array(
            'label'       => '법률/법무',
            'description' => '신뢰, 권위, 전문성을 표현',
            'palettes'    => array(
                array(
                    'name'      => 'Classic Navy',
                    'primary'   => '#1e3a5f',
                    'secondary' => '#8b7355',
                    'accent'    => '#c9a962',
                    'text'      => '#2c3e50',
                    'bg'        => '#f8f9fa',
                ),
                array(
                    'name'      => 'Burgundy Trust',
                    'primary'   => '#722f37',
                    'secondary' => '#2c3e50',
                    'accent'    => '#d4af37',
                    'text'      => '#333333',
                    'bg'        => '#ffffff',
                ),
            ),
        ),
        'medical' => array(
            'label'       => '의료/헬스케어',
            'description' => '청결, 신뢰, 전문성, 안정감',
            'palettes'    => array(
                array(
                    'name'      => 'Medical Blue',
                    'primary'   => '#0077b6',
                    'secondary' => '#00b4d8',
                    'accent'    => '#90e0ef',
                    'text'      => '#023e8a',
                    'bg'        => '#f8f9fa',
                ),
                array(
                    'name'      => 'Clean Green',
                    'primary'   => '#2d6a4f',
                    'secondary' => '#40916c',
                    'accent'    => '#74c69d',
                    'text'      => '#1b4332',
                    'bg'        => '#ffffff',
                ),
            ),
        ),
        'tech' => array(
            'label'       => '기술/IT/스타트업',
            'description' => '혁신, 현대성, 미래지향',
            'palettes'    => array(
                array(
                    'name'      => 'Tech Blue',
                    'primary'   => '#2563eb',
                    'secondary' => '#3b82f6',
                    'accent'    => '#06b6d4',
                    'text'      => '#1e293b',
                    'bg'        => '#f8fafc',
                ),
                array(
                    'name'      => 'Startup Purple',
                    'primary'   => '#7c3aed',
                    'secondary' => '#8b5cf6',
                    'accent'    => '#a78bfa',
                    'text'      => '#1f2937',
                    'bg'        => '#faf5ff',
                ),
                array(
                    'name'      => 'Cyber Neon',
                    'primary'   => '#00ff88',
                    'secondary' => '#0891b2',
                    'accent'    => '#f472b6',
                    'text'      => '#e2e8f0',
                    'bg'        => '#0f172a',
                ),
            ),
        ),
        'finance' => array(
            'label'       => '금융/은행/보험',
            'description' => '안정, 신뢰, 보안, 성장',
            'palettes'    => array(
                array(
                    'name'      => 'Finance Green',
                    'primary'   => '#047857',
                    'secondary' => '#059669',
                    'accent'    => '#10b981',
                    'text'      => '#064e3b',
                    'bg'        => '#f0fdf4',
                ),
                array(
                    'name'      => 'Bank Blue',
                    'primary'   => '#1e40af',
                    'secondary' => '#3b82f6',
                    'accent'    => '#60a5fa',
                    'text'      => '#1e3a8a',
                    'bg'        => '#eff6ff',
                ),
            ),
        ),
        'fashion' => array(
            'label'       => '패션/뷰티/럭셔리',
            'description' => '세련됨, 고급스러움, 트렌디',
            'palettes'    => array(
                array(
                    'name'      => 'Luxury Gold',
                    'primary'   => '#0f0f0f',
                    'secondary' => '#d4af37',
                    'accent'    => '#f5f5dc',
                    'text'      => '#1a1a1a',
                    'bg'        => '#ffffff',
                ),
                array(
                    'name'      => 'Blush Pink',
                    'primary'   => '#be185d',
                    'secondary' => '#ec4899',
                    'accent'    => '#fbcfe8',
                    'text'      => '#831843',
                    'bg'        => '#fdf2f8',
                ),
                array(
                    'name'      => 'Minimalist Black',
                    'primary'   => '#171717',
                    'secondary' => '#404040',
                    'accent'    => '#a3a3a3',
                    'text'      => '#0a0a0a',
                    'bg'        => '#fafafa',
                ),
            ),
        ),
        'food' => array(
            'label'       => '음식/레스토랑/카페',
            'description' => '식욕, 따뜻함, 친근함',
            'palettes'    => array(
                array(
                    'name'      => 'Warm Orange',
                    'primary'   => '#ea580c',
                    'secondary' => '#f97316',
                    'accent'    => '#fdba74',
                    'text'      => '#7c2d12',
                    'bg'        => '#fff7ed',
                ),
                array(
                    'name'      => 'Coffee Brown',
                    'primary'   => '#78350f',
                    'secondary' => '#92400e',
                    'accent'    => '#d97706',
                    'text'      => '#451a03',
                    'bg'        => '#fffbeb',
                ),
                array(
                    'name'      => 'Fresh Green',
                    'primary'   => '#16a34a',
                    'secondary' => '#22c55e',
                    'accent'    => '#86efac',
                    'text'      => '#14532d',
                    'bg'        => '#f0fdf4',
                ),
            ),
        ),
        'education' => array(
            'label'       => '교육/학원/온라인강의',
            'description' => '지식, 성장, 신뢰, 친근함',
            'palettes'    => array(
                array(
                    'name'      => 'Academic Blue',
                    'primary'   => '#1d4ed8',
                    'secondary' => '#3b82f6',
                    'accent'    => '#fbbf24',
                    'text'      => '#1e3a8a',
                    'bg'        => '#eff6ff',
                ),
                array(
                    'name'      => 'Growth Green',
                    'primary'   => '#15803d',
                    'secondary' => '#22c55e',
                    'accent'    => '#fde047',
                    'text'      => '#14532d',
                    'bg'        => '#f0fdf4',
                ),
            ),
        ),
        'creative' => array(
            'label'       => '크리에이티브/디자인/예술',
            'description' => '창의성, 독창성, 표현력',
            'palettes'    => array(
                array(
                    'name'      => 'Creative Gradient',
                    'primary'   => '#8b5cf6',
                    'secondary' => '#ec4899',
                    'accent'    => '#06b6d4',
                    'text'      => '#1f2937',
                    'bg'        => '#faf5ff',
                ),
                array(
                    'name'      => 'Bold Orange',
                    'primary'   => '#f97316',
                    'secondary' => '#84cc16',
                    'accent'    => '#14b8a6',
                    'text'      => '#1c1917',
                    'bg'        => '#fffbeb',
                ),
            ),
        ),
        'real_estate' => array(
            'label'       => '부동산/건설/인테리어',
            'description' => '안정감, 신뢰, 고급스러움',
            'palettes'    => array(
                array(
                    'name'      => 'Estate Navy',
                    'primary'   => '#1e3a5f',
                    'secondary' => '#0d9488',
                    'accent'    => '#d4af37',
                    'text'      => '#0f172a',
                    'bg'        => '#f8fafc',
                ),
                array(
                    'name'      => 'Earth Tone',
                    'primary'   => '#78716c',
                    'secondary' => '#a8a29e',
                    'accent'    => '#ca8a04',
                    'text'      => '#44403c',
                    'bg'        => '#fafaf9',
                ),
            ),
        ),
        'nonprofit' => array(
            'label'       => '비영리/NGO/사회공헌',
            'description' => '희망, 따뜻함, 신뢰',
            'palettes'    => array(
                array(
                    'name'      => 'Hope Green',
                    'primary'   => '#059669',
                    'secondary' => '#0891b2',
                    'accent'    => '#f59e0b',
                    'text'      => '#064e3b',
                    'bg'        => '#ecfdf5',
                ),
                array(
                    'name'      => 'Warm Heart',
                    'primary'   => '#dc2626',
                    'secondary' => '#f97316',
                    'accent'    => '#fbbf24',
                    'text'      => '#7f1d1d',
                    'bg'        => '#fef2f2',
                ),
            ),
        ),
    );

    /**
     * 분위기별 색상 조정 규칙
     */
    private $mood_adjustments = array(
        'professional' => array(
            'saturation' => -10,
            'brightness' => 0,
            'description' => '전문적이고 신뢰감 있는',
        ),
        'playful' => array(
            'saturation' => 15,
            'brightness' => 10,
            'description' => '밝고 즐거운',
        ),
        'elegant' => array(
            'saturation' => -5,
            'brightness' => -10,
            'description' => '세련되고 고급스러운',
        ),
        'warm' => array(
            'hue_shift' => 15, // 따뜻한 색상 쪽으로
            'description' => '따뜻하고 친근한',
        ),
        'cool' => array(
            'hue_shift' => -15, // 차가운 색상 쪽으로
            'description' => '시원하고 현대적인',
        ),
        'bold' => array(
            'saturation' => 20,
            'brightness' => 5,
            'description' => '대담하고 강렬한',
        ),
        'soft' => array(
            'saturation' => -20,
            'brightness' => 15,
            'description' => '부드럽고 편안한',
        ),
        'dark' => array(
            'brightness' => -30,
            'description' => '다크 모드',
        ),
    );

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_ajax_jj_ai_get_industry_palettes', array( $this, 'ajax_get_industry_palettes' ) );
        add_action( 'wp_ajax_jj_ai_recommend_palette', array( $this, 'ajax_recommend_palette' ) );
        add_action( 'wp_ajax_jj_ai_analyze_harmony', array( $this, 'ajax_analyze_harmony' ) );
        add_action( 'wp_ajax_jj_ai_generate_variations', array( $this, 'ajax_generate_variations' ) );
    }

    /**
     * 업종별 팔레트 목록 반환
     */
    public function get_industry_palettes( $industry = null ) {
        if ( $industry && isset( $this->industry_palettes[ $industry ] ) ) {
            return $this->industry_palettes[ $industry ];
        }
        return $this->industry_palettes;
    }

    /**
     * 업종 + 분위기 기반 팔레트 추천
     *
     * @param string $industry 업종 ID
     * @param string $mood 분위기
     * @param array $preferences 추가 선호도
     * @return array 추천 팔레트
     */
    public function recommend_palette( $industry, $mood = 'professional', $preferences = array() ) {
        $base_palettes = array();

        // 업종별 기본 팔레트 가져오기
        if ( isset( $this->industry_palettes[ $industry ] ) ) {
            $base_palettes = $this->industry_palettes[ $industry ]['palettes'];
        } else {
            // 기본 팔레트
            $base_palettes = array(
                array(
                    'name'      => 'Default Blue',
                    'primary'   => '#2563eb',
                    'secondary' => '#3b82f6',
                    'accent'    => '#60a5fa',
                    'text'      => '#1f2937',
                    'bg'        => '#ffffff',
                ),
            );
        }

        // 분위기 조정 적용
        $adjusted_palettes = array();
        foreach ( $base_palettes as $palette ) {
            $adjusted = $this->apply_mood_adjustment( $palette, $mood );
            $adjusted['mood'] = $mood;
            $adjusted['industry'] = $industry;

            // 색상 조화 점수 계산
            $adjusted['harmony_score'] = $this->calculate_harmony_score( $adjusted );

            // CSS 변수 생성
            $adjusted['css_variables'] = $this->generate_css_variables( $adjusted );

            // 설정 패치 생성
            $adjusted['settings_patch'] = $this->generate_settings_patch( $adjusted );

            $adjusted_palettes[] = $adjusted;
        }

        // 조화 점수로 정렬
        usort( $adjusted_palettes, function( $a, $b ) {
            return $b['harmony_score'] - $a['harmony_score'];
        } );

        return array(
            'industry'    => $industry,
            'mood'        => $mood,
            'description' => isset( $this->industry_palettes[ $industry ] )
                ? $this->industry_palettes[ $industry ]['description']
                : '',
            'palettes'    => $adjusted_palettes,
        );
    }

    /**
     * 분위기 조정 적용
     */
    private function apply_mood_adjustment( $palette, $mood ) {
        if ( ! isset( $this->mood_adjustments[ $mood ] ) ) {
            return $palette;
        }

        $adjustment = $this->mood_adjustments[ $mood ];
        $adjusted = $palette;

        foreach ( array( 'primary', 'secondary', 'accent' ) as $key ) {
            if ( isset( $palette[ $key ] ) ) {
                $adjusted[ $key ] = $this->adjust_color( $palette[ $key ], $adjustment );
            }
        }

        // 다크 모드인 경우 배경/텍스트 반전
        if ( $mood === 'dark' ) {
            $adjusted['bg'] = '#0f172a';
            $adjusted['text'] = '#e2e8f0';
        }

        return $adjusted;
    }

    /**
     * 색상 조정 (HSL 기반)
     */
    private function adjust_color( $hex, $adjustment ) {
        $hsl = $this->hex_to_hsl( $hex );

        if ( isset( $adjustment['hue_shift'] ) ) {
            $hsl['h'] = ( $hsl['h'] + $adjustment['hue_shift'] + 360 ) % 360;
        }
        if ( isset( $adjustment['saturation'] ) ) {
            $hsl['s'] = max( 0, min( 100, $hsl['s'] + $adjustment['saturation'] ) );
        }
        if ( isset( $adjustment['brightness'] ) ) {
            $hsl['l'] = max( 0, min( 100, $hsl['l'] + $adjustment['brightness'] ) );
        }

        return $this->hsl_to_hex( $hsl['h'], $hsl['s'], $hsl['l'] );
    }

    /**
     * 색상 조화 점수 계산
     */
    public function calculate_harmony_score( $palette ) {
        $score = 100;

        $primary_hsl = $this->hex_to_hsl( $palette['primary'] );
        $secondary_hsl = $this->hex_to_hsl( $palette['secondary'] );
        $accent_hsl = isset( $palette['accent'] ) ? $this->hex_to_hsl( $palette['accent'] ) : null;

        // 1. Primary-Secondary 색상 차이 분석
        $hue_diff = abs( $primary_hsl['h'] - $secondary_hsl['h'] );
        if ( $hue_diff > 180 ) {
            $hue_diff = 360 - $hue_diff;
        }

        // 보색 (180도 차이) 또는 유사색 (30도 이내) 보너스
        if ( $hue_diff >= 150 && $hue_diff <= 180 ) {
            $score += 15; // 보색 조화
        } elseif ( $hue_diff <= 30 ) {
            $score += 10; // 유사색 조화
        } elseif ( $hue_diff >= 110 && $hue_diff <= 130 ) {
            $score += 12; // 삼원색 조화
        }

        // 2. 명도 대비 확인
        $luminance_diff = abs( $primary_hsl['l'] - $secondary_hsl['l'] );
        if ( $luminance_diff >= 20 && $luminance_diff <= 50 ) {
            $score += 10; // 적절한 명도 대비
        }

        // 3. 채도 균형
        $saturation_diff = abs( $primary_hsl['s'] - $secondary_hsl['s'] );
        if ( $saturation_diff <= 30 ) {
            $score += 5; // 채도 균형
        }

        // 4. 텍스트-배경 대비 (WCAG 기준)
        if ( isset( $palette['text'] ) && isset( $palette['bg'] ) ) {
            $contrast = $this->calculate_contrast_ratio( $palette['text'], $palette['bg'] );
            if ( $contrast >= 7 ) {
                $score += 15; // AAA 수준
            } elseif ( $contrast >= 4.5 ) {
                $score += 10; // AA 수준
            } elseif ( $contrast < 3 ) {
                $score -= 20; // 대비 부족
            }
        }

        return min( 100, max( 0, $score ) );
    }

    /**
     * 대비 비율 계산 (WCAG)
     */
    private function calculate_contrast_ratio( $color1, $color2 ) {
        $l1 = $this->get_relative_luminance( $color1 );
        $l2 = $this->get_relative_luminance( $color2 );

        $lighter = max( $l1, $l2 );
        $darker = min( $l1, $l2 );

        return ( $lighter + 0.05 ) / ( $darker + 0.05 );
    }

    /**
     * 상대 휘도 계산
     */
    private function get_relative_luminance( $hex ) {
        $rgb = $this->hex_to_rgb( $hex );

        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $r = $r <= 0.03928 ? $r / 12.92 : pow( ( $r + 0.055 ) / 1.055, 2.4 );
        $g = $g <= 0.03928 ? $g / 12.92 : pow( ( $g + 0.055 ) / 1.055, 2.4 );
        $b = $b <= 0.03928 ? $b / 12.92 : pow( ( $b + 0.055 ) / 1.055, 2.4 );

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * 색상 변환: HEX → RGB
     */
    private function hex_to_rgb( $hex ) {
        $hex = ltrim( $hex, '#' );
        return array(
            'r' => hexdec( substr( $hex, 0, 2 ) ),
            'g' => hexdec( substr( $hex, 2, 2 ) ),
            'b' => hexdec( substr( $hex, 4, 2 ) ),
        );
    }

    /**
     * 색상 변환: HEX → HSL
     */
    private function hex_to_hsl( $hex ) {
        $rgb = $this->hex_to_rgb( $hex );
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        $l = ( $max + $min ) / 2;

        if ( $max === $min ) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );

            switch ( $max ) {
                case $r:
                    $h = ( ( $g - $b ) / $d + ( $g < $b ? 6 : 0 ) ) / 6;
                    break;
                case $g:
                    $h = ( ( $b - $r ) / $d + 2 ) / 6;
                    break;
                case $b:
                    $h = ( ( $r - $g ) / $d + 4 ) / 6;
                    break;
            }
        }

        return array(
            'h' => round( $h * 360 ),
            's' => round( $s * 100 ),
            'l' => round( $l * 100 ),
        );
    }

    /**
     * 색상 변환: HSL → HEX
     */
    private function hsl_to_hex( $h, $s, $l ) {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ( $s === 0 ) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * ( 1 + $s ) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $this->hue_to_rgb( $p, $q, $h + 1 / 3 );
            $g = $this->hue_to_rgb( $p, $q, $h );
            $b = $this->hue_to_rgb( $p, $q, $h - 1 / 3 );
        }

        return sprintf( '#%02x%02x%02x', round( $r * 255 ), round( $g * 255 ), round( $b * 255 ) );
    }

    private function hue_to_rgb( $p, $q, $t ) {
        if ( $t < 0 ) $t += 1;
        if ( $t > 1 ) $t -= 1;
        if ( $t < 1 / 6 ) return $p + ( $q - $p ) * 6 * $t;
        if ( $t < 1 / 2 ) return $q;
        if ( $t < 2 / 3 ) return $p + ( $q - $p ) * ( 2 / 3 - $t ) * 6;
        return $p;
    }

    /**
     * 색상 변형 생성 (유사색, 보색, 삼원색 등)
     */
    public function generate_variations( $base_color ) {
        $hsl = $this->hex_to_hsl( $base_color );

        return array(
            'original'     => $base_color,
            'lighter'      => $this->hsl_to_hex( $hsl['h'], $hsl['s'], min( 100, $hsl['l'] + 15 ) ),
            'darker'       => $this->hsl_to_hex( $hsl['h'], $hsl['s'], max( 0, $hsl['l'] - 15 ) ),
            'complementary'=> $this->hsl_to_hex( ( $hsl['h'] + 180 ) % 360, $hsl['s'], $hsl['l'] ),
            'analogous_1'  => $this->hsl_to_hex( ( $hsl['h'] + 30 ) % 360, $hsl['s'], $hsl['l'] ),
            'analogous_2'  => $this->hsl_to_hex( ( $hsl['h'] + 330 ) % 360, $hsl['s'], $hsl['l'] ),
            'triadic_1'    => $this->hsl_to_hex( ( $hsl['h'] + 120 ) % 360, $hsl['s'], $hsl['l'] ),
            'triadic_2'    => $this->hsl_to_hex( ( $hsl['h'] + 240 ) % 360, $hsl['s'], $hsl['l'] ),
            'split_comp_1' => $this->hsl_to_hex( ( $hsl['h'] + 150 ) % 360, $hsl['s'], $hsl['l'] ),
            'split_comp_2' => $this->hsl_to_hex( ( $hsl['h'] + 210 ) % 360, $hsl['s'], $hsl['l'] ),
        );
    }

    /**
     * CSS 변수 생성
     */
    private function generate_css_variables( $palette ) {
        $vars = array();
        $vars['--jj-primary-color'] = $palette['primary'];
        $vars['--jj-secondary-color'] = $palette['secondary'];

        if ( isset( $palette['accent'] ) ) {
            $vars['--jj-accent-color'] = $palette['accent'];
        }
        if ( isset( $palette['text'] ) ) {
            $vars['--jj-text-color'] = $palette['text'];
        }
        if ( isset( $palette['bg'] ) ) {
            $vars['--jj-bg-color'] = $palette['bg'];
        }

        // 호버 색상 자동 생성
        $primary_hsl = $this->hex_to_hsl( $palette['primary'] );
        $vars['--jj-primary-color-hover'] = $this->hsl_to_hex(
            $primary_hsl['h'],
            $primary_hsl['s'],
            max( 0, $primary_hsl['l'] - 10 )
        );

        $secondary_hsl = $this->hex_to_hsl( $palette['secondary'] );
        $vars['--jj-secondary-color-hover'] = $this->hsl_to_hex(
            $secondary_hsl['h'],
            $secondary_hsl['s'],
            max( 0, $secondary_hsl['l'] - 10 )
        );

        return $vars;
    }

    /**
     * ACF CSS Master 설정 패치 생성
     */
    private function generate_settings_patch( $palette ) {
        $primary_hsl = $this->hex_to_hsl( $palette['primary'] );
        $primary_hover = $this->hsl_to_hex(
            $primary_hsl['h'],
            $primary_hsl['s'],
            max( 0, $primary_hsl['l'] - 10 )
        );

        $secondary_hsl = $this->hex_to_hsl( $palette['secondary'] );
        $secondary_hover = $this->hsl_to_hex(
            $secondary_hsl['h'],
            $secondary_hsl['s'],
            max( 0, $secondary_hsl['l'] - 10 )
        );

        return array(
            'palettes' => array(
                'brand' => array(
                    'primary_color'        => $palette['primary'],
                    'primary_color_hover'  => $primary_hover,
                    'secondary_color'      => $palette['secondary'],
                    'secondary_color_hover'=> $secondary_hover,
                ),
                'system' => array(
                    'site_bg'     => $palette['bg'] ?? '#ffffff',
                    'content_bg'  => $palette['bg'] ?? '#ffffff',
                    'text_color'  => $palette['text'] ?? '#1f2937',
                    'link_color'  => $palette['primary'],
                ),
            ),
            'buttons' => array(
                'primary' => array(
                    'background_color'       => $palette['primary'],
                    'background_color_hover' => $primary_hover,
                    'text_color'             => $this->get_contrast_text( $palette['primary'] ),
                    'text_color_hover'       => $this->get_contrast_text( $primary_hover ),
                    'border_color'           => $palette['primary'],
                    'border_color_hover'     => $primary_hover,
                ),
                'secondary' => array(
                    'background_color'       => $palette['secondary'],
                    'background_color_hover' => $secondary_hover,
                    'text_color'             => $this->get_contrast_text( $palette['secondary'] ),
                    'text_color_hover'       => $this->get_contrast_text( $secondary_hover ),
                    'border_color'           => $palette['secondary'],
                    'border_color_hover'     => $secondary_hover,
                ),
            ),
            'forms' => array(
                'field' => array(
                    'border_color_focus' => $palette['primary'],
                ),
            ),
        );
    }

    /**
     * 배경색에 맞는 대비 텍스트 색상 반환
     */
    private function get_contrast_text( $bg_color ) {
        $luminance = $this->get_relative_luminance( $bg_color );
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    // ====== AJAX Handlers ======

    public function ajax_get_industry_palettes() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );

        $industry = isset( $_POST['industry'] ) ? sanitize_key( $_POST['industry'] ) : null;
        $palettes = $this->get_industry_palettes( $industry );

        wp_send_json_success( array(
            'industries' => array_keys( $this->industry_palettes ),
            'labels'     => wp_list_pluck( $this->industry_palettes, 'label' ),
            'moods'      => array_keys( $this->mood_adjustments ),
            'mood_labels'=> wp_list_pluck( $this->mood_adjustments, 'description' ),
            'palettes'   => $palettes,
        ) );
    }

    public function ajax_recommend_palette() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );

        $industry = isset( $_POST['industry'] ) ? sanitize_key( $_POST['industry'] ) : 'tech';
        $mood = isset( $_POST['mood'] ) ? sanitize_key( $_POST['mood'] ) : 'professional';

        $result = $this->recommend_palette( $industry, $mood );

        wp_send_json_success( $result );
    }

    public function ajax_analyze_harmony() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );

        $primary = isset( $_POST['primary'] ) ? sanitize_hex_color( $_POST['primary'] ) : '#2563eb';
        $secondary = isset( $_POST['secondary'] ) ? sanitize_hex_color( $_POST['secondary'] ) : '#3b82f6';
        $text = isset( $_POST['text'] ) ? sanitize_hex_color( $_POST['text'] ) : '#1f2937';
        $bg = isset( $_POST['bg'] ) ? sanitize_hex_color( $_POST['bg'] ) : '#ffffff';

        $palette = array(
            'primary'   => $primary,
            'secondary' => $secondary,
            'text'      => $text,
            'bg'        => $bg,
        );

        $score = $this->calculate_harmony_score( $palette );
        $contrast = $this->calculate_contrast_ratio( $text, $bg );

        wp_send_json_success( array(
            'harmony_score'  => $score,
            'contrast_ratio' => round( $contrast, 2 ),
            'wcag_level'     => $contrast >= 7 ? 'AAA' : ( $contrast >= 4.5 ? 'AA' : 'Fail' ),
            'suggestions'    => $this->get_harmony_suggestions( $score, $contrast ),
        ) );
    }

    public function ajax_generate_variations() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );

        $color = isset( $_POST['color'] ) ? sanitize_hex_color( $_POST['color'] ) : '#2563eb';
        $variations = $this->generate_variations( $color );

        wp_send_json_success( array(
            'base_color'  => $color,
            'variations'  => $variations,
        ) );
    }

    /**
     * 조화 개선 제안
     */
    private function get_harmony_suggestions( $score, $contrast ) {
        $suggestions = array();

        if ( $score < 70 ) {
            $suggestions[] = __( '색상 조화를 개선하려면 보색 또는 유사색 조합을 고려해보세요.', 'acf-css-ai-extension' );
        }

        if ( $contrast < 4.5 ) {
            $suggestions[] = __( '텍스트 가독성을 높이기 위해 배경과 텍스트의 대비를 높여주세요. (WCAG AA 기준: 4.5:1)', 'acf-css-ai-extension' );
        }

        if ( empty( $suggestions ) ) {
            $suggestions[] = __( '현재 색상 조합이 훌륭합니다!', 'acf-css-ai-extension' );
        }

        return $suggestions;
    }
}

// 초기화
add_action( 'init', function() {
    JJ_AI_Color_Recommender::instance();
} );
