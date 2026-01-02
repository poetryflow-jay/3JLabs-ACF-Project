<?php
/**
 * [Phase 20] 플러그인 활성화 테스트 스크립트
 * 
 * WordPress 환경에서 직접 실행하여 플러그인 활성화 및 초기화를 테스트합니다.
 * 
 * 사용법: WordPress 루트 디렉토리에서 실행
 *   php test_plugin_activation.php
 */

// WordPress 로드
require_once __DIR__ . '/wp-load.php';

if (!defined('ABSPATH')) {
    die('WordPress가 로드되지 않았습니다.');
}

echo "========================================\n";
echo "Phase 20: 플러그인 활성화 테스트\n";
echo "========================================\n\n";

// 플러그인 경로
$plugin_file = 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php';
$plugin_path = WP_PLUGIN_DIR . '/' . dirname($plugin_file);

echo "📦 플러그인 경로 확인...\n";
echo "   경로: {$plugin_path}\n";

if (!file_exists($plugin_path)) {
    die("❌ 플러그인 경로를 찾을 수 없습니다.\n");
}

echo "   ✅ 플러그인 폴더 존재\n\n";

// 파일 확인
echo "📄 핵심 파일 확인...\n";

$critical_files = [
    'acf-css-really-simple-style-guide.php' => '메인 파일',
    'includes/class-jj-file-integrity-monitor.php' => '파일 무결성 모니터',
    'includes/class-jj-security-enhancer.php' => '보안 강화 모듈',
    'includes/class-jj-license-manager.php' => '라이센스 관리자',
];

$all_files_exist = true;
foreach ($critical_files as $file => $description) {
    $file_path = $plugin_path . '/' . $file;
    $exists = file_exists($file_path);
    $status = $exists ? '✅' : '❌';
    echo "   {$status} {$description}: " . ($exists ? '존재' : '없음') . "\n";
    if (!$exists) {
        $all_files_exist = false;
    }
}

echo "\n";

// 언어 파일 확인
echo "🌐 언어 파일 확인...\n";
$languages_path = $plugin_path . '/languages';
if (is_dir($languages_path)) {
    $mo_files = glob($languages_path . '/*.mo');
    $po_files = glob($languages_path . '/*.po');
    echo "   ✅ MO 파일: " . count($mo_files) . "개\n";
    echo "   ✅ PO 파일: " . count($po_files) . "개\n";
} else {
    echo "   ❌ languages 폴더 없음\n";
}

echo "\n";

// 플러그인 활성화 상태 확인
echo "🔌 플러그인 활성화 상태 확인...\n";
$active_plugins = get_option('active_plugins', []);
$plugin_basename = $plugin_file;
$is_active = in_array($plugin_basename, $active_plugins);

if ($is_active) {
    echo "   ✅ 플러그인 활성화됨\n\n";
    
    // 클래스 존재 확인
    echo "🔍 클래스 로드 확인...\n";
    
    $classes_to_check = [
        'JJ_File_Integrity_Monitor' => '파일 무결성 모니터',
        'JJ_Security_Enhancer' => '보안 강화 모듈',
        'JJ_License_Manager' => '라이센스 관리자',
        'JJ_Simple_Style_Guide' => '메인 클래스',
    ];
    
    foreach ($classes_to_check as $class_name => $description) {
        $exists = class_exists($class_name);
        $status = $exists ? '✅' : '❌';
        echo "   {$status} {$description}: " . ($exists ? '로드됨' : '로드 안 됨') . "\n";
    }
    
    echo "\n";
    
    // 보안 모듈 초기화 확인
    echo "🔒 보안 모듈 초기화 확인...\n";
    
    if (class_exists('JJ_File_Integrity_Monitor')) {
        try {
            $monitor = JJ_File_Integrity_Monitor::instance();
            echo "   ✅ File Integrity Monitor 인스턴스 생성 성공\n";
        } catch (Exception $e) {
            echo "   ❌ File Integrity Monitor 인스턴스 생성 실패: " . $e->getMessage() . "\n";
        }
    }
    
    if (class_exists('JJ_Security_Enhancer')) {
        try {
            $enhancer = JJ_Security_Enhancer::instance();
            echo "   ✅ Security Enhancer 인스턴스 생성 성공\n";
        } catch (Exception $e) {
            echo "   ❌ Security Enhancer 인스턴스 생성 실패: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    
    // 버전 확인
    if (defined('JJ_STYLE_GUIDE_VERSION')) {
        echo "📦 플러그인 버전: " . JJ_STYLE_GUIDE_VERSION . "\n";
    }
    
} else {
    echo "   ⚠️ 플러그인 비활성화됨\n";
    echo "   💡 WordPress 관리자에서 플러그인을 활성화하세요.\n";
}

echo "\n";
echo "========================================\n";
echo "✅ 테스트 완료!\n";
echo "========================================\n";
