<?php
namespace Faid\Tests\StaticObservable;

use \Faid\Debug\Debug;
use \Faid\StaticObservable;

/**
 * Smoke tests for staticObservable
 * Class basicStaticObservableTest
 * @package Faid\Tests
 */
class EventControllerMock extends StaticObservable {
	public static function cleanUp() {
		self::$eventMap = array();
	}
	public static function testCallEvent($event, $arguments ) {
		return self::callEvent( $event, $arguments );
	}
	public static function testCallFilter( $event, $data ) {
		return self::callFilter( $event, $data);
	}
}