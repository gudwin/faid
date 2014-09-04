<?php
namespace Faid\Debug {

	class ErrorRenderer extends baseRenderer {
		public static function render( $errno, $errstr, $errfile = '', $errline = '' ) {
			$skip = !self::isDebugEnabled() || self::ignoreError( $errno );

			if ( $skip ) {
				return;
			}
			self::cleanOutputIfNecessary();

			$message = self::buildErrorMessage( $errno, $errstr, $errfile, $errline );

			print $message;
			print Debug::getFileSource( $errfile, $errline );
		}

		protected static function ignoreError( $errno ) {
			// Проверяем, возможно сейчас установлен режим игнорирования этого типа ошибки
			$currentErrorLevel = ini_get( 'error_reporting' );
			$result            = !( $errno & $currentErrorLevel );
			return $result;
		}

		protected static function buildErrorMessage( $errno, $errstr, $errfile, $errline ) {
			if ( "cli" == php_sapi_name() ) {
				$template = ' Error type: %s;  %s [%s:%s]' . "\r\n";
			} else {
				$template = "<div style='color:red;font-size:18px'><strong> Error type: %s; Error information [%s:%s]</strong></div>";
			}
			$message = sprintf( $template, $errno, $errstr, $errfile, $errline );
			return $message;
		}
	}
}
?>