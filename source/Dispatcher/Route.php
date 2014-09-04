<?php
namespace Faid\Dispatcher {
	class Route {
		protected $ready = false;

		protected $action = false;

		protected $controller = false;

		protected $request = false;

		protected $callback = false;

		/**
		 * @param $request
		 */
		public function __construct( $config = array()) {
			$defaultConfig = array(
				'controller' => '',
				'action' => '',
				'request' => null,
				'callback' => null
			);
			$config = array_merge( $defaultConfig, $config );

			$this->action = $config['action'];
			$this->controller = $config['controller'];
			$this->request = $config['request'];
			$this->callback = $config['callback'];
			$this->ready = false;
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
		public function getCallback( ) {
			return $this->callback;
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

		protected function getRouteCallback( ) {
			if ( empty( $this->callback )) {
				//
				if ( !empty($this->controller) ) {
					if ( !is_object($this->controller) ) {
						$this->controller = new $this->controller();
					}
					if ( $this->controller instanceof \Faid\Controller\Controller ) {
						$this->controller->beforeAction($this->request);
					}
					$callback = array($this->controller, $this->action);
				} else {
					$callback = $this->action;
				}
			} else {
				$callback = $this->callback;
			}
			//
			if ( !is_callable($callback) ) {
				throw new RouteException('Route failed to dispatch. Callback not callable');
			}
			return $callback;

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