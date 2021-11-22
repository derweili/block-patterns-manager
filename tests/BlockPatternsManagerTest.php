<?php

declare(strict_types=1);

use Brain\Monkey\Filters;
use Derweili\BlockPatternsManager\AdminPage;
use Derweili\BlockPatternsManager\BlockPatternsManager;

final class BlockPatternsManagerTest extends PluginDefaultTestCase
{

	/**
	 * Test if register function actually registers functions
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	public function testShouldAddHooksOnRegistationMethod() {
		$block_patterns_manager_instance = new BlockPatternsManager();
		$block_patterns_manager_instance->register();

		self::assertNotFalse( has_action('admin_init', [ $block_patterns_manager_instance, 'load_all_patterns' ], 50 ) );
		self::assertNotFalse( has_filter('admin_init', [ $block_patterns_manager_instance, 'register_settings' ] ) );
		self::assertNotFalse( has_filter('admin_init', [ $block_patterns_manager_instance, 'unregister_block_patterns' ], 500 ) );
	}

	/**
	 * Test if register function actually registers admin_menu and set_screen functions
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	public function testShouldAddCallPatternsFilter() {

		$manager = new BlockPatternsManager();
		$manager->get_all_patterns();
		$this->assertTrue( Filters\applied('block_pattern_manager_all_patterns') > 0 );
	}

	/**
	 * Test if register function actually registers admin_menu and set_screen functions
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	public function testShouldSaveAndReturnSettings() {

		$manager = new BlockPatternsManager();
		$demo_settings = [
			'pattern1' => 'my-test-capability',
			'pattern2' => 'my-test-capability2',
			'pattern3' => '',
		];


		$manager->save_settings( $demo_settings );
		$returned_settings = $manager->get_settings();

		$this->assertSame( $demo_settings, $returned_settings );
	}

	public function testShouldUnregisterPatternWhenUserDoesNotHaveCapability() {
		$mock = Mockery::mock(BlockPatternsManager::class);
		$mock->makePartial();

		$mock->shouldReceive('save_settings');

		$demo_settings = [
			'pattern1' => 'manage_options',
		];

		$mock->shouldReceive('get_settings')->andReturn($demo_settings);
		$mock->shouldReceive('is_pattern_registered')->with('pattern1')->andReturn(true);
		$mock->shouldReceive('current_user_can')->with('manage_options')->andReturn(false);
		$mock->shouldReceive('unregister_block_pattern')->with('pattern1')->andReturn(true);



		$mock->unregister_block_patterns();
	}

	public function testShouldNotUnregisterPatternWhenUserDoesHaveCapability() {
		$mock = Mockery::mock(BlockPatternsManager::class);
		$mock->makePartial();

		$mock->shouldReceive('save_settings');

		$demo_settings = [
			'pattern1' => 'manage_options',
		];

		$mock->shouldReceive('get_settings')->andReturn($demo_settings);
		$mock->shouldReceive('is_pattern_registered')->with('pattern1')->andReturn(true);
		$mock->shouldReceive('current_user_can')->with('manage_options')->andReturn(true);
		$mock->shouldNotReceive('unregister_block_pattern');

		$mock->unregister_block_patterns();
	}

	public function testShouldNotUnregisterPatternWhenCapabilityIsEmpty() {
		$mock = Mockery::mock(BlockPatternsManager::class);
		$mock->makePartial();

		$mock->shouldReceive('save_settings');

		$demo_settings = [
			'pattern1' => '',
		];

		$mock->shouldReceive('get_settings')->andReturn($demo_settings);
		$mock->shouldReceive('is_pattern_registered')->with('pattern1')->andReturn(true);
		$mock->shouldReceive('current_user_can')->with('manage_options')->andReturn(false);
		$mock->shouldNotReceive('unregister_block_pattern');

		$mock->unregister_block_patterns();
	}

	public function testShouldNotUnregisterPatternWhenPatternIsNotRegistered() {
		$mock = Mockery::mock(BlockPatternsManager::class);
		$mock->makePartial();

		$mock->shouldReceive('save_settings');

		$demo_settings = [
			'pattern1' => '',
		];

		$mock->shouldReceive('get_settings')->andReturn($demo_settings);
		$mock->shouldReceive('is_pattern_registered')->with('pattern1')->andReturn(false);
		// $mock->shouldReceive('current_user_can')->with('manage_options')->andReturn(false);
		$mock->shouldNotReceive('unregister_block_pattern');

		$mock->unregister_block_patterns();
	}
}