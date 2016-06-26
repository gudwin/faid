<?php
namespace Faid {
	use \Faid\Configure\Configure;

	class UParser extends StaticObservable {
		static public function parsePHPFile( $szTemplateFile, $viewVariables = null ) {

			$szOldObContents = ob_get_contents();
			if ( sizeof( ob_list_handlers() ) > 0 ) {
				ob_clean();
			} else {
				ob_start();
			}
			self::callEvent( 'UParser::before_parse', $szTemplateFile, $viewVariables );
			if ( file_exists( $szTemplateFile ) ) {
				if ( !empty( $viewVariables ) ) {
					extract( $viewVariables );
				}
				include $szTemplateFile;
				$content = ob_get_contents();
			} else {
				throw new Exception( 'Failed to parse PHP code' );
			}
			ob_clean();
			print $szOldObContents;
			self::callEvent( 'UParser::after_parse', $szTemplateFile, $viewVariables, $content );
			return $content;
		}

		/**
		 * @param $szContent
		 * @param $aVariables
		 *
		 * @return string
		 * @throws \Exception
		 */
		static public function parsePHPCode( $szContent, $viewVariables = null ) {

			self::callEvent( 'UParser::before_parse_code', $szContent, $viewVariables );
			try {
				$baseDir = Configure::read( 'UParser.tmp_dir' );
			}
			catch ( ConfigureException $e ) {
				throw new \Exception( 'Directive "UParser.tmp_dir" not defined ' );
			}
			$szOld = ob_get_contents();
			ob_clean();

			$__szPath = $baseDir . uniqid();
			file_put_contents( $__szPath, $szContent );
			if ( !empty( $viewVariables ) ) {
				extract( $viewVariables );
			}

			include $__szPath;
			unlink( $__szPath );
			$szContent = ob_get_contents();
			ob_clean();
			print $szOld;

			self::callEvent( 'UParser::after_parse_code', $szContent, $viewVariables, $szContent );

			return $szContent;

		}
	}
}
?>