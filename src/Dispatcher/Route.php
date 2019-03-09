<?php
/**
 *
 * @version 1.0.0
 */

namespace Faid\Dispatcher {
    class Route
    {
        protected $isTestCalled = false;

        protected $action = false;

        protected $controller = false;

        protected $request = false;

        protected $callback = false;

        protected $name = '';

        /**
         * @param $request
         */
        public function __construct($config = array())
        {
            $defaultConfig = array(
                'controller' => '',
                'action' => '',
                'request' => null,
                'callback' => null,
                'name' => null,
            );
            $config = array_merge($defaultConfig, $config);
            $this->action = $config['action'];
            $this->controller = $config['controller'];
            $this->request = $config['request'];
            $this->callback = $config['callback'];
            $this->name = !empty($config['name']) ? $config['name'] : uniqid('route_');
            $this->isTestCalled = false;
        }

        public function getName()
        {
            return $this->name;
        }

        /**
         *
         */
        public function getAction()
        {
            return $this->action;
        }

        /**
         *
         */
        public function getController()
        {
            return $this->controller;
        }

        /**
         * @param $action
         */
        public function setAction($action)
        {
            $this->action = $action;
        }

        /**
         * @param $controller
         */
        public function setController($controller)
        {
            $this->controller = $controller;
        }

        public function getCallback()
        {
            return $this->callback;
        }

        /**
         * @param $request
         *
         * @return bool
         */
        public function test($request)
        {
            $this->request = $request;
            $this->ready = false;
            return false;
        }

        /**
         * Called by dispatcher class. Main idea is - prepare request class to be called from controller
         */
        public function prepareRequest()
        {

        }

        /**
         * @throws RouteException
         */
        public function dispatch()
        {
            if ( !$this->isTestCalled ) {
                $this->isTestCalled = true;
                $this->test($this->request);
            }
        }
    }
}
?>