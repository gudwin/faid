<?php
namespace Faid\Debug {
	use \Faid\Configure\Configure;
	class baseRenderer {
		protected static function isDebugEnabled( ) {
			$debug = Configure::read('Debug');
			return $debug;
		}
		protected static function cleanOutputIfNecessary( ) {
			if ( sizeof(ob_list_handlers()) > 0 ) {
				ob_clean();
			}
		}
	}
}
?>