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
         * @var Route
         */
        protected $activeRoute = null;

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
        public function getActiveRoute() {
            return $this->activeRoute;
        }

        /**
         * @return Route
         */
        public function run()
        {
            $this->activeRoute = $this->findRoute($this->request);
            //
            self::callEvent('Dispatcher.Route', $this->activeRoute);
            //
            $this->activeRoute->prepareRequest();
            //
            return $this->activeRoute;
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