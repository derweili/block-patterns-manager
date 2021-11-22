<?php

class BlockPatternsManagerMock {

	/**
	 * Generates a mock object on the singleton Someclass util object.
	 *
	 * @param array $name
	 * @return void
	 */
	public static function expects() {
		// Mock the object
		$mock = \PHPUnit_Framework_MockObject_Generator::getMock(
			'Derweili\BlockPatternsManager\BlockPatternsManager',
			array('save_settings'),
			array(),
			'',
			false
		);

		// Replace protected self reference with mock object
		$ref = new \ReflectionProperty('Derweili\BlockPatternsManager\BlockPatternsManager', 'instance');
		$ref->setAccessible(true);
		$ref->setValue(null, $mock);

		// Set expectations and return values
		$mock
			->expects(new \PHPUnit_Framework_MockObject_Matcher_InvokedCount(1))
			->method('save_settings');
	}

	public static function cleanup() {
		$ref = new \ReflectionProperty('Derweili\BlockPatternsManager\BlockPatternsManager', 'instance');
		$ref->setAccessible(true);
		$ref->setValue(null, null);
	}

}

