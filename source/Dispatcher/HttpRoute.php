<?php
namespace Faid\Dispatcher {
	use \Faid\Request\HttpRequest;

	class HttpRoute extends Route {
		/**
		 * @var string
		 */
		protected $urlTemplate = '';

		/**
		 * @return string
		 */
		public function getUrlTemplate() {
			return $this->urlTemplate;
		}

		/**
		 * @param $urlTemplate
		 * @param $options
		 *
		 * @return HttpRoute
		 * @throws RouteException
		 */
		public static function create($urlTemplate, $options) {
			if ( empty($urlTemplate) ) {
				throw new RouteException('Route url template not specified');
			}

			$result = new HttpRoute(array());

			$result->urlTemplate = $urlTemplate;
			foreach ($options as $key => $value) {
				$key = strtolower($key);
				if ( isset($result->$key) ) {
					$result->$key = $value;
				}
			}

			return $result;
		}

		/**
		 * @param $request
		 *
		 * @return bool
		 */
		public function test($request) {
			parent::test($request);
			if ( !$this->request instanceof HttpRequest ) {
				throw new RouteException('Request object must be instance of \\Faid\\Request\\HttpRequest');
			}
			//
			$regExp = $this->getRegexp();
			//
			$this->ready = preg_match($regExp, $request->url());

			//
			return $this->ready;
		}

		protected function getRegExp() {
			$result = '@' . $this->urlTemplate . '@si';
			// Replace wildcard
			$result = str_replace('*', '(.*)', $result);
			//
			$result = preg_replace('/:(\w+)/', '([\w-]+)', $result);

			//
			return $result;
		}

		/**
		 * @throws RouteException
		 * @return HttpRoute
		 */
		public function dispatch() {
			parent::dispatch();
			//
			$callback = $this->getCallback();
			call_user_func($callback);
			//
			$isController = is_array( $callback ) && is_object( $callback[0]) && ( $callback[0] instanceof \Faid\Controller\Controller );
			//
			if ( $isController ) {
				$controller = $callback[0];
				$controller->afterAction( );
			}
		}
		protected function getCallback( ) {
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
			//
			if ( !is_callable($callback) ) {
				throw new RouteException('Route failed to dispatch. Callback not callable');
			}
			return $callback;

		}
		/**
		 *
		 */
		public function prepareRequest() {
			$regExp = $this->getRegExp();
			preg_match($regExp, $this->request->url(), $matches);
			$params = array();
			//
			$unnamedParamIndex = 1;

			if ( preg_match_all("/(\*)|:([\w-]+)/", $this->urlTemplate, $argument_keys) ) {
				// grab array with matches
				$argument_keys = $argument_keys[ 0 ];

				// loop trough parameter names, store matching value in $params array
				foreach ($argument_keys as $key => $name) {
					if ( '*' != $name ) {
						$name = substr($name, 1);
					}
					if ( isset($matches[ $key + 1 ]) ) {
						if ( '*' != $name ) {
							if ( !in_array($name, array('action', 'controller')) ) {
								$params[ $name ] = $matches[ $key + 1 ];
							} else {
								$this->$name = strtolower($matches[ $key + 1 ]);
							}
						} else {
							$list = explode('/', $matches[ $key + 1 ]);
							foreach ($list as $row) {
								$name = 'param' . $unnamedParamIndex;
								$unnamedParamIndex++;
								$params[ $name ] = $row;
							}
						}


					}
				}
			}
			$this->request->set($params);
		}
	}
}
?>