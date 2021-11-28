<?php

use Derweili\BlockPatternsManager\Plugin;
use Derweili\BlockPatternsManager\BlockPatternsManager;
use Derweili\BlockPatternsManager\AdminPage;
use Derweili\BlockPatternsManager\BlockPatternsListTable;

class BlockPatternsListTableTest extends Codeception\TestCase\WPTestCase {

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

	public function testCapabilitiesShouldBeStrings() {
		$GLOBALS['hook_suffix'] = isset($GLOBALS['hook_suffix']) ? $GLOBALS['hook_suffix'] : "";

		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$default_capability_groups = $listTable->get_default_capability_groups();


		foreach ($default_capability_groups as $group) {
			$this->assertIsString($group['label']);

			foreach ($group['capabilities'] as $capability) {

				$this->assertIsString($capability);
			}
		}
	}

	public function testShouldGetDefaultCapabilitiesAsArrayOfStrings() {
		$GLOBALS['hook_suffix'] = isset($GLOBALS['hook_suffix']) ? $GLOBALS['hook_suffix'] : "";

		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$capabilities = $listTable->get_default_capabilities();


		foreach ($capabilities as $capability) {

			$this->assertIsString($capability);
		}
	}

	public function testShouldInclude() {
		$GLOBALS['hook_suffix'] = isset($GLOBALS['hook_suffix']) ? $GLOBALS['hook_suffix'] : "";

		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$capabilities = $listTable->get_default_capabilities();

		$this->assertTrue( in_array( 'disable_for_all_users', $capabilities ) );
	}

	public function testShouldReturnFalseForBuildInCapabilities() {
		$GLOBALS['hook_suffix'] = isset($GLOBALS['hook_suffix']) ? $GLOBALS['hook_suffix'] : "";

		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$capabilities = $listTable->get_default_capabilities();

		
		foreach ($capabilities as $capability) {
			$this->assertFalse( $listTable->is_custom_capability( $capability ) );
		}
	}

	public function testShouldReturnTrueForCustomCapabilities() {
		$GLOBALS['hook_suffix'] = isset($GLOBALS['hook_suffix']) ? $GLOBALS['hook_suffix'] : "";

		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );

		$this->assertTrue( $listTable->is_custom_capability( 'my_custom_capability' ) );
	}
}
