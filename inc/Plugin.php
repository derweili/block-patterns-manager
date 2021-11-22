<?php

namespace Derweili\BlockPatternsManager;

/**
 * Plugin base class
 */
class Plugin {

	private $admin_page = null;

	public function run() {
		$this->register_admin_hooks();
	}

	public function register_admin_hooks() {
		$block_patterns_manager = new BlockPatternsManager();
		$block_patterns_manager->register();
		
		$this->admin_page = new AdminPage($block_patterns_manager);
		$this->admin_page->register();
		
		$remote_patterns = new RemotePatterns( $block_patterns_manager );
		$remote_patterns->register();

		/**
		 * Add settings link to plugin page
		 */
		$plugin = plugin_basename(_get_plugin_directory() . '/plugin.php'); 
		add_filter("plugin_action_links_$plugin", [$this, 'plugin_settings_link'] );
	}

	/**
	 * Add a settings link to the plugin page
	 */
	public function plugin_settings_link( $links ) {
		$menu_slug = AdminPage::get_menu_slug();

		$settings_link = '<a href="' . admin_url( 'tools.php?page=' . $menu_slug ) . '">' . __( 'Settings', 'block-patterns-manager' ) . '</a>';
		$links[] = $settings_link;

		return $links;
	}
}