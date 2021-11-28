<?php

use Derweili\BlockPatternsManager\Plugin;

/**
 * Create admin_url() stub
 */
if( ! function_exists( 'admin_url' ) ) {
	function admin_url( $path = '' ) {
		return 'http://localhost/wp58rc/wp-admin/admin.php?page=block-patterns-manager';
	}
}

class PluginTest extends \Codeception\Test\Unit
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

    // tests
    public function testShouldAddLinkToArray()
    {
      $plugin = new Plugin();
      $links = $plugin->plugin_settings_link([]);
      
      $this->assertIsArray($links);
      $this->assertEquals(count( $links ), 1);
      $this->assertIsString($links[0]);
    }
}