<?php
namespace Faid\Debug {

	class ExceptionRenderer extends baseRenderer {
		/**
		 * @param $exception
		 */
		public static function render( $exception ) {
			$skip = !self::isDebugEnabled();

			if ( $skip ) {
				return;
			}

			self::cleanOutputIfNecessary();

			print nl2br($exception);
			print Debug::getFileSource( $exception->getFile(), $exception->getLine() );
		}
	}
}
?>