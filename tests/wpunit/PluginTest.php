<?php

use Derweili\BlockPatternsManager\Plugin;


class PluginTest extends Codeception\TestCase\WPTestCase {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	// tests
	public function testShouldAddLinkToArray() {
		$plugin = new Plugin();
		$links = $plugin->plugin_settings_link([]);
		
		$this->assertIsArray($links);
		$this->assertEquals(count( $links ), 1);
		$this->assertIsString($links[0]);
	}
}
