<?php
declare(strict_types=1);

use Brain\Monkey\Functions;

final class WebhookManagerTest extends TestCase
{
    public function testWebhookAddsSignatureHeaderWhenSecretProvided(): void
    {
        require_once __DIR__ . '/../acf-css-really-simple-style-management-center-master/includes/integrations/class-jj-webhook-manager.php';

        // Config
        Functions\when('get_option')->justReturn([
            'enabled' => true,
            'endpoints' => ['https://example.com/hook'],
            'secret' => 'secret123',
            'events' => ['style_settings_updated'],
            'payload_mode' => 'minimal',
            'timeout_seconds' => 5,
        ]);

        // WP helpers
        Functions\when('home_url')->justReturn('https://site.test');
        Functions\when('wp_get_current_user')->justReturn((object) ['ID' => 1, 'user_login' => 'admin']);
        Functions\when('wp_json_encode')->alias(function ($v) { return json_encode($v); });
        Functions\when('is_wp_error')->justReturn(false);
        Functions\when('wp_remote_retrieve_response_code')->justReturn(200);

        // Capture request args
        $captured = [];
        Functions\when('wp_remote_post')->alias(function ($url, $args) use (&$captured) {
            $captured = [$url, $args];
            return ['response' => ['code' => 200]];
        });

        $m = JJ_Webhook_Manager::instance();
        $m->maybe_send('style_settings_updated', ['source' => 'unit', 'new' => ['a' => 1], 'old' => []], true);

        $this->assertSame('https://example.com/hook', $captured[0]);
        $this->assertArrayHasKey('headers', $captured[1]);
        $this->assertArrayHasKey('X-JJ-Signature', $captured[1]['headers']);
        $this->assertNotSame('', $captured[1]['headers']['X-JJ-Signature']);
    }
}


