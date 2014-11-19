<?php
namespace Faid\Request {
	class Request extends \Faid\StaticObservable {
		protected $data = array();
		protected $validationErrors = array();

		public function __construct( $initialData = array()) {
			$this->set( $initialData );
		}

		public function __set($key, $value) {
			return $this->set( $key, $value );
		}

		public function __get($key) {
			return $this->get( $key );
		}
		public function set( $key, $value = null) {
			if ( !is_scalar( $key )) {
				$data = $key;
				foreach ( $data as $key=>$row ) {
					$this->set( $key, $row );
				}
				return ;
			}
			$this->data[ $key ] = $value;
		}
		public function get( $key ) {
			if ( !isset( $this->data[ $key ])) {
				$error = sprintf( 'Parameter `%s` not found', $key );
				throw new \Exception( $error );
			}
			return $this->data[ $key ];
		}
		public function url( ) {

		}
		public function domain( $domainName = null ) {

		}
		/**
		 * @param bool $uri
		 *
		 * @return bool
		 */
		public function uri( $uri = null) {
		}
		public function addValidator($fieldName, $validationMethod) {

		}

		public function getValidationErrors() {

		}

		public function validate() {

		}
	}
}
?>