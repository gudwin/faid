<?php
/**
 * 
 * Enter description here ...
 * @param unknown_type $fileList
 */
function packFiles( $sourceDir, $fileList, $fileHeader ) {	
	$data = array();
	foreach ($fileList as $row) {
		$path = $sourceDir.$row;
		$header = str_replace('{!#file#!}',$row, $fileHeader); 
		$data[] = $header . file_get_contents($path);
	}
	return $data;	
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $outputFile
 * @param unknown_type $outputMajorFile
 * @param unknown_type $data
 */
function compilationStore( $data, $slug, $baseDir, $version, $revision, $ext )  {
	$dir = $baseDir . $version . '/';
	// Create dir for next version
	if (!file_exists($dir)) {
		mkdir($dir);
	}
	
	$outputFile = sprintf('%s%s-%s-%d.%s',$dir,$slug,$version,$revision,$ext);
	$outputMajorFile = sprintf('%s%s-%s.%s',$dir, $slug, $version,$ext);
	$resultFile = sprintf('%slatest_release.%s',$baseDir,$ext);
	file_put_contents($outputFile, implode("\r\n",$data));
	file_put_contents($outputMajorFile, implode("\r\n",$data));
	file_put_contents($resultFile, implode("\r\n",$data));
}
/**
 * 
 * Enter description here ...
 * @param unknown_type $header
 * @param unknown_type $fileList
 * @param unknown_type $baseDir
 * @param unknown_type $versionPath
 * @param unknown_type $version
 */
function compile( $slug, $sourceDir,$fileList, $baseDir, $version, $ext = 'php' ) {
	$header = file_get_contents( $sourceDir.'loader');
	$fileHeader = file_get_contents( $sourceDir.'file_header');
	
	$versionPath = $sourceDir.'current_version';
	// Load version info
	$compileVersion = loadVersionInfo( $versionPath );
	// Insert revision
	$header = str_replace('{!#revision#!}',$compileVersion['revision'], $header);
	$header = str_replace('{!#version#!}',$version, $header); 
	$compileVersion['revision']++;
	// Create compiled file contents
	$revision = $compileVersion['revision'];
	$data = array( $header );
	$data = array_merge( $data, packFiles( $sourceDir, $fileList, $fileHeader ));
	compilationStore( $data,$slug,$baseDir, $version, $revision, $ext );
	// Store version 
	file_put_contents($versionPath, serialize($compileVersion));
}
/**
 * 
 * Enter description here ...
 * @param unknown_type $path
 */
function loadVersionInfo( $path ) { 
	$compileVersion = null;
	$versionInfo = file_get_contents( $path);
	if (!empty($versionInfo)) {
		$compileVersion = unserialize($versionInfo);
	}  else {
		$compileVersion = array(
			'revision' => 0
		);
	}
	return $compileVersion;
}