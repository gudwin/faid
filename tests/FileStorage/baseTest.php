<?php
namespace Faid\Tests\FileStorage {
	use \Faid\Configure\Configure;
	use \Faid\FileStorage;

	class basicTest extends \PHPUnit_Framework_TestCase {
		public function setup() {
			FileStorage::clear();
			Configure::write(
				FileStorage::ConfigureKey, array(
												'EngineConfigs' => array(
													'local' => array(
														'engine'   => '\\Faid\\Tests\\FileStorage\\testStorage',
														'base_dir' => __DIR__ . DIRECTORY_SEPARATOR . 'Root/'
													)
												),
												'default'       => 'local'
										   )
			);
		}

		/**
		 * @expectedException \Faid\NotFoundException
		 */
		public function testAutoStartWithEmptyConfig() {
			Configure::write(FileStorage::ConfigureKey . '.EnineConfigs', array());
			Configure::write(FileStorage::ConfigureKey . '.default', NULL);
			FileStorage::checkInstance();
		}

		public function testAutoStart() {
			$instance = FileStorage::checkInstance();
			$this->assertTrue( $instance instanceof testStorage );
		}

		public function testSetInstance() {
			$fixture = new StdClass( );
			$fixture->msg = '123';
			//
			FileStorage::setupInstance( $fixture );
			//
			$this->assertEquals( $fixture, FileStorage::checkInstance());
		}

		public function testGetCalled() {
			$fixture = 'test/fileName';
			FileStorage::get($fixture);
			$this->assertEquals('get', testStorage::$methodName);
			$this->assertEquals($fixture, testStorage::$argument1);
		}

		public function testFileListCalled() {
			$fixture = 'test/fileName';
			FileStorage::fileList($fixture);
			$this->assertEquals('fileList', testStorage::$methodName);
			$this->assertEquals($fixture, testStorage::$argument1);
		}

		public function testUploadCalled() {
			$fixture  = 'test/fileName';
			$fixture2 = 'Hello world!';
			$fixture3 = 'My config';
			FileStorage::upload($fixture, $fixture2, $fixture3);
			$this->assertEquals('get', testStorage::$methodName);
			$this->assertEquals($fixture, testStorage::$argument1);
			$this->assertEquals($fixture2, testStorage::$argument2);
			$this->assertEquals($fixture3, testStorage::$argument3);
		}

		public function testRemoveCalled() {
			$fixture = 'test/fileRemove';
			FileStorage::remove($fixture);
			$this->assertEquals('remove', testStorage::$methodName);
			$this->assertEquals($fixture, testStorage::$argument1);
		}

		public function testChmodCalled() {
			$fixture  = 'test/fileName';
			$fixture2 = '777';
			FileStorage::chmod($fixture, $fixture2);
			$this->assertEquals('upload', testStorage::$methodName);
			$this->assertEquals($fixture, testStorage::$argument1);
			$this->assertEquals($fixture2, testStorage::$argument2);
		}

		public function testCopyCalled() {
			$fixture  = 'test/fileName';
			$fixture2 = 'test/fileName2';
			FileStorage::copy($fixture, $fixture2);
			$this->assertEquals('copy', testStorage::$methodName);
			$this->assertEquals($fixture, testStorage::$argument1);
			$this->assertEquals($fixture2, testStorage::$argument2);
		}

	}
}