<?php
namespace Faid\Tests;

use \Faid\View\View;
use \Faid\View\Exception as ViewException;

require_once __DIR__ . '/basicHelper.php';
class basicViewTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var null
	 */
	protected $layoutPath = NULL;

	/**
	 * @var null
	 */
	protected $viewPath = NULL;

	protected $helperViewPath = NULL;

	/**
	 *
	 */
	public function setUp() {
		$this->viewPath       = __DIR__ . DIRECTORY_SEPARATOR . 'view.tpl';
		$this->layoutPath     = __DIR__ . DIRECTORY_SEPARATOR . 'layout.tpl';
		$this->helperViewPath = __DIR__ . DIRECTORY_SEPARATOR . 'helper.tpl';

		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @expectedException \Faid\View\Exception
	 */
	public function testUnknownViewFile() {
		$view = new View($this->viewPath . '_1');
	}


	/**
	 * @expectedException \Faid\View\Exception
	 */
	public function testUnknownLayout() {
		$view = new View($this->viewPath);
		$view->setLayout('non_existent_layout_path');
	}

	/**
	 *
	 */
	public function testSetGetVars() {
		$view = new View($this->viewPath);
		//
		$view->set('a', 1);
		$view->set(
			array(
				 'b' => 2,
				 'c' => 3,
				 'd' => 4,
				 'e' => array(
					 'f' => 5
				 )
			)
		);
		//
		$viewVars = $view->getViewVars();
		//
		$this->assertArrayHasKey('a', $viewVars);
		//
		$this->assertEquals($viewVars[ 'a' ], 1);
		$this->assertEquals($viewVars[ 'b' ], 2);
		$this->assertEquals($viewVars[ 'e' ][ 'f' ], 5);
	}

	/**
	 *
	 */
	public function testSetGetLayout() {
		$view = new View($this->viewPath);
		//
		$this->assertEquals(NULL, $view->getLayout());
		//
		$view->setLayout($this->layoutPath);
		//
		$this->assertEquals($view->getLayout()->getPath(), $this->layoutPath);
	}

	/**
	 *
	 */
	public function testBeforeRenderEvent() {
		$view   = new View($this->viewPath);
		$helper = new basicHelper();
		$view->set('msg', 'bla-bla');
		$view->addHelper($helper);
		$this->assertEquals(false, $helper->getFlag());
		$view->render();
		$this->assertEquals(true, $helper->getFlag());
	}

	/**
	 *
	 */
	public function testRender() {
		$view = new View($this->viewPath);
		$view->set('msg', 'hello world!');
		//
		$result = $view->render();
		//
		$this->assertRegExp('#hello world\!#msi', $result);
		$this->assertRegExp('#view footer#msi', $result);

	}

	/**
	 *
	 */
	public function testRenderWithLayout() {
		$view = new View($this->viewPath);
		$view->setLayout($this->layoutPath);
		$view->set('msg', 'hello world!');
		//
		$result = $view->render();
		//
		$parts = explode('hello world!', $result);
		$this->assertEquals(3, sizeof($parts));
		$this->assertRegExp('#view footer#msi', $result);
		$this->assertRegExp('#layout footer#msi', $result);
	}

	public function testGetPath() {
		$view = new View($this->viewPath);
		$this->AssertEquals($this->viewPath, $view->getPath());
	}

	public function testIsRendered() {
		//
		$view = new View($this->viewPath);
		//
		$this->assertFalse($view->isRendered());
		//
		$view->set('msg', 'hello world!');
		//
		$result = $view->render();

		$this->assertTrue($view->isRendered());
	}
	public function testEmptyValuesSupported() {
		//
		$view = new View($this->viewPath);
		//
		$this->assertFalse($view->isRendered());
		//
		$view->set('msg', null);
		//
		$result = $view->render();

		$this->assertTrue($view->isRendered());

		$expectedResult = <<<HTML
View header


View footer
HTML;
		$this->assertEquals( $expectedResult, $result );

	}

}