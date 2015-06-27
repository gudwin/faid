<?php
namespace Faid\Dispatcher {
    use Faid\Request\Request;
    use \Faid\StaticObservable;
    use \Faid\Request\HttpRequest;

    class Dispatcher extends StaticObservable
    {
        /**
         * @var array
         */
        protected $routes = array();

        /**
         * @var HttpRequest
         */
        protected $request = null;

        /**
         * @param HttpRequest $request
         */
        public function __construct(Request $request)
        {
            $this->request = $request;
        }

        public function getRequest()
        {
            return $this->request;
        }

        public function getNamed($name)
        {
            foreach ( $this->routes as $route ) {
                if ( $route->getName() == $name ) {
                    return $route;
                }
            }
            throw new RouteException(sprintf('Route with name %s not found', $name));
        }

        /**
         * @param Route $route
         */
        public function addRoute(Route $route)
        {
            $this->routes[] = $route;
        }


        /**
         * @return Route
         */
        public function run()
        {
            $route = $this->findRoute($this->request);
            //
            self::callEvent('Dispatcher.Route', $route);
            //
            $route->prepareRequest();

            //
            return $route;
        }

        /**
         * @param $request
         *
         * @return HttpRoute
         */
        protected function findRoute()
        {
            foreach ($this->routes as $row) {
                if ($row->test($this->request)) {
                    return $row;
                }
            }
            throw new RouteException('No matched route was found');
        }
    }
}
?>