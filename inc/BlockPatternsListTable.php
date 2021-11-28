<?php

namespace Derweili\BlockPatternsManager;

use \WP_List_Table;

/**
 * Admin Table to list all the block patterns
 * 
 * Extends the WP_List_Table class
 */
class BlockPatternsListTable extends WP_List_Table {


	/**
	 * BlockPatternsManager
	 */
	private $block_patterns_manager = null;


	/** Class constructor */
	public function __construct( $block_patterns_manager ) {
		/**
		 * check if block_patterns_manager ist instance of BlockPatternsManager
		 */
		if ( ! $block_patterns_manager instanceof BlockPatternsManager ) {
			throw new \Exception( '$block_patterns_manager must be instance of BlockPatternsManager' );
		}

		$this->block_patterns_manager = $block_patterns_manager;

		parent::__construct( [
			'singular' => __( 'Block Pattern', 'block-patterns-manager' ), //singular name of the listed records
			'plural' => __( 'Block Patterns', 'block-patterns-manager' ), //plural name of the listed records
			'ajax' => false //should this table support ajax?
		] );
	}

	/**
	 * Helper function to get all default capabilities we show in the table
	 * The Capabilities are grouped by type so we can use them as <optgoup> in the select input
	 */
	public function get_default_capability_groups() {
		return [
			[
				'label' => __('Posts', 'block-patterns-manager'),
				'capabilities' => [
					'edit_posts',
					'edit_others_posts',
					'edit_published_posts',
					'publish_posts',
					'edit_pages',
					'delete_posts',
					'delete_published_posts',
					'delete_private_posts',
					'delete_others_posts',
					'edit_private_posts',
					'read_private_posts',
					'manage_categories',
				]
			],
			[
				'label' => __('Pages', 'block-patterns-manager'),
				'capabilities' => [
					'publish_pages',
					'edit_others_pages',
					'edit_published_pages',
					'delete_pages',
					'delete_others_pages',
					'delete_published_pages',
					'delete_private_pages',
					'edit_private_pages',
					'read_private_pages',
				]
			],
			[
				'label' => __('Media', 'block-patterns-manager'),
				'capabilities' => [
					'edit_files',
					'upload_files',
					'unfiltered_upload',
				]
			],
			[
				'label' => __('Users', 'block-patterns-manager'),
				'capabilities' => [
					'edit_users',
					'delete_users',
					'create_users',
					'promote_users',
				]
			],
			[
				'label' => __('Themes', 'block-patterns-manager'),
				'capabilities' => [
					'switch_themes',
					'edit_themes',
					'edit_theme_options',
					'install_themes',
					'delete_themes',
					'update_themes',
				]
			],
			[
				'label' => __('Plugins', 'block-patterns-manager'),
				'capabilities' => [
					'activate_plugins',
					'edit_plugins',
					'install_plugins',
					'update_plugins',
					'delete_plugins',
				]
			],
			[
				'label' => __('Admin', 'block-patterns-manager'),
				'capabilities' => [
					'manage_options',
					'import',
					'unfiltered_html',
					'customize',
					'update_core',
				]
			],
			[
				'label' => __('Network', 'block-patterns-manager'),
				'capabilities' => [
					'create_sites',
					'delete_sites',
					'manage_network',
					'manage_sites',
					'manage_network_users',
					'manage_network_themes',
					'manage_network_options',
					'manage_network_plugins',
					'upgrade_network',
					'setup_network',
					'upload_plugins',
					'upload_themes',
					'delete_site',
				]
			],
			[
				'label' => __('Comments', 'block-patterns-manager'),
				'capabilities' => [
					'moderate_comments',
					'edit_comment',
				]
			],
			[
				'label' => __('Other', 'block-patterns-manager'),
				'capabilities' => [
					'read',
					'export',
					'manage_links',
					'edit_dashboard',
				]
			]
		];
	}

	/**
	 * Get all the default capabilities from the get_default_capability_groups() function but ungrouped
	 */
	public function get_default_capabilities() {
		$default_capabilities = [];
		
		foreach ( $this->get_default_capability_groups() as $group ) {
			$default_capabilities = array_merge( $default_capabilities, $group['capabilities'] );
		}

		$default_capabilities[] = 'disable_for_all_users';

		return $default_capabilities;
	}

	/**
	 * Check if a given capability is in our default capabilities array or if it is a custom capability
	 * 
	 * @return bool | True if it is a default capability, false if it is a custom capability
	 */
	public function is_custom_capability( $capability ) {
		if( empty( $capability ) ) return false;

		if( in_array( $capability, $this->get_default_capabilities() ) ) return false;

		return true;
	}

	/**
	 * Render the capabilities Dropdown for a given block pattern
	 */
	public function capabilities_dropdown( $item ) {
		ob_start();
		$capability_settings = $this->block_patterns_manager->get_settings();

		$item_capability = isset( $capability_settings[ $item[ 'name' ] ] ) && $capability_settings[$item['name']] ? $capability_settings[$item['name']] : '';

		?>
			<div class="block-pattern-capability">
				<select name="capabilities[<?= $item['name']; ?>]-select" id="" class="capabilities-select">
					<option value=""><?php _e('Any', 'block-patterns-manager'); ?></option>

					<?php foreach ( $this->get_default_capability_groups() as $capability_group ) : ?>
						<optgroup label="<?= $capability_group['label']; ?>">
							<?php foreach ( $capability_group['capabilities'] as $capability ) : ?>
								<option value="<?= $capability; ?>" <?= $item_capability === $capability ? 'selected' : ''; ?>><?= $capability; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>

					<option value="disable_for_all_users" <?= $item_capability === 'disable_for_all_users' ? 'selected' : ''; ?>><?php _e( 'Disable for all users', 'block-patterns-manager' ); ?> </option>
					<option value="custom" <?= $this->is_custom_capability( $item_capability ) ? 'selected' : ''; ?>><?php _e( 'Select Custom Capability', 'block-patterns-manager' ); ?> </option>
				</select>
				<input type="hidden" value="<?= $item_capability ?>" name="capabilities[<?= $item['name']; ?>]" placeholder="<?= __('Custom capability name', 'block-patterns-manager'); ?>">
			</div>

		<?php


		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}

	/**
	 * Info box if no block patterns are found
	 */
	public function no_items() {
		_e( 'No Block Patterns registered.', 'block-patterns-manager' );
	}

	/**
	 * Render method for column content
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'capability':
				return $this->capabilities_dropdown( $item );
			default:
				return $item[ $column_name ];
		}
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$block_patterns = $this->block_patterns_manager->get_all_patterns();

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();
		
		// $per_page = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items = count($block_patterns);
		
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page' => $total_items //WE have to determine how many items to show on a page
		] );

		/**
		 * Sort items optionally based on GET Parameters
		 */
		if( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) && \array_key_exists( $_GET['orderby'], $this->get_sortable_columns() ) ) {
			\usort( $block_patterns, function( $a, $b ) {
				if( $_GET['order'] === 'asc' ) {
					return $a[ $_GET['orderby'] ] > $b[ $_GET['orderby'] ];
				} else {
					return $a[ $_GET['orderby'] ] < $b[ $_GET['orderby'] ];
				}
			} );
		}
		
		$this->items = $block_patterns;
	}

	/**
	* Associative array of columns
	*
	* @return array
	*/
	function get_columns() {
		$columns = [
		// 'cb' => '<input type="checkbox" />',
			'title' => __( 'Block pattern title', 'block-patterns-manager' ),
			'name' => __( 'Block pattern name', 'block-patterns-manager' ),
			'description' => __( 'Block pattern description', 'block-patterns-manager' ),
			'capability' => __('Required capability to use the block pattern', 'block-patterns-manager')
		];
		
		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', true ),
			'name' => array( 'name', true ),
		);
		
		return $sortable_columns;
	}

	/**
	 * Render the default table from the WP_List_Table class and out custom Javascript
	 */
	public function display() {
		parent::display();

		?>
			<script>
				$block_pattern_capabilities = document.querySelectorAll('.block-pattern-capability');
				$block_pattern_capabilities.forEach(function(el) {

					const selectInput = el.querySelector('select')
					const customInput = el.querySelector('input')

					function syncCapabilitySelect( value ) {
						if( value === 'custom' ) {
							customInput.setAttribute('type', 'text');
							// customInput.value = '';
						} else {
							customInput.setAttribute('type', 'hidden');
							customInput.value = value;
						}
						
					}
					selectInput.addEventListener('change', function(e) {
						syncCapabilitySelect( e.target.value );
					});
					syncCapabilitySelect( selectInput.value );
				});

			</script>

		<?php
	}

}