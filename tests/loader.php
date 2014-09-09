<?php
use \Faid\Configure\Configure;

if ($_SERVER['argc'] > 0 ) {
	define('CLI_MODE', true );
}

require_once dirname(__FILE__).'/../buildlist.php';

require_once __DIR__ . '/baseTest.php';
loadBuildList( dirname(__FILE__).'/../source/');

Configure::write('Debug', true);