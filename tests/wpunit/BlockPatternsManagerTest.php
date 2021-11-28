<?php

use Derweili\BlockPatternsManager\Plugin;
use Derweili\BlockPatternsManager\BlockPatternsManager;

class BlockPatternsManagerTest extends Codeception\TestCase\WPTestCase {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	/**
	 * Test if register function actually registers functions
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	// public function testShouldAddHooksOnRegistationMethod() {
	// 	$block_patterns_manager_instance = new BlockPatternsManager();
	// 	$block_patterns_manager_instance->register();

	// 	global $wp_filter;
	// 	var_dump($wp_filter);

		
	// 	add_action('admin_init', 'test');
	// 	// $this->assertTrue( has_action('admin_init', 'test' ) );
		
	// 	die(print_r($GLOBALS['wp_filter']['admin_init'][0], true));
	// }

}
