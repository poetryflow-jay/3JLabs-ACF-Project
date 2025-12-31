<?php
declare(strict_types=1);

use Brain\Monkey\Functions;

final class CssOptimizerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // WP functions used in JJ_CSS_Optimizer constructor
        Functions\when('get_option')->justReturn([
            'performance' => ['enable_tree_shaking' => true],
            'palettes' => [],
            'typography' => [],
            'buttons' => [],
        ]);
        Functions\when('add_filter')->justReturn(true);
        Functions\when('add_action')->justReturn(true);
    }

    public function testMinifyRemovesCommentsAndWhitespace(): void
    {
        require_once __DIR__ . '/../acf-css-really-simple-style-management-center-master/includes/class-jj-css-optimizer.php';

        $opt = JJ_CSS_Optimizer::instance();
        $css = "/* comment */\n.a { color: red; }\n";

        $min = $opt->minify_css($css);
        $this->assertSame('.a{color:red;}', $min);
    }

    public function testTreeShakeRemovesKnownUnusedBlocks(): void
    {
        require_once __DIR__ . '/../acf-css-really-simple-style-management-center-master/includes/class-jj-css-optimizer.php';

        $opt = JJ_CSS_Optimizer::instance();
        $css = ".jj-palette-system{color:#111}.keep{color:#222}";

        $out = $opt->tree_shake_css($css);
        $this->assertStringNotContainsString('.jj-palette-system', $out);
        $this->assertStringContainsString('.keep{color:#222}', $out);
    }
}


