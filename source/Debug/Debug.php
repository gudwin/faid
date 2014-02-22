<?php

namespace Faid\Debug {
	use \Faid\Configure\Configure;
	/**
	 * That class used for handling exceptions and error in run time
	 */
	class Debug {
		///////////////////////////////////////////////////////////////////////////
		// Public static method
		public static function out() {
			displayCallerCode(1);
			$aData = func_get_args();
			var_dump_list($aData);
			die();
		}

		/**
		 * Sets default config values
		 */
		public static function setDefaults() {
			// Write default config
			Configure::write('Debug', true);
			Configure::write('Error.Handler', array('\Faid\Debug\Debug', 'errorHandler'));
			Configure::write('Error.Level', E_ALL | E_WARNING | E_STRICT);
			Configure::write('Exception.Handler', array('\Faid\Debug\Debug', 'exceptionHandler'));
			Configure::write('Exception.Renderer', '\Faid\Debug\ExceptionRenderer');
			Configure::write('Error.Renderer', '\Faid\Debug\ErrorRenderer');
			Configure::write('FatalError.Handler',null);
			// Link exception and error event handlers
			self::linkErrorHandlers();
			// register handler for fatal errors
			self::registerShutdown();
			// setup event listener for changing events
			self::setEventListeners();
		}

		/**
		 * Default exception handler, calls default exception renderer
		 *
		 * @param $exception
		 */
		public static function exceptionHandler($exception) {
			$className = Configure::read('Exception.Renderer');
			call_user_func(array($className, 'render'), $exception);
			die();
		}

		/**
		 * @desc Высылает письмо об ошибке на ADMIN_EMAIL
		 * @return
		 */
		public static function errorHandler($errno, $errstr, $errfile = '', $errline = '') {

			$className = Configure::read('Error.Renderer');
			call_user_func(array($className, 'render'), $errno, $errstr, $errfile, $errline);
			$debugDisabled = false == Configure::read('Debug');
			if ( $debugDisabled ) {
				$trace = defaultDebugBackTrace(false);
				// than we on production server, send an email to administrator
				$template = ' Error type: %s;  %s [%s:%s]' . "\r\n";
				$message  = sprintf($template, $errno, $errstr, $errfile, $errline);
				$trace    = defaultErrorSend($message, $trace);
			}
		}

		/**
		 *
		 */
		public static function enable() {
			self::linkErrorHandlers();
		}

		/**
		 * Disable error handler
		 */
		public static function disable() {
			set_error_handler(
				function () {
				}, E_ALL | E_WARNING | E_STRICT
			);
			set_exception_handler(
				function () {
				}
			);
		}

		public static function getFileSource($file, $current) {
			$fileSource  = file($file);
			$from        = max(0, $current - 5);
			$to          = min($from + 10, sizeof($fileSource));
			$isPlainText = (php_sapi_name() == "cli") ;
			$result      = '';
			if ( $isPlainText ) {
				$result .= sprintf("Error source: %s \r\n", $file);
				$template = "%10s[%6d]%s\r\n";
			} else {
				$result .= sprintf('<div class="error" style="margin-bottom:20px"><h2>Error source:</h2> <span style="font-style:italic;">"%s"</span>', $file);
				$template = '<div class="error_line" style="%s;height:18px;min-width:600px;clear:left;" ><span style="float:left;margin-right:20px;line-height:18px;">[%6d]</span><span style="line-height:18px;">%s</span></div>' . "\r\n";
			}
			for ($i = $from; $i < $to; $i++) {
				$currentStyle = '';
				if ( !$isPlainText ) {
					$currentStyle     = 'border-bottom:1px solid gray;';
					$fileSource[ $i ] = str_replace(' ', '&nbsp;', htmlspecialchars($fileSource[ $i ]));
					$fileSource[ $i ] = str_replace("\t", '<span style="width:20px;display:inline-block;"><!-- --></span>', $fileSource[ $i ]);
					if ( $i == $current - 1 ) {
						$currentStyle .= 'background-color:#eee;';
					}
				}
				if ( $i == $current - 1 ) {
					if ( !$isPlainText ) {
						$currentStyle .= 'background-color:#eee;';
					} else {
						$currentStyle = ' ACTIVE ';
					}
				}
				$result .= sprintf($template, $currentStyle, $i + 1, $fileSource[ $i ]);
			}
			if ( $isPlainText ) {

			} else {
				$result .= '</div>';
			}

			return $result;
		}

		public static function registerShutdown() {
			register_shutdown_function(array('\Faid\Debug\Debug', 'fatalErrorShutDown'));
		}

		/**
		 * That function not customizeable, because fatal errors usually happens because of business logic
		 */
		public static function fatalErrorShutDown() {
			$a = error_get_last();
			if ( !is_null($a) ) {
				$isFatalError = ($a['type'] === E_ERROR || $a['type'] === E_USER_ERROR);
				if ( $isFatalError ) {

					$callback = Configure::read('FatalError.Handler');

					if ( is_callable( $callback )) {
						call_user_func( $callback, $a['message'], $a['file'],$a['line']);
					} else {
						if ( !empty($a[ 'file' ]) ) {
							print self::getFileSource($a[ 'file' ], $a[ 'line' ]);
						}
						printf('<h2 style="color:#CC0000">Fatal Error: %s</h2>', $a[ 'message' ]);
					}
				}
			}
		}

		/**
		 * Setups handlers for errors and exceptions
		 */
		protected static function linkErrorHandlers() {
			$errorHandler     = Configure::read('Error.Handler');
			$errorLevel       = Configure::read('Error.Level');
			$exceptionHandler = Configure::read('Exception.Handler');
			// enable error handler
			set_error_handler($errorHandler, $errorLevel);
			// enable exception handler
			set_exception_handler($exceptionHandler);
		}

		protected static function setEventListeners() {
			Configure::addEventListener('Configure.write', array('\Faid\Debug\Debug', 'onConfigureWrite'));
		}

		public static function onConfigureWrite($key, $data) {
			$isDebugKey = (strpos('Error', $key) === 0) || (strpos('Exception', $key) === 0);
			if ( $isDebugKey ) {
				// re-init error handlers
				self::linkErrorHandlers();
			} else {
			}
		}

	}


	function array_strip_slashes(&$aValue) {
		if ( is_array($aValue) ) {
			$aNew = array();
			foreach ($aValue as $key => $value) {
				if ( is_array($aValue[ $key ]) )
					array_strip_slashes($aValue[ $key ]);
				else
					$aValue[ $key ] = stripslashes($value);
				$aNew[ stripslashes($key) ] = $aValue[ $key ];
			}
			$aValue = $aNew;
		} else {
			$aValue = stripslashes($aValue);
		}
	}

	function var_dump2($var = NULL) {
		displayCallerCode(1);
		$aData = func_get_args();
		var_dump_list($aData);

	}

	function var_dump_list($aData) {
		foreach ($aData as $var) {
			var_dump($var);
		}
	}

	function _debugWithOutput() {
		displayCallerCode(1);
		call_user_func_array('_debug', func_get_args());
	}

	/**
	 * Выводит массив данных в виде таблицы. Функция применяется к двумерным массивам
	 * @param array $table     двухмерный массив
	 * @param int $columnWidth ширина колонки
	 */
	function outputDisplayTable($table, $columnWidth = 10) {
		if ( empty($table) ) {
			printf(' Empty Table ');
		}
		$columnHeader = array_keys($table[ 0 ]);
		// Для этого считаем, сколько у нас колонок
		$columnCount = count(array_keys($columnHeader));
		// Рассчитываем ширину таблицы
		$totalWidth = $columnCount * $columnWidth + 1;

		// Выводим разделительную линию
		$line = "\r\n" . '|' . str_repeat('-', $totalWidth - 2) . '|' . "\r\n";
		$tpl  = '%' . ($columnWidth - 2) . '.' . ($columnWidth - 2) . 's |';
		print $line;
		// Выводим заголовки таблицы
		print('|');
		foreach ($columnHeader as $row) {
			printf($tpl, $row);
		}
		// Выводим разделительную линию
		print $line;
		// Выводим ряды
		foreach ($table as $key => $row) {
			// Начало ряда
			print '|';

			// Каждая колонка
			foreach ($row as $value) {
				printf($tpl, $value);
			}
			print $line;
		}

	}


	function defaultErrorSend($errstr, $trace = '') {
		$date   = date('Y-m-D H:i');
		$server = print_r($_SERVER, true);

		$szPost    = print_r($_POST, true);
		$szGet     = print_r($_GET, true);
		$szMessage = <<<EOD
		Hi!
	There is an error
	Trace : {$trace}
	Date : [{$date}]
	Post : [{$szPost}]
	GET : [{$szGet}]
	Error message : $errstr;

	--
	Best regards, FAID
EOD;
		$result    = mail(ADMIN_EMAIL, '[Error report] :' . SITE_NAME, $szMessage);

		return $result;
	}

	function defaultDebugBackTrace($output = true, $trace = NULL) {
		// А это значит, что режим отладки врублен!
		// выводим бегтрейс ошибки
		if ( empty($trace) ) {
			$trace = debug_backtrace();
		} else {

		}
		$tplError = ' [%d] %s:%s %s%s%s ';
		$result   = '';
		foreach ($trace as $key => $row) {
			//
			$result .= sprintf(
				$tplError,
				$key,
				isset($row[ 'file' ]) ? $row[ 'file' ] : '',
				isset($row[ 'line' ]) ? $row[ 'line' ] : '',
				isset($row[ 'class' ]) ? $row[ 'class' ] : '',
				isset($row[ 'type' ]) ? $row[ 'type' ] : '',
				isset($row[ 'function' ]) ? $row[ 'function' ] : ''
			);
			// Если выводим ошибку в CLI-моде

			if ( "cli" == php_sapi_name()) {
				// То добавляем перевод строки
				$result .= "\r\n";
			} else {
				// Иначе html-тег + перевод строки
				$result .= sprintf("<br/>\r\n");
			}
		}
		if ( $output ) {
			print $result;
		}

		return $result;
	}

	/**
	 *
	 * Выводит кусок исходных кодов из файла вызвавшего функцию
	 * @param int $fromErrorHandler устанавливайте значение данной переменной, только если она вызывается из промежуточной функции (например перехватчика ошибок)
	 */
	function displayCallerCode($fromErrorHandler = 0, $output = true) {
		// Отображаем блок текста
		$trace = debug_backtrace();
		$caller = $trace[0 + $fromErrorHandler];

		$result = '';
		if (!isset($caller['file'])) {
			if ( isset( $trace[0 + $fromErrorHandler + 1] )) {
				$caller = $trace[0 + $fromErrorHandler + 1];
			}
		}
		if (isset($caller['file'])) {
			$result = Debug::getFileSource( $caller['file'], $caller['line'] );
		} else {
			$result = '<hr/> <h2>Empty error source</h2> <hr/>';
		}
		if ( $output ) {
			print $result;
		}
		return $result;
	}

	function null_func() {
	}

	Debug::setDefaults();
}
?>