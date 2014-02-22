<?php
namespace Faid {
	use \Faid\Configure\Configure;
	class UParser extends StaticObservable {
		static public function parsePHPFile($szTemplateFile,$aVariables) {

			$szOldObContents = ob_get_contents();
			if ( sizeof( ob_list_handlers() ) > 0 ) {
				ob_clean();
			} else {
				ob_start();
			}
			self::callEvent('UParser::before_parse',$szTemplateFile,$aVariables);
			$szContent = '';
			if (file_exists($szTemplateFile))
			{
				extract($aVariables);
				include $szTemplateFile;
				$szContent = ob_get_contents();
			}
			else
			{
				// Если не отключена отладка, то выводим данные
				if (DEBUG != 0) {
					$isDirWriteable = is_writable(dirname($szTemplateFile));
					$szContent = '<h1>Template file not found</h1>'."\r\n";
					$szContent .= '<p style="color:red">You should create a template file</p>'."\r\n";
					$szContent .= '<h2>Information:</h2>'."\r\n";
					//if ($isDirWriteable) {
					//	$szContent .= '<h2 style="color:green">New template view created</h2>'."\r\n";
					//	$szContent .= "<p>Physical file path: '.$szTemplateFile.'</p>"."\r\n";
					//}
					$szContent .= '[Path] = '.$szTemplateFile."\r\n";
					$szContent .= '<h2>Variables:</h2>."\r\n"';
					$szContent .= '<pre style="size:125;">'.print_r($aVariables,true).'</pre>'."\r\n";
					$szContent .= '<hr/>'."\r\n";
					$szContent .= '<a href="http://extasy-cms.ru">Extasy framework '.EXTASY_VERSION.'</a>'."\r\n";
					/*
					if ($isDirWriteable) {
						file_put_contents($szTemplateFile, $szContent);
						self::callEvent('UParser::template_created',$szTemplateFile,$aVariables);
					}*/
				}
			}
			ob_clean();
			print $szOldObContents;
			self::callEvent('UParser::after_parse',$szTemplateFile,$aVariables,$szContent);
			return $szContent;
		}

		/**
		 * @param $szContent
		 * @param $aVariables
		 * @return string
		 * @throws \Exception
		 */
		static public function parsePHPCode($szContent,$aVariables) {

			self::callEvent('UParser::before_parse_code',$szContent,$aVariables);
			try {
				$baseDir = Configure::read('UParser.tmp_dir');
			} catch ( ConfigureException $e ) {
				throw new \Exception('Directive "UParser.tmp_dir" not defined ');
			}
			$szOld = ob_get_contents();
			ob_clean();

			$__szPath = $baseDir.session_id();
			file_put_contents($__szPath,$szContent);
			extract($aVariables);
			include $__szPath;
			unlink($__szPath);
			$szContent = ob_get_contents();
			ob_clean();
			print $szOld;

			self::callEvent('UParser::after_parse_code',$szContent,$aVariables, $szContent);

			return $szContent;

		}
	}
}
?>