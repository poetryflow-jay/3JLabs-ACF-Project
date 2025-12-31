<?php
declare(strict_types=1);

use Brain\Monkey\Functions;

final class RestApiNetworkLockTest extends TestCase
{
    public function testRestApiBlocksUpdateWhenNetworkLocked(): void
    {
        require_once __DIR__ . '/../acf-css-really-simple-style-management-center-master/includes/api/class-jj-style-guide-rest-api.php';

        // Fake multisite controller
        require_once __DIR__ . '/../acf-css-really-simple-style-management-center-master/includes/multisite/class-jj-multisite-controller.php';

        // Stub multisite controller behavior
        Functions\when('get_site_option')->justReturn([
            'enabled' => true,
            'allow_site_override' => false,
            'network_options' => [],
        ]);
        Functions\when('is_multisite')->justReturn(true);
        Functions\when('add_action')->justReturn(true);

        // REST request stub
        $req = new class {
            public function get_param($k) { return ['a' => 1]; }
        };

        // Ensure option functions exist
        Functions\when('get_option')->justReturn([]);

        $api = JJ_Style_Guide_REST_API::instance();
        $res = $api->update_settings($req);

        // In our implementation, error() returns WP_Error when available; here it falls back to array.
        $this->assertIsArray($res);
        $this->assertSame('network_locked', $res['code']);
    }
}


