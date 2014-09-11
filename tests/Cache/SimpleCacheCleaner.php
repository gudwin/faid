<?php


namespace Faid\tests\Cache;


class SimpleCacheCleaner extends \Faid\SimpleCache {
	public static function setUp() {
		self::$instance = null;
	}
} 