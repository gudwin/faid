<?php
/***
  * Compiles all php files into one big 
  */
include 'buildlist.php';


$version = '0.4';

$fileList = returnBuildList();

$baseDir = dirname(__FILE__).'/../release/';
$sourceDir =  dirname(__FILE__).'/../source/';


require_once dirname( __FILE__ ).'/compile.lib.php';
compile( 'faid', $sourceDir, $fileList, $baseDir, $version, 'php');

print 'All ok';
