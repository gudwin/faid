<?php
use \Faid\Configure\Configure;

if ($_SERVER['argc'] > 0 ) {
	define('CLI_MODE', true );
}

require_once dirname(__FILE__).'/../buildlist.php';

require_once __DIR__ . '/baseTest.php';
loadBuildList( dirname(__FILE__).'/../source/');

Configure::write('Debug', true);
Configure::write( 'Error.Handler', function ($errno, $errstr, $errfile = '', $errline = '') {
	var_dump( $errno );
	var_dump( $errstr );
	var_dump( $errfile );
	var_dump( $errline );
	printf( '%s[ %d ] %d: %s', $errfile, $errline, $errno, $errstr );
	die;
});
Configure::write( 'Exception.Handler', function ( $e ) {
	var_dump( $e );
	die;
} );
Configure::write( 'FatalError.Handler', function ($message, $file, $line) {
	$error = sprintf( 'FAID: Fatal error catched "%s"  at : "%s:%d"', $message, $file, $line );
	print $error;
	die;
});
