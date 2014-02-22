<?php
/**
 * Contains Configure exception description
 */
namespace Faid\Configure {
	/**
	 * Class ConfigureException
	 */
	class ConfigureException extends \Exception {
		/**
		 * Constructs event
		 * @param string $key
		 */
		public function __construct( $key ) {
			$msg = 'Failed to find key `%s` in Configure data';
			$msg = sprintf( $msg, $key );
			parent::__construct( $msg );
		}
	}
}
?>