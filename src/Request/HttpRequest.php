<?php
namespace Faid\Request {
	class HttpRequest extends Request {
		protected $uri = false;
        protected $url = null;
		protected $domainName = '';
		/**
		 * @param array $data
		 */
		public function __construct( $data = array() ) {
			if ( empty( $data )) {
				$data = $_REQUEST ;
			}
			//
			parent::__construct( $data ) ;
			//
			$this->detectURI();
			$this->detectDomain();
		}

		/**
		 * @return string
		 */
		public function url( $url = null ) {
            if ( is_null( $url )) {
                if ( is_null( $this->url)) {
                    $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
                    $result = sprintf('//%s%s',
                        $host,
                        $this->uri( )
                    );
                    return $result;
                }
                return $this->url;
            } else {
                $this->url = $url;
                $this->uri = null;
            }

		}

		/**
		 * @return mixed
		 */
		public function getMethod( ) {
			return $_SERVER['REQUEST_METHOD'];
		}
		public function domain( $domainName = null ) {
			if ( !empty( $domainName )) {
				$this->domainName =$domainName;
			} else {
				return $this->domainName;
			}

		}
		/**
		 * @param bool $uri
		 *
		 * @return bool
		 */
		public function uri( $uri = null) {
			if ( !empty( $uri )) {
				$this->uri = $uri;
                $this->url = null;
			}
			return $this->uri;
		}

		protected function detectURI( ) {
			if (!empty($_SERVER['PATH_INFO'])) {
				$uri = $_SERVER['PATH_INFO'];
			} elseif (isset($_SERVER['REQUEST_URI'])) {
				$uri = $_SERVER['REQUEST_URI'];
			} elseif (isset($_SERVER['PHP_SELF']) && isset($_SERVER['SCRIPT_NAME'])) {
				$uri = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF']);
			} elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
				$uri = $_SERVER['HTTP_X_REWRITE_URL'];
			} elseif ($var = env('argv')) {
				$uri = $var[0];
			}
			if (strpos($uri, '?') !== false) {
				list($uri) = explode('?', $uri, 2);
			}
			if (empty($uri) || $uri == '/' || $uri == '//') {
				$uri = '/';
			}
			$this->uri = $uri;
		}
		protected function detectDomain( ) {
			if ( !empty( $_SERVER['HTTP_HOST'])) {
				$this->domainName = $_SERVER['HTTP_HOST'];
			}
		}
	}
}
?>