<?php
if ($_SERVER['argc'] > 0 ) {
	define('CLI_MODE', true );
}

require_once dirname(__FILE__).'/../compile/buildlist.php';
loadBuildList( dirname(__FILE__).'/../source/');