<?php

namespace Derweili\BlockPatternsManager;

class AdminPage {
	private static $menu_slug = 'block-patterns-manager';

	private $nonce_key = 'block-patterns-manager-nonce';
	private $nonce_action = 'save-block-patterns-manager-nonce';

	private static $instance = null;

	/**
	 * BlockPatternsManager
	 */
	private $block_patterns_manager = null;

	/**
	 * Registered patterns array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $patterns = array();

	/**
	 * Construct Method
	 * 
	 * @since 1.0.0
	 * @param $block_patterns_manager BlockPatternsManager
	 * @return void
	 * 
	 */
	public function __construct( $block_patterns_manager ) {
		/**
		 * check if block_patterns_manager ist instance of BlockPatternsManager
		 */
		if ( ! $block_patterns_manager instanceof BlockPatternsManager ) {
			throw new \Exception( '$block_patterns_manager must be instance of BlockPatternsManager' );
		}

		$this->block_patterns_manager = $block_patterns_manager;
	}

	public static function get_menu_slug() {
		return self::$menu_slug;
	}

	/**
	 * Todo add test to check if actions and filters are registerd
	 */
	public function register() {
		add_action( 'admin_menu', array($this, 'add_menu_page') );
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Register the menu page.
	 * 
	 * Todo: add test to check if menu page is registerd
	 */
	public function add_menu_page() {
		$hook = add_management_page(
			__('Block Patterns Manager', 'block-patterns-manager'),
			__('Block Patterns Manager', 'block-patterns-manager'),
			'manage_options',
			self::get_menu_slug(),
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
		
		$this->block_patterns_list_table = new BlockPatternsListTable( $this->block_patterns_manager );
		$this->save_settings();
	}
	
	/**
	 * Render the options page
	 * 
	 * Todo: Write tests
	 */
	public function options_page() {
		$settings = $this->block_patterns_manager->get_settings();

		?>
			<div class="wrap">
				<h2><?php _e('Block Patterns Manager', 'block-patterns-manager'); ?></h2>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form method="post" id="block-patterns-manager-admin-page-form">
									
									<?php
										$this->block_patterns_list_table->prepare_items();
										$this->block_patterns_list_table->display();
									?>
									<button class="button-primary" type="submit"><?php _e('Save Settings', 'block-patterns-manager'); ?></button>
									<?php wp_nonce_field( $this->nonce_action ); ?>
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

	/**
	 * Save admin page settings
	 * 
	 * Todo: add test to check if admin_notices are added
	 */
	public function save_settings() {

		/**
		 * If nonce is not valid, return false
		 */
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], $this->nonce_action ) ) return false;


		$block_patterns_capabilities = [];

		// sanitize input
		foreach ($_POST['capabilities'] as $key => $value) {
			$block_patterns_capabilities[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
		}

		$this->block_patterns_manager->save_settings( $block_patterns_capabilities );

		add_action( 'admin_notices', [$this, 'save_settings_notice'] );

		return true;
	}

	/**
	 * Render the admin notice
	 */
	public function save_settings_notice() {
		?>
    <div class="notice notice-success is-dismissible block-patterns-manager-settings-saved">
        <p><?php _e( 'Settings saved', 'block-patterns-manager' ); ?></p>
    </div>
    <?php
	}
}