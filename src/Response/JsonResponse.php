<?php
/**
 * @package Faid\Response
 */
namespace Faid\Response {
	use \Faid\Response as baseResponse;
	use \Faid\Debug;
	/**
	 * Class json
	 * @package Faid\Response
	 */
	class JsonResponse extends Response {
		protected $data = array();
		public function set( $key, $value) {
			$this->data[ $key ] = $value;
		}
		public function setData( $key = null, $value = null ) {
			if ( is_array( $key) ) {
				$this->data = array_merge( $this->data, $key );
			} else {
				$this->data[ $key ] = $value;
			}
			parent::setData( $this->data );
		}
		public function getData( ) {
			return $this->data;
		}
		public function send(){
			parent::send();
			print json_encode( $this->data );
		}
	}
}
?>