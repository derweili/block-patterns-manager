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
		$GLOBALS['hook_suffix'] = isset($GLOBALS['hook_suffix']) ? $GLOBALS['hook_suffix'] : "";
	}

	protected function _after() {
	}

	public function get_demo_patterns() {
		return [
			[
				'title'       => 'C - My Pattern Title',
				'categories'	=> [ 'category1', 'category2' ],
				'viewportWidth' => 1440,
				'description' => 'My Description',
				'content'	=> '<h1>My Pattern title</h1>',
				'name'				=> 'b-manager/mypatterntitle'
			],
			[
				'title'       => 'A - My Pattern Title',
				'categories'	=> [ 'category1', 'category2' ],
				'viewportWidth' => 1440,
				'description' => 'My Description',
				'content'	=> '<h1>My Pattern title</h1>',
				'name'				=> 'x-manager/mypatterntitle'
			],
			[
				'title'       => 'X - My Pattern Title',
				'categories'	=> [ 'category1', 'category2' ],
				'viewportWidth' => 1440,
				'description' => 'My Description',
				'content'	=> '<h1>My Pattern title</h1>',
				'name'				=> 'a-manager/mypatterntitle'
			],
			[
				'title'       => 'B - My Pattern Title',
				'categories'	=> [ 'category1', 'category2' ],
				'viewportWidth' => 1440,
				'description' => 'My Description',
				'content'	=> '<h1>My Pattern title</h1>',
				'name'				=> 'c-manager/mypatterntitle'
			]
		];
	}

	public function getDefaultBlockPatternsManagerMock() {
		$mock = $this->getMockBuilder(BlockPatternsManager::class)
			->setMethods(['get_all_patterns'])
      ->getMock();

		$mock
      ->method('get_all_patterns')
      ->willReturn($this->get_demo_patterns());

		return $mock;
	}

	public function getDefaultBlockPatternsListTableMock() {
		$listTableMock = $this->getMockBuilder(BlockPatternsListTable::class)
		->setConstructorArgs( [ $this->getDefaultBlockPatternsManagerMock() ] )
		->setMethods(['process_bulk_action', 'get_pagenum', 'set_pagination_args', 'get_column_info' ])
		->getMock();

		$listTableMock
			->method('get_pagenum')
			->willReturn(1);

		$listTableMock
			->method('set_pagination_args')
			->willReturn(null);

		$listTableMock
			->method('get_column_info')
			->willReturn([
				[
					"title"	=> "Block pattern title",
					"name"	=> "Block pattern name",
					"description"	=> "Block pattern description",
					"capability"	=> "Required capability to use the block pattern",
				]
			]);


		$listTableMock
			->method('process_bulk_action')
			->willReturn(null);

		return $listTableMock;
	}

	public function testCapabilitiesShouldBeStrings() {
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
		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$capabilities = $listTable->get_default_capabilities();


		foreach ($capabilities as $capability) {

			$this->assertIsString($capability);
		}
	}

	public function testShouldInclude() {
		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$capabilities = $listTable->get_default_capabilities();

		$this->assertTrue( in_array( 'disable_for_all_users', $capabilities ) );
	}

	public function testShouldReturnFalseForBuildInCapabilities() {
		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$capabilities = $listTable->get_default_capabilities();

		
		foreach ($capabilities as $capability) {
			$this->assertFalse( $listTable->is_custom_capability( $capability ) );
		}
	}

	public function testShouldReturnTrueForCustomCapabilities() {
		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );

		$this->assertTrue( $listTable->is_custom_capability( 'my_custom_capability' ) );
	}

	public function testShouldSetItems() {
		
		$listTableMock = $this->getDefaultBlockPatternsListTableMock();

		$listTableMock->prepare_items();
			
		// dbut output items
		// codecept_debug($listTableMock->items);

		// test if items is array
		$this->assertIsArray($listTableMock->items);

		// test if items is not empty
		$this->assertNotEmpty($listTableMock->items);

		// test if items has two items
		$this->assertCount(4, $listTableMock->items);
	}

	public function testShouldSortItemsAscByTitle() {
		
		$listTableMock = $this->getDefaultBlockPatternsListTableMock();

		$_GET['orderby'] = 'title';
		$_GET['order'] = 'asc';

		$listTableMock->prepare_items();

		// test if items are ordered asc
		$this->assertEquals( 'A - My Pattern Title', $listTableMock->items[0]['title'] );
	}

	public function testShouldSortItemsDescByTitle() {
		
		$listTableMock = $this->getDefaultBlockPatternsListTableMock();

		
		$_GET['orderby'] = 'title';
		$_GET['order'] = 'desc';

		$listTableMock->prepare_items();

		// test if items are ordered asc
		$this->assertEquals( 'X - My Pattern Title', $listTableMock->items[0]['title'] );
	}

	public function testShouldSortItemsAscByName() {
		
		$listTableMock = $this->getDefaultBlockPatternsListTableMock();

		$_GET['orderby'] = 'name';
		$_GET['order'] = 'asc';

		$listTableMock->prepare_items();

		// test if items are ordered asc
		$this->assertEquals( 'a-manager/mypatterntitle', $listTableMock->items[0]['name'] );
	}

	public function testShouldSortItemsDescByName() {
		
		$listTableMock = $this->getDefaultBlockPatternsListTableMock();

		
		$_GET['orderby'] = 'name';
		$_GET['order'] = 'desc';

		$listTableMock->prepare_items();

		// test if items are ordered asc
		$this->assertEquals( 'x-manager/mypatterntitle', $listTableMock->items[0]['name'] );
	}


	public function testShouldHaveFourColumns() {
		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$columns = $listTable->get_columns();

		// test if items is array
		$this->assertIsArray($columns);

		// test if items has two items
		$this->assertCount(4, $columns);
	}

	public function testSholdBeAssoziativeArrayOfStrings() {
		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$columns = $listTable->get_columns();

		foreach ($columns as $key => $value) {
			$this->assertIsString($key);
			$this->assertIsString($value);
		}
	}

	public function testShouldReturnSortableColumns() {
		$listTable = new BlockPatternsListTable( $this->getDefaultBlockPatternsManagerMock() );
		
		$sortableColumns = $listTable->get_sortable_columns();

		$this->assertCount(2, $sortableColumns);

		foreach ($sortableColumns as $key => $value) {
			$this->assertIsString($key);
			$this->assertIsString($value[0]);
			$this->assertTrue($value[1]);
		}

		$this->assertArrayHasKey('title', $sortableColumns);
		$this->assertArrayHasKey('name', $sortableColumns);
	}

	public function testShouldRenderTable() {
		$listTableMock = $this->getMockBuilder(BlockPatternsListTable::class)
		->setConstructorArgs( [ $this->getDefaultBlockPatternsManagerMock() ] )
		->setMethods(['process_bulk_action', 'get_pagenum', 'set_pagination_args' ])
		->getMock();

		$listTableMock
			->method('get_pagenum')
			->willReturn(1);

		$listTableMock
			->method('set_pagination_args')
			->willReturn(null);

		$listTableMock
			->method('process_bulk_action')
			->willReturn(null);

		$listTableMock->prepare_items();

		ob_start();
		
		$listTableMock->display();

		$output = ob_get_clean();

		$this->assertNotEmpty($output);

		$this->assertStringContainsString('wp-list-table', $output);
	}
}
