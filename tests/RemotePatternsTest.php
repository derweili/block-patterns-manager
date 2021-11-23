<?php

declare(strict_types=1);

use Derweili\BlockPatternsManager\RemotePatterns;
use Derweili\BlockPatternsManager\BlockPatternsManager;

final class RemotePatternsTest extends PluginDefaultTestCase
{

	public function tearDown(): void {
		Mockery::close();

		parent::tearDown();
	}

	/**
	 * Return an empty BlockPatternManager mock class
	 */
	public function getDefaultBlockPatternsManagerMock() {
		$mock = Mockery::mock(BlockPatternsManager::class);
		return $mock;
	}

	/**
	 * Test if register function actually registers functions
	 * 
	 * Todo: Mock Block Patterns Manager
	 */
	public function testShouldAddHooksOnRegistationMethod() {
		$block_patterns_manager_instance = new RemotePatterns( $this->getDefaultBlockPatternsManagerMock() );
		$block_patterns_manager_instance->register();

		self::assertNotFalse( has_filter('should_load_remote_block_patterns', [ $block_patterns_manager_instance, 'disable_remote_block_patterns' ] ) );
		self::assertNotFalse( has_filter('block_pattern_manager_all_patterns', [ $block_patterns_manager_instance, 'add_pattern_directory_to_pattern_list' ] ) );
	}

	/**
	 * 
	 */
	public function testShouldAddPatternDirectoryToPatternList() {
		/**
		 * check if it is instance of AdminPage class
		 */
		$remote_patterns = new RemotePatterns( $this->getDefaultBlockPatternsManagerMock() );

		$block_patterns = $remote_patterns->add_pattern_directory_to_pattern_list( [] );

		$this->assertIsArray( $block_patterns );
		$this->assertEquals( count( $block_patterns ) , 1 );
		$this->assertEquals( $block_patterns[0]['name'], 'block_pattern_directory' );
	}

	/**
	 * Test if add_pattern_directory_to_pattern_list() actually adds
	 * the pattern to the pattern-array and preserve existing patterns
	 */
	public function testShouldAddPatternDirectoryToExistingPatternList() {
		/**
		 * check if it is instance of AdminPage class
		 */
		$remote_patterns = new RemotePatterns( $this->getDefaultBlockPatternsManagerMock() );

		$block_patterns = $remote_patterns->add_pattern_directory_to_pattern_list( [
			[
				'name' => 'existingItem',
				'title' => 'existingItemTitle',
				'description' => 'Description'
			]
		] );

		$this->assertIsArray( $block_patterns );
		$this->assertEquals( count( $block_patterns ) , 2 );
		$this->assertEquals( $block_patterns[0]['name'], 'existingItem' ); // make sure exising array is not overwritten
		$this->assertEquals( $block_patterns[1]['name'], 'block_pattern_directory' ); // make sure new item is added to array
	}
	
	public function testShouldLoadSettingsFromBlockPatternsManager() {

		$mock = Mockery::mock(BlockPatternsManager::class);
		$mock->shouldReceive('get_settings')->andReturn('');

		$remote_patterns = new RemotePatterns( $mock );

		$block_patterns = $remote_patterns->load_disable_remote_patterns_capability_setting();
	}

	public function testShouldLoadRemotePatternsCapabilityFromGeneralSettings() {

		$mock = Mockery::mock(BlockPatternsManager::class);
		$mock->shouldReceive('get_settings')->andReturn([
			'block_pattern_directory' => 'manage_options'
		]);

		$remote_patterns = new RemotePatterns( $mock );
		$capability = $remote_patterns->load_disable_remote_patterns_capability_setting();

		$this->assertEquals( $capability, 'manage_options' );
	}

	public function testShouldReturnFalseIfNoRemovePatternsCapabilitiesFound() {

		$mock = Mockery::mock(BlockPatternsManager::class);
		$mock->shouldReceive('get_settings')->andReturn([]);

		$remote_patterns = new RemotePatterns( $mock );
		$capability = $remote_patterns->load_disable_remote_patterns_capability_setting();

		$this->assertEquals( $capability, false );
	}

	public function testShouldLoadCapability() {
		$blockPatternsManagerMock = Mockery::mock(BlockPatternsManager::class);

		$demo_capability = 'manage_options';

		
		$mock = Mockery::mock(RemotePatterns::class, [
			$blockPatternsManagerMock
		]);

		$mock->shouldReceive('load_disable_remote_patterns_capability_setting')->andReturn($demo_capability)->times(1);

		$mock->makePartial();

		/**
		 * Run get settings two times
		 * 
		 * First time it should call load_disable_remote_patterns_capability_setting() method
		 * Second time should return stored capability
		 */
		$setting_1 = $mock->get_setting();

		$this->assertEquals( $setting_1, $demo_capability );
	}

	public function testShouldStoreAndReuseStoredCapability() {
		$blockPatternsManagerMock = Mockery::mock(BlockPatternsManager::class);

		$demo_capability = 'manage_options';

		
		$mock = Mockery::mock(RemotePatterns::class, [
			$blockPatternsManagerMock
		]);

		$mock->shouldReceive('load_disable_remote_patterns_capability_setting')->andReturn($demo_capability)->times(1);

		$mock->makePartial();

		/**
		 * Run get settings two times
		 * 
		 * First time it should call load_disable_remote_patterns_capability_setting() method
		 * Second time should return stored capability
		 */
		$setting_1 = $mock->get_setting();
		$setting_2 = $mock->get_setting();

		$this->assertEquals( $setting_1, $demo_capability );
		$this->assertEquals( $setting_2, $demo_capability );
	}

	public function testShouldDisableRemotePatternsWhenUserNotHasThePermission() {
		$blockPatternsManagerMock = Mockery::mock(BlockPatternsManager::class);

		$demo_capability = 'manage_options';

		$mock = Mockery::mock(RemotePatterns::class, [
			$blockPatternsManagerMock
		]);

		$mock->shouldReceive('get_setting')->andReturn($demo_capability)->times(2);
		$mock->shouldReceive('current_user_can')->with($demo_capability)->andReturn(false)->times(1);

		$mock->makePartial();

		$this->assertEquals(
			false,
			$mock->disable_remote_block_patterns( true )
		);
	}

	public function testShouldNotDisableRemotePatternsWhenUserHasThePermission() {
		$blockPatternsManagerMock = Mockery::mock(BlockPatternsManager::class);

		$demo_capability = 'manage_options';

		$mock = Mockery::mock(RemotePatterns::class, [
			$blockPatternsManagerMock
		]);

		$mock->shouldReceive('get_setting')->andReturn($demo_capability)->times(2);
		$mock->shouldReceive('current_user_can')->with($demo_capability)->andReturn(true)->times(1);

		$mock->makePartial();

		$this->assertEquals(
			true,
			$mock->disable_remote_block_patterns( true )
		);
	}

	public function testShouldNotDisableRemotePatternIfCapabilitiesEmpty() {
		$blockPatternsManagerMock = Mockery::mock(BlockPatternsManager::class);

		$demo_capability = 'manage_options';

		$mock = Mockery::mock(RemotePatterns::class, [
			$blockPatternsManagerMock
		]);

		$mock->shouldReceive('get_setting')->andReturn(false)->times(1);

		$mock->makePartial();

		$this->assertEquals(
			true,
			$mock->disable_remote_block_patterns( true )
		);
	}
}