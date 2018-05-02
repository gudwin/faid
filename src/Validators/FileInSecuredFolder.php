<?php

namespace Faid\Validators {


	class FileInSecuredFolder {
		protected $baseFolder = null;
		protected $offset = null;

		public function __construct( $baseFolder ) {
			$validFolder = file_exists( $baseFolder ) && is_dir( $baseFolder );
			if ( !$validFolder ) {
				throw new Exception( sprintf( '$baseFolder not exists - "%s"', $baseFolder ) );
			}
			$this->baseFolder = $baseFolder;
		}

		protected function test() {
		}

		public function isValid( $path ) {
			$path = realpath( $path );
			if ( empty( $path ) ) {
				return false;
			}
			$basePart = substr( $path, 0, strlen( $this->baseFolder ) );

			$valid = $basePart === $this->baseFolder;

			return $valid;
		}

		public function getOffset( $path ) {
			if ( $this->isValid( $path ) ) {
				return substr( $path, strlen( $this->baseFolder ) );
			} else {
				throw new \InvalidArgumentException( 'Incorrect path' );
			}
		}

	}
}
?>