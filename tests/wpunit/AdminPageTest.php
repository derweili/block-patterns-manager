<?php

use Derweili\BlockPatternsManager\Plugin;
use Derweili\BlockPatternsManager\BlockPatternsManager;
use Derweili\BlockPatternsManager\AdminPage;
class AdminPageTest extends Codeception\TestCase\WPTestCase {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	public function getDefaultBlockPatternsManagerMock() {
		$mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->getMock();

		return $mock;
	}

	public function testShouldSaveSettings() {

		$nonce_key = 'save-block-patterns-manager-nonce';

		// create nonce
		$nonce = wp_create_nonce( $nonce_key );
		
		$_POST['_wpnonce'] = $nonce;
		$_POST['capabilities'] = [
			'twentytwentyone/large-text' => 'manage_options',
		];

		$mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->getMock();

		$admin_page_instance = new AdminPage( $mock );
		$has_saved_settings = $admin_page_instance->save_settings();

		$this->assertTrue( $has_saved_settings );
	}

	public function testShouldNotSaveSettingsIfNonceInvalid() {
		// create nonce
		$nonce = 'invalidnonce';
		
		$_POST['_wpnonce'] = $nonce;
		$_POST['capabilities'] = [
			'twentytwentyone/large-text' => 'manage_options',
		];

		$mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->getMock();

		$admin_page_instance = new AdminPage( $mock );
		$has_saved_settings = $admin_page_instance->save_settings();

		$this->assertFalse( $has_saved_settings );
	}

	public function testShouldNotSaveSettingsIfNonceNotPresent() {		
		$_POST['capabilities'] = [
			'twentytwentyone/large-text' => 'manage_options',
		];

		$mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->getMock();

		$admin_page_instance = new AdminPage( $mock );
		$has_saved_settings = $admin_page_instance->save_settings();

		$this->assertFalse( $has_saved_settings );
	}
}
