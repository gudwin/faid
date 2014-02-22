<?php
namespace Faid\Tests;
class basicHelper {
	protected $flag = false;
	protected $viewMethodCalled = false;

	/**
	 *
	 */
	public function __construct( ) {
		$this->flag = false;
	}

	/**
	 * @param $view
	 */
	public function beforeRender( $view ) {
		$this->flag = true;
	}
	public function getFlag( ) {
		return $this->flag;
	}
	public function viewMethod( ) {
		$this->viewMethodCalled = true;
	}
	public function isViewMethodCalled( ) {
		return $this->viewMethodCalled;
	}
}