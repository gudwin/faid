<?php

namespace Faid\Tests\Controller;

use Faid\tests\baseTest;
use \Faid\View\View;
use \Faid\Response\JsonResponse;


class basicTest extends baseTest
{
    const ViewParamName = 'testKey';

    const ViewParamValue = 'value';

    /**
     * @var string
     */
    protected $viewPath = '';

    /**
     *
     */
    public function setUp(): void
    {
        $this->viewPath = __DIR__ . DIRECTORY_SEPARATOR . 'testView.php';
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testSet()
    {
        $view = new View($this->viewPath);
        $controller = new TestController();
        $controller->setView($view);
        $controller->set(self::ViewParamName, self::ViewParamValue);
        $this->assertEquals(self::ViewParamValue, $view->get(self::ViewParamName));
    }

    public function testAutoRender()
    {
        //
        $this->expectOutputString(self::ViewParamValue);
        $view = new View($this->viewPath);
        $view->set(self::ViewParamName, self::ViewParamValue);
        //
        $view->set(self::ViewParamName, self::ViewParamValue);
        $controller = new TestController();
        $controller->setView($view);
        $controller->afterAction();
    }

    /**
     *
     */
    public function testAutoSendResponse()
    {
        $expected = array(
            self::ViewParamName => self::ViewParamValue
        );
        $this->expectOutputString(json_encode($expected));
        //
        $response = new JsonResponse();
        $response->set(self::ViewParamName, self::ViewParamValue);
        //
        $controller = new TestController();
        $controller->setResponse($response);
        //
        $controller->afterAction();
        //
    }
}
