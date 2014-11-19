<?php
namespace Faid\Request {
	class CommandLineRequest extends Request {
		protected $uri = false;
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
			if ( !empty( $data) ) {
				$this->parseURL();
			}

		}

		/**
		 * @return string
		 */
		public function url() {
			$result = sprintf( 'http://%s%s', $this->domain(), $this->uri());
			return $result;
		}

		/**
		 * @return mixed
		 */
		public function getMethod( ) {
			return 'GET';
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
		public function uri( $uri = null ) {
			if ( !is_null( $uri )) {
				if ( !empty( $uri )) {
					if ( $uri[0] != '/') {
						$uri  = '/'. $uri;
					}
				} else {
					$uri = '/';
				}

				$this->uri = $uri;
			}
			return $this->uri;
		}

		protected function parseURL( ) {
			if ( preg_match( '/http\:\/\/([^\/]+)(.*)/i',$this->data[0], $matchers)) {
				$this->domainName = $matchers[1];
				$this->uri = $matchers[2];
			}
			if ( empty( $this->uri )) {
				$this->uri = '/';
			}
		}

	}
}
?>