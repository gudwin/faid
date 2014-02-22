<?php
namespace Faid\Dispatcher {
	class Route {
		protected $ready = false;

		protected $action = false;

		protected $controller = false;

		protected $request = false;

		/**
		 * @param $request
		 */
		public function __construct( ) {
		}

		/**
		 *
		 */
		public function getAction() {
			return $this->action;
		}

		/**
		 *
		 */
		public function getController() {
			return $this->controller;
		}

		/**
		 * @param $action
		 */
		public function setAction($action) {
			$this->action = $action;
		}

		/**
		 * @param $controller
		 */
		public function setController($controller) {
			$this->controller = $controller;
		}

		/**
		 * @param $request
		 *
		 * @return bool
		 */
		public function test($request) {
			$this->request = $request;
			//
			$this->ready = false;
			//
			return false;
		}

		/**
		 * Called by dispatcher class. Main idea is - prepare request class to be called from controller
		 */
		public function prepareRequest( ) {

		}
		/**
		 * @throws RouteException
		 */
		public function dispatch() {
			if ( !$this->ready ) {
				throw new RouteException();
			}
		}
	}
}
?>