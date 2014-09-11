<?php

namespace Faid\tests\Cache;


class TestCacheEngine implements \Faid\Cache\Engine\CacheEngineInterface {
	public static $lastMethod = '';
	public static $methodParams = array();
	public function setUp( ) {
		self::$lastMethod = '';
		self::$methodParams = array();
	}
	public function get( $key ) {
		self::$lastMethod = __METHOD__ ;
		self::$methodParams = func_get_args();
	}

	public function clear( $key ) {
		self::$lastMethod = __METHOD__;
		self::$methodParams = func_get_args();
	}

	public function set( $key, $value, $timeActual = 0 ) {
		self::$lastMethod = __METHOD__;
		self::$methodParams = func_get_args();
	}

	public function isActual( $key ) {
		self::$lastMethod = __METHOD__;
		self::$methodParams = func_get_args();
	}
} 