<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pattern Learner - AI-powered CSS pattern detection and suggestion
 * - Tracks user's CSS modifications
 * - Detects patterns in style changes
 * - Suggests similar changes across site
 * 
 * @package ACF_CSS_Neural_Link
 * @version 6.1.0
 * @author Jenny (UX) + Jason (Implementation) + Mikael (ML Algorithm)
 */
class JJ_Pattern_Learner {
    private static $instance = null;
    private $patterns_option_key = 'jj_css_learned_patterns';
    private $history_option_key = 'jj_css_modification_history';
    private $max_history = 100;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Track style changes
        add_action( 'update_option_jj_style_guide_settings', array( $this, 'track_style_change' ), 10, 2 );
    }

    /**
     * Track style modifications
     */
    public function track_style_change( $old_value, $new_value ) {
        if ( empty( $old_value ) || empty( $new_value ) ) {
            return;
        }

        // Detect what changed
        $changes = $this->detect_changes( $old_value, $new_value );
        
        if ( empty( $changes ) ) {
            return;
        }

        // Record change to history
        $this->record_change( $changes );
        
        // Learn patterns from changes
        $this->learn_patterns( $changes );
    }

    /**
     * Detect what changed between old and new settings
     */
    private function detect_changes( $old, $new ) {
        $changes = array();
        
        foreach ( $new as $key => $value ) {
            if ( ! isset( $old[ $key ] ) || $old[ $key ] !== $value ) {
                $change_type = $this->classify_change( $key, $old[ $key ] ?? null, $value );
                
                if ( $change_type ) {
                    $changes[] = array(
                        'key' => $key,
                        'old_value' => $old[ $key ] ?? null,
                        'new_value' => $value,
                        'type' => $change_type,
                        'timestamp' => current_time( 'mysql' ),
                    );
                }
            }
        }
        
        return $changes;
    }

    /**
     * Classify the type of change
     */
    private function classify_change( $key, $old_value, $new_value ) {
        // Color changes
        if ( strpos( $key, 'color' ) !== false || $this->is_color( $new_value ) ) {
            return 'color_change';
        }
        
        // Font changes
        if ( strpos( $key, 'font' ) !== false ) {
            return 'font_change';
        }
        
        // Spacing changes (padding, margin)
        if ( strpos( $key, 'padding' ) !== false || strpos( $key, 'margin' ) !== false ) {
            return 'spacing_change';
        }
        
        // Border changes
        if ( strpos( $key, 'border' ) !== false ) {
            return 'border_change';
        }
        
        // Button styles
        if ( strpos( $key, 'button' ) !== false ) {
            return 'button_change';
        }
        
        return 'other';
    }

    /**
     * Check if value is a color
     */
    private function is_color( $value ) {
        if ( ! is_string( $value ) ) {
            return false;
        }
        
        // Hex color
        if ( preg_match( '/^#[0-9a-fA-F]{3,6}$/', $value ) ) {
            return true;
        }
        
        // RGB/RGBA
        if ( preg_match( '/^rgba?\(/', $value ) ) {
            return true;
        }
        
        return false;
    }

    /**
     * Record change to history
     */
    private function record_change( $changes ) {
        $history = get_option( $this->history_option_key, array() );
        
        // Add new changes
        foreach ( $changes as $change ) {
            array_unshift( $history, $change );
        }
        
        // Keep only last N changes
        if ( count( $history ) > $this->max_history ) {
            $history = array_slice( $history, 0, $this->max_history );
        }
        
        update_option( $this->history_option_key, $history, false );
    }

    /**
     * Learn patterns from changes
     * Uses frequency analysis and co-occurrence detection
     */
    private function learn_patterns( $changes ) {
        $patterns = get_option( $this->patterns_option_key, array(
            'frequent_types' => array(),
            'co_occurrences' => array(),
            'sequences' => array(),
        ) );
        
        foreach ( $changes as $change ) {
            $type = $change['type'];
            
            // Track frequency
            if ( ! isset( $patterns['frequent_types'][ $type ] ) ) {
                $patterns['frequent_types'][ $type ] = 0;
            }
            $patterns['frequent_types'][ $type ]++;
            
            // Track co-occurrences (what changes happen together)
            $other_types = array_unique( array_column( $changes, 'type' ) );
            foreach ( $other_types as $other_type ) {
                if ( $other_type === $type ) {
                    continue;
                }
                
                $pair_key = $type . '|' . $other_type;
                if ( ! isset( $patterns['co_occurrences'][ $pair_key ] ) ) {
                    $patterns['co_occurrences'][ $pair_key ] = 0;
                }
                $patterns['co_occurrences'][ $pair_key ]++;
            }
        }
        
        // Detect sequences (what follows what)
        $history = get_option( $this->history_option_key, array() );
        $recent_changes = array_slice( $history, 0, 10 );
        
        for ( $i = 0; $i < count( $recent_changes ) - 1; $i++ ) {
            $current_type = $recent_changes[ $i ]['type'];
            $next_type = $recent_changes[ $i + 1 ]['type'];
            
            $seq_key = $current_type . '->' . $next_type;
            if ( ! isset( $patterns['sequences'][ $seq_key ] ) ) {
                $patterns['sequences'][ $seq_key ] = 0;
            }
            $patterns['sequences'][ $seq_key ]++;
        }
        
        update_option( $this->patterns_option_key, $patterns, false );
    }

    /**
     * Get suggestions based on current change
     */
    public function get_suggestions( $current_change_type ) {
        $patterns = get_option( $this->patterns_option_key, array() );
        $suggestions = array();
        
        // Find co-occurring changes
        if ( isset( $patterns['co_occurrences'] ) ) {
            foreach ( $patterns['co_occurrences'] as $pair => $count ) {
                list( $type1, $type2 ) = explode( '|', $pair );
                
                if ( $type1 === $current_change_type || $type2 === $current_change_type ) {
                    $suggested_type = ( $type1 === $current_change_type ) ? $type2 : $type1;
                    
                    if ( ! isset( $suggestions[ $suggested_type ] ) ) {
                        $suggestions[ $suggested_type ] = array(
                            'type' => $suggested_type,
                            'confidence' => 0,
                            'reason' => 'co_occurrence',
                        );
                    }
                    
                    $suggestions[ $suggested_type ]['confidence'] += $count;
                }
            }
        }
        
        // Find sequential changes
        if ( isset( $patterns['sequences'] ) ) {
            foreach ( $patterns['sequences'] as $seq => $count ) {
                list( $from, $to ) = explode( '->', $seq );
                
                if ( $from === $current_change_type ) {
                    if ( ! isset( $suggestions[ $to ] ) ) {
                        $suggestions[ $to ] = array(
                            'type' => $to,
                            'confidence' => 0,
                            'reason' => 'sequence',
                        );
                    }
                    
                    $suggestions[ $to ]['confidence'] += $count * 1.5; // Sequences are stronger signals
                }
            }
        }
        
        // Sort by confidence
        uasort( $suggestions, function( $a, $b ) {
            return $b['confidence'] - $a['confidence'];
        } );
        
        // Return top 3 suggestions
        return array_slice( $suggestions, 0, 3 );
    }

    /**
     * Get human-readable suggestion text
     */
    public function format_suggestion( $suggestion ) {
        $messages = array(
            'color_change' => __( '색상도 함께 변경해보는 건 어떠세요? 이전에 자주 함께 수정하셨습니다.', 'acf-css-neural-link' ),
            'font_change' => __( '폰트 스타일도 조정하시겠어요? 보통 색상 변경 후 폰트를 수정하십니다.', 'acf-css-neural-link' ),
            'spacing_change' => __( '여백도 조정하면 더 균형잡힐 것 같아요.', 'acf-css-neural-link' ),
            'border_change' => __( '테두리 스타일도 함께 업데이트하시겠습니까?', 'acf-css-neural-link' ),
            'button_change' => __( '버튼 스타일도 통일하면 좋을 것 같습니다.', 'acf-css-neural-link' ),
        );
        
        $type = $suggestion['type'];
        $confidence = $suggestion['confidence'];
        
        return array(
            'message' => $messages[ $type ] ?? __( '이 변경사항도 고려해보세요.', 'acf-css-neural-link' ),
            'confidence_level' => $this->get_confidence_level( $confidence ),
            'type' => $type,
        );
    }

    /**
     * Convert confidence score to level
     */
    private function get_confidence_level( $score ) {
        if ( $score >= 10 ) {
            return 'high'; // 매우 권장
        } elseif ( $score >= 5 ) {
            return 'medium'; // 권장
        } else {
            return 'low'; // 제안
        }
    }

    /**
     * Get modification statistics
     */
    public function get_stats() {
        $history = get_option( $this->history_option_key, array() );
        $patterns = get_option( $this->patterns_option_key, array() );
        
        $type_counts = array();
        foreach ( $history as $change ) {
            $type = $change['type'];
            if ( ! isset( $type_counts[ $type ] ) ) {
                $type_counts[ $type ] = 0;
            }
            $type_counts[ $type ]++;
        }
        
        return array(
            'total_changes' => count( $history ),
            'change_types' => $type_counts,
            'most_frequent' => $patterns['frequent_types'] ?? array(),
            'patterns_learned' => count( $patterns['co_occurrences'] ?? array() ) + count( $patterns['sequences'] ?? array() ),
        );
    }

    /**
     * Reset all learned patterns
     */
    public function reset_patterns() {
        delete_option( $this->patterns_option_key );
        delete_option( $this->history_option_key );
    }
}
