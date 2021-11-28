<?php

use Derweili\BlockPatternsManager\AdminPage;
use Derweili\BlockPatternsManager\BlockPatternsManager;

class AdminPageTest extends \Codeception\Test\Unit
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

  public function getDefaultBlockPatternsManagerMock() {
    return $this->make(BlockPatternsManager::class);
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
}