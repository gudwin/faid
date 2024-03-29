<?php

namespace Faid\tests\Cache;

use \Faid\SimpleCache;
use \Faid\Configure\Configure;
use \Faid\Configure\ConfigureException;

class SimpleCacheTest extends \Faid\tests\baseTest
{

    public function setUp(): void
    {
        Configure::write(
            SimpleCache::ConfigurePath,
            array(
                'Engine' => '\\Faid\\tests\\Cache\\TestCacheEngine'
            )
        );
        SimpleCacheCleaner::setUp();
    }

    /**
     */
    public function testGetInstanceWithoutConfig()
    {
        $this->expectException(ConfigureException::class);
        Configure::write(SimpleCache::ConfigurePath, null);
        SimpleCache::getInstance();
    }

    public function testAutoloadInstance()
    {
        Configure::write(
            SimpleCache::ConfigurePath,
            array(
                'Engine' => '\\Faid\\tests\\Cache\\TestCacheEngine'
            )
        );
        $result = SimpleCache::getInstance();
        $this->assertTrue($result instanceof TestCacheEngine);
    }

    public function testMethodsCalled()
    {
        $calls = array(
            array('method' => 'get', 'params' => array('someFixture')),
            array('method' => 'set', 'params' => array('keyName', 'keyValue', 0)),
            array('method' => 'isActual', 'params' => array('keyName')),
            array('method' => 'clear', 'params' => array('keyName')),
        );
        foreach ($calls as $row) {
            call_user_func_array(array('\\Faid\\SimpleCache', $row['method']), $row['params']);

            $tmp = explode('::', TestCacheEngine::$lastMethod);
            $this->AssertEquals($row['method'], $tmp[1]);
            $this->AssertEquals($row['params'], TestCacheEngine::$methodParams);
        }
    }
}
