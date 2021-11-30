<?php
/**
 * Plugin Name:     Block Patterns Manager
 * Plugin URI:      https://derweili.de
 * Description:     Block Patterns Manager
 * Author:          TW Werbeagenten Heidelberg GmbH
 * Author URI:      https://derweili.de
 * Text Domain:     block-patterns-manager
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         block-patterns-manager
 */

// Your code starts here.
namespace Derweili\BlockPatternsManager;

//  Exit if accessed directly.
defined('ABSPATH') || exit;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ){
	require __DIR__ . '/vendor/autoload.php';
}

/**
 * Gets this plugin's absolute directory path.
 *
 * @since  2.1.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_directory() {
	return __DIR__;
}

/**
 * Gets this plugin's URL.
 *
 * @since  2.1.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_url() {
	static $plugin_url;

	if ( empty( $plugin_url ) ) {
		$plugin_url = plugins_url( null, __FILE__ );
	}

	return $plugin_url;
}

( new Plugin() )->run();