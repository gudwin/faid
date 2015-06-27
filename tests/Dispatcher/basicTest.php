<?php
namespace Faid\tests\Dispatcher;

require_once __DIR__ . DIRECTORY_SEPARATOR . '../baseTest.php';

abstract class BasicTest extends \Faid\tests\baseTest {
	const fixtureTestUrl = '/Controller/1/view/helloWorld/2';

	/**
	 *
	 */
	public function setUp() {
		parent::setUp();
		//
		require_once __DIR__ . '/testController.php';
		require_once __DIR__ . '/testFunction.php';

	}

}