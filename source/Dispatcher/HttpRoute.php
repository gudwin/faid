<?php
namespace Faid\Dispatcher {
	use \Faid\Request\HttpRequest;

	class HttpRoute extends Route {
		/**
		 * @var string
		 */
		protected $urlTemplate = '';

		/**
		 * @param $urlTemplate
		 */
		public function __construct( $config = array() ) {

			if ( empty($config['url']) ) {
				throw new RouteException('Route url template not specified');
			}
			$this->urlTemplate = $config['url'];

			//
			parent::__construct( $config );
		}
		/**
		 * @return string
		 */
		public function getUrlTemplate() {
			return $this->urlTemplate;
		}

		/**
		 * @param $request
		 *
		 * @return bool
		 */
		public function test($request) {
			parent::test($request);
			//
			$regExp = $this->getRegexp();
			//
			$this->ready = @preg_match($regExp, $request->url());

			//
			return $this->ready;
		}

		protected function getRegExp() {
			$result = '@' . $this->urlTemplate . '@si';
			// Replace wildcard
			$result = str_replace('*', '(.*)', $result);
			//
			$result = preg_replace('/:(\w+)/', '([\w-\.]+)', $result);

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
			$callback = $this->getRouteCallback();
			call_user_func($callback);
			//
			$isController = is_array( $callback ) && is_object( $callback[0]) && ( $callback[0] instanceof \Faid\Controller\Controller );
			//
			if ( $isController ) {
				$controller = $callback[0];
				$controller->afterAction( );
			}
		}
        public function buildUrl( $data = []) {
            krsort( $data );
            $search = [];
            $replacements = [];

            foreach ( $data as $key=>$row ) {
                $search[] = ':' . $key;
                $replacements[] = $row;
            }
            return str_replace( $search, $replacements, $this->urlTemplate );
        }
		/**
		 *
		 */
		public function prepareRequest() {
			$regExp = $this->getRegExp();
			preg_match($regExp, $this->request->url(), $matches);
			//
			$unnamedParamIndex = 1;

			if ( preg_match_all("/(\*)|:([\w-]+)/", $this->urlTemplate, $argument_keys) ) {
				$params = array();
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
				$this->request->set($params);
			}

		}
	}
}
?>