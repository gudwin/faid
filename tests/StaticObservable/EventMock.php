<?php
namespace Faid\Tests\StaticObservable;

class EventMock {
	protected static $result;
	public static function cleanUp() {
		self::$result = 0;
	}
	public static function getResult() {
		return self::$result;
	}
	public static function sum( $value ) {
		self::$result += intval( $value );
	}
	public static function call1() {
		self::sum( 1 );
	}
	public static function call2() {
		self::sum( 2 );
	}
}