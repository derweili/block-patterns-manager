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

	private $disable_remote_patterns_capability = null;

	private $remote_patterns_key = 'block_pattern_directory';

	/**
	 * BlockPatternsManager
	 */
	private $block_patterns_manager = null;

	public function __construct( $block_patterns_manager ) {
		/**
		 * check if block_patterns_manager ist instance of BlockPatternsManager
		 */
		if ( ! $block_patterns_manager instanceof BlockPatternsManager ) {
			throw new \Exception( '$block_patterns_manager must be instance of BlockPatternsManager' );
		}

		$this->block_patterns_manager = $block_patterns_manager;
	}

	/**
	 * Add the required hooks
	 */
	public function register() {
		// add the filter to the block pattern directory
		add_filter('should_load_remote_block_patterns', [ $this, 'disable_remote_block_patterns' ]);

		/**
		 * add the block pattern directory as a pattern to BlockPatternsManager patterns list
		 */
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
		$block_patterns_capabilities =  $this->block_patterns_manager->get_settings();

		if( isset( $block_patterns_capabilities[$this->remote_patterns_key] ) && ! empty( $block_patterns_capabilities[ $this->remote_patterns_key ] ) ) {
			return $block_patterns_capabilities[$this->remote_patterns_key];
		} else {
			false;
		}
	}

	/**
	 * Get the current disable remote patterns capability
	 * If no capability is stored yet, load it from the BlockPatternsManager settings
	 */
	public function get_setting() {
		if( null === $this->disable_remote_patterns_capability ) {
			$this->disable_remote_patterns_capability = $this->load_disable_remote_patterns_capability_setting();
		}
		return $this->disable_remote_patterns_capability;
	}

	/**
	 * Disable remote block pattern based on settings
	 */
	public function disable_remote_block_patterns( $should_load_remote_block_patterns ) {
		if( $this->get_setting() && ! $this->current_user_can( $this->get_setting() ) ) {
			return false;
		}
		
		return $should_load_remote_block_patterns;
	}

	public function current_user_can( $capability ) {
		return current_user_can( $capability );
	}
}