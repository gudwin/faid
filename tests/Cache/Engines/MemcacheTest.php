<?php

namespace Faid\tests\SimpleCache\Engines;

use \Faid\Cache\Engine\Memcache;
use \Faid\Cache\Exception;
use \Faid\Configure\Configure;

class MemcacheTest extends \Faid\tests\baseTest
{
    const UnknownKey        = 'some_unknown_key';
    const LongCacheLifetime = 1000;
    protected $config = [
        'servers' => [
            [
                'host' => '127.0.0.1',
                'port' => 11211
            ]
        ]
    ];

    public function setUp(): void
    {
        if (!class_exists('Memcache')) {
            $this->markTestIncomplete('Memcache extension is not available');
        }
        Configure::write(
            Memcache::ConfigurePath,
            $this->config
        );
    }

    /**
     * @expectedException \Faid\Configure\ConfigureException
     */
    public function testAutoloadWithEmptyConfig()
    {
        Configure::write(Memcache::ConfigurePath, null);
        new Memcache();
    }

    /**
     * @expectedException \Faid\Cache\Exception
     */
    public function testInvalidConfig()
    {
        Configure::write(Memcache::ConfigurePath, array('my_stuff' => ''));
        new Memcache();
    }

    public function testAutoload()
    {
        new Memcache();
    }

    /**
     * @expectedException \Faid\Cache\Exception
     */
    public function testGetUnknown()
    {
        $instance = new Memcache();
        $instance->get(self::UnknownKey);
    }

    public function testSetWithUnknownKey()
    {
        $fixture = array(1, 2, 3, 4);

        $instance = new Memcache();
        $instance->set(self::UnknownKey, $fixture);
    }

    public function testSet()
    {
        $key     = 'my_key';
        $fixture = array(1, 2, 3, 4);

        $instance = new Memcache();
        $instance->set($key, $fixture);
    }

    public function testGet()
    {
        $key     = 'my_key';
        $fixture = array(1, 2, 3, 4);

        $instance = new Memcache();
        $instance->set($key, $fixture);
        //
        $instance = new Memcache();
        $result   = $instance->get($key);
        //
        $this->AssertEquals($result, $fixture);
    }


    public function testClearWithUnknownKey()
    {
        $instance = new Memcache();
        $instance->clear(self::UnknownKey);
    }

    public function testClear()
    {
        $key      = 'test';
        $instance = new Memcache();
        $data     = array(1, 2, 3, 4);

        $instance->set($key, $data, self::LongCacheLifetime);

        $instance->clear($key);

        try {
            $instance->get($key);
            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
        }
    }

    public function testUnknownCacheIsActual()
    {
        $instance = new Memcache();
        $this->assertFalse($instance->isActual(self::UnknownKey));
    }

    public function testIsNotActual()
    {
        $key      = 'test';
        $time     = 1;
        $instance = new Memcache();
        $instance->set($key, 'test', $time);
        sleep($time);
        $this->assertFalse($instance->isActual($key));
    }

    public function testIsActual()
    {
        $key      = 'test';
        $time     = 1;
        $instance = new Memcache();
        $instance->set($key, 'test', $time);
        $this->assertTrue($instance->isActual($key));
    }

    public function testPrefixesSupported()
    {
        $key = 'test';
        $fixtureA = 'Hello';
        $fixtureB = 'World!';
        $configA = $this->config;
        $configA['prefix'] = 'a';
        $configB = $this->config;
        $configB['prefix'] = 'b';
        //
        Configure::write(Memcache::ConfigurePath, $configA);
        $instance = new Memcache();
        $instance->set($key, $fixtureA);
        Configure::write(Memcache::ConfigurePath, $configB);
        $instance = new Memcache();
        $instance->set($key, $fixtureB);
        //
        Configure::write(Memcache::ConfigurePath, $configA);
        $instance = new Memcache();
        $this->assertEquals($instance->get($key), $fixtureA);
        //
        Configure::write(Memcache::ConfigurePath, $configB);
        $instance = new Memcache();
        $this->assertEquals($instance->get($key), $fixtureB);
        $this->AssertTrue($instance->isActual($key));
        $instance->clear($key);
        $this->AssertFalse($instance->isActual($key));

        Configure::write(Memcache::ConfigurePath, $configA);
        $instance = new Memcache();
        $this->assertEquals($instance->get($key), $fixtureA);
        $this->AssertTrue($instance->isActual($key));
        $instance->clear($key);
        $this->AssertFalse($instance->isActual($key));
    }
}
