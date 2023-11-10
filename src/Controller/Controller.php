<?php
namespace Faid\Controller {
	use \Faid\Response\Response;
	use \Faid\Response\HttpResponse;
	use \Faid\StaticObservable;
	use \Faid\View\View;

	/**
	 * Basic page Controller (13.01.2006)
	 *
	 */
	class Controller extends StaticObservable
	{
		/**
		 * @var null
		 */
		protected $request = NULL;

		/**
		 * @var Response
		 */
		protected $response = NULL;

		/**
		 * @var View
		 */
		protected $view = NULL;
		/**
		 * @var bool
		 */
		protected $rendered = false;

		/**
		 *
		 * Enter description here ...
		 */
		public function __construct()
		{
		}

		/**
		 * Has to be called
		 */
		public function beforeAction( $request )
		{

		}

		/**
		 *
		 */
		public function render()
		{
			$this->view->render();
			$this->rendered = true;
		}

		/**
         * Ensurses that the response is created. 
         * Ensures that the response is sent.
        
		 */
		public function afterAction()
		{
			//
			if (empty( $this->response )) {
				//
				$isNeedRender = !empty( $this->view ) && !$this->view->isRendered();
				//
				$this->response = new HttpResponse();
				//
				if ( $isNeedRender ) {
					//
					$this->response->setData( $this->view->render() );
				} else {
				}
			}
			// Forcefully send response 
			if (!$this->response->isSent()) {
				//
				$this->response->send();
			}
		}

		/**
		 *
		 */
		protected function send( ) {
			//
			if ( empty( $this->response )) {
				//
				$isViewNotRendered = !empty( $this->view ) && !$this->view->isRendered();
				//
				if ( $isViewNotRendered) {
					//
					$this->getDefaultHTTPResponse();
					//
					$this->response->setData( $this->view->render());
				} else {
                    // @todo probably to fix this behavior 
                    // so it the this->view is empty we actually do nothing and return a null 
                    // not sure if it a real problem now 
                }
			}
			if ( !empty( $this->response )) {
				$this->response->send( );
			}
			return $this->response;
		}
		public function set( $key, $value ) {
			if ( !empty( $this->view )) {
				$this->view->set( $key, $value );
			} else {
				throw new Exception('View not defined / the $view is empty');
			}
		}

		/**
		 * @return Response
		 */
		protected function getDefaultHTTPResponse( ) {
			if ( !empty( $this->response )) {
				$this->response = new HttpResponse();
			}
			return $this->response;
		}
	}
}
?>