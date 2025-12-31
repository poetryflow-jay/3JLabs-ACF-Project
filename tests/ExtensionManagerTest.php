<?php
declare(strict_types=1);

use Brain\Monkey\Functions;

final class ExtensionManagerTest extends TestCase
{
    public function testRegisterRejectsDuplicateIds(): void
    {
        require_once __DIR__ . '/../acf-css-really-simple-style-management-center-master/includes/extensions/class-jj-extension-manager.php';

        Functions\when('add_action')->justReturn(true);
        Functions\when('sanitize_key')->alias(function ($v) { return strtolower(preg_replace('/[^a-z0-9_-]/', '', (string) $v)); });

        $m = JJ_Extension_Manager::instance();

        $ext = new class {
            public function get_id() { return 'My-Ext'; }
        };

        $this->assertTrue($m->register($ext));
        $this->assertFalse($m->register($ext));
    }
}


