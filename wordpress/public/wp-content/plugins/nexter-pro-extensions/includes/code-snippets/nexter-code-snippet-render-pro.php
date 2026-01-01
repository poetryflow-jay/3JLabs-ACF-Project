<?php
/**
 * Nexter Builder Code Snippets Pro Render
 *
 * @package Nexter Extensions Pro
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nexter_Builder_Code_Snippets_Render_Pro')) {

    class Nexter_Builder_Code_Snippets_Render_Pro {

        /**
         * Member Variable
         */
        private static $instance = null;

        /**
         * CSS selector Pro location output storage
         */
        public static $css_selector_snippet_output = array();

        /**
         * Snippet loaded IDs storage
         */
        public static $snippet_loaded_ids_pro = array(
            'css' => array(),
            'javascript' => array(),
            'php' => array(),
            'htmlmixed' => array(),
        );

        /**
         * Snippet type
         */
        private static $snippet_type = 'nxt-code-snippet';

        /**
         * Dynamic shortcode attributes storage
         */
        public $nxt_shortcode_dynamic_attrs = array();

        /**
         * Check if code snippets functionality is enabled
         */
        private function is_code_snippets_enabled() {
            $get_opt = get_option('nexter_extra_ext_options');
            $code_snippets_enabled = true;

            if (isset($get_opt['code-snippets']) && isset($get_opt['code-snippets']['switch'])) {
                $code_snippets_enabled = !empty($get_opt['code-snippets']['switch']);
            }

            return $code_snippets_enabled;
        }

        /**
         * Initiator
         */
        public static function get_instance() {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         */
        public function __construct() {
            // Only proceed if code snippets are enabled
            if (!$this->is_code_snippets_enabled()) {
                return;
            }

            // Hook into the Free plugin's execution
            add_action('wp', array($this, 'init_pro_features'), 1);
            
            // Pro-only location handlers
            add_filter('nexter_should_skip_pro_location', array($this, 'handle_pro_location_check'), 10, 2);
            add_filter('nexter_execute_pro_location', array($this, 'execute_pro_location_logic'), 10, 4);
            
            // Pro conditional logic
            add_filter('nexter_evaluate_pro_condition', array($this, 'evaluate_pro_conditional_logic'), 10, 3);
            
            // Pro content insertion (percentage/word-based locations)
            add_filter('nexter_process_pro_content_insertion', array($this, 'process_pro_content_insertion'), 10, 4);
            
            // Scheduling
            add_filter('nexter_check_pro_schedule_restrictions', array($this, 'check_schedule_restrictions'), 10, 2);
            
            // Hook into the Pro location filters
            add_filter('nexter_check_pro_css_selector_locations', array($this, 'handle_css_selector_locations'), 10, 1);
            add_filter('nexter_get_pro_css_selector_snippets', array($this, 'get_css_selector_snippets'), 10, 2);
            add_filter('nexter_populate_pro_css_snippet_output', array($this, 'populate_css_snippet_output'), 10, 2);
            add_filter('nexter_process_pro_css_selector_output', array($this, 'process_css_selector_output'), 10, 1);
            add_filter('nexter_map_pro_css_location_to_position', array($this, 'map_css_location_to_position'), 10, 1);
            add_filter('nexter_get_css_selector_locations', array($this, 'get_css_selector_locations_list'), 10, 1);
            add_filter('nexter_debug_pro_functionality', array($this, 'debug_pro_functionality'), 10, 1);
            
            // Register with main snippet tracking system
            add_filter('nexter_loaded_snippet_ids', array($this, 'register_pro_snippet_ids'), 10, 1);
            
            // Shortcode attribute support for free version's PHP executor
            add_filter('nexter_php_snippet_attributes', array($this, 'provide_shortcode_attributes'), 10, 3);
            
            // Shortcode and AJAX functionality
            add_action('wp_ajax_find_where_shortcode_usage', array($this, 'nxt_find_where_shortcode_usage'));
            add_action('wp_loaded', array($this, 'home_page_code_execute'));
        }

        /**
         * Initialize Pro features
         */
        public function init_pro_features() {
            if (!is_admin()) {
                // Initialize CSS Selector functionality for Pro
                add_action('wp', array($this, 'init_css_selector_pro_functionality'), 5);
            }
        }

        /**
         * Handle Pro location check
         * 
         * @param bool $should_skip
         * @param string $location
         * @return bool
         */
        public function handle_pro_location_check($should_skip, $location) {
            $pro_locations = $this->get_pro_only_locations();
            
            if (in_array($location, $pro_locations)) {
                // Pro location detected, don't skip - we'll handle it
                return false;
            }
            
            return $should_skip;
        }

        /**
         * Get Pro-only locations
         * 
         * @return array
         */
        private function get_pro_only_locations() {
            return array_merge(
                $this->get_css_selector_locations(),
                $this->get_content_based_locations()
            );
        }

        /**
         * Get CSS Selector locations
         * 
         * @return array
         */
        private function get_css_selector_locations() {
            return [
                'before_html_element',
                'after_html_element', 
                'start_html_element',
                'end_html_element',
                'replace_html_element'
            ];
        }

        /**
         * Get Content-based locations
         * 
         * @return array
         */
        private function get_content_based_locations() {
            return [
                'insert_after_words',
                'insert_every_words',
                'insert_middle_content',
                'insert_after_25',
                'insert_after_75',
                'insert_after_33',
                'insert_after_66',
                'insert_after_80'
            ];
        }

        /**
         * Execute Pro location logic
         * 
         * @param mixed $result
         * @param int $snippet_id
         * @param string $content
         * @param string $location
         * @return mixed
         */
        public function execute_pro_location_logic($result, $snippet_id, $content, $location) {
            // Handle CSS Selector locations
            if (in_array($location, $this->get_css_selector_locations())) {
                return $this->handle_css_selector_location($snippet_id, $content, $location);
            }
            
            // Handle Content-based locations
            if (in_array($location, $this->get_content_based_locations())) {
                return $this->handle_content_based_location($snippet_id, $content, $location);
            }
            
            return $result;
        }

        /**
         * Handle CSS Selector location execution
         * 
         * @param int $snippet_id
         * @param string $content
         * @param string $location
         * @return bool
         */
        private function handle_css_selector_location($snippet_id, $content, $location) {
            // Check if snippet should execute based on conditions
            if (!$this->should_snippet_execute($snippet_id)) {
                return false;
            }
            
            $css_selector = get_post_meta($snippet_id, 'nxt-code-css-selector', true);
            $element_index = get_post_meta($snippet_id, 'nxt-code-element-index', true);
            
            if (empty($css_selector)) {
                return false;
            }

            // Track this snippet for admin bar display only after validation
            $type = get_post_meta($snippet_id, 'nxt-code-type', true);
            $this->track_pro_snippet($snippet_id, $type);

            // Store CSS selector snippet data for frontend processing
            $css_selector_data = array(
                'id' => $snippet_id,
                'selector' => $css_selector,
                'position' => $this->map_css_location_to_position($location),
                'content' => $content,
                'element_index' => !empty($element_index) ? intval($element_index) : 0
            );

            // Add to global CSS selector snippets for processing
            global $nexter_css_selector_snippets;
            if (!isset($nexter_css_selector_snippets)) {
                $nexter_css_selector_snippets = array();
            }
            $nexter_css_selector_snippets[] = $css_selector_data;
            
            return true;
        }

        /**
         * Handle Content-based location execution
         * 
         * @param int $snippet_id
         * @param string $content
         * @param string $location
         * @return bool
         */
        private function handle_content_based_location($snippet_id, $content, $location) {
            // Check if snippet should execute based on conditions
            if (!$this->should_snippet_execute($snippet_id)) {
                return false;
            }
            
            // Track this snippet for admin bar display only after validation
            $type = get_post_meta($snippet_id, 'nxt-code-type', true);
            $this->track_pro_snippet($snippet_id, $type);
            
            // Content-based locations are handled via content filters
            switch ($location) {
                case 'insert_middle_content':
                    add_filter('the_content', function($post_content) use ($content, $snippet_id) {
                        return $this->insert_at_content_percentage($post_content, $content, 50);
                    }, 10);
                    break;
                    
                case 'insert_after_25':
                    add_filter('the_content', function($post_content) use ($content, $snippet_id) {
                        return $this->insert_at_content_percentage($post_content, $content, 25);
                    }, 10);
                    break;
                    
                case 'insert_after_75':
                    add_filter('the_content', function($post_content) use ($content, $snippet_id) {
                        return $this->insert_at_content_percentage($post_content, $content, 75);
                    }, 10);
                    break;
                    
                case 'insert_after_33':
                    add_filter('the_content', function($post_content) use ($content, $snippet_id) {
                        return $this->insert_at_content_percentage($post_content, $content, 33);
                    }, 10);
                    break;
                    
                case 'insert_after_66':
                    add_filter('the_content', function($post_content) use ($content, $snippet_id) {
                        return $this->insert_at_content_percentage($post_content, $content, 66);
                    }, 10);
                    break;
                    
                case 'insert_after_80':
                    add_filter('the_content', function($post_content) use ($content, $snippet_id) {
                        return $this->insert_at_content_percentage($post_content, $content, 80);
                    }, 10);
                    break;
                    
                case 'insert_after_words':
                    $word_count = get_post_meta($snippet_id, 'nxt-code-word-count', true);
                    if (!empty($word_count)) {
                        add_filter('the_content', function($post_content) use ($content, $word_count) {
                            return $this->insert_after_word_count($post_content, $content, intval($word_count));
                        }, 10);
                    }
                    break;
                    
                case 'insert_every_words':
                    $word_interval = get_post_meta($snippet_id, 'nxt-code-word-interval', true);
                    if (!empty($word_interval)) {
                        add_filter('the_content', function($post_content) use ($content, $word_interval) {
                            return $this->insert_every_word_count($post_content, $content, intval($word_interval));
                        }, 10);
                    }
                    break;
            }
            
            return true;
        }

        /**
         * Process Pro content insertion filter from free plugin
         */
        public function process_pro_content_insertion($content, $location, $insert_content, $snippet_id) {
            // Handle different Pro location types
            switch ($location) {
                case 'insert_middle_content':
                    return $this->insert_at_content_percentage($content, $insert_content, 50);
                    
                case 'insert_after_25':
                    return $this->insert_at_content_percentage($content, $insert_content, 25);
                    
                case 'insert_after_33':
                    return $this->insert_at_content_percentage($content, $insert_content, 33);
                    
                case 'insert_after_66':
                    return $this->insert_at_content_percentage($content, $insert_content, 66);
                    
                case 'insert_after_75':
                    return $this->insert_at_content_percentage($content, $insert_content, 75);
                    
                case 'insert_after_80':
                    return $this->insert_at_content_percentage($content, $insert_content, 80);
                    
                case 'insert_after_words':
                    $word_target = get_post_meta($snippet_id, 'nxt-insert-word-count', true);
                    if (!$word_target || !is_numeric($word_target)) {
                        $word_target = 100; // Default to 100 words
                    }
                    return $this->insert_after_word_count($content, $insert_content, (int)$word_target);
                    
                case 'insert_every_words':
                    $word_interval = get_post_meta($snippet_id, 'nxt-insert-word-interval', true);
                    if (!$word_interval || !is_numeric($word_interval)) {
                        $word_interval = 200; // Default to every 200 words
                    }
                    return $this->insert_every_word_count($content, $insert_content, (int)$word_interval);
                    
                default:
                    return $content;
            }
        }

        /**
         * Insert content at percentage of total content
         */
        private function insert_at_content_percentage($content, $insert_content, $percentage) {
            // Remove HTML tags for word counting
            $text_content = wp_strip_all_tags($content);
            $word_count = str_word_count($text_content);
            $target_position = intval(($word_count * $percentage) / 100);
            
            return $this->insert_after_word_count($content, $insert_content, $target_position);
        }

        /**
         * Insert content after specific word count
         */
        private function insert_after_word_count($content, $insert_content, $target_words) {
            if ($target_words <= 0) {
                return $content;
            }
            
            // Split content into words while preserving HTML
            $words = explode(' ', $content);
            $current_word_count = 0;
            $result = '';
            $inserted = false;
            
            foreach ($words as $word) {
                $result .= $word . ' ';
                
                // Count actual words (not HTML tags)
                if (strip_tags($word) !== '') {
                    $current_word_count++;
                }
                
                // Insert after target word count
                if (!$inserted && $current_word_count >= $target_words) {
                    $result .= $insert_content . ' ';
                    $inserted = true;
                }
            }
            
            // If we couldn't insert (content too short), append to end
            if (!$inserted) {
                $result .= $insert_content;
            }
            
            return trim($result);
        }

        /**
         * Insert content at every word interval
         */
        private function insert_every_word_count($content, $insert_content, $word_interval) {
            if ($word_interval <= 0) {
                return $content;
            }
            
            $words = explode(' ', $content);
            $current_word_count = 0;
            $result = '';
            
            foreach ($words as $word) {
                $result .= $word . ' ';
                
                // Count actual words (not HTML tags)
                if (strip_tags($word) !== '') {
                    $current_word_count++;
                }
                
                // Insert at every interval
                if ($current_word_count > 0 && $current_word_count % $word_interval === 0) {
                    $result .= $insert_content . ' ';
                }
            }
            
            return trim($result);
        }

        /**
         * Initialize CSS Selector Pro functionality
         */
        public function init_css_selector_pro_functionality() {
            // Add CSS selector processing to Free plugin's output buffer
            add_action('wp_footer', array($this, 'process_css_selector_snippets'), 1);
        }

        /**
         * Process CSS Selector snippets
         */
        public function process_css_selector_snippets() {
            global $nexter_css_selector_snippets;
            
            if (empty($nexter_css_selector_snippets) || !is_array($nexter_css_selector_snippets)) {
                return;
            }

            // Generate JavaScript for CSS selector manipulation
            echo '<script type="text/javascript">';
            echo 'document.addEventListener("DOMContentLoaded", function() {';
            
            foreach ($nexter_css_selector_snippets as $snippet) {
                $selector = esc_js($snippet['selector']);
                $position = esc_js($snippet['position']);
                $content = wp_json_encode($snippet['content']);
                $element_index = intval($snippet['element_index']);
                
                echo "try {";
                echo "var elements = document.querySelectorAll('{$selector}');";
                echo "if (elements.length > 0) {";
                
                if ($element_index > 0) {
                    echo "var targetElement = elements[{$element_index} - 1];";
                    echo "if (targetElement) {";
                } else {
                    echo "elements.forEach(function(targetElement) {";
                }
                
                switch ($position) {
                    case 'before':
                        echo "targetElement.insertAdjacentHTML('beforebegin', {$content});";
                        break;
                    case 'after':
                        echo "targetElement.insertAdjacentHTML('afterend', {$content});";
                        break;
                    case 'prepend':
                        echo "targetElement.insertAdjacentHTML('afterbegin', {$content});";
                        break;
                    case 'append':
                        echo "targetElement.insertAdjacentHTML('beforeend', {$content});";
                        break;
                    case 'replace':
                        echo "targetElement.outerHTML = {$content};";
                        break;
                }
                
                if ($element_index > 0) {
                    echo "}";
                } else {
                    echo "});";
                }
                
                echo "}";
                echo "} catch(e) { console.warn('Nexter CSS Selector Error:', e); }";
            }
            
            echo '});';
            echo '</script>';
            
            // Clear processed snippets
            $nexter_css_selector_snippets = array();
        }

        /**
         * Evaluate Pro conditional logic
         * 
         * @param mixed $result
         * @param string $field
         * @param array $condition_data
         * @return mixed
         */
        public function evaluate_pro_conditional_logic($result, $field, $condition_data) {
            $pro_fields = $this->get_pro_conditional_fields();
            
            if (!in_array($field, $pro_fields)) {
                return $result;
            }
            $operator = isset( $condition_data['operator'] ) ? $condition_data['operator'] : 'is';
            $values = isset( $condition_data['values'] ) ? $condition_data['values'] : [];
            
            switch ( $field ) {
                case 'user_meta':
                    return $this->evaluate_user_meta_condition($operator, $values);
                case 'device_type':
                    return $this->evaluate_device_type_condition($operator, $values);
                case 'cookie_name':
                    return $this->evaluate_cookie_name_condition($operator, $values);
                case 'cookie_value':
                    return $this->evaluate_cookie_value_condition($operator, $values);
                case 'country':
                    return $this->evaluate_country_condition($operator, $values);
                case 'continent':
                    return $this->evaluate_continent_condition($operator, $values);
                case 'session':
                    return $this->evaluate_session_condition($operator, $values);
                case 'taxonomy_term':
                    return $this->evaluate_taxonomy_term_condition($operator, $values);
                case 'page_url':
                    return $this->evaluate_page_url_condition($operator, $values);
                case 'post_meta':
                    return $this->evaluate_post_meta_condition($operator, $values);
                case 'post_status':
                    return $this->evaluate_post_status_condition($operator, $values);
                case 'page_template':
                    return $this->evaluate_page_template_condition($operator, $values);
                case 'author':
                    return $this->evaluate_author_condition($operator, $values);
                case 'url_path':
                    return $this->evaluate_url_path_condition($operator, $values);
                case 'http_referrer':
                    return $this->evaluate_http_referrer_condition($operator, $values);
                case 'user_agent':
                    return $this->evaluate_user_agent_condition($operator, $values);
                case 'query_string':
                    return $this->evaluate_query_string_condition($operator, $values);
                case 'date':
                    return $this->evaluate_date_condition($operator, $values);
                case 'date_time':
                    return $this->evaluate_date_time_condition($operator, $values);
                case 'current_time':
                    return $this->evaluate_time_condition($operator, $values);
                case 'snippet_loaded':
                    return $this->evaluate_snippet_loaded_condition($operator, $values);
                case 'ip_address':
                    return $this->evaluate_ip_address_condition($operator, $values);
                default:
                    return false;
            }
        }

        /**
         * Get Pro conditional fields
         * 
         * @return array
         */
        private function get_pro_conditional_fields() {
            return [
                'user_meta',
                'device_type',
                'cookie_name', 
                'cookie_value',
                'country',
                'continent',
                'session',
                'taxonomy_term',
                'page_url',
                'post_meta',
                'post_status',
                'page_template',
                'author',
                'url_path',
                'http_referrer',
                'user_agent',
                'query_string',
                'date',
                'date_time',
                'current_time',
                'snippet_loaded',
                'ip_address'
            ];
        }

        /**
         * Check schedule restrictions
         * 
         * @param bool $should_skip
         * @param int $snippet_id
         * @return bool
         */
        public function check_schedule_restrictions($should_skip, $snippet_id) {
            // Get schedule dates
            $start_date = get_post_meta($snippet_id, 'nxt-code-startdate', true);
            $end_date = get_post_meta($snippet_id, 'nxt-code-enddate', true);

            // If no schedule dates are set, don't skip
            if (empty($start_date) && empty($end_date)) {
                return $should_skip;
            }
            
            $now = current_time('mysql');
            
            // Check if current time is before start date
            if (!empty($start_date) && $now < $start_date) {
                return true; // Not yet time to start
            }
            
            // Check if current time is after end date
            if (!empty($end_date) && $now > $end_date) {
                return true; // Already ended
            }
            
            return $should_skip;
        }

        // Placeholder methods for Pro conditional logic evaluation
        // These will be populated by moving the actual implementations from the Free plugin

        private function evaluate_user_meta_condition($operator, $values) {
            // Must be logged in to have user meta
            if ( ! is_user_logged_in() ) {
                return in_array( $operator, ['is_not', 'not_contains', 'not_exists', 'is_empty'] );
            }

            $user_id = get_current_user_id();

            foreach ( $values as $value_obj ) {
                // Extract meta key and value from simple text input
                $input_value = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $input_value ) ) {
                    continue;
                }

                // Support format: "meta_key" or "meta_key=meta_value"
                if ( strpos( $input_value, '=' ) !== false ) {
                    list( $meta_key, $expected_value ) = explode( '=', $input_value, 2 );
                    $meta_key = trim( $meta_key );
                    $expected_value = trim( $expected_value );
                } else {
                    $meta_key = trim( $input_value );
                    $expected_value = '';
                }

                if ( empty( $meta_key ) ) {
                    continue;
                }

                $user_meta_value = get_user_meta( $user_id, $meta_key, true );

                // Handle different operators
                switch ( $operator ) {
                    case 'exists':
                        if ( metadata_exists( 'user', $user_id, $meta_key ) ) {
                            return true;
                        }
                        break;

                    case 'not_exists':
                        if ( ! metadata_exists( 'user', $user_id, $meta_key ) ) {
                            return true;
                        }
                        break;

                    case 'is_empty':
                        if ( empty( $user_meta_value ) ) {
                            return true;
                        }
                        break;

                    case 'is_not_empty':
                        if ( ! empty( $user_meta_value ) ) {
                            return true;
                        }
                        break;

                    case 'is':
                        if ( empty( $expected_value ) ) {
                            // If no specific value provided, just check if meta exists and not empty
                            if ( ! empty( $user_meta_value ) ) {
                                return true;
                            }
                        } else {
                            // Compare specific value
                            if ( $user_meta_value == $expected_value ) {
                                return true;
                            }
                        }
                        break;

                    case 'is_not':
                        if ( empty( $expected_value ) ) {
                            // If no specific value provided, check if meta doesn't exist or is empty
                            if ( empty( $user_meta_value ) ) {
                                return true;
                            }
                        } else {
                            // Compare specific value
                            if ( $user_meta_value != $expected_value ) {
                                return true;
                            }
                        }
                        break;

                    case 'contains':
                        if ( ! empty( $expected_value ) && is_string( $user_meta_value ) ) {
                            if ( strpos( $user_meta_value, $expected_value ) !== false ) {
                                return true;
                            }
                        }
                        break;

                    case 'not_contains':
                        if ( ! empty( $expected_value ) && is_string( $user_meta_value ) ) {
                            if ( strpos( $user_meta_value, $expected_value ) === false ) {
                                return true;
                            }
                        } elseif ( empty( $user_meta_value ) ) {
                            // Empty value doesn't contain anything
                            return true;
                        }
                        break;
                }
            }

            // Default return based on operator for negative conditions
            return in_array( $operator, ['is_not', 'not_contains', 'not_exists', 'is_empty'] );
        }

        private function evaluate_device_type_condition($operator, $values) {
            $is_mobile = wp_is_mobile();
            $current_device = $is_mobile ? 'mobile' : 'desktop';
            
            foreach ( $values as $value_obj ) {
                $target_device = isset( $value_obj['value'] ) ? strtolower( $value_obj['value'] ) : strtolower( $value_obj );
                
                if ( empty( $target_device ) ) {
                    continue;
                }
                
                // Normalize device types
                if ( in_array( $target_device, ['phone', 'tablet', 'mobile'] ) ) {
                    $target_device = 'mobile';
                } elseif ( in_array( $target_device, ['desktop', 'computer', 'pc'] ) ) {
                    $target_device = 'desktop';
                }
                
                switch ( $operator ) {
                    case 'is':
                        if ( $current_device === $target_device ) {
                            return true;
                        }
                        break;
                        
                    case 'is_not':
                        if ( $current_device === $target_device ) {
                            return false;
                        }
                        break;
                }
            }
            
            return $operator === 'is_not';
        }

        private function evaluate_cookie_name_condition($operator, $values) {
            foreach ( $values as $value_obj ) {
                $cookie_name = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $cookie_name ) ) {
                    continue;
                }
                
                $cookie_exists = isset( $_COOKIE[ $cookie_name ] );
                
                if ( $operator === 'exists' && $cookie_exists ) {
                    return true;
                } elseif ( $operator === 'not_exists' && ! $cookie_exists ) {
                    return true;
                }
            }
            
            return false; // Always return false if no match found
        }

        private function evaluate_cookie_value_condition($operator, $values) {
            foreach ( $values as $value_obj ) {
                $input_value = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $input_value ) ) {
                    continue;
                }
                
                // Support format: "cookie_name=cookie_value"
                if ( strpos( $input_value, '=' ) !== false ) {
                    list( $cookie_name, $expected_value ) = explode( '=', $input_value, 2 );
                    $cookie_name = trim( $cookie_name );
                    $expected_value = trim( $expected_value );
                } else {
                    // If no = sign, treat whole string as cookie name and check if it exists with any value
                    $cookie_name = trim( $input_value );
                    $expected_value = '';
                }
                
                if ( empty( $cookie_name ) ) {
                    continue;
                }
                
                $cookie_exists = isset( $_COOKIE[ $cookie_name ] );
                $cookie_value = $cookie_exists ? $_COOKIE[ $cookie_name ] : '';
                
                // Handle different operators
                switch ( $operator ) {
                    case 'is':
                        if ( $cookie_exists && $cookie_value === $expected_value ) {
                            return true;
                        }
                        break;
                        
                    case 'is_not':
                        if ( !$cookie_exists || $cookie_value !== $expected_value ) {
                            return true;
                        }
                        break;
                        
                    case 'contains':
                        if ( $cookie_exists && !empty( $expected_value ) && strpos( $cookie_value, $expected_value ) !== false ) {
                            return true;
                        }
                        break;
                        
                    case 'not_contains':
                        if ( !$cookie_exists || empty( $expected_value ) || strpos( $cookie_value, $expected_value ) === false ) {
                            return true;
                        }
                        break;
                }
            }
            
            // If we get here, NONE of the conditions matched
            return false; // Return false for ALL operators when no matches found
        }

        private function evaluate_country_condition($operator, $values) {
            $user_location = $this->get_user_geolocation();
            
            if ( empty( $user_location['country'] ) && empty( $user_location['countryCode'] ) ) {
                return $operator === 'is_not'; // Default behavior when geolocation fails
            }
            
            // Get both country name and country code from user location
            $user_country_name = isset( $user_location['country'] ) ? strtolower( trim( $user_location['country'] ) ) : '';
            $user_country_code = isset( $user_location['countryCode'] ) ? strtoupper( trim( $user_location['countryCode'] ) ) : '';
            
            foreach ( $values as $value_obj ) {
                $target_country = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_country ) ) {
                    continue;
                }
                
                $target_country = trim( $target_country );
                
                // Check if user provided country code (2 letters) or country name
                $condition_met = false;
                if ( strlen( $target_country ) === 2 ) {
                    // User provided country code (e.g., "IN")
                    $condition_met = ( strtoupper( $target_country ) === $user_country_code );
                } else {
                    // User provided country name (e.g., "India")
                    $condition_met = ( strtolower( $target_country ) === $user_country_name );
                }
                
                switch ( $operator ) {
                    case 'is':
                        if ( $condition_met ) {
                            return true;
                        }
                        break;
                        
                    case 'is_not':
                        if ( $condition_met ) {
                            return false; // If user IS in this country, "is_not" should be false
                        }
                        break;
                        
                    case 'contains':
                        // For contains, check partial matches in country name
                        if ( $condition_met || stripos( $user_country_name, strtolower( $target_country ) ) !== false ) {
                            return true;
                        }
                        break;
                        
                    case 'not_contains':
                        // For not_contains, if there's any match, return false
                        if ( $condition_met || stripos( $user_country_name, strtolower( $target_country ) ) !== false ) {
                            return false;
                        }
                        break;
                }
            }
            
            // Return true for negative operators if no matches found, false for positive operators
            return in_array( $operator, ['is_not', 'not_contains'] );
        }

        private function evaluate_continent_condition($operator, $values) {
            $user_location = $this->get_user_geolocation();

            if ( empty( $user_location['continent'] ) ) {
                return $operator === 'is_not'; // Default behavior when geolocation fails
            }
            
            $user_continent = strtolower( trim( $user_location['continent'] ) );

            
            foreach ( $values as $value_obj ) {
                $target_continent = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_continent ) ) {
                    continue;
                }
                
                // Convert 2-letter continent code to continent name if needed
                if ( strlen( $target_continent ) === 2 ) {
                    $target_continent = $this->continent_code_to_name( $target_continent );
                } else {
                    $target_continent = strtolower( trim( $target_continent ) );
                }
                
                switch ( $operator ) {
                    case 'is':
                        if ( $user_continent === $target_continent ) {
                            return true;
                        }
                        break;
                        
                    case 'is_not':
                        if ( $user_continent === $target_continent ) {
                            return false; // If user IS in this continent, "is_not" should be false
                        }
                        break;
                        
                    case 'contains':
                        if ( strpos( $user_continent, $target_continent ) !== false ) {
                            return true;
                        }
                        break;
                        
                    case 'not_contains':
                        if ( strpos( $user_continent, $target_continent ) !== false ) {
                            return false; // If continent contains the target, "not_contains" should be false
                        }
                        break;
                }
            }
            
            // Return true for negative operators if no matches found, false for positive operators
            return in_array( $operator, ['is_not', 'not_contains'] );
        }

        private function evaluate_session_condition($operator, $values) {
            // Start session if not already started
            if ( session_status() === PHP_SESSION_NONE ) {
                session_start();
            }

            foreach ( $values as $value_obj ) {
                $input = isset( $value_obj['value'] ) ? sanitize_text_field( $value_obj['value'] ) : sanitize_text_field( $value_obj );
                
                if ( empty( $input ) ) {
                    continue;
                }

                $condition_met = false;
                
                // Parse input format: "session_key" or "session_key=expected_value"
                if ( strpos( $input, '=' ) !== false ) {
                    // Format: "session_key=expected_value"
                    list( $session_key, $expected_value ) = explode( '=', $input, 2 );
                    $session_key = trim( sanitize_text_field( $session_key ) );
                    $expected_value = trim( sanitize_text_field( $expected_value ) );
                    
                    // Check if session variable exists and has expected value
                    $session_exists = isset( $_SESSION[ $session_key ] );
                    $actual_value = $session_exists ? sanitize_text_field( $_SESSION[ $session_key ] ) : '';
                    
                    switch ( $operator ) {
                        case 'exists':
                            $condition_met = $session_exists && ( $actual_value === $expected_value );
                            break;
                        case 'not_exists':
                            $condition_met = !$session_exists || ( $actual_value !== $expected_value );
                            break;
                        case 'is':
                            $condition_met = $session_exists && ( strcasecmp( $actual_value, $expected_value ) === 0 );
                            break;
                        case 'is_not':
                            $condition_met = !$session_exists || ( strcasecmp( $actual_value, $expected_value ) !== 0 );
                            break;
                        case 'contains':
                            $condition_met = $session_exists && ( stripos( $actual_value, $expected_value ) !== false );
                            break;
                        case 'not_contains':
                            $condition_met = !$session_exists || ( stripos( $actual_value, $expected_value ) === false );
                            break;
                    }
                } else {
                    // Format: "session_key" (checking session variable existence only)
                    $session_key = trim( sanitize_text_field( $input ) );
                    $session_exists = isset( $_SESSION[ $session_key ] );
                    $actual_value = $session_exists ? sanitize_text_field( $_SESSION[ $session_key ] ) : '';
                    
                    switch ( $operator ) {
                        case 'exists':
                        case 'is':
                            $condition_met = $session_exists && !empty( $_SESSION[ $session_key ] );
                            break;
                        case 'not_exists':
                        case 'is_not':
                            $condition_met = !$session_exists || empty( $_SESSION[ $session_key ] );
                            break;
                        case 'contains':
                            // For contains without value, check if session key exists
                            $condition_met = $session_exists;
                            break;
                        case 'not_contains':
                            // For not_contains without value, check if session key doesn't exist
                            $condition_met = !$session_exists;
                            break;
                    }
                }

                // Return true immediately if condition is met
                if ( $condition_met ) {
                    return true;
                }
            }

            // If we get here, none of the conditions matched
            return false; // Return false for ALL operators when no matches found
        }

        private function evaluate_taxonomy_term_condition($operator, $values) {
            // Only works on taxonomy archives or posts with terms
            if ( ! is_tax() && ! is_category() && ! is_tag() && ! is_singular() ) {
                return $operator === 'is_not';
            }
            
            // Track if any valid check was performed
            $any_valid_check = false;
            
            foreach ( $values as $value_obj ) {
                $target_term = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_term ) ) {
                    continue;
                }
                
                // We now have at least one valid check
                $any_valid_check = true;
                $condition_met = false;
                
                if ( is_tax() || is_category() || is_tag() ) {
                    // On taxonomy archive pages
                    $current_term = get_queried_object();
                    if ( $current_term && isset( $current_term->term_id ) ) {
                        // Handle different term ID formats
                        if ( is_numeric( $target_term ) ) {
                            $condition_met = ( $current_term->term_id == $target_term );
                        } elseif ( strpos( $target_term, 'term-' ) === 0 ) {
                            $target_id = (int) str_replace( 'term-', '', $target_term );
                            $condition_met = ( $current_term->term_id == $target_id );
                        } else {
                            // Compare by slug or name
                            $condition_met = ( $current_term->slug === $target_term || $current_term->name === $target_term );
                        }
                    }
                } else {
                    // On singular posts - check if post has this term
                    global $post;
                    if ( $post && isset( $post->ID ) ) {
                        // Get all terms for this post across all taxonomies
                        $target_taxonomy = isset( $value_obj['taxonomy'] ) ? $value_obj['taxonomy'] : null;
                        $post_terms = wp_get_post_terms( $post->ID, $target_taxonomy ?: get_taxonomies(), [ 'fields' => 'all' ] );
                        
                        if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {
                            foreach ( $post_terms as $term ) {
                                if ( is_numeric( $target_term ) ) {
                                    if ( $term->term_id == $target_term ) {
                                        $condition_met = true;
                                        break;
                                    }
                                } elseif ( strpos( $target_term, 'term-' ) === 0 ) {
                                    $target_id = (int) str_replace( 'term-', '', $target_term );
                                    if ( $term->term_id == $target_id ) {
                                        $condition_met = true;
                                        break;
                                    }
                                } else {
                                    // Compare by slug or name
                                    if ( $term->slug === $target_term || $term->name === $target_term ) {
                                        $condition_met = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                
                // For "is" operator, return true immediately on first match
                if ( $operator === 'is' && $condition_met ) {
                    return true;
                }
                
                // For "is_not" operator, return false immediately if a match is found
                if ( $operator === 'is_not' && $condition_met ) {
                    return false;
                }
            }
            
            // If we performed at least one valid check:
            // - For "is" operator: no matches were found, so return false
            // - For "is_not" operator: no matches were found, so return true
            if ( $any_valid_check ) {
                return $operator === 'is_not';
            }
            
            // If no valid checks were performed (e.g., all empty values)
            return $operator === 'is_not';
        }

        private function evaluate_page_url_condition($operator, $values) {
            // Get current full URL
            $current_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            foreach ( $values as $value_obj ) {
                $target_url = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_url ) ) {
                    continue;
                }
                
                switch ( $operator ) {
                    case 'is':
                        if ( $current_url === $target_url ) {
                            return true;
                        }
                        break;
                    case 'is_not':
                        if ( $current_url == $target_url ) {
                            return false;
                        }
                        break;
                    case 'contains':
                        if ( strpos( $current_url, $target_url ) !== false ) {
                            return true;
                        }
                        break;
                    case 'not_contains':
                        if ( strpos( $current_url, $target_url ) ) {
                            return false;
                        }
                        break;
                    case 'starts_with':
                        if(function_exists('str_starts_with')){
                            if(str_starts_with( $current_url, $target_url )){
                                return true;
                            }
                        }else{
                            return substr( $current_url, 0, strlen( $target_url ) ) === $target_url;
                        }
                        break;
                    case 'ends_with':
                        if(function_exists('str_ends_with')){
                            if(str_ends_with( $current_url, $target_url )){
                                return true;
                            }
                        }else{
                            return substr( $current_url, -strlen( $target_url ) ) === $target_url;
                        }
                        break;
                }
            }
            
            return in_array( $operator, ['is_not', 'not_contains'] );
        }

        private function evaluate_post_meta_condition($operator, $values) {
            global $post;
            
            if ( ! is_singular() || ! $post ) {
                return in_array( $operator, ['is_not', 'not_contains', 'not_exists', 'is_empty'] );
            }
            
            foreach ( $values as $value_obj ) {
                $input = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $input ) ) {
                    continue;
                }
                
                // Parse input: "meta_key" or "meta_key=meta_value"
                if ( strpos( $input, '=' ) !== false ) {
                    list( $meta_key, $expected_value ) = explode( '=', $input, 2 );
                    $meta_key = trim( $meta_key );
                    $expected_value = trim( $expected_value );
                } else {
                    $meta_key = trim( $input );
                    $expected_value = '';
                }
                
                if ( empty( $meta_key ) ) {
                    continue;
                }
                
                // Get both the single value and all values for the meta key
                $meta_value_single = get_post_meta( $post->ID, $meta_key, true );
                $meta_values_all = get_post_meta( $post->ID, $meta_key, false );
                
                switch ( $operator ) {
                    case 'exists':
                        if ( metadata_exists( 'post', $post->ID, $meta_key ) ) {
                            return true;
                        }
                        break;
                    case 'not_exists':
                        if ( ! metadata_exists( 'post', $post->ID, $meta_key ) ) {
                            return true;
                        }
                        break;
                    case 'is_empty':
                        if ( empty( $meta_value_single ) ) {
                            return true;
                        }
                        break;
                    case 'is_not_empty':
                        if ( ! empty( $meta_value_single ) ) {
                            return true;
                        }
                        break;
                    case 'is':
                        if ( empty( $expected_value ) ) {
                            if ( ! empty( $meta_value_single ) ) {
                                return true;
                            }
                        } else {
                            // First check direct string comparison
                            if ( $meta_value_single == $expected_value ) {
                                return true;
                            }
                            
                            // Then check if the meta value is an array and contains the expected value
                            if ( is_array( $meta_values_all ) ) {
                                // Check if the expected value exists as an exact array element
                                if ( in_array( $expected_value, $meta_values_all ) ) {
                                    return true;
                                }
                                
                                // Also check if any array element as a string equals the expected value
                                foreach ( $meta_values_all as $val ) {
                                    if ( (string)$val == (string)$expected_value ) {
                                        return true;
                                    }
                                }
                            }
                        }
                        break;
                    case 'is_not':
                        if ( empty( $expected_value ) ) {
                            if ( empty( $meta_value_single ) ) {
                                return true;
                            }
                        } else {
                            // Check both single value and array format
                            if ( $meta_value_single != $expected_value ) {
                                // Only return true if the value doesn't exist in the array either
                                if ( !is_array( $meta_values_all ) || !in_array( $expected_value, $meta_values_all ) ) {
                                    return true;
                                }
                            }
                        }
                        break;
                    case 'contains':
                        if ( ! empty( $expected_value ) ) {
                            // Check if single value contains the expected value
                            if ( is_string( $meta_value_single ) && strpos( $meta_value_single, $expected_value ) !== false ) {
                                return true;
                            }
                            // Also check if any value in the array contains the expected value
                            if ( is_array( $meta_values_all ) ) {
                                foreach ( $meta_values_all as $val ) {
                                    if ( is_string( $val ) && strpos( $val, $expected_value ) !== false ) {
                                        return true;
                                    }
                                }
                            }
                        }
                        break;
                    case 'not_contains':
                        if ( ! empty( $expected_value ) ) {
                            $contains = false;
                            
                            // Check if single value contains the expected value
                            if ( is_string( $meta_value_single ) && strpos( $meta_value_single, $expected_value ) !== false ) {
                                $contains = true;
                            }
                            
                            // Also check if any value in the array contains the expected value
                            if ( !$contains && is_array( $meta_values_all ) ) {
                                foreach ( $meta_values_all as $val ) {
                                    if ( is_string( $val ) && strpos( $val, $expected_value ) !== false ) {
                                        $contains = true;
                                        break;
                                    }
                                }
                            }
                            
                            if ( !$contains ) {
                                return true;
                            }
                        } elseif ( empty( $meta_value_single ) && empty( $meta_values_all ) ) {
                            return true;
                        }
                        break;
                }
            }
            
            return in_array( $operator, ['is_not', 'not_contains', 'not_exists', 'is_empty'] );
        }

            private function evaluate_post_status_condition($operator, $values) {
        global $post;
        
        if ( ! $post ) {
            return $operator === 'is_not';
        }
        
        $current_status = get_post_status( $post->ID );

        // Handle empty values
        if (empty($values)) {
            return $operator === 'is_not';
        }
        
        // Special handling for 'is_not' operator
        if ($operator === 'is_not') {
            // For "is_not", return false if ANY value matches
            foreach ( $values as $value_obj ) {
                $target_status = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_status ) ) {
                    continue;
                }
                
                if ( $current_status === $target_status ) {
                    return false; // Found a match, so "is_not" should be false
                }
            }
            return true; // No matches found, so "is_not" is true
        }
        // Standard handling for 'is' operator
        else {
            // For "is", return true if ANY value matches
            foreach ( $values as $value_obj ) {
                $target_status = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_status ) ) {
                    continue;
                }
                
                if ( $current_status === $target_status ) {
                    return true; // Found a match for 'is'
                }
            }
            return false; // No matches found for 'is'
        }
    }

        private function evaluate_page_template_condition($operator, $values) {
            if ( ! is_page() ) {
                return $operator === 'is_not';
            }
            
            $current_template = get_page_template_slug();

            if ( empty( $current_template ) ) {
                $current_template = 'default';
            }
            
            foreach ( $values as $value_obj ) {
                $target_template = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_template ) ) {
                    continue;
                }
                
                if ( $operator === 'is' && $current_template === $target_template ) {
                    return true;
                } elseif ( $operator === 'is_not' && $current_template === $target_template ) {
                    return false;
                }
            }
            
            return $operator === 'is_not';
        }

        private function evaluate_author_condition($operator, $values) {
            global $post;
            
            // Check if we're on a singular page or author archive
            if ( ( ! is_singular() && ! is_author() ) || ! $post ) {
                return false;
            }
            
            // Get current author ID
            $current_author_id = $post->post_author;
            
            // If no values to check, return true for "is_not" and false for "is"
            if (empty($values)) {
                return $operator === 'is_not';
            }
            
            // Flag to track if we find any author match
            $found_match = false;
            
            foreach ( $values as $value_obj ) {
                // Handle both array format and string format
                $target_author = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_author ) ) {
                    continue;
                }
                
                $condition_met = false;
                
                // Handle different author ID formats
                if ( is_numeric( $target_author ) ) {
                    // Direct numeric ID
                    $condition_met = ( (int)$current_author_id === (int)$target_author );
                } elseif ( strpos( $target_author, 'user-' ) === 0 ) {
                    // "user-123" format from author select field
                    $target_id = (int) str_replace( 'user-', '', $target_author );
                    $condition_met = ( (int)$current_author_id === $target_id );
                } else {
                    // Username or display name comparison (for legacy support)
                    $author = get_userdata( $current_author_id );
                    if ( $author ) {
                        $condition_met = ( $author->user_login === $target_author || $author->display_name === $target_author );
                    }
                }
                
                // If condition is met, update our match flag
                if ($condition_met) {
                    $found_match = true;
                    
                    // For "is" operator, we can return true immediately on first match
                    if ($operator === 'is') {
                        return true;
                    }
                }
            }
            
            // Return results based on operator and whether we found a match
            if ($operator === 'is') {
                return $found_match; // Must have found at least one match
            } else { // is_not
                return !$found_match; // Must not have found any matches
            }
        }

        private function evaluate_url_path_condition($operator, $values) {
            $current_path = $_SERVER['REQUEST_URI'];
            $current_path = strtok( $current_path, '?' ); // Remove query parameters
            
            // Handle cases with empty values
            if (empty($values)) {
                return in_array($operator, ['is_not', 'not_contains']);
            }
            
            // Special handling for negative operators
            if ($operator === 'is_not') {
                // For "is_not", return false if ANY value matches
                foreach ($values as $value_obj) {
                    $target_path = isset($value_obj['value']) ? $value_obj['value'] : $value_obj;
                    
                    if (empty($target_path)) {
                        continue;
                    }
                    
                    if ($current_path === $target_path) {
                        return false; // Found a match, so "is_not" should be false
                    }
                }
                return true; // No matches found, so "is_not" is true
            }
            else if ($operator === 'not_contains') {
                // For "not_contains", return false if ANY value is contained
                foreach ($values as $value_obj) {
                    $target_path = isset($value_obj['value']) ? $value_obj['value'] : $value_obj;
                    
                    if (empty($target_path)) {
                        continue;
                    }
                    
                    if (strpos($current_path, $target_path) !== false) {
                        return false; // Found a match, so "not_contains" should be false
                    }
                }
                return true; // No matches found, so "not_contains" is true
            }
            
            // Handle positive operators - standard way
            foreach ($values as $value_obj) {
                $target_path = isset($value_obj['value']) ? $value_obj['value'] : $value_obj;
                
                if (empty($target_path)) {
                    continue;
                }
                
                switch ($operator) {
                    case 'is':
                        if ($current_path === $target_path) {
                            return true;
                        }
                        break;
                    case 'contains':
                        if (strpos($current_path, $target_path) !== false) {
                            return true;
                        }
                        break;
                    case 'starts_with':
                        if (strpos($current_path, $target_path) === 0) {
                            return true;
                        }
                        break;
                    case 'ends_with':
                        if (substr($current_path, -strlen($target_path)) === $target_path) {
                            return true;
                        }
                        break;
                }
            }
            
            return false; // Default return for positive operators if no matches
        }

        private function evaluate_http_referrer_condition($operator, $values) {
            $referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
            
            foreach ( $values as $value_obj ) {
                $target_referrer = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_referrer ) ) {
                    continue;
                }
                
                switch ( $operator ) {
                    case 'is':
                        if ( $referrer === $target_referrer ) {
                            return true;
                        }
                        break;
                    case 'is_not':
                        if ( $referrer === $target_referrer ) {
                            return false;
                        }
                        break;
                    case 'contains':
                        if ( ! empty( $referrer ) && strpos( $referrer, $target_referrer ) !== false ) {
                            return true;
                        }
                        break;
                    case 'not_contains':
                        if ( ! empty( $referrer ) && strpos( $referrer, $target_referrer ) !== false ) {
                            return false;
                        }
                        break;
                    case 'exists':
                        if ( ! empty( $referrer ) ) {
                            return true;
                        }
                        break;
                    case 'not_exists':
                        if ( empty( $referrer ) ) {
                            return true;
                        }
                        break;
                }
            }
            
            return in_array( $operator, ['is_not', 'not_contains', 'not_exists'] );
        }

        private function evaluate_user_agent_condition($operator, $values) {
            $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';

            // Handle empty values
            if (empty($values)) {
                return in_array($operator, ['is_not', 'not_contains']);
            }
            
            // Special handling for negative operators
            if ($operator === 'is_not') {
                // For "is_not", return false if ANY value matches
                foreach ($values as $value_obj) {
                    $target_agent = isset($value_obj['value']) ? $value_obj['value'] : $value_obj;
                    
                    if (empty($target_agent)) {
                        continue;
                    }
                    
                    if ($user_agent === $target_agent) {
                        return false; // Found a match, so "is_not" should be false
                    }
                }
                return true; // No matches found, so "is_not" is true
            }
            else if ($operator === 'not_contains') {
                // For "not_contains", return false if ANY value is contained
                foreach ($values as $value_obj) {
                    $target_agent = isset($value_obj['value']) ? $value_obj['value'] : $value_obj;
                    
                    if (empty($target_agent)) {
                        continue;
                    }
                    
                    if (stripos($user_agent, $target_agent) !== false) {
                        return false; // Found a match, so "not_contains" should be false
                    }
                }
                return true; // No matches found, so "not_contains" is true
            }
            
            // Handle positive operators - standard way
            foreach ($values as $value_obj) {
                $target_agent = isset($value_obj['value']) ? $value_obj['value'] : $value_obj;
                
                if (empty($target_agent)) {
                    continue;
                }
                
                switch ($operator) {
                    case 'is':
                        if ($user_agent === $target_agent) {
                            return true;
                        }
                        break;
                    case 'contains':
                        if (stripos($user_agent, $target_agent) !== false) {
                            return true;
                        }
                        break;
                }
            }
            
            return false; // Default return for positive operators if no matches
        }

        private function evaluate_query_string_condition($operator, $values) {
            $query_string = isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : '';
            
            foreach ( $values as $value_obj ) {
                $target_query = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_query ) ) {
                    continue;
                }
                
                // Handle format: "param" or "param=value"
                if ( strpos( $target_query, '=' ) !== false ) {
                    list( $param_name, $expected_value ) = explode( '=', $target_query, 2 );
                    $param_name = trim( $param_name );
                    $expected_value = trim( $expected_value );
                    
                    $actual_value = isset( $_GET[ $param_name ] ) ? $_GET[ $param_name ] : '';
                    
                    switch ( $operator ) {
                        case 'is':
                            if ( $actual_value === $expected_value ) {
                                return true;
                            }
                            break;
                        case 'is_not':
                            if ( $actual_value !== $expected_value ) {
                                return true;
                            }
                            break;
                        case 'contains':
                            if ( strpos( $actual_value, $expected_value ) !== false ) {
                                return true;
                            }
                            break;
                        case 'not_contains':
                            if ( strpos( $actual_value, $expected_value ) === false ) {
                                return true;
                            }
                            break;
                    }
                } else {
                    // Just checking parameter existence
                    $param_exists = isset( $_GET[ $target_query ] );
                    
                    if ( $operator === 'exists' && $param_exists ) {
                        return true;
                    } elseif ( $operator === 'not_exists' && ! $param_exists ) {
                        return true;
                    }
                }
            }
            
            return in_array( $operator, ['is_not', 'not_contains', 'not_exists'] );
        }

        private function evaluate_date_condition($operator, $values) {
            $current_date = current_time( 'Y-m-d' );
            
            foreach ( $values as $value_obj ) {
                $target_date = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_date ) ) {
                    continue;
                }
                
                switch ( $operator ) {
                    case 'is':
                        if ( $current_date === $target_date ) {
                            return true;
                        }
                        break;
                    case 'is_not':
                        if ( $current_date === $target_date ) {
                            return false;
                        }
                        break;
                    case 'before':
                        if ( $current_date < $target_date ) {
                            return true;
                        }
                        break;
                    case 'after':
                        if ( $current_date > $target_date ) {
                            return true;
                        }
                        break;
                }
            }
            
            return false;
        }

        private function evaluate_date_time_condition($operator, $values) {
            // Use Y-m-d H:i format to ignore seconds, making comparisons more practical
            $current_datetime = current_time('Y-m-d H:i');
            
            // Special handling for 'is_not' operator
            if ($operator === 'is_not') {
                // For "is_not", return false if ANY value matches
                foreach ($values as $value_obj) {
                    $target_datetime = isset($value_obj['value']) ? $value_obj['value'] : $value_obj;
                    
                    if (empty($target_datetime)) {
                        continue;
                    }
                    
                    // Ensure target datetime also ignores seconds for consistent comparison
                    if (strlen($target_datetime) > 16) { // if it has seconds component (Y-m-d H:i:s)
                        $target_datetime = substr($target_datetime, 0, 16); // keep only Y-m-d H:i
                    }
                    
                    if ($current_datetime === $target_datetime) {
                        return false; // Found a match, so "is_not" should be false
                    }
                }
                return true; // No matches found, so "is_not" is true
            }
            // Handle other operators (is, before, after)
            else {
                foreach ($values as $value_obj) {
                    $target_datetime = isset($value_obj['value']) ? $value_obj['value'] : $value_obj;
                    
                    if (empty($target_datetime)) {
                        continue;
                    }
                    
                    // Ensure target datetime also ignores seconds for consistent comparison
                    if (strlen($target_datetime) > 16) { // if it has seconds component (Y-m-d H:i:s)
                        $target_datetime = substr($target_datetime, 0, 16); // keep only Y-m-d H:i
                    }
                    
                    switch ($operator) {
                        case 'is':
                            if ($current_datetime === $target_datetime) {
                                return true;
                            }
                            break;
                        case 'before':
                            if ($current_datetime < $target_datetime) {
                                return true;
                            }
                            break;
                        case 'after':
                            if ($current_datetime > $target_datetime) {
                                return true;
                            }
                            break;
                    }
                }
                return false; // No matches found for other operators
            }
        }

        private function evaluate_time_condition($operator, $values) {
            $current_time = current_time( 'H:i' );
            
            foreach ( $values as $value_obj ) {
                $target_time = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;
                
                if ( empty( $target_time ) ) {
                    continue;
                }
                
                switch ( $operator ) {
                    case 'is':
                        if ( $current_time === $target_time ) {
                            return true;
                        }
                        break;
                    case 'is_not':
                        if ( $current_time === $target_time ) {
                            return false;
                        }
                        break;
                    case 'before':
                        if ( $current_time < $target_time ) {
                            return true;
                        }
                        break;
                    case 'after':
                        if ( $current_time > $target_time ) {
                            return true;
                        }
                        break;
                }
            }
            
            return $operator === 'is_not';
        }

        /**
         * Globally track a snippet ID across different rendering contexts
         * 
         * @param int|array $snippet_id Snippet ID or array with 'value' key
         * @param string|null $type Snippet type (optional)
         * @return bool Whether the snippet was already tracked
         */
        public function global_track_snippet($snippet_id, $type = null) {
            // Normalize snippet ID
            if (is_array($snippet_id) && isset($snippet_id['value'])) {
                $snippet_id = $snippet_id['value'];
            }
            $snippet_id = intval($snippet_id);

            // If no type provided, try to get from post meta
            if ($type === null) {
                $type = get_post_meta($snippet_id, 'nxt-code-type', true);
            }

            // Validate type
            $valid_types = ['css', 'javascript', 'php', 'htmlmixed'];
            if (!in_array($type, $valid_types)) {
                $type = 'php'; // Default fallback
            }

            // Ensure the type's array exists
            if (!isset(self::$snippet_loaded_ids_pro[$type]) || !is_array(self::$snippet_loaded_ids_pro[$type])) {
                self::$snippet_loaded_ids_pro[$type] = [];
            }

            // Check if snippet is already tracked
            $is_tracked = false;
            foreach (self::$snippet_loaded_ids_pro[$type] as $tracked_id) {
                // Handle both integer and array inputs
                $current_id = is_array($tracked_id) ? 
                    (isset($tracked_id['value']) ? $tracked_id['value'] : 0) : 
                    $tracked_id;
                
                if (intval($current_id) === $snippet_id) {
                    $is_tracked = true;
                    break;
                }
            }

            // Add if not already tracked
            if (!$is_tracked) {
                self::$snippet_loaded_ids_pro[$type][] = $snippet_id;
            }

            return $is_tracked;
        }

        /**
         * Track a pro snippet during rendering
         * 
         * @param int $snippet_id Snippet ID
         * @param string|null $type Snippet type
         */
        private function track_pro_snippet($snippet_id, $type = null) {
            return $this->global_track_snippet($snippet_id, $type);
        }

        /**
         * Manually track a snippet ID
         * 
         * @param int|array $snippet_id Snippet ID or array with 'value' key
         * @param string|null $type Snippet type (optional)
         * @return bool Whether the snippet was already tracked
         */
        public function track_snippet_manually($snippet_id, $type = null) {
            return $this->global_track_snippet($snippet_id, $type);
        }

        private function evaluate_snippet_loaded_condition($operator, $values) {
            // Normalize values to extract snippet IDs
            $snippet_ids = array_map(function($value) {
                // If it's an array with 'value' key, extract the ID
                if (is_array($value) && isset($value['value'])) {
                    return intval($value['value']);
                    }
                // If it's already an integer, use it directly
                return intval($value);
            }, (array)$values);

            // Get loaded snippet IDs from Pro plugin
            $loaded_snippet_ids = array();
            foreach (['css', 'javascript', 'php', 'htmlmixed'] as $type) {
                if (!empty(self::$snippet_loaded_ids_pro[$type])) {
                    // Normalize loaded snippet IDs
                    $type_loaded_ids = array_map(function($loaded_snippet) {
                        // If it's an array with 'value' key, extract the ID
                        if (is_array($loaded_snippet) && isset($loaded_snippet['value'])) {
                            return intval($loaded_snippet['value']);
                        }
                        // If it's already an integer, use it directly
                        return intval($loaded_snippet);
                    }, self::$snippet_loaded_ids_pro[$type]);
            
                    $loaded_snippet_ids = array_merge($loaded_snippet_ids, $type_loaded_ids);
                }
            }

            // Attempt to track any untracked snippets
            foreach ($snippet_ids as $snippet_id) {
                // Try to track the snippet if not already tracked
                $this->global_track_snippet($snippet_id);
            }

            // Refresh loaded snippet IDs after tracking
            $loaded_snippet_ids = array();
            foreach (['css', 'javascript', 'php', 'htmlmixed'] as $type) {
                if (!empty(self::$snippet_loaded_ids_pro[$type])) {
                    $type_loaded_ids = array_map(function($loaded_snippet) {
                        if (is_array($loaded_snippet) && isset($loaded_snippet['value'])) {
                            return intval($loaded_snippet['value']);
                        }
                        return intval($loaded_snippet);
                    }, self::$snippet_loaded_ids_pro[$type]);

                    $loaded_snippet_ids = array_merge($loaded_snippet_ids, $type_loaded_ids);
                }
            }

            // Determine result based on operator
            switch ($operator) {
                case 'is_loaded':
                    // Check if ALL specified snippet IDs are loaded
                    $intersected_ids = array_intersect($snippet_ids, $loaded_snippet_ids);
                    $result = count($intersected_ids) === count($snippet_ids);
                    return $result;
                case 'is_not_loaded':
                    // Check if NONE of the specified snippet IDs are loaded
                    $intersected_ids = array_intersect($snippet_ids, $loaded_snippet_ids);
                    $result = count($intersected_ids) === 0;
                    return $result;
                default:
                    return false;
            }
        }

        private function evaluate_ip_address_condition($operator, $values) {
            $user_ip = $this->get_user_ip_address();
            
            if ( empty( $user_ip ) ) {
                return in_array( $operator, ['is_not', 'not_contains'] );
            }

            foreach ( $values as $value_obj ) {
                $ip_condition = isset( $value_obj['value'] ) ? $value_obj['value'] : $value_obj;

                if ( empty( $ip_condition ) ) {
                    continue;
                }

                $condition_met = false;

                // Check for CIDR notation (e.g., 192.168.1.0/24)
                if ( strpos( $ip_condition, '/' ) !== false ) {
                    $condition_met = $this->ip_in_cidr_range( $user_ip, $ip_condition );
                }
                // Check for IP range with wildcard (e.g., 192.168.1.*)
                elseif ( strpos( $ip_condition, '*' ) !== false ) {
                    $pattern = str_replace( '*', '.*', preg_quote( $ip_condition, '/' ) );
                    $condition_met = (bool) preg_match( '/^' . $pattern . '$/', $user_ip );
                }
                // Check for partial IP match for contains/not_contains
                elseif ( $operator === 'contains' || $operator === 'not_contains' ) {
                    $condition_met = strpos( $user_ip, $ip_condition ) !== false;
                }
                // Check for exact IP match
                else {
                    $condition_met = ( $user_ip === $ip_condition );
                }

                // Modify the logic to handle 'not_contains' more explicitly
                if ( $operator === 'is' && $condition_met ) {
                    return true;
                } elseif ( $operator === 'is_not' && ! $condition_met ) {
                    return true;
                } elseif ( $operator === 'contains' && $condition_met ) {
                    return true;
                } elseif ( $operator === 'not_contains' ) {
                    // For 'not_contains', return false if the IP contains the condition
                    if ( $condition_met ) {
                        return false;
                    }
                }
            }

            // Default behavior for 'not_contains' is true if no match is found
            return $operator === 'not_contains';
        }

        /**
         * Check if an IP address is within a CIDR range
         */
        private function ip_in_cidr_range( $ip, $cidr ) {
            list( $subnet, $mask ) = explode( '/', $cidr );
            
            if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) && filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
                // IPv4
                return ( ip2long( $ip ) & ~((1 << (32 - $mask)) - 1) ) === ip2long( $subnet );
            } elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) && filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
                // IPv6 (simplified check)
                $ip_bin = inet_pton( $ip );
                $subnet_bin = inet_pton( $subnet );
                $mask_bin = str_repeat( 'f', $mask / 4 ) . dechex( 0xf0 << (4 - ($mask % 4)) ) . str_repeat( '0', (128 - $mask) / 4 );
                return ($ip_bin & pack('H*', $mask_bin)) === $subnet_bin;
            }
            
            return false;
        }

        /**
         * Handle CSS selector locations - determines if location is a Pro CSS selector location
         */
        public function handle_css_selector_locations($location) {
            $css_selector_locations = [
                'before_html_element',
                'after_html_element', 
                'start_html_element',
                'end_html_element',
                'replace_html_element'
            ];
            
            return in_array($location, $css_selector_locations);
        }

        /**
         * Get CSS selector snippets for Pro locations
         */
        public function get_css_selector_snippets($default_snippets, $snippet_type) {
            global $wpdb;
            
            $css_selector_locations = $this->get_css_selector_locations();
            
            if (empty($css_selector_locations)) {
                return $default_snippets;
            }
            
            $locations_placeholder = implode(',', array_fill(0, count($css_selector_locations), '%s'));
            
            $query = $wpdb->prepare("
                SELECT p.ID
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'nxt-code-location'
                INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'nxt-css-selector'
                WHERE p.post_type = %s
                AND p.post_status = 'publish'
                AND pm1.meta_value IN ($locations_placeholder)
                AND pm2.meta_value != ''
            ", array_merge([$snippet_type], $css_selector_locations));
            
            $snippets = $wpdb->get_results($query);
            
            if (empty($snippets)) {
                return $default_snippets;
            }
            
            $css_selector_snippets = array();
            
            foreach ($snippets as $snippet) {
                $snippet_id = $snippet->ID;
                
                // Check if snippet should execute based on conditions
                if (!$this->should_snippet_execute($snippet_id)) {
                    continue;
                }
                
                $location = get_post_meta($snippet_id, 'nxt-code-location', true);
                $css_selector = get_post_meta($snippet_id, 'nxt-css-selector', true);
                $element_index = get_post_meta($snippet_id, 'nxt-element-index', true);
                $type = get_post_meta($snippet_id, 'nxt-code-type', true);
                
                if (!empty($css_selector)) {
                    $position = $this->map_css_location_to_position($location);
                    
                    // Convert 1-based user input to 0-based indexing for DOM library
                    $dom_index = -1; // Default to -1 (no specific index)
                    if (!empty($element_index)) {
                        $user_index = intval($element_index);
                        if ($user_index > 0) {
                            $dom_index = $user_index - 1; // Convert from 1-based to 0-based
                        }
                    }
                    
                    $css_selector_snippets[] = array(
                        'id' => $snippet_id,
                        'css_selector' => $css_selector,
                        'position' => $position,
                        'index' => $dom_index
                    );
                }
            }
            
            return $css_selector_snippets;
        }

        /**
         * Map CSS selector location to DOM manipulation position
         */
        public function map_css_location_to_position($location) {
            $mapping = [
                'before_html_element' => 'before',     // Before HTML Element
                'after_html_element' => 'after',       // After HTML Element  
                'start_html_element' => 'prepend',     // At the start of HTML Element
                'end_html_element' => 'append',        // At the end of HTML Element
                'replace_html_element' => 'replace'    // Replace HTML Element
            ];
            
            return isset($mapping[$location]) ? $mapping[$location] : 'after';
        }

        /**
         * Check if a snippet should execute (schedule, conditional logic, location context checks)
         */
        private function should_snippet_execute($snippet_id) {
            // Schedule condition check
            $should_skip_schedule = $this->check_schedule_restrictions(false, $snippet_id);
            if ($should_skip_schedule) {
                return false;
            }

            // Smart Conditional Logic check - handled by existing system
            $smart_conditions = get_post_meta($snippet_id, 'nxt-smart-conditional-logic', true);
            if (!empty($smart_conditions) && class_exists('Nexter_Builder_Display_Conditional_Rules')) {
                if (!Nexter_Builder_Display_Conditional_Rules::evaluate_smart_conditional_logic($smart_conditions)) {
                    return false; // Skip this snippet, Smart Conditional Logic not met
                }
            }

            // Status check
            $is_active = get_post_meta($snippet_id, 'nxt-code-status', true);
            if ($is_active != '1') {
                return false;
            }

            // Location context check - ensure snippet should load on current page
            $location = get_post_meta($snippet_id, 'nxt-code-location', true);
            if (!empty($location) && !$this->should_snippet_load_on_current_page($location)) {
                return false;
            }

            return true;
        }

        /**
         * Check if a snippet should load on current page based on its location
         */
        private function should_snippet_load_on_current_page($location) {
            // Empty location means old system snippets - always load
            if (empty($location)) {
                return true;
            }

            // Check Pro-only locations first
            if (in_array($location, $this->get_pro_only_locations())) {
                return true; // Pro locations are validated by the Pro plugin's own logic
            }

            // Check global locations - these should always load
            if (class_exists('Nexter_Global_Code_Handler') && Nexter_Global_Code_Handler::is_global_location($location)) {
                return true;
            }

            // Check eCommerce locations
            if (class_exists('Nexter_ECommerce_Code_Handler') && Nexter_ECommerce_Code_Handler::is_ecommerce_location($location)) {
                return $this->validate_ecommerce_location($location);
            }

            // Check page-specific locations
            if (class_exists('Nexter_Page_Specific_Code_Handler') && Nexter_Page_Specific_Code_Handler::is_page_specific_location($location)) {
                return $this->validate_page_specific_location($location);
            }

            // Default to true for unknown locations (backwards compatibility)
            return true;
        }

        /**
         * Validate eCommerce location against current page context
         */
        private function validate_ecommerce_location($location) {
            // Check if required plugin is active
            if (class_exists('Nexter_ECommerce_Code_Handler')) {
                // Check WooCommerce locations
                if (Nexter_ECommerce_Code_Handler::is_woocommerce_location($location)) {
                    if (!Nexter_ECommerce_Code_Handler::is_woocommerce_active()) {
                        return false; // WooCommerce not active
                    }
                    return $this->validate_woocommerce_location($location);
                }

                // Check EDD locations
                if (Nexter_ECommerce_Code_Handler::is_edd_location($location)) {
                    if (!Nexter_ECommerce_Code_Handler::is_edd_active()) {
                        return false; // EDD not active
                    }
                    return $this->validate_edd_location($location);
                }

                // Check MemberPress locations
                if (Nexter_ECommerce_Code_Handler::is_memberpress_location($location)) {
                    if (!Nexter_ECommerce_Code_Handler::is_memberpress_active()) {
                        return false; // MemberPress not active
                    }
                    return $this->validate_memberpress_location($location);
                }
            }

            return true;
        }

        /**
         * Validate WooCommerce location against current page context
         */
        private function validate_woocommerce_location($location) {
            // Shop/product listing locations
            if (strpos($location, 'shop') !== false || strpos($location, 'list_products') !== false) {
                return (function_exists('is_shop') && is_shop()) || 
                       (function_exists('is_product_category') && is_product_category()) || 
                       (function_exists('is_product_tag') && is_product_tag());
            }

            // Single product locations
            if (strpos($location, 'single_product') !== false) {
                return function_exists('is_product') && is_product();
            }

            // Cart locations
            if (strpos($location, 'cart') !== false) {
                return function_exists('is_cart') && is_cart();
            }

            // Checkout locations
            if (strpos($location, 'checkout') !== false) {
                return function_exists('is_checkout') && is_checkout();
            }

            // Thank you page locations
            if (strpos($location, 'thank_you') !== false) {
                return function_exists('is_order_received_page') && is_order_received_page();
            }

            return true; // Default for unknown WooCommerce locations
        }

        /**
         * Validate EDD location against current page context
         */
        private function validate_edd_location($location) {
            // Download locations
            if (strpos($location, 'download') !== false) {
                return function_exists('is_singular') && is_singular('download');
            }

            // Cart/checkout locations
            if (strpos($location, 'cart') !== false || strpos($location, 'checkout') !== false) {
                return function_exists('edd_is_checkout') && edd_is_checkout();
            }

            return true; // Default for unknown EDD locations
        }

        /**
         * Validate MemberPress location against current page context
         */
        private function validate_memberpress_location($location) {
            // Checkout locations
            if (strpos($location, 'checkout') !== false) {
                return function_exists('is_page') && function_exists('get_query_var') && 
                       is_page() && get_query_var('action') === 'checkout';
            }

            // Account locations
            if (strpos($location, 'account') !== false) {
                return function_exists('is_page') && function_exists('get_query_var') && 
                       is_page() && get_query_var('action') === 'account';
            }

            // Login locations
            if (strpos($location, 'login') !== false) {
                return function_exists('is_page') && function_exists('get_query_var') && 
                       is_page() && get_query_var('action') === 'login';
            }

            // Unauthorized message locations - always load as they're applied via filter
            if (strpos($location, 'unauthorized') !== false) {
                return true;
            }

            return true; // Default for unknown MemberPress locations
        }

        /**
         * Validate page-specific location against current page context
         */
        private function validate_page_specific_location($location) {
            // Archive-only locations
            $archive_only_locations = [
                'insert_before_excerpt',
                'insert_after_excerpt',
                'between_posts',
                'before_post',
                'after_post'
            ];

            // Singular-only locations (including advanced content insertions)
            $singular_only_locations = [
                'insert_before_content',
                'insert_after_content',
                'insert_before_paragraph',
                'insert_after_paragraph',
                'insert_before_post',
                'insert_after_post',
                // Advanced content insertion locations
                'insert_after_words',
                'insert_every_words',
                'insert_middle_content',
                'insert_after_25',
                'insert_after_33',
                'insert_after_66',
                'insert_after_75',
                'insert_after_80'
            ];

            // Check page context
            if (in_array($location, $archive_only_locations)) {
                return !is_singular(); // Show only on archive pages
            }

            if (in_array($location, $singular_only_locations)) {
                return is_singular(); // Show only on singular pages
            }

            return true; // Default for other page-specific locations
        }

        /**
         * Populate the snippet output array with CSS selector snippets
         */
        public function populate_css_snippet_output($default_output, $css_selector_snippets) {
            self::$css_selector_snippet_output = array();
            
            foreach ($css_selector_snippets as $snippet_data) {
                $snippet_id = $snippet_data['id'];
                $type = get_post_meta($snippet_id, 'nxt-code-type', true);
                
                // Get the code content
                $output = '';
                switch ($type) {
                    case 'htmlmixed':
                        $output = get_post_meta($snippet_id, 'nxt-htmlmixed-code', true);
                        break;
                    case 'css':
                        $css_code = get_post_meta($snippet_id, 'nxt-css-code', true);
                        if (!empty($css_code)) {
                            $output = '<style>' . wp_specialchars_decode($css_code) . '</style>';
                        }
                        break;
                    case 'javascript':
                        $js_code = get_post_meta($snippet_id, 'nxt-javascript-code', true);
                        if (!empty($js_code)) {
                            $output = '<script>' . html_entity_decode($js_code, ENT_QUOTES) . '</script>';
                        }
                        break;
                    case 'php':
                        $php_code = get_post_meta($snippet_id, 'nxt-php-code', true);
                        if (!empty($php_code)) {
                            // Execute PHP and capture output
                            $code_hidden_execute = get_post_meta($snippet_id, 'nxt-code-php-hidden-execute', true);
                            if ($code_hidden_execute === 'yes') {
                                ob_start();
                                
                                // Use existing PHP executor for safety
                                try {
                                    Nexter_Builder_Code_Snippets_Executor::get_instance()->execute_php_snippet($php_code, $snippet_id);
                                } catch (Exception $e) {
                                    // Handle PHP execution errors gracefully
                                    error_log('Nexter Pro: PHP snippet execution error for snippet ' . $snippet_id . ': ' . $e->getMessage());
                                }
                                
                                $output = ob_get_clean();
                            }
                        }
                        break;
                }
                
                if (!empty($output)) {
                    $snippet_entry = array(
                        'css_selector' => $snippet_data['css_selector'],
                        'location' => $snippet_data['position'],
                        'index' => $snippet_data['index'],
                        'output' => apply_filters('nexter_css_selector_snippet_output', $output, $snippet_id, $type)
                    );
                    
                    self::$css_selector_snippet_output[] = $snippet_entry;
                }
            }
            
            return self::$css_selector_snippet_output;
        }

        /**
         * Process CSS selector output buffer - handles the DOM manipulation
         */
        public function process_css_selector_output($output) {
            // Early return for empty output
            if (empty($output) || strlen(trim($output)) === 0) {
                return $output;
            }
            
            // Early return if no snippets to process
            if (empty(self::$css_selector_snippet_output)) {
                return $output;
            }
            
            // Ensure the Simple HTML DOM library is loaded
            $dom_file = '';
            
            // Use Pro plugin's local copy of Simple HTML DOM library
            if (defined('NXT_PRO_EXT_DIR')) {
                $pro_dom_file = NXT_PRO_EXT_DIR . 'includes/code-snippets/nexter-simple-html-dom.php';
                if (file_exists($pro_dom_file)) {
                    $dom_file = $pro_dom_file;
                }
            }
            
            if (empty($dom_file) || !file_exists($dom_file)) {
                // Cannot find Simple HTML DOM library, return original output
                return $output;
            }
            
            require_once $dom_file;
            
            // Check if function exists
            if (!function_exists('nxtext_str_get_html1')) {
                return $output;
            }
            
            $html = nxtext_str_get_html1($output);

            if (!$html) {
                return $output;
            }

            $modifications_made = 0;
            
            foreach (self::$css_selector_snippet_output as $index => $snippet) {
                if (empty($snippet['css_selector'])) {
                    continue;
                }
                
                $elements = $html->find($snippet['css_selector'], $snippet['index']);
                
                if (is_null($elements)) {
                    continue;
                }
                
                $element_count = is_array($elements) ? count($elements) : 1;
                
                if (is_array($elements)) {
                    foreach ($elements as $element) {
                        $this->insert_output_by_location($snippet['location'], $snippet['output'], $element);
                        $modifications_made++;
                    }
                } else {
                    $this->insert_output_by_location($snippet['location'], $snippet['output'], $elements);
                    $modifications_made++;
                }
            }

            $result = $html->save();
            $html->clear(); // Clean up memory
            
            return $result;
        }

        /**
         * Insert output at specified DOM location
         */
        private function insert_output_by_location($location, $output, &$element) {
            switch ($location) {
                case 'before':
                    $element->outertext = $output . $element->outertext;
                    break;
                case 'after':
                    $element->outertext = $element->outertext . $output;
                    break;
                case 'prepend':
                    $element->innertext = $output . $element->innertext;
                    break;
                case 'append':
                    $element->innertext = $element->innertext . $output;
                    break;
                case 'replace':
                    $element->outertext = $output;
                    break;
            }
        }

        /**
         * Get CSS selector locations list for Free plugin
         */
        public function get_css_selector_locations_list($default_locations) {
            return [
                'before_html_element',
                'after_html_element', 
                'start_html_element',
                'end_html_element',
                'replace_html_element'
            ];
        }

        /**
         * Debug method to test if Pro plugin is receiving filter calls
         */
        public function debug_pro_functionality() {
            return [
                'before_html_element',
                'after_html_element', 
                'start_html_element',
                'end_html_element',
                'replace_html_element'
            ];
        }

        /**
         * Register Pro snippet IDs with main tracking system
         */
        public function register_pro_snippet_ids($all_snippets) {
            // Merge Pro snippet IDs by type
            foreach (['css', 'javascript', 'php', 'htmlmixed'] as $type) {
                if (!empty(self::$snippet_loaded_ids_pro[$type])) {
                    if (!isset($all_snippets[$type])) {
                        $all_snippets[$type] = array();
                    }
                    $all_snippets[$type] = array_unique(array_merge($all_snippets[$type], self::$snippet_loaded_ids_pro[$type]));
                }
            }
            return $all_snippets;
        }

        /**
         * Get user geolocation data with caching and fallbacks
         */
        private function get_user_geolocation() {
            $user_ip = $this->get_user_ip_address();
            
            // Create unique cache key based on IP address
            $cache_key = 'nxt_geolocation_' . md5( $user_ip );
            
            // Check if we already have location data in transients
            $cached_location = get_transient( $cache_key );
            
            if ( $cached_location !== false ) {
                return $cached_location;
            }
            
            // Handle local/private IPs with intelligent fallbacks
            if ( $this->is_local_ip( $user_ip ) ) {
                $local_location = $this->get_local_ip_fallback();
                
                // Cache local IP fallback for 24 hours (longer since it won't change)
                set_transient( $cache_key, $local_location, 24 * HOUR_IN_SECONDS );
                
                return $local_location;
            }
            
            // Fetch fresh geolocation data from API
            $location_data = $this->fetch_geolocation_data( $user_ip );
            
            // Cache the result for 6 hours (public IPs can change, but not frequently)
            set_transient( $cache_key, $location_data, 6 * HOUR_IN_SECONDS );
            
            return $location_data;
        }

        /**
         * Get the user's real IP address
         */
        private function get_user_ip_address() {
            // Priority order for IP detection - improved for hosted environments
            $ip_headers = [
                'HTTP_CF_CONNECTING_IP',     // Cloudflare
                'HTTP_CLIENT_IP',            // Shared Internet/Proxy
                'HTTP_X_REAL_IP',            // Nginx proxy
                'HTTP_X_FORWARDED_FOR',      // Standard proxy header
                'HTTP_X_FORWARDED',          // Alternative proxy header
                'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster environments
                'HTTP_FORWARDED_FOR',        // Alternative forwarded header
                'HTTP_FORWARDED',            // RFC 7239
                'REMOTE_ADDR'                // Direct connection
            ];

            foreach ( $ip_headers as $header ) {
                if ( ! empty( $_SERVER[ $header ] ) ) {
                    $ip_list = $_SERVER[ $header ];
                    
                    // Handle comma-separated IP lists (common with X-Forwarded-For)
                    $ips = explode( ',', $ip_list );
                    
                    foreach ( $ips as $ip ) {
                        $ip = trim( $ip );
                        
                        // Validate IP and ensure it's not private/reserved (except for development)
                        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
                            return $ip;
                        }
                    }
                }
            }

            // Fallback to REMOTE_ADDR even if it's a private IP (for local development)
            return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
        }

        /**
         * Fetch geolocation data from external service
         */
        private function fetch_geolocation_data( $ip ) {
            $default_location = array(
                'country' => 'unknown',
                'continent' => 'unknown'
            );
            
            // Try multiple free geolocation services with fallbacks
            $services = array(
                'http://ip-api.com/php/' . $ip . '?lang=en',
                'http://ip-api.com/json/' . $ip . '?fields=country,continent',
                'https://ipapi.co/' . $ip . '/json/',
                'http://www.geoplugin.net/json.gp?ip=' . $ip
            );
            
            foreach ( $services as $service_url ) {
                $response = $this->safe_http_request( $service_url );
                
                if ( ! empty( $response ) ) {
                    $data = null;
                    
                    // Parse based on service format
                    if ( strpos( $service_url, 'ip-api.com/php/' ) !== false ) {
                        // PHP serialized format
                        $data = @unserialize( $response );
                    } else {
                        // JSON format
                        $data = json_decode( $response, true );
                    }
                    
                    if ( ! empty( $data ) ) {
                        $location = $this->parse_geolocation_response( $data, $service_url );
                        
                        if ( ! empty( $location['country'] ) && $location['country'] !== 'unknown' ) {
                            return $location;
                        }
                    }
                }
            }
            
            return $default_location;
        }

        /**
         * Parse geolocation response from different services
         */
        private function parse_geolocation_response( $data, $service_url ) {
            $location = array(
                'country' => 'unknown',
                'continent' => 'unknown'
            );
            
            // Parse response based on service
            if ( strpos( $service_url, 'ip-api.com/php/' ) !== false ) {
                // ip-api.com PHP format - provides more detailed information
                if ( isset( $data['status'] ) && $data['status'] === 'success' ) {
                    if ( isset( $data['country'] ) ) {
                        $location['country'] = strtolower( $data['country'] );
                    }
                    if ( isset( $data['countryCode'] ) ) {
                        $location['countryCode'] = strtoupper( $data['countryCode'] );
                        // Derive continent from country code
                        $location['continent'] = $this->country_code_to_continent( $data['countryCode'] );
                    }
                }
            } elseif ( strpos( $service_url, 'ip-api.com/json/' ) !== false ) {
                // ip-api.com JSON format
                if ( isset( $data['country'] ) ) {
                    $location['country'] = strtolower( $data['country'] );
                }
                if ( isset( $data['countryCode'] ) ) {
                    $location['countryCode'] = strtoupper( $data['countryCode'] );
                }
                if ( isset( $data['continent'] ) ) {
                    $location['continent'] = strtolower( $data['continent'] );
                }
            } elseif ( strpos( $service_url, 'ipapi.co' ) !== false ) {
                // ipapi.co format
                if ( isset( $data['country_name'] ) ) {
                    $location['country'] = strtolower( $data['country_name'] );
                }
                if ( isset( $data['country_code'] ) ) {
                    $location['countryCode'] = strtoupper( $data['country_code'] );
                }
                if ( isset( $data['continent_code'] ) ) {
                    $location['continent'] = $this->continent_code_to_name( $data['continent_code'] );
                }
            } elseif ( strpos( $service_url, 'geoplugin.net' ) !== false ) {
                // geoplugin.net format
                if ( isset( $data['geoplugin_countryName'] ) ) {
                    $location['country'] = strtolower( $data['geoplugin_countryName'] );
                }
                if ( isset( $data['geoplugin_continentName'] ) ) {
                    $location['continent'] = strtolower( $data['geoplugin_continentName'] );
                }
            }
            
            return $location;
        }

        /**
         * Convert continent code to continent name
         */
        private function continent_code_to_name( $code ) {
            $continents = array(
                'AF' => 'africa',
                'AN' => 'antarctica',
                'AS' => 'asia',
                'EU' => 'europe',
                'NA' => 'north america',
                'OC' => 'oceania',
                'SA' => 'south america'
            );
            
            $code = strtoupper( trim( $code ) );
            return isset( $continents[ $code ] ) ? $continents[ $code ] : 'unknown';
        }

        /**
         * Convert country code to continent name
         */
        private function country_code_to_continent( $country_code ) {
            $country_code = strtoupper( trim( $country_code ) );
            
            // Mapping of country codes to continents
            $country_continents = array(
                // North America
                'US' => 'north america', 'CA' => 'north america', 'MX' => 'north america',
                'GT' => 'north america', 'BZ' => 'north america', 'SV' => 'north america',
                'HN' => 'north america', 'NI' => 'north america', 'CR' => 'north america',
                'PA' => 'north america', 'CU' => 'north america', 'JM' => 'north america',
                'HT' => 'north america', 'DO' => 'north america', 'PR' => 'north america',
                'TT' => 'north america', 'BB' => 'north america', 'GD' => 'north america',
                'LC' => 'north america', 'VC' => 'north america', 'AG' => 'north america',
                'DM' => 'north america', 'KN' => 'north america', 'BS' => 'north america',
                
                // South America  
                'BR' => 'south america', 'AR' => 'south america', 'CL' => 'south america',
                'PE' => 'south america', 'CO' => 'south america', 'VE' => 'south america',
                'EC' => 'south america', 'BO' => 'south america', 'PY' => 'south america',
                'UY' => 'south america', 'GY' => 'south america', 'SR' => 'south america',
                'GF' => 'south america', 'FK' => 'south america',
                
                // Europe
                'DE' => 'europe', 'GB' => 'europe', 'FR' => 'europe', 'IT' => 'europe',
                'ES' => 'europe', 'PL' => 'europe', 'RO' => 'europe', 'NL' => 'europe',
                'BE' => 'europe', 'GR' => 'europe', 'PT' => 'europe', 'CZ' => 'europe',
                'HU' => 'europe', 'SE' => 'europe', 'AT' => 'europe', 'BY' => 'europe',
                'CH' => 'europe', 'BG' => 'europe', 'RS' => 'europe', 'SK' => 'europe',
                'DK' => 'europe', 'FI' => 'europe', 'NO' => 'europe', 'IE' => 'europe',
                'HR' => 'europe', 'BA' => 'europe', 'SI' => 'europe', 'LT' => 'europe',
                'LV' => 'europe', 'EE' => 'europe', 'MK' => 'europe', 'AL' => 'europe',
                'MD' => 'europe', 'UA' => 'europe', 'RU' => 'europe', 'IS' => 'europe',
                'LU' => 'europe', 'MT' => 'europe', 'CY' => 'europe', 'MC' => 'europe',
                'SM' => 'europe', 'VA' => 'europe', 'AD' => 'europe', 'LI' => 'europe',
                'ME' => 'europe', 'XK' => 'europe',
                
                // Asia
                'CN' => 'asia', 'IN' => 'asia', 'ID' => 'asia', 'PK' => 'asia',
                'BD' => 'asia', 'JP' => 'asia', 'PH' => 'asia', 'VN' => 'asia',
                'TR' => 'asia', 'IR' => 'asia', 'TH' => 'asia', 'MM' => 'asia',
                'KR' => 'asia', 'IQ' => 'asia', 'AF' => 'asia', 'SA' => 'asia',
                'UZ' => 'asia', 'MY' => 'asia', 'NP' => 'asia', 'YE' => 'asia',
                'KP' => 'asia', 'LK' => 'asia', 'KZ' => 'asia', 'SY' => 'asia',
                'KH' => 'asia', 'JO' => 'asia', 'AZ' => 'asia', 'UA' => 'asia',
                'TJ' => 'asia', 'IL' => 'asia', 'LA' => 'asia', 'LB' => 'asia',
                'SG' => 'asia', 'OM' => 'asia', 'KW' => 'asia', 'GE' => 'asia',
                'MN' => 'asia', 'AM' => 'asia', 'QA' => 'asia', 'BH' => 'asia',
                'AE' => 'asia', 'BT' => 'asia', 'BN' => 'asia', 'MV' => 'asia',
                'TM' => 'asia', 'KG' => 'asia', 'TL' => 'asia',
                
                // Africa
                'NG' => 'africa', 'ET' => 'africa', 'EG' => 'africa', 'ZA' => 'africa',
                'KE' => 'africa', 'UG' => 'africa', 'DZ' => 'africa', 'SD' => 'africa',
                'MA' => 'africa', 'AO' => 'africa', 'GH' => 'africa', 'MZ' => 'africa',
                'MG' => 'africa', 'CM' => 'africa', 'CI' => 'africa', 'NE' => 'africa',
                'BF' => 'africa', 'ML' => 'africa', 'MW' => 'africa', 'ZM' => 'africa',
                'SO' => 'africa', 'SN' => 'africa', 'TD' => 'africa', 'ZW' => 'africa',
                'GN' => 'africa', 'RW' => 'africa', 'BJ' => 'africa', 'TN' => 'africa',
                'BI' => 'africa', 'TZ' => 'africa', 'TG' => 'africa', 'SL' => 'africa',
                'LY' => 'africa', 'LR' => 'africa', 'CF' => 'africa', 'MR' => 'africa',
                'ER' => 'africa', 'GM' => 'africa', 'BW' => 'africa', 'GA' => 'africa',
                'LS' => 'africa', 'GW' => 'africa', 'GQ' => 'africa', 'MU' => 'africa',
                'SZ' => 'africa', 'DJ' => 'africa', 'RE' => 'africa', 'KM' => 'africa',
                'CV' => 'africa', 'ST' => 'africa', 'SC' => 'africa', 'SS' => 'africa',
                
                // Oceania
                'AU' => 'oceania', 'PG' => 'oceania', 'NZ' => 'oceania', 'FJ' => 'oceania',
                'NC' => 'oceania', 'SB' => 'oceania', 'VU' => 'oceania', 'PF' => 'oceania',
                'WS' => 'oceania', 'KI' => 'oceania', 'TO' => 'oceania', 'MH' => 'oceania',
                'PW' => 'oceania', 'CK' => 'oceania', 'NR' => 'oceania', 'TV' => 'oceania',
                'NU' => 'oceania', 'TK' => 'oceania', 'WF' => 'oceania', 'AS' => 'oceania',
                'GU' => 'oceania', 'MP' => 'oceania', 'FM' => 'oceania',
            );
            
            return isset( $country_continents[ $country_code ] ) ? $country_continents[ $country_code ] : 'unknown';
        }

        /**
         * Check if IP is local/private
         */
        private function is_local_ip( $ip ) {
            // Local and private IP ranges
            $local_ips = array(
                '127.0.0.1',
                'localhost',
                '::1'
            );
            
            if ( in_array( $ip, $local_ips ) ) {
                return true;
            }
            
            // Check private IP ranges
            if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
                return true;
            }
            
            return false;
        }

        /**
         * Get intelligent fallback location for local/private IPs
         */
        private function get_local_ip_fallback() {
            // Allow developers to override the default location
            $override_location = apply_filters( 'nexter_local_ip_location_override', null );
            if ( ! empty( $override_location ) && is_array( $override_location ) ) {
                return array_merge( 
                    array( 'country' => 'unknown', 'continent' => 'unknown' ), 
                    $override_location 
                );
            }

            // Check for WordPress site language to provide intelligent defaults
            $site_locale = get_locale();
            $default_location = $this->get_location_from_locale( $site_locale );
            
            // Allow constant override for development
            if ( defined( 'NEXTER_DEV_COUNTRY' ) && defined( 'NEXTER_DEV_CONTINENT' ) ) {
                $default_location['country'] = strtolower( NEXTER_DEV_COUNTRY );
                $default_location['continent'] = strtolower( NEXTER_DEV_CONTINENT );
                
                // Add country code if defined
                if ( defined( 'NEXTER_DEV_COUNTRY_CODE' ) ) {
                    $default_location['countryCode'] = strtoupper( NEXTER_DEV_COUNTRY_CODE );
                }
            }
            
            return $default_location;
        }

        /**
         * Get probable location based on WordPress locale
         */
        private function get_location_from_locale( $locale ) {
            $locale_locations = array(
                // English locales
                'en_US' => array( 'country' => 'united states', 'continent' => 'north america' ),
                'en_GB' => array( 'country' => 'united kingdom', 'continent' => 'europe' ),
                'en_AU' => array( 'country' => 'australia', 'continent' => 'oceania' ),
                'en_CA' => array( 'country' => 'canada', 'continent' => 'north america' ),
                'en_NZ' => array( 'country' => 'new zealand', 'continent' => 'oceania' ),
                'en_ZA' => array( 'country' => 'south africa', 'continent' => 'africa' ),
                
                // European locales
                'de_DE' => array( 'country' => 'germany', 'continent' => 'europe' ),
                'fr_FR' => array( 'country' => 'france', 'continent' => 'europe' ),
                'es_ES' => array( 'country' => 'spain', 'continent' => 'europe' ),
                'it_IT' => array( 'country' => 'italy', 'continent' => 'europe' ),
                'pt_PT' => array( 'country' => 'portugal', 'continent' => 'europe' ),
                'nl_NL' => array( 'country' => 'netherlands', 'continent' => 'europe' ),
                'sv_SE' => array( 'country' => 'sweden', 'continent' => 'europe' ),
                'da_DK' => array( 'country' => 'denmark', 'continent' => 'europe' ),
                'no_NO' => array( 'country' => 'norway', 'continent' => 'europe' ),
                'fi_FI' => array( 'country' => 'finland', 'continent' => 'europe' ),
                'pl_PL' => array( 'country' => 'poland', 'continent' => 'europe' ),
                'ru_RU' => array( 'country' => 'russia', 'continent' => 'europe' ),
                
                // Asian locales  
                'ja' => array( 'country' => 'japan', 'continent' => 'asia' ),
                'zh_CN' => array( 'country' => 'china', 'continent' => 'asia' ),
                'zh_TW' => array( 'country' => 'taiwan', 'continent' => 'asia' ),
                'ko_KR' => array( 'country' => 'south korea', 'continent' => 'asia' ),
                'hi_IN' => array( 'country' => 'india', 'continent' => 'asia' ),
                'th' => array( 'country' => 'thailand', 'continent' => 'asia' ),
                'vi' => array( 'country' => 'vietnam', 'continent' => 'asia' ),
                
                // South American locales
                'pt_BR' => array( 'country' => 'brazil', 'continent' => 'south america' ),
                'es_AR' => array( 'country' => 'argentina', 'continent' => 'south america' ),
                'es_MX' => array( 'country' => 'mexico', 'continent' => 'north america' ),
                'es_CO' => array( 'country' => 'colombia', 'continent' => 'south america' ),
                
                // Other locales
                'ar' => array( 'country' => 'saudi arabia', 'continent' => 'asia' ),
                'he_IL' => array( 'country' => 'israel', 'continent' => 'asia' ),
                'tr_TR' => array( 'country' => 'turkey', 'continent' => 'asia' ),
            );
            
            // Check exact match first
            if ( isset( $locale_locations[ $locale ] ) ) {
                return $locale_locations[ $locale ];
            }
            
            // Check language prefix (e.g., 'en' from 'en_US')
            $lang_prefix = substr( $locale, 0, 2 );
            foreach ( $locale_locations as $loc => $location ) {
                if ( strpos( $loc, $lang_prefix . '_' ) === 0 ) {
                    return $location;
                }
            }
            
            // Default fallback - US for development
            return array( 'country' => 'united states', 'continent' => 'north america' );
        }

        /**
         * Safe HTTP request with timeout and error handling
         */
        private function safe_http_request( $url ) {
            $args = array(
                'timeout' => 5,
                'redirection' => 2,
                'user-agent' => 'WordPress/Nexter-Extension',
                'sslverify' => false
            );
            
            $response = wp_remote_get( $url, $args );
            
            if ( is_wp_error( $response ) ) {
                return '';
            }
            
            $response_code = wp_remote_retrieve_response_code( $response );
            if ( $response_code !== 200 ) {
                return '';
            }
            
            return wp_remote_retrieve_body( $response );
        }

        /**
         * Compress JavaScript code
         */
        public function nxt_compress_js_code( $code ) {
            $code = preg_replace( '/\/\/[^\n\r]*/', '', $code ); /* Remove single-line comments. */
            $code = preg_replace( '/\/\*[\s\S]*?\*\//', '', $code ); /* Remove multi-line comments. */
            $code = preg_replace( '/\s+/', ' ', $code ); /* Remove extra whitespace. */
            $code = preg_replace( '/\s*([{};,:])\s*/', '$1', $code ); /* Remove spaces around characters. */

            return $code;
        }

        /**
         * Compress CSS code
         */
        public function nxt_compress_css_code( $code ) {
            $code = preg_replace( '!/\*.*?\*/!s', '', $code ); /* Remove comments. */
            $code = preg_replace( '/\s+/', ' ', $code ); /* Remove whitespace. */
            $code = str_replace( array( '; ', ': ', ' {', '{ ', ', ', '} ', ' }' ), array( ';', ':', '{', '{', ',', '}', '}' ), $code );

            return $code;
        }

        /**
         * Compress HTML code
         */
        public function nxt_compress_html_code( $code ) {
            /* Remove HTML comments. */
            $code = preg_replace( '/<!--(.*?)-->/', '', $code );

            /* Process <style> blocks. */
            $code = preg_replace_callback(
                '/<style\b[^>]*>(.*?)<\/style>/is',
                function ( $matches ) {
                    $css = $matches[1];
                    /* Minify CSS: Remove comments and unnecessary whitespace. */
                    $css = preg_replace( '!/\*.*?\*/!s', '', $css );
                    $css = preg_replace( '/\s+/', ' ', $css );
                    $css = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $css );
                    return '<style>' . trim( $css ) . '</style>';
                },
                $code
            );

            /* Process <script> blocks. */
            $code = preg_replace_callback(
                '/<script\b[^>]*>(.*?)<\/script>/is',
                function ( $matches ) {
                    $js = $matches[1];
                    $js = preg_replace( '/\/\/.*?(\r?\n)/', '', $js );
                    $js = preg_replace( '/\/\*.*?\*\//s', '', $js );
                    $js = preg_replace( '/\s+/', ' ', $js );
                    $js = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $js );
                    return '<script>' . trim( $js ) . '</script>';
                },
                $code
            );

            /* Remove unnecessary whitespace outside of <style> and <script>. */
            $code = preg_replace( '/>\s+</', '><', $code );
            $code = preg_replace( '/\s+/', ' ', $code );

            return trim( $code );
        }

        /**
         * Find where shortcode is used - AJAX handler
         */
        public function nxt_find_where_shortcode_usage() {
            check_ajax_referer('nxt-code-snippet', 'nonce');

            if (!current_user_can('edit_posts')) {
                wp_send_json_error('Permission denied');
            }

            if (empty($_POST['post_id'])) {
                wp_send_json_error('Missing post_id');
            }

            $snippet_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

            $custom_shortcode = get_post_meta( $snippet_id, 'nxt-code-customname', true );

            $page     = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
            $per_page = 100;

            $post_types = get_post_types( array( 'public' => true ) );

            // Add post types to params.
            $params = $post_types;

            global $wpdb;

            $search_terms   = array();
            $search_terms[] = '[nexter_snippet';
            if ( $custom_shortcode ) {
                $search_terms[] = '[' . $custom_shortcode;
            }

            $like_clauses = array();
            foreach ( $search_terms as $term ) {
                $like_clauses[] = 'post_content LIKE %s';
                $params[]       = '%' . $wpdb->esc_like( $term ) . '%';
            }
            $where_like = implode( ' OR ', $like_clauses );

            $offset = ( $page - 1 ) * $per_page;

            $post_type_placeholders = implode( ', ', array_fill( 0, count( $post_types ), '%s' ) );

            $params[] = $per_page;
            $params[] = $offset;
            
            $candidate_ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
                    "SELECT ID FROM {$wpdb->posts} 
                    WHERE post_type IN ($post_type_placeholders)
                    AND post_status != 'trash'
                    AND ($where_like)
                    LIMIT %d OFFSET %d",
                    $params
                )
            );
            
            $candidate_ids = array_unique( $candidate_ids );
                
            
            $matching_posts = array();

            if ( ! empty( $candidate_ids ) ) {
                $candidate_posts = get_posts(
                    array(
                        'post_type'      => array_values( $post_types ),
                        'post_status'    => 'any',
                        'posts_per_page' => - 1,
                        'include'        => $candidate_ids,
                    )
                );

                foreach ( $candidate_posts as $post ) {
                    $content = $post->post_content;
                    if ( has_shortcode( $content, 'nexter_snippet' ) && preg_match( '/\[nexter_snippet[^\]]*post_id\s*=\s*["\']?' . preg_quote( $snippet_id, '/' ) . '["\']?[^\]]*\]/', $content ) ) {
                        $matching_posts[] = $post;
                        continue;
                    }

                    if ( $custom_shortcode && has_shortcode( $content, $custom_shortcode ) && preg_match( '/\[' . preg_quote( $custom_shortcode, '/' ) . '[^\]]*post_id\s*=\s*["\']?' . preg_quote( $snippet_id, '/' ) . '["\']?[^\]]*\]/', $content ) ) {
                        $matching_posts[] = $post;
                    }
                }
            }

            $results = [];
            if($matching_posts){
                foreach ( $matching_posts as $post ) {
                    $title = get_the_title( $post ) ?: sprintf( __( 'Untitled (#%d)', 'nexter-pro-extensions' ), $post->ID );
                    $url = get_edit_post_link( $post->ID );
                    $post_type_obj = get_post_type_object( $post->post_type );
                    $type = $post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type;

                    $results[] = [
                        'name'      => $title,
                        'url'       => html_entity_decode($url),
                        'post_type' => $type,
                    ];
                }
            }

            wp_send_json_success($results);
        }

        /**
         * Initialize shortcodes on page load
         */
        public function home_page_code_execute(){

            $snippets = get_posts([
                'post_type'   => self::$snippet_type,
                'post_status' => 'publish',
                'numberposts' => -1,
            ]);

            foreach ($snippets as $snippet) {
                $post_id = $snippet->ID;
                $insertion_type   = get_post_meta($post_id, 'nxt-code-insertion', true);

                if( empty($insertion_type) || (!empty($insertion_type) && $insertion_type == 'auto')){
                    continue;
                }
            
                // Always register default shortcode
                add_shortcode('nexter_snippet', [$this, 'nexter_render_shortcode']);
            
                // Check for custom shortcode
                $custom_shortcode = get_post_meta($post_id, 'nxt-code-customname', true);
            
                if ($insertion_type === 'shortcode' && !empty($custom_shortcode)) {
                    add_shortcode($custom_shortcode, function($atts = [], $content = null, $shortcode_tag = '') use ($post_id) {
                        // Pass all original shortcode attributes + post_id
                        $merged_atts = array_merge($atts, ['post_id' => $post_id]);
                    
                        // Store the attributes inside the class
                        $this->nxt_shortcode_dynamic_attrs = $merged_atts;
                    
                        return $this->nexter_render_shortcode($merged_atts);
                    });
                }
            }
            
        }

        /**
         * Render snippet output with compression and attribute support
         */
        public function nexter_render_snippet_output($post_id) {
            $type = get_post_meta($post_id, 'nxt-code-type', true); // php, html, css, javascript
            $code = get_post_meta($post_id, 'nxt-' . $type . '-code', true);
    
            if (!$code) {
                return '<!-- No code found -->';
            }

            // Get predefined custom attributes from snippet metadata
            $custom_attrs = get_post_meta($post_id, 'nxt-code-shortcodeattr', true);
            if (!is_array($custom_attrs)) {
                $custom_attrs = [];
            }

            // Get actual values passed from shortcode
            $atts = isset($this->nxt_shortcode_dynamic_attrs) ? $this->nxt_shortcode_dynamic_attrs : [];

            // Remove post_id from attributes to avoid conflicts
            unset($atts['post_id']);

            // If no custom attributes are defined, use all passed attributes
            if (empty($custom_attrs)) {
                $custom_attrs = array_keys($atts);
            }

            // Replace {{key}} in non-PHP code or define vars for PHP
            foreach ($custom_attrs as $key) {
                $value = isset($atts[$key]) ? $atts[$key] : '';

                if ($type == 'php') {
                    // Use variable variable to set dynamic variable names
                    ${$key} = $value;
                } else {
                    // Replace placeholders, using esc_html to prevent XSS
                    $code = str_replace('{{' . $key . '}}', esc_html($value), $code);
                }
            }

            $compresscode = get_post_meta($post_id, 'nxt-code-compresscode', true);

            if(!empty($compresscode)){
                switch ( $type ) {
                    case 'htmlmixed':
                        $code = $this->nxt_compress_html_code( $code );
                        break;
                    case 'css':
                        $code = $this->nxt_compress_css_code( $code );
                        break;
                    case 'javascript':
                        $code = $this->nxt_compress_js_code( $code );
                        break;
                }
            }
    
            ob_start();
    
            switch ($type) {
                case 'php':
                    ob_start();
                    eval($code);
                    return ob_get_clean();
    
                case 'htmlmixed':
                case 'text':
                    echo $code;
                    break;
    
                case 'css':
                    echo '<style>' . $code . '</style>';
                    break;
    
                case 'javascript':
                    echo '<script>' . $code . '</script>';
                    break;
    
                default:
                    echo $code;
                    break;
            }
    
            return ob_get_clean();
        }
    
        /**
         * Render shortcode with Pro features
         */
        public function nexter_render_shortcode($atts = []) {
            // Ensure all attributes are preserved, not just post_id
            $original_atts = $atts;

            $atts = shortcode_atts([
                'post_id' => 0,
            ], $atts, 'nexter_snippet');
    
            $post_id = intval($atts['post_id']);

            if (empty($post_id)) {
                return '<!-- No snippet ID provided -->';
            }

            // Store ALL original attributes, not just those from shortcode_atts
            $this->nxt_shortcode_dynamic_attrs = $original_atts;

            $code_status = get_post_meta( $post_id, 'nxt-code-status', true );

            if( empty($code_status) ){
                return;
            }

            // Schedule condition check
            $start = get_post_meta($post_id, 'nxt-code-startdate', true);
            $end   = get_post_meta($post_id, 'nxt-code-enddate', true);
            $now   = current_time('mysql');
    
            if ((!empty($start) && $now < $start) || (!empty($end) && $now > $end)) {
                return; // Skip this snippet, out of schedule
            }



            // Smart Conditional Logic check
            $smart_conditions = get_post_meta($post_id, 'nxt-smart-conditional-logic', true);
            if (!empty($smart_conditions) && class_exists('Nexter_Builder_Display_Conditional_Rules')) {
                if (!Nexter_Builder_Display_Conditional_Rules::evaluate_smart_conditional_logic($smart_conditions)) {
                    return '<!-- Smart Conditional Logic not met -->';
                }
            }

            $type = get_post_meta($post_id, 'nxt-code-type', true);

            // Track the snippet ID
            $this->global_track_snippet($post_id, $type);

            return $this->nexter_render_snippet_output($post_id);
        }

        /**
         * Provide shortcode attributes to the free version's PHP executor
         * 
         * @param array $attributes
         * @param int $post_id
         * @param string $php_code
         * @return array
         */
        public function provide_shortcode_attributes($attributes, $post_id, $php_code) {
            // Get predefined custom attributes from snippet metadata
            $custom_attrs = get_post_meta($post_id, 'nxt-code-shortcodeattr', true);
            if (!is_array($custom_attrs)) {
                $custom_attrs = [];
            }

            // Get actual values passed from shortcode
            $atts = isset($this->nxt_shortcode_dynamic_attrs) ? $this->nxt_shortcode_dynamic_attrs : [];

            // Remove post_id from attributes to avoid conflicts
            unset($atts['post_id']);

            // If no custom attributes are defined, use all passed attributes
            if (empty($custom_attrs)) {
                $custom_attrs = array_keys($atts);
            }

            // Build final attributes array
            $final_attrs = array();
            foreach ($custom_attrs as $key) {
                if (isset($atts[$key])) {
                    $final_attrs[$key] = $atts[$key];
                }
            }

            return $final_attrs;
        }
    }

    // Initialize the Pro class if Pro plugin is active
    if (defined('NXT_PRO_EXT')) {
        Nexter_Builder_Code_Snippets_Render_Pro::get_instance();
    }
}