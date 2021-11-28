<?php

class AdminPageCest
{
    public function _before(AcceptanceTester $I)
    {
			$I->loginAsAdmin();
    }

    public function testShouldSaveSettings( AcceptanceTester $I ) {
        $I->amOnPluginsPage();
        $I->amOnAdminPage('/tools.php?page=block-patterns-manager');
        
        /**
         * Save settings first time
         */
        $first_test_capability = 'manage_options';

        $I->submitForm( '#block-patterns-manager-admin-page-form', [
            'capabilities[block_pattern_directory]' => $first_test_capability,
        ]);

        $I->seeMessage('.block-patterns-manager-settings-saved');

        $returned_setting = $I->grabValueFrom('input[name="capabilities[block_pattern_directory]"]');
        $returned_setting_2 = $I->grabValueFrom('select[name="capabilities[block_pattern_directory]-select"]');
        $I->canSeeInField('input[name="capabilities[block_pattern_directory]"]', $first_test_capability);
        $I->canSeeInField('select[name="capabilities[block_pattern_directory]-select"]', $first_test_capability);

        /**
         * Save settings second time
         */
        $second_test_capability = 'edit_posts';

        $I->submitForm( '#block-patterns-manager-admin-page-form', [
            'capabilities[block_pattern_directory]' => $second_test_capability,
        ]);

        $I->seeMessage('.block-patterns-manager-settings-saved');

        $returned_setting = $I->grabValueFrom('input[name="capabilities[block_pattern_directory]"]');
        $I->canSeeInField('input[name="capabilities[block_pattern_directory]"]', $second_test_capability);
        $I->canSeeInField('select[name="capabilities[block_pattern_directory]-select"]', $second_test_capability);

    }

    public function testShouldSetSelectToCustom( AcceptanceTester $I ) {
        $I->amOnPluginsPage();
        $I->amOnAdminPage('/tools.php?page=block-patterns-manager');
        
        /**
         * Save settings first time
         */
        $first_test_capability = 'my_custom_capability';

        $I->submitForm( '#block-patterns-manager-admin-page-form', [
            'capabilities[block_pattern_directory]' => $first_test_capability,
        ]);

        $I->seeMessage('.block-patterns-manager-settings-saved');

        $I->canSeeInField('input[name="capabilities[block_pattern_directory]"]', $first_test_capability); // input should be set to our custom capability
        $I->canSeeInField('select[name="capabilities[block_pattern_directory]-select"]', 'custom'); // select should be set to custom
    }
}


