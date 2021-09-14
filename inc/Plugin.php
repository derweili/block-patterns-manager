<?php

namespace Derweili\BlockPatternsManager;

/**
 * Plugin base class
 */
class Plugin {
	public function run() {
		$this->register_admin_hooks();
	}

	public function register_admin_hooks() {
		BlockPatternsManager::get_instance()->register();
		AdminPage::get_instance()->register();
		RemotePatterns::get_instance()->register();

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
		$menu_slug = AdminPage::get_instance()->get_menu_slug();

		$settings_link = '<a href="' . admin_url( 'tools.php?page=' . $menu_slug ) . '">' . __( 'Settings', 'block-patterns-manager' ) . '</a>';
		$links[] = $settings_link;

		return $links;
	}
}