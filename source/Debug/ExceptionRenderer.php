<?php
namespace Faid\Debug {
	/**
	 * User: Gisma
	 * Date: 12.03.13
	 * Time: 7:58
	 * To change this template use File | Settings | File Templates.
	 */

	class ExceptionRenderer {
		/**
		 * @param $exception
		 */
		public static function render( \Exception $exception ) {
			print nl2br($exception);
			print Debug::getFileSource( $exception->getFile(), $exception->getLine() );
		}
	}
}
?>