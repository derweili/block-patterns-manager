<?php

declare(strict_types=1);

use Derweili\BlockPatternsManager\AdminPage;
use Derweili\BlockPatternsManager\BlockPatternsManager;

final class AdminPageTest extends PluginDefaultTestCase
{

	public function tearDown(): void {
		Mockery::close();

		parent::tearDown();
	}

	public function getDefaultBlockPatternsManagerMock() {
		$mock = Mockery::mock(BlockPatternsManager::class);
		return $mock;
	}

	public function testShouldCreateErrorWhenInstantiatedWithoutBlockPatternsManager(): void
	{
		$this->expectException(Exception::class);
		new AdminPage( 'test' );
	}

	public function testShouldReturnMenuSlug(): void
	{
		$expected_menu_slug = 'block-patterns-manager';
		$received_menu_slug = AdminPage::get_menu_slug();

		$this->assertEquals(
			$expected_menu_slug,
			$received_menu_slug
		);
	}

	/**
	 * Test if register function actually registers admin_menu and set_screen functions
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	public function testShouldAddHooksOnRegistationMethod() {
		$admin_page_instance = new AdminPage( $this->getDefaultBlockPatternsManagerMock() );
		$admin_page_instance->register();

		self::assertNotFalse( has_action('admin_menu', [ $admin_page_instance, 'add_menu_page' ]) );
		self::assertNotFalse( has_filter('set-screen-option', [ AdminPage::class, 'set_screen' ], 10 ) );
	}

	/**
	 * Test if the add_menu_page() method registers the menu page and the screen options
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	public function testShouldRegisterManagementPageAndScreenOptionsFunction() {
		$admin_page_instance = new AdminPage( $this->getDefaultBlockPatternsManagerMock() );
		$admin_page_instance->add_menu_page();

		$expected_menu_slug = $admin_page_instance->get_menu_slug();

		self::assertNotFalse( has_action('load-' . $expected_menu_slug, [ $admin_page_instance, 'screen_option' ]) );
	}
	
	/**
	 * Check if nonce is checked
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	public function testShouldNotSaveSettingsIfNonceIsNotValid() {
		$_POST['_wpnonce'] = 'invalid_nonce';

		$admin_page_instance = new AdminPage( $this->getDefaultBlockPatternsManagerMock() );
		$admin_page_instance->save_settings();

		self::assertFalse( has_action('admin_notices', [ $admin_page_instance, 'save_settings_notice' ]) );
	}
	
	/**
	 * Test
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	public function testShouldSaveSettings() {

		/**
		 * Sub get_option()
		 * 
		 * Todo:
		 * - Mock BlockPatternsManager::get_instance()
		 * - Mock BlockPatternsManager::save_settings()
		 */
		
		$_POST['_wpnonce'] = 'valid';
		$_POST['capabilities'] = [
			'twentytwentyone/large-text' => 'manage_options',
		];

		$mock = Mockery::mock(BlockPatternsManager::class);
		$mock->shouldReceive('save_settings');

		$admin_page_instance = new AdminPage( $mock );
		$admin_page_instance->save_settings();

		self::assertNotFalse( has_action('admin_notices', [ $admin_page_instance, 'save_settings_notice' ]) );
	}
}