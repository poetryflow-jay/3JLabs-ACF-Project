<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI CSS Performance Optimizer
 * - Analyzes generated CSS for redundancy
 * - Suggests combining similar rules
 * - Auto-minification and optimization
 * 
 * @package ACF_CSS_Manager
 * @version 22.1.4
 * @author Jason (AI Logic) + Mikael (Performance)
 */
class JJ_CSS_Optimizer_AI {
    private static $instance = null;
    private $optimizations_option_key = 'jj_css_optimizations';
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_ajax_jj_apply_css_optimization', array( $this, 'ajax_apply_optimization' ) );
    }

    /**
     * AJAX: Apply CSS optimization
     */
    public function ajax_apply_optimization() {
        check_ajax_referer( 'jj_optimizer_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $settings = get_option( 'jj_style_guide_settings', array() );
        $current_css = isset( $settings['custom_css'] ) ? $settings['custom_css'] : '';

        if ( empty( $current_css ) ) {
            wp_send_json_error( array( 'message' => __( 'CSS가 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // Optimize
        $optimized = $this->optimize( $current_css );
        
        // Save optimized version
        $settings['custom_css'] = $optimized;
        update_option( 'jj_style_guide_settings', $settings );

        $stats = $this->get_optimization_stats( $current_css );
        
        wp_send_json_success( array( 
            'message' => sprintf( __( '최적화 완료! %d%% 크기 절감', 'acf-css-really-simple-style-management-center' ), $stats['savings_percent'] ),
            'stats' => $stats
        ) );
    }

    /**
     * Analyze CSS and find optimization opportunities
     */
    public function analyze_css( $css ) {
        $suggestions = array();
        
        // 1. Duplicate rule detection
        $duplicates = $this->find_duplicate_rules( $css );
        if ( ! empty( $duplicates ) ) {
            $suggestions[] = array(
                'type' => 'duplicate_rules',
                'severity' => 'high',
                'message' => sprintf( __( '%d개의 중복된 CSS 규칙이 발견되었습니다.', 'acf-css-really-simple-style-management-center' ), count( $duplicates ) ),
                'details' => $duplicates,
                'savings' => $this->estimate_savings( $duplicates ),
            );
        }
        
        // 2. Redundant property detection
        $redundant = $this->find_redundant_properties( $css );
        if ( ! empty( $redundant ) ) {
            $suggestions[] = array(
                'type' => 'redundant_properties',
                'severity' => 'medium',
                'message' => sprintf( __( '%d개의 중복 속성이 발견되었습니다.', 'acf-css-really-simple-style-management-center' ), count( $redundant ) ),
                'details' => $redundant,
            );
        }
        
        // 3. Mergeable selectors
        $mergeable = $this->find_mergeable_selectors( $css );
        if ( ! empty( $mergeable ) ) {
            $suggestions[] = array(
                'type' => 'mergeable_selectors',
                'severity' => 'medium',
                'message' => sprintf( __( '%d개의 선택자를 통합할 수 있습니다.', 'acf-css-really-simple-style-management-center' ), count( $mergeable ) ),
                'details' => $mergeable,
            );
        }
        
        // 4. Minification potential
        $minified = $this->minify_css( $css );
        $size_before = strlen( $css );
        $size_after = strlen( $minified );
        $savings_percent = $size_before > 0 ? round( ( ( $size_before - $size_after ) / $size_before ) * 100, 1 ) : 0;
        
        if ( $savings_percent > 10 ) {
            $suggestions[] = array(
                'type' => 'minification',
                'severity' => 'low',
                'message' => sprintf( __( '압축으로 %d%% 크기 절감 가능 (%d → %d bytes)', 'acf-css-really-simple-style-management-center' ), $savings_percent, $size_before, $size_after ),
                'minified' => $minified,
                'savings_bytes' => $size_before - $size_after,
            );
        }
        
        return $suggestions;
    }

    /**
     * Find duplicate CSS rules
     */
    private function find_duplicate_rules( $css ) {
        $rules = array();
        $duplicates = array();
        
        // Parse CSS into rules
        preg_match_all( '/([^{]+)\{([^}]+)\}/s', $css, $matches, PREG_SET_ORDER );
        
        foreach ( $matches as $match ) {
            $selector = trim( $match[1] );
            $properties = trim( $match[2] );
            
            $rule_hash = md5( $properties );
            
            if ( isset( $rules[ $rule_hash ] ) ) {
                $duplicates[] = array(
                    'original' => $rules[ $rule_hash ],
                    'duplicate' => $selector,
                    'properties' => $properties,
                );
            } else {
                $rules[ $rule_hash ] = $selector;
            }
        }
        
        return $duplicates;
    }

    /**
     * Find redundant properties within same selector
     */
    private function find_redundant_properties( $css ) {
        $redundant = array();
        
        preg_match_all( '/([^{]+)\{([^}]+)\}/s', $css, $matches, PREG_SET_ORDER );
        
        foreach ( $matches as $match ) {
            $selector = trim( $match[1] );
            $properties_block = trim( $match[2] );
            
            // Split into individual properties
            $properties = array_filter( array_map( 'trim', explode( ';', $properties_block ) ) );
            $prop_map = array();
            
            foreach ( $properties as $prop ) {
                if ( strpos( $prop, ':' ) === false ) continue;
                
                list( $name, $value ) = array_map( 'trim', explode( ':', $prop, 2 ) );
                
                if ( isset( $prop_map[ $name ] ) ) {
                    $redundant[] = array(
                        'selector' => $selector,
                        'property' => $name,
                        'first_value' => $prop_map[ $name ],
                        'redundant_value' => $value,
                    );
                }
                
                $prop_map[ $name ] = $value;
            }
        }
        
        return $redundant;
    }

    /**
     * Find selectors with identical properties that can be merged
     */
    private function find_mergeable_selectors( $css ) {
        $rules_by_properties = array();
        $mergeable = array();
        
        preg_match_all( '/([^{]+)\{([^}]+)\}/s', $css, $matches, PREG_SET_ORDER );
        
        foreach ( $matches as $match ) {
            $selector = trim( $match[1] );
            $properties = trim( $match[2] );
            
            $prop_hash = md5( $properties );
            
            if ( ! isset( $rules_by_properties[ $prop_hash ] ) ) {
                $rules_by_properties[ $prop_hash ] = array(
                    'selectors' => array(),
                    'properties' => $properties,
                );
            }
            
            $rules_by_properties[ $prop_hash ]['selectors'][] = $selector;
        }
        
        // Find groups with 2+ selectors
        foreach ( $rules_by_properties as $data ) {
            if ( count( $data['selectors'] ) > 1 ) {
                $mergeable[] = array(
                    'selectors' => $data['selectors'],
                    'properties' => $data['properties'],
                    'merged' => implode( ', ', $data['selectors'] ) . ' { ' . $data['properties'] . ' }',
                );
            }
        }
        
        return $mergeable;
    }

    /**
     * Minify CSS
     */
    public function minify_css( $css ) {
        // Remove comments
        $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        
        // Remove whitespace
        $css = preg_replace( '/\s+/', ' ', $css );
        
        // Remove space around special characters
        $css = preg_replace( '/\s*([{}:;,>+~])\s*/', '$1', $css );
        
        // Remove trailing semicolons
        $css = preg_replace( '/;}/','}',$css );
        
        return trim( $css );
    }

    /**
     * Estimate file size savings
     */
    private function estimate_savings( $duplicates ) {
        $total_bytes = 0;
        
        foreach ( $duplicates as $dup ) {
            $total_bytes += strlen( $dup['properties'] );
        }
        
        return array(
            'bytes' => $total_bytes,
            'kb' => round( $total_bytes / 1024, 2 ),
        );
    }

    /**
     * Auto-optimize CSS
     */
    public function optimize( $css, $options = array() ) {
        $defaults = array(
            'remove_duplicates' => true,
            'merge_selectors' => true,
            'minify' => true,
        );
        
        $options = array_merge( $defaults, $options );
        $optimized = $css;
        
        // Remove duplicates
        if ( $options['remove_duplicates'] ) {
            $duplicates = $this->find_duplicate_rules( $optimized );
            foreach ( $duplicates as $dup ) {
                // Remove duplicate occurrences (keep first)
                $pattern = '/' . preg_quote( $dup['duplicate'], '/' ) . '\s*\{[^}]+\}/s';
                $optimized = preg_replace( $pattern, '', $optimized, 1 );
            }
        }
        
        // Minify
        if ( $options['minify'] ) {
            $optimized = $this->minify_css( $optimized );
        }
        
        return $optimized;
    }

    /**
     * Get optimization statistics
     */
    public function get_optimization_stats( $css ) {
        $original_size = strlen( $css );
        $optimized = $this->optimize( $css );
        $optimized_size = strlen( $optimized );
        
        $suggestions = $this->analyze_css( $css );
        
        return array(
            'original_size' => $original_size,
            'optimized_size' => $optimized_size,
            'savings_bytes' => $original_size - $optimized_size,
            'savings_percent' => $original_size > 0 ? round( ( ( $original_size - $optimized_size ) / $original_size ) * 100, 1 ) : 0,
            'suggestions_count' => count( $suggestions ),
            'suggestions' => $suggestions,
        );
    }
}
