<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Multi-Armed Bandit Optimizer
 * - Thompson Sampling for real-time nudge optimization
 * - Automatically selects best-performing nudges
 * 
 * @package ACF_Nudge_Flow
 * @version 22.2.0
 * @author Mikael (Algorithm) + Jason (Implementation) + Jenny (UX)
 */
class JJ_MAB_Optimizer {
    private static $instance = null;
    private $stats_option_key = 'jj_nudge_mab_stats';
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Thompson Sampling: Beta 분포 기반 확률적 선택
     * 
     * @param array $nudges 넛지 목록 (각 넛지의 'id', 'successes', 'failures' 포함)
     * @return string 선택된 넛지 ID
     */
    public function select_nudge( $nudges ) {
        if ( empty( $nudges ) ) {
            return null;
        }

        $samples = array();
        
        foreach ( $nudges as $nudge ) {
            $alpha = ( isset( $nudge['successes'] ) ? (int) $nudge['successes'] : 0 ) + 1;
            $beta  = ( isset( $nudge['failures'] ) ? (int) $nudge['failures'] : 0 ) + 1;
            
            // Beta 분포에서 샘플 추출 (근사치)
            $sample = $this->beta_sample( $alpha, $beta );
            $samples[ $nudge['id'] ] = $sample;
        }
        
        // 가장 높은 샘플값을 가진 넛지 선택
        arsort( $samples );
        return key( $samples );
    }

    /**
     * Beta 분포 샘플링 (간단한 근사)
     * 실제 Beta 분포는 복잡하므로, Gamma 분포 근사 사용
     */
    private function beta_sample( $alpha, $beta ) {
        $x = $this->gamma_sample( $alpha );
        $y = $this->gamma_sample( $beta );
        return $x / ( $x + $y );
    }

    /**
     * Gamma 분포 샘플링 (Marsaglia and Tsang 방법)
     */
    private function gamma_sample( $alpha ) {
        if ( $alpha < 1 ) {
            return $this->gamma_sample( $alpha + 1 ) * pow( mt_rand() / mt_getrandmax(), 1 / $alpha );
        }
        
        $d = $alpha - 1 / 3;
        $c = 1 / sqrt( 9 * $d );
        
        while ( true ) {
            $x = $this->randn();
            $v = pow( 1 + $c * $x, 3 );
            
            if ( $v <= 0 ) continue;
            
            $u = mt_rand() / mt_getrandmax();
            
            if ( $u < 1 - 0.0331 * pow( $x, 4 ) ) {
                return $d * $v;
            }
            
            if ( log( $u ) < 0.5 * pow( $x, 2 ) + $d * ( 1 - $v + log( $v ) ) ) {
                return $d * $v;
            }
        }
    }

    /**
     * 표준 정규 분포 샘플링 (Box-Muller)
     */
    private function randn() {
        $u1 = mt_rand() / mt_getrandmax();
        $u2 = mt_rand() / mt_getrandmax();
        return sqrt( -2 * log( $u1 ) ) * cos( 2 * M_PI * $u2 );
    }

    /**
     * 넛지 성과 기록
     * 
     * @param string $nudge_id
     * @param bool $success (전환 성공 여부)
     */
    public function record_outcome( $nudge_id, $success ) {
        $stats = get_option( $this->stats_option_key, array() );
        
        if ( ! isset( $stats[ $nudge_id ] ) ) {
            $stats[ $nudge_id ] = array(
                'successes' => 0,
                'failures'  => 0,
                'impressions' => 0,
            );
        }
        
        $stats[ $nudge_id ]['impressions']++;
        
        if ( $success ) {
            $stats[ $nudge_id ]['successes']++;
        } else {
            $stats[ $nudge_id ]['failures']++;
        }
        
        update_option( $this->stats_option_key, $stats, false );
    }

    /**
     * 넛지별 전환율 계산
     */
    public function get_conversion_rates() {
        $stats = get_option( $this->stats_option_key, array() );
        $rates = array();
        
        foreach ( $stats as $nudge_id => $data ) {
            $total = $data['successes'] + $data['failures'];
            $rates[ $nudge_id ] = $total > 0 ? ( $data['successes'] / $total ) * 100 : 0;
        }
        
        return $rates;
    }

    /**
     * 통계 리셋
     */
    public function reset_stats( $nudge_id = null ) {
        if ( $nudge_id ) {
            $stats = get_option( $this->stats_option_key, array() );
            unset( $stats[ $nudge_id ] );
            update_option( $this->stats_option_key, $stats, false );
        } else {
            delete_option( $this->stats_option_key );
        }
    }
}
