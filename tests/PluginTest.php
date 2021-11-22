<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Derweili\BlockPatternsManager\Plugin;

/**
 * Create admin_url() stub
 */
function admin_url($path)
{
		return 'http://localhost/wp58rc/wp-admin/admin.php?page=block-patterns-manager';
}

final class PluginTest extends TestCase
{
	/**
	 * Add Link to plugin settings links array
	 */
	public function testShouldAddLinkToArray(): void
	{
		$plugin = new Plugin();
		$links = $plugin->plugin_settings_link([]);
		
		$this->assertIsArray($links);
		$this->assertEquals(count( $links ), 1);
		$this->assertIsString($links[0]);
	}
}