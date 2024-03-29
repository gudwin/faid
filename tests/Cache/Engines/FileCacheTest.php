<?php

namespace Faid\tests\SimpleCache\Engines;

use \Faid\Cache\Exception;
use \Faid\Cache\Engine\FileCache;
use \Faid\Configure\Configure;
use \Faid\Configure\ConfigureException;

class FileCacheTest extends \Faid\tests\baseTest
{
    const Path = '/tmp/';
    const UnknownKeyFixture = 'unknown_key';
    const KeyFixture = 'testfile';
    const CacheActualTime = 1000;

    public static function setUpBeforeClass(): void
    {

        if (!file_exists(self::getPath())) {
            mkdir(self::getPath());
            chmod(self::getPath(), 0777);
        }
    }

    /**
     * Очищает папку кешей
     */
    public function setUp(): void
    {
        $this->clearFiles();
        Configure::write(
            FileCache::ConfigurePath,
            array(
                'BaseDir' => self::getPath()
            )
        );
    }

    public function tearDown(): void
    {
        $this->clearFiles();
    }

    protected static function getPath()
    {
        return __DIR__ . self::Path;
    }

    protected function clearFiles()
    {
        $path = self::getPath();
        if ($handle = opendir(self::getPath())) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    unlink($path . $entry);
                }
            }
        }
    }

    /**
     */
    public function testWithEmptyConfig()
    {
        $this->expectException(ConfigureException::class);
        Configure::write(FileCache::ConfigurePath, null);
        new FileCache();
    }

    /**
     * Создает кеш с пустым ключом
     */
    public function testSetWithEmptyKey()
    {
        $this->expectException(Exception::class);
        $instance = new FileCache();
        $instance->set('', array(1, 2, 3), self::CacheActualTime);
    }

    /**
     * Тестирует создание кеша
     */
    public function testSet()
    {
        $fixture = array(1, 2, 3, 4);
        $instance = new FileCache();
        $instance->set(self::KeyFixture, $fixture, self::CacheActualTime);
        $testPath = self::getPath() . (self::KeyFixture);
        $this->assertTrue(file_exists($testPath), "File $testPath does not exist");
    }

    /**t
     * Проверяет загрузку несуществующего кеша
     */
    public function testGetWithUknownKey()
    {
        $this->expectException(Exception::class);
        $instance = new FileCache();
        $instance->get(self::UnknownKeyFixture);
    }

    /**
     * @expectedException Exception
     */
    public function testGetSecurity()
    {
        $this->expectException(Exception::class);
        $instance = new FileCache();
        $instance->get('../FileCacheTest.php');
    }

    /**
     * Проверяет загрузку
     */
    public function testGet()
    {

        $instance = new FileCache();
        $initialData = array(1, 2, 3, 4);
        $instance->set(self::KeyFixture, $initialData, self::CacheActualTime);
        $instance = new FileCache();
        $data = $instance->get(self::KeyFixture);
        $this->assertEquals($data, $initialData);
        // cover cached variant
        $data = $instance->get(self::KeyFixture);
        $this->assertEquals($data, $initialData);
    }


    /**
     * Проверяем очистку кеша при несуществующем хеше
     */
    public function testClearWithUnknownKey()
    {
        $this->ExpectException(Exception::class);
        $instance = new FileCache();
        $instance->clear(self::UnknownKeyFixture);
    }

    /**
     * Проверяет очистку хеша
     */
    public function testClear()
    {
        $this->expectException(Faid\Cache\Exception::class);
        $key = 'test';
        $instance = new FileCache();
        $data = array(1, 2, 3, 4);

        $instance->set($key, $data, self::CacheActualTime);

        $instance->clear($key);

        $this->expectException(Exception::class);
        $instance->get($key);
        $this->fail('Exception must be thrown');
    }

    public function testUnknownCacheIsActual()
    {
        $instance = new FileCache();
        $this->assertFalse($instance->isActual(self::UnknownKeyFixture));
    }

    public function testIsNotActual()
    {
        $key = 'test';
        $time = 1;
        $instance = new FileCache();
        $instance->set($key, 'test', $time);
        sleep($time);
        $this->assertFalse($instance->isActual($key));
    }

    public function testIsActual()
    {
        $key = 'test';
        $time = 1;
        $instance = new FileCache();
        $instance->set($key, 'test', $time);
        $this->assertTrue($instance->isActual($key));
    }

    /**
     * 
     */
    public function testGetNotActualCache()
    {
        $this->expectException(Exception::class);
        $key = 'test';
        $time = 1;
        $instance = new FileCache();
        $instance->set($key, 'test', $time);
        sleep($time + 1);
        $instance->get($key);
    }

    public function testGetPersistentCache()
    {
        $key = 'test';
        $value = 'fixture';
        $time = 0;
        //
        $instance = new FileCache();
        $instance->set($key, $value, $time);
        $this->assertEquals($value, $instance->get($key));
        sleep(1);
        $this->assertEquals($value, $instance->get($key));
    }
}
