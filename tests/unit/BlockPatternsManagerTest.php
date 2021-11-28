<?php

use Derweili\BlockPatternsManager\BlockPatternsManager;
use Brain\Monkey;
use Brain\Monkey\Filters;
use Brain\Monkey\Actions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class BlockPatternsManagerTest extends \Codeception\Test\Unit
{

  use MockeryPHPUnitIntegration;

  protected function setUp() : void {
      parent::setUp();
      Monkey\setUp();
  }

  protected function tearDown() : void {
      Monkey\tearDown();
      parent::tearDown();
  }

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
    $demo_settings = [
      'pattern1' => 'manage_options',
    ];

    $mock = $this->getMockBuilder(BlockPatternsManager::class)
    ->setMethods(['get_settings', 'is_pattern_registered', 'current_user_can', 'unregister_block_pattern'])
    ->getMock();

    $mock->method('get_settings')
      ->willReturn( $demo_settings );

    $mock->expects($this->once())
      ->method('is_pattern_registered')
      ->with('pattern1')
      ->willReturn(true);

    $mock->expects($this->once())
      ->method('current_user_can')
      ->with('manage_options')
      ->willReturn(false);

    $mock->expects($this->once())
      ->method('unregister_block_pattern')
      ->with('pattern1');

		$mock->unregister_block_patterns();
	}

  public function testShouldNotUnregisterPatternWhenUserDoesHaveCapability() {

  	$demo_settings = [
			'pattern1' => 'manage_options'
		];

    $mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->setMethods(['get_settings', 'is_pattern_registered', 'current_user_can', 'unregister_block_pattern'])
      ->getMock();

    $mock->method('get_settings')
      ->willReturn( $demo_settings );

    $mock->expects($this->once())
      ->method('is_pattern_registered')
      ->with('pattern1')
      ->willReturn(true);
    
    $mock->expects($this->once())
      ->method('current_user_can')
      ->with('manage_options')
      ->willReturn(true);

    $mock->expects($this->never())
      ->method('unregister_block_pattern');

    $mock->unregister_block_patterns();
	}

  public function testShouldNotUnregisterPatternWhenCapabilityIsEmpty() {

  	$demo_settings = [
			'pattern1' => ''
		];

    $mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->setMethods(['get_settings', 'is_pattern_registered', 'current_user_can', 'unregister_block_pattern'])
      ->getMock();

    $mock->method('get_settings')
      ->willReturn( $demo_settings );

    $mock->expects($this->once())
      ->method('is_pattern_registered')
      ->with('pattern1')
      ->willReturn(true);

    $mock->expects($this->never())
      ->method('unregister_block_pattern');

    $mock->unregister_block_patterns();
	}

  public function testShouldNotUnregisterPatternWhenPatternIsNotRegistered() {

  	$demo_settings = [
			'pattern1' => 'manage_options'
		];

    $mock = $this->getMockBuilder(BlockPatternsManager::class)
      ->setMethods(['get_settings', 'is_pattern_registered', 'current_user_can', 'unregister_block_pattern'])
      ->getMock();

    $mock->method('get_settings')
      ->willReturn( $demo_settings );

    $mock->expects($this->once())
      ->method('is_pattern_registered')
      ->with('pattern1')
      ->willReturn(false);

    $mock->expects($this->never())
      ->method('unregister_block_pattern');

    $mock->unregister_block_patterns();
	}


	/**
	 * Test if register function actually registers admin_menu and set_screen functions
	 * 
	 */
	public function testShouldAddCallPatternsFilter() {

		$manager = new BlockPatternsManager();
		$manager->get_all_patterns();
		$this->assertTrue( Filters\applied('block_pattern_manager_all_patterns') > 0 );
	}
}