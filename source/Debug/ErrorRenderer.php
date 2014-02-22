<?php
namespace Faid\Debug {
	/**
	 * Created by JetBrains PhpStorm.
	 * User: Gisma
	 * Date: 12.03.13
	 * Time: 7:58
	 * To change this template use File | Settings | File Templates.
	 */
	class ErrorRenderer {
		public static function render( $errno,$errstr,$errfile = '', $errline = '' ) {
			// Проверяем, возможно сейчас установлен режим игнорирования этого типа ошибки
			$currentErrorLevel = ini_get('error_reporting');
			if (!($errno & $currentErrorLevel)) {
				// тогда ничего не делаем
				return ;
			}
			if ( sizeof(ob_list_handlers()) > 0 ) {
				ob_clean();
			}

			if ( "cli" == php_sapi_name() ) {
				$template = ' Error type: %s;  %s [%s:%s]'."\r\n" ;
			}
			else {
				$template = "<div style='color:red;font-size:18px'><strong> Error type: %s;	  %s Error information [%s:%s]</strong></div>";
			}
			$message = sprintf($template,$errno,$errstr,$errfile,$errline);
			print $message;
			print Debug::getFileSource( $errfile, $errline );
		}
	}
}
?>