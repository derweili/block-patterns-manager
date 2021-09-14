<?php

namespace Derweili\BlockPatternsManager;

class AdminPage {
	private $menu_slug = 'block-patterns-manager';

	private $nonce_key = 'block-patterns-manager-nonce';

	private static $instance = null;

	/**
	 * Registered patterns array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $patterns = array();


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
		}

		return self::$instance;
	}

	public function get_menu_slug() {
		return $this->menu_slug;
	}

	public function register() {
		add_action( 'admin_menu', array($this, 'add_menu_page') );
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );

	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function add_menu_page() {
		// add_menu_page( __('Block Patterns Manager', 'block-patterns-manager'), __('Block Patterns Manager', 'block-patterns-manager'), 'manage_options', array( $this, 'options_page' ) , 'dashicons-align-wide' );
		// add_menu_page( __('Block Patterns Manager', 'block-patterns-manager'), __('Block Patterns Manager', 'block-patterns-manager'), 'manage_options', $this->get_menu_slug(), array( $this, 'options_page' ), 'dashicons-align-wide', null );
		$hook = add_management_page(
			__('Block Patterns Manager', 'block-patterns-manager'),
			__('Block Patterns Manager', 'block-patterns-manager'),
			'manage_options',
			$this->get_menu_slug(),
			array( $this, 'options_page' ),
			'dashicons-align-wide',
			null
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}

	/**
	* Screen options
	*/
	public function screen_option() {

		$option = 'per_page';
		// $args = [
		// 'label' => 'Block Patterns',
		// 'default' => 5,
		// 'option' => 'customers_per_page'
		// ];
		
		// add_screen_option( $option, $args );
		
		$this->block_patterns_list_table = new BlockPatternsListTable();
		$this->save_settings();
	}
	
	public function options_page() {
		$settings = BlockPatternsManager::get_instance()->get_settings();
		?>
			<div class="wrap">
				<h2><?php _e('Block Patterns Manager', 'block-patterns-manager'); ?></h2>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form method="post">
									
									<?php
										$this->block_patterns_list_table->prepare_items();
										$this->block_patterns_list_table->display();
									?>
									<button class="button-primary" type="submit"><?php _e('Save Settings', 'block-patterns-manager'); ?></button>
									<?php wp_nonce_field( $this->$nonce_key ); ?>
								</form>
							</div>
						</div>
						<div id="postbox-container-1" class="postbox-container">
			
							<div class="meta-box-sortables">
			
								<div class="postbox">
			
									<h2><span><?php esc_attr_e(
												'Block Pattern Manager', 'WpAdminStyle'
											); ?></span></h2>
			
									<div class="inside">
										<p>
											You can add a required capability to each block pattern individually. You can also add a required capability for loading external block patterns from the block pattern directory.
										</p>
										<p>
											You can disable block patterns completely by selecting "Disable for all users".
										</p>
									</div>
									<!-- .inside -->
			
								</div>
								<!-- .postbox -->
			
							</div>
							<!-- .meta-box-sortables -->
			
						</div>
						<!-- #postbox-container-1 .postbox-container -->
					</div>
					<!-- sidebar -->

					<br class="clear">
				</div>
			</div>
		<?php
	}

	public function save_settings() {
		if ( isset( $_POST['_wpnonce'] ) || wp_verify_nonce( $_POST['_wpnonce'], 'block_patterns_manager_nonce' ) ) {

			$block_patterns_capabilities = [];

			// sanitize input
			foreach ($_POST['capabilities'] as $key => $value) {
			 $block_patterns_capabilities[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
			}
			BlockPatternsManager::get_instance()->save_settings( $block_patterns_capabilities );

			add_action( 'admin_notices', [$this, 'save_settings_notice'] );

		}
	}

	public function save_settings_notice() {
		?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Settings saved', 'block-patterns-manager' ); ?></p>
    </div>
    <?php
	}
}