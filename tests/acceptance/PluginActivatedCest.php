<?php

class PluginActivatedCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
    }

    public function seePluginActivated( AcceptanceTester $I ) {
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->seePluginActivated( 'block-patterns-manager' );
    }
}
