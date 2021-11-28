<?php

use Derweili\BlockPatternsManager\RemotePatterns;
use Derweili\BlockPatternsManager\BlockPatternsManager;

class RemotePatternsTest extends \Codeception\Test\Unit
{

  /**
   * @var \UnitTester
   */
  protected $tester;
  
  protected function _before()
  {
  }

  protected function _after()
  {
  }

  /**
	 * Return an empty BlockPatternManager mock class
	 */
	public function getDefaultBlockPatternsManagerMock() {
		$mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->getMock();

    return $mock;
	}

  public function testShouldAddPatternDirectoryToPatternList() {
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
    $mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->setMethods(['get_settings'])
      ->getMock();

    $mock->expects($this->once())
      ->method('get_settings')
      ->willReturn([
        'block_pattern_directory' => 'manage_options'
      ]);

		$remote_patterns = new RemotePatterns( $mock );
		$capability = $remote_patterns->load_disable_remote_patterns_capability_setting();

		$this->assertEquals( $capability, 'manage_options' );
	}

  public function testShouldReturnFalseIfNoRemovePatternsCapabilitiesFound() {
    $mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->setMethods(['get_settings'])
      ->getMock();

    $mock->expects($this->once())
      ->method('get_settings')
      ->willReturn([]);

		$remote_patterns = new RemotePatterns( $mock );
		$capability = $remote_patterns->load_disable_remote_patterns_capability_setting();

		$this->assertEquals( $capability, false );
	}

  public function testShouldLoadCapability() {
    $mock = $this->getMockBuilder(RemotePatterns::class)
      ->setConstructorArgs( [ $this->getDefaultBlockPatternsManagerMock() ] )
      ->setMethods(['load_disable_remote_patterns_capability_setting'])
      ->getMock();

    $demo_capability = 'manage_options';

    $mock->expects($this->once())
      ->method('load_disable_remote_patterns_capability_setting')
      ->willReturn($demo_capability);

    $setting_1 = $mock->get_setting();

		$this->assertEquals( $setting_1, $demo_capability );
	}

  public function testShouldStoreAndReuseStoredCapability() {
    $mock = $this->getMockBuilder(RemotePatterns::class)
      ->setConstructorArgs( [ $this->getDefaultBlockPatternsManagerMock() ] )
      ->setMethods(['load_disable_remote_patterns_capability_setting'])
      ->getMock();

    $demo_capability = 'manage_options';

    $mock->expects($this->once())
      ->method('load_disable_remote_patterns_capability_setting')
      ->willReturn($demo_capability);

    $setting_1 = $mock->get_setting();
    $setting_2 = $mock->get_setting();

		$this->assertEquals( $setting_1, $demo_capability );
		$this->assertEquals( $setting_2, $demo_capability );
	}

  public function testShouldDisableRemotePatternsWhenUserNotHasThePermission() {
    $mock = $this->getMockBuilder(RemotePatterns::class)
      ->setConstructorArgs( [ $this->getDefaultBlockPatternsManagerMock() ] )
      ->setMethods(['get_setting', 'current_user_can'])
      ->getMock();

    $demo_capability = 'manage_options';

    $mock->expects( $this->exactly(2) )
      ->method('get_setting')
      ->willReturn($demo_capability);

    $mock->expects( $this->once() )
      ->method('current_user_can')
      ->with($demo_capability)
      ->willReturn(false);

    $this->assertEquals(
      $mock->should_load_remote_block_patterns( true ),
      false
    );
	}

  public function testShouldNotDisableRemotePatternsWhenUserHasThePermission() {
    $mock = $this->getMockBuilder(RemotePatterns::class)
      ->setConstructorArgs( [ $this->getDefaultBlockPatternsManagerMock() ] )
      ->setMethods(['get_setting', 'current_user_can'])
      ->getMock();

    $demo_capability = 'manage_options';

    $mock->expects( $this->exactly(2) )
      ->method('get_setting')
      ->willReturn($demo_capability);

    $mock->expects( $this->once() )
      ->method('current_user_can')
      ->with($demo_capability)
      ->willReturn(true);

    $this->assertEquals(
      $mock->should_load_remote_block_patterns( true ),
      true
    );
	}

  public function testShouldNotDisableRemotePatternsWhenrequiredCapabilityIsEmpty() {
    $mock = $this->getMockBuilder(RemotePatterns::class)
      ->setConstructorArgs( [ $this->getDefaultBlockPatternsManagerMock() ] )
      ->setMethods(['get_setting', 'current_user_can'])
      ->getMock();

    $demo_capability = '';

    $mock->expects( $this->exactly(1) )
      ->method('get_setting')
      ->willReturn($demo_capability);

    $this->assertEquals(
      $mock->should_load_remote_block_patterns( true ),
      true
    );
	}
}