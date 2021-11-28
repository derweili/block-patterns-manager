<?php

namespace Derweili\BlockPatternsManager;

use \WP_Block_Patterns_Registry;

/**
 * Base class to manage block patterns.
 * 
 * Implements all required methods to get all patterns, save and update all settings.
 * Implements a methods which disables the block pattern if the user is not allowed to use it.
 */
class BlockPatternsManager {
	/**
	 * Registered patterns array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public static $patterns = array();

	private static $instance = null;

	private $capabilities_settings_key = 'block_patterns_capabilities';

	private $capabilities_settings = null;

	/**
	 * Load the settings from the database on the first run.
	 */
	public function __construct() {
		// load setttings;
		$this->capabilities_settings = $this->load_settings();
	}

	/**
	 * Register all required hooks
	 * 
	 * Loads all currently registered patterns and adds them to the registry.
	 * Register our settings to the WP settings API.
	 * Load the unregistered patterns method which disables block patterns if the user is not allowed to use them.
	 * 
	 * Todo: add tests to check if all those functions are registered correctly
	 */
	public function register() {
		add_action( 'admin_init', array($this, 'load_all_patterns'), 50);
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'unregister_block_patterns' ], 500 );
	}

	/**
	 * Load all currently registered patterns and add them to the registry.
	 * 
	 * Todo: Create Tests
	 */
	public function load_all_patterns() {
		self::$patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();	
	}

	/**
	 * Get all registered which we loaded before.
	 * 
	 * Implents a filter to add additional (fake) patterns.
	 * This filter is used by the RemovePattern class to add the block pattern directory to the list of patterns.
	 */
	public function get_all_patterns() {
		return apply_filters( 'block_pattern_manager_all_patterns', self::$patterns );
	}

	/**
	 * Register the settings to the WP settings API.
	 */
	public function register_settings() {
		register_setting( 'block_patterns_manager_settings', $this->capabilities_settings_key, 'array', __('List of block patterns and their required capabilities', 'block-patterns-manager') ); 
	}

	/**
	 * Method to save the settings to the database.
	 */
	public function save_settings( array $settings ) {
		update_option( $this->capabilities_settings_key, $settings );
		$this->capabilities_settings = $settings;
	}

	/**
	 * Get the settings from the database.
	 */
	private function load_settings() {
		return get_option( $this->capabilities_settings_key, [] );
	}

	/**
	 * Get the settings we loaded before.
	 * If we did not load them before, load them from the Database.
	 */
	public function get_settings() {
		if( null === $this->capabilities_settings ) {
			$this->capabilities_settings = $this->load_settings();
		}
		return $this->capabilities_settings;
	}

	/**
	 * Unregister Block Patterns based on capabilities
	 */
	public function unregister_block_patterns() {
		$settings = $this->get_settings();

		foreach ($settings as $pattern_name => $capability) {
			// only unregister if the pattern is registered
			if( $this->is_pattern_registered( $pattern_name ) ) {

				// unregister if a capability is selected (not empty) and user has not the capability
				if(  $capability && ! empty( $capability ) && ! $this->current_user_can( $capability ) ) {
					$unregister_result = $this->unregister_block_pattern( $pattern_name );
				}
			}
		}
	}

	/**
	 * Wrapper function for current_user_can() WordPress Capabilities Check
	 */
	public function current_user_can( $capability ) {
		return current_user_can( $capability );
	}

	/**
	 * Wrapper function for unregister_block_pattern WordPress function
	 */
	public function unregister_block_pattern( $pattern_name ) {
		return unregister_block_pattern( $pattern_name );
	}

	/**
	 * Check if a pattern is registered
	 */
	public function is_pattern_registered( $pattern_name ) {
		return WP_Block_Patterns_Registry::get_instance()->is_registered( $pattern_name );
	}
}