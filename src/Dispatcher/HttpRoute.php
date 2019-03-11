<?php
/**
 * @version: 1.0.1
 */

namespace Faid\Dispatcher {

    use \Faid\Request\HttpRequest;

    class HttpRoute extends Route
    {
        /**
         * @var string
         */
        protected $urlTemplate = '';

        /**
         * @param $urlTemplate
         */
        public function __construct($config = array())
        {

            if (empty($config['url'])) {
                throw new RouteException('Route url template not specified');
            }
            $this->urlTemplate = $config['url'];
            parent::__construct($config);
        }

        /**
         * @return string
         */
        public function getUrlTemplate()
        {
            return $this->urlTemplate;
        }

        /**
         * @param $request
         *
         * @return bool
         */
        public function test($request)
        {
            parent::test($request);
            $regExp = $this->getRegexp();
            return @preg_match($regExp, $request->url());
        }

        protected function getRegExp()
        {
            $result = '@' . $this->urlTemplate . '@si';
            $result = str_replace('*', '(.*)', $result);
            $result = preg_replace('/:(\w+)/', '([\w-\.]+)', $result);
            return $result;
        }

        protected function getRouteCallback()
        {
            $isControllerClass = !empty($this->controller) && class_exists($this->controller);
            $isControllerMethod = $isControllerClass && is_callable([$this->controller, $this->action]);
            $isObjectController = !empty($this->controller) && is_object($this->controller);
            $isObjectControllerWithMethod = $isObjectController && is_callable([$this->controller, $this->action]);
            $isFunction = !empty($this->action) && is_callable($this->action);
            $isCallbackFunction = !empty($this->callback) && is_callable($this->callback);

            if ($isObjectControllerWithMethod) {
                $callback = [$this->controller, $this->action];
            } elseif ($isControllerMethod) {
                $callback = [new $this->controller(), $this->action];
            } elseif ($isFunction) {
                $callback = $isFunction;
            } elseif ($isCallbackFunction) {
                $callback = $this->callback;
            }

            if (empty( $callback) || !is_callable($callback)) {
                $error = sprintf('Route failed to dispatch. Callback not callable: %s', print_r($callback, true));
                throw new RouteException($error);
            }
            // @todo To reintegrate with Extasy\Usecase
            // if ($this->controller instanceof \Faid\Controller\Controller) {
            //    $this->controller->beforeAction($this->request);
            // }
            return $callback;

        }

        /**
         * @throws RouteException
         * @return HttpRoute
         */
        public function dispatch()
        {
            parent::dispatch();
            $callback = $this->getRouteCallback();
            call_user_func($callback, $this->request, $this);
            // @todo To reintegrate that code
//			$isController = is_array( $callback ) && is_object( $callback[0]) && ( $callback[0] instanceof \Faid\Controller\Controller );
//			if ( $isController ) {
//				$controller = $callback[0];
//				$controller->afterAction( );
//			}
        }

        public function buildUrl($data = [])
        {
            krsort($data);
            $search = [];
            $replacements = [];

            foreach ($data as $key => $row) {
                $search[] = ':' . $key;
                $replacements[] = $row;
            }
            return str_replace($search, $replacements, $this->urlTemplate);
        }

        /**
         *
         */
        public function prepareRequest()
        {
            $regExp = $this->getRegExp();
            preg_match($regExp, $this->request->url(), $matches);
            $unnamedParamIndex = 1;
            if (preg_match_all("/(\*)|:([\w-]+)/", $this->urlTemplate, $argument_keys)) {
                $params = array();
                $argument_keys = $argument_keys[0];
                foreach ($argument_keys as $key => $name) {
                    if ('*' != $name) {
                        $name = substr($name, 1);
                    }
                    if (isset($matches[$key + 1])) {
                        if ('*' != $name) {
                            if (!in_array($name, array('action', 'controller'))) {
                                $params[$name] = $matches[$key + 1];
                            } else {
                                $this->$name = strtolower($matches[$key + 1]);
                            }
                        } else {
                            $list = explode('/', $matches[$key + 1]);
                            foreach ($list as $row) {
                                $name = 'param' . $unnamedParamIndex;
                                $unnamedParamIndex++;
                                $params[$name] = $row;
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