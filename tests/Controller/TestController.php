<?php

namespace Faid\Tests\Controller;

use \Faid\View\View;

class TestController extends \Faid\Controller\Controller {
	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		return $this->view->get( $key );
	}

	/**
	 * @param View $view
	 */
	public function setView( View $view ) {
		$this->view = $view;
	}
	public function setResponse( \Faid\Response\Response $response) {
		$this->response = $response;
	}
	public function callRender( ) {
		return $this->render();
	}

}