<?php

namespace Faid\Validators;


class FileInSecuredFolder extends \Faid\Validator {
	protected $baseFolder = null;
	public function __construct( $baseFolder ) {
		$validFolder =  file_exists( $baseFolder ) && is_dir( $baseFolder );
		if ( !$validFolder ) {
			throw new Exception( sprintf( '$baseFolder not exists - "%s"', $baseFolder ) );
		}
		$this->baseFolder = $baseFolder;
	}
	protected function test( ) {
	}
	public function isValid( $path ) {
		$path = realpath( $path );
		if ( empty( $path )) {
			return false;
		}
		$basePart = substr( $path, 0, strlen( $this->baseFolder ));

		return $basePart === $this->baseFolder ;
	}

}
