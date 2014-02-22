<?php
namespace Faid\Tests\FileStorage {
	class localStorageTest extends \PHPUnit_Framework_TestCase {
		public function testGetFileList( ) {
		}

		/**
		 * @expectedException \Faid\NotFoundException
		 */
		public function testReadUnknownFile( ) {
		}

		/**
		 * @expectedException \Faid\NotFoundException
		 */
		public function testUploadToUnknownPath( ) {
		}
		public function testUpload( ) {
		}
		public function testExists( ) {
		}

		/**
		 * @expectedException \Faid\NotFoundException
		 */
		public function setChmodOnUnknown( ) {
		}

		/**
		 * @expectedException \Faid\NotFoundException ( )
		 */
		public function testRemoveUnknown( ) {
		}
		public function testRemove( ) {
		}
		public function testRemoveFolder( ) {
		}
	}
}