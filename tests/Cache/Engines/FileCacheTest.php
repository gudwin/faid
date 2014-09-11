<?php
namespace Faid\tests\SimpleCache\Engines;

use \Faid\Cache\Exception;
use \Faid\Cache\Engine\FileCache;
use \Faid\Configure\Configure;
use \Faid\Configure\ConfigureException;

class FileCacheTest extends \Faid\tests\baseTest {
	const Path              = '/tmp/';
	const UnknownKeyFixture = 'unknown_key';
	const KeyFixture        = 'testfile';
	const CacheActualTime   = 1000;

	public static function setUpBeforeClass() {
		if ( !file_exists( self::getPath() ) ) {
			mkdir( self::getPath() );
			chmod( self::getPath(), 0777 );
		}
	}

	/**
	 * Очищает папку кешей
	 */
	public function setUp() {
		$this->clearFiles();
		Configure::write( FileCache::ConfigurePath,
						  array(
							  'BaseDir' => self::getPath()
						  ) );
	}

	public function tearDown() {
		$this->clearFiles();
	}

	protected static function getPath() {
		return __DIR__ . self::Path;
	}

	protected function clearFiles() {
		$path = self::getPath();
		if ( $handle = opendir( self::getPath() ) ) {
			/* This is the correct way to loop over the directory. */
			while ( false !== ( $entry = readdir( $handle ) ) ) {
				if ( $entry != "." && $entry != ".." ) {
					unlink( $path . $entry );
				}
			}
		}
	}

	/**
	 * @expectedException \Faid\Configure\ConfigureException
	 */
	public function testWithEmptyConfig() {
		Configure::write( FileCache::ConfigurePath, null );
		new FileCache();
	}

	public function testAutoload() {
		new FileCache();
	}

	/**
	 * Создает кеш с пустым ключом
	 * @expectedException Exception
	 */
	public function testSetWithEmptyKey() {
		$instance = new FileCache();
		$instance->set( '', array( 1, 2, 3 ), self::CacheActualTime );
	}

	/**
	 * Тестирует создание кеша
	 */
	public function testSet() {
		$fixture  = array( 1, 2, 3, 4 );
		$instance = new FileCache();
		$instance->set( self::KeyFixture, $fixture, self::CacheActualTime );
		$testPath = self::getPath() . ( self::KeyFixture );
		if ( !file_exists( $testPath ) ) {
			die( $testPath );
			$this->fail();
		}
	}

	/**
	 * Проверяет загрузку несуществующего кеша
	 * @expectedException Exception
	 */
	public function testGetWithUknownKey() {
		$instance = new FileCache();
		$instance->get( self::UnknownKeyFixture );
	}

	/**
	 * @expectedException Exception
	 */
	public function testGetSecurity() {
		$instance = new FileCache();
		$instance->get( '../FileCacheTest.php' );
	}

	/**
	 * Проверяет загрузку
	 */
	public function testGet() {

		$instance = new FileCache();
		$data     = array( 1, 2, 3, 4 );
		$instance->set( self::KeyFixture, $data, self::CacheActualTime );
		$instance = new FileCache();
		$data     = $instance->get( self::KeyFixture );
		$this->assertEquals( $data, $data );
	}


	/**
	 * Проверяем очистку кеша при несуществующем хеше
	 * @expectedException Exception
	 */
	public function testClearWithUnknownKey() {
		$instance = new FileCache();
		$instance->clear( self::UnknownKeyFixture );
	}

	/**
	 * Проверяет очистку хеша
	 */
	public function testClear() {
		$key      = 'test';
		$instance = new FileCache();
		$data     = array( 1, 2, 3, 4 );

		$instance->set( $key, $data, self::CacheActualTime );

		$instance->clear( $key );

		try {
			$instance->get( $key );
			$this->fail( 'Exception must be thrown' );
		}
		catch ( Exception $e ) {

		}
	}

	public function testUnknownCacheIsActual() {
		$instance = new FileCache();
		$this->assertFalse( $instance->isActual( self::UnknownKeyFixture ) );
	}

	public function testIsNotActual() {
		$key      = 'test';
		$time     = 1;
		$instance = new FileCache();
		$instance->set( $key, 'test', $time );
		sleep( $time );
		$this->assertFalse( $instance->isActual( $key ) );
	}

	public function testIsActual() {
		$key      = 'test';
		$time     = 1;
		$instance = new FileCache();
		$instance->set( $key, 'test', $time );
		$this->assertTrue( $instance->isActual( $key ) );
	}
}
