<?php

namespace Derweili\BlockPatternsManager;

/**
 * Enable disable Remote Patterns from the block pattern directory
 */
class RemotePatterns {
	/**
	 * Registered patterns array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $patterns = array();

	private static $instance = null;

	private $disable_remote_patterns = null;

	private $remote_patterns_key = 'block_pattern_directory';

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Block_Patterns_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			return self::$instance;
		}

		return self::$instance;
	}

	public function __construct() {
		// load setttings;

		$this->disable_remote_patterns = $this->load_disable_remote_patterns_capability_setting();
	}

	/**
	 * Add the required hooks
	 */
	public function register() {
		// add the filter to the block pattern directory
		add_filter('should_load_remote_block_patterns', [ $this, 'disable_remote_block_patterns' ]);

		// add the block pattern directory as a pattern
		add_filter('block_pattern_manager_all_patterns', [ $this, 'add_pattern_directory_to_pattern_list' ]);
	}

	/**
	 * Add the block pattern directory as a "fake" pattern to the all patterns list
	 */
	public function add_pattern_directory_to_pattern_list( $block_patterns ) {
		/**
		 * Add additional items
		 */
		$block_patterns[] = [
			'name' => 'block_pattern_directory',
			'title' => 'Block Pattern Directory',
			'description' => 'Load external block patterns from the <a href="https://wordpress.org/patterns/" target="_blank" >Block Pattern Directory</a>'
		];
		return $block_patterns;
	}

	/**
	 * Load the current settings for the disable remote patterns capability
	 */
	public function load_disable_remote_patterns_capability_setting () {
		$block_patterns_capabilities = BlockPatternsManager::get_instance()->get_settings();

		if( isset( $block_patterns_capabilities[$this->remote_patterns_key] ) && ! empty( $block_patterns_capabilities[ $this->remote_patterns_key ] ) ) {
			return $block_patterns_capabilities[$this->remote_patterns_key];
		} else {
			false;
		}
	}

	/**
	 * Get the current disable remote patterns capability
	 * If no capability is set, load it from the settings
	 */
	public function get_setting() {
		if( null === $this->disable_remote_patterns ) {
			$this->disable_remote_patterns = $this->load_disable_remote_patterns_capability_setting();
		}
		return $this->disable_remote_patterns;
	}

	/**
	 * Disable remote block pattern based on settings
	 */
	public function disable_remote_block_patterns( $should_load_remote_block_patterns ) {
		if( $this->get_setting() && ! current_user_can( $this->get_setting() ) ) {
			return false;
		}
		
		return $should_load_remote_block_patterns;
	}
}