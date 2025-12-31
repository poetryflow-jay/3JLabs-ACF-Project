<?php
declare(strict_types=1);

use Brain\Monkey\Functions;

final class MultisiteControllerTest extends TestCase
{
    public function testNetworkOverridesWhenEnabledAndOverrideDisabled(): void
    {
        require_once __DIR__ . '/../acf-css-really-simple-style-management-center-master/includes/multisite/class-jj-multisite-controller.php';

        Functions\when('is_multisite')->justReturn(true);
        Functions\when('add_action')->justReturn(true);
        Functions\when('add_submenu_page')->justReturn(true);
        Functions\when('get_site_option')->justReturn([
            'enabled' => true,
            'allow_site_override' => false,
            'network_options' => ['a' => 1],
        ]);

        $ms = JJ_Multisite_Controller::instance();
        $out = $ms->filter_site_options(['a' => 2, 'b' => 3]);

        $this->assertSame(['a' => 1], $out);
    }
}


