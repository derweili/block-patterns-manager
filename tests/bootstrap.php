<?php

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;

// require __DIR__ . '/mocks/BlockPatternsManagerMock.php';

class PluginDefaultTestCase extends TestCase {
	// Adds Mockery expectations to the PHPUnit assertions count.
	use MockeryPHPUnitIntegration;

	protected function setUp(): void {
			parent::setUp();
			Monkey\setUp();
	}
	protected function tearDown(): void {
			Monkey\tearDown();
			parent::tearDown();
	}
}

/**
 * Create default mocks
 */


/**
 * WordPress i18n mocks
 */
function __( $text, $domain = 'default' ) {
	return $text;
}

function _e( $text, $domain = 'default' ) {
	echo $text;
}


/**
 * Creact add_management_page mock
 */
function add_management_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return $menu_slug;
}

/**
 * Create wp_verify_nonce mock
 */
function wp_verify_nonce( $nonce, $action = -1 ) {
	return $nonce === 'valid';
}

/**
 * Create wp_die() mock
 */
function wp_die( $message = '', $title = '', $args = array() ) {
	return $message;
}

/**
 * Mock sanitize_text_field
 */
function sanitize_text_field( $text ) {
	return $text;
}



/**
 * Mock get_option
 */
function get_option($option_name, $default = false) {
	return 'test_value';
}

/**
 * Mock update_option
 */
function update_option($option_name, $newvalue, $autoload = 'yes') {
	return true;
}
