<?php
namespace Faid\Tests;

use \Faid\View\View;
use \Faid\View\Exception as ViewException;

require_once __DIR__ . '/basicHelper.php';
class helperViewTest extends \PHPUnit_Framework_TestCase {


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
		$this->helperViewPath = __DIR__ . DIRECTORY_SEPARATOR . 'helper.tpl';

		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @expectedException \Faid\View\Exception
	 */
	public function testUnknownHelperClass() {
		$view = new View($this->viewPath);
		$view->addHelper('unknownHelper', 'unknown');
	}

	/**
	 * @expectedException \Faid\View\Exception
	 */
	public function testHelperWithName() {
		$view = new View($this->viewPath);
		//
		$helper = new basicHelper();
		$view->addHelper($helper);
		//
		$this->assertEquals($helper, $view->basicHelper);
	}

	public function testHelperObjectAccessableFromView() {
		$view   = new View($this->helperViewPath);
		$helper = new basicHelper();
		$view->addHelper($helper, 'basic');
		$view->render();
		$this->AssertTrue($helper->isViewMethodCalled());
	}
	public function testHelpersSupportsUpperCase( ) {
		$view   = new View($this->helperViewPath);
		$helper = new basicHelper();
		$view->addHelper($helper, 'Basic');
		$this->assertTrue( !empty( $view->Basic )) ;
	}

	/**
	 * @expectedException \Faid\View\Exception
	 */
	public function testGetUnknownHelper() {
		$view = new View($this->viewPath);
		$view->Unknown;
	}

	public function testGetNameHelper() {
		$view   = new View($this->viewPath);
		$helper = new basicHelper();
		$view->addHelper($helper, 'CoolName');
		$this->assertEquals($helper, $view->CoolName);
	}
}