<?php

namespace Faid\tests\Dispatcher;

use \Faid\Controller\Controller as Controller;

class TestController extends Controller
{
    protected static $called = false;

    protected static $beforeActionCalled = false;

    public function __construct()
    {
        self::$called             = false;
        self::$beforeActionCalled = false;
    }

    /**
     * @param $request
     */
    public function beforeAction($request)
    {
        self::$beforeActionCalled = true;
    }

    /**
     * @return bool
     */
    public static function getBeforeActionCalled()
    {
        return self::$beforeActionCalled;
    }

    /**
     * @return bool
     */
    public static function getCalled()
    {
        return self::$called;
    }

    /**
     * Test action method
     */
    public function someAction()
    {
        self::$called = true;
    }
}
