<?php
namespace Faid\Tests\StaticObservable;

use \Faid\Debug\Debug;
use \Faid\StaticObservable;

require_once __DIR__ . '/EventMock.php';
require_once __DIR__ . '/EventControllerMock.php';
/**
 * Smoke tests for staticObservable
 * Class basicStaticObservableTest
 * @package Faid\Tests
 */
class basicStaticObservableTest extends \PHPUnit_Framework_TestCase {

	const eventNameFixture = 'testEvent';
	/**
	 *
	 */
	public function setUp() {
		EventControllerMock::cleanUp();
		EventMock::cleanUp();
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function testCallEvents() {
		$called = false;
		$fixture = 'myText';
		EventControllerMock::addEventListener( self::eventNameFixture,  function ( $data ) use ( &$called, $fixture ) {
			if ( $data == $fixture ) {
				$called = true;
			} else {
				throw new Exception('Function Argument not equal to original data ');
			}

		});
		//
		EventControllerMock::testCallEvent( self::eventNameFixture,$fixture );
		//
		$this->assertEquals( $called, true );
	}
	public function testCallFilter() {

		$startData = 'Hello';
		$finalData = 'Hello lovely PHP world!';
		//
		EventControllerMock::addEventListener( self::eventNameFixture, function ( $message) {
			return $message . ' lovely PHP';
		});
		//
		EventControllerMock::addEventListener( self::eventNameFixture, function ( $message) {
			return $message . ' world!';
			});
		//
		$resultData = EventControllerMock::testCallFilter( self::eventNameFixture, $startData);
		//
		$this->assertEquals( $resultData, $finalData );

	}
}