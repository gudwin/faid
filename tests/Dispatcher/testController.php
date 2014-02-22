<?php
namespace Faid\Tests;

use \Faid\Controller\Controller;

class testController extends Controller {
	static protected $called = false;

	static protected $beforeActionCalled = false;

	public function __construct() {
		self::$called             = false;
		self::$beforeActionCalled = false;
	}

	/**
	 * @param $request
	 */
	public function beforeAction($request) {
		self::$beforeActionCalled = true;
	}

	/**
	 * @return bool
	 */
	public static function getBeforeActionCalled() {
		return self::$beforeActionCalled;
	}

	/**
	 * @return bool
z	 */
	public static function getCalled() {
		return self::$called;
	}

	/**
	 * Test action method
	 */
	public function someAction() {
		self::$called = true;
	}
}