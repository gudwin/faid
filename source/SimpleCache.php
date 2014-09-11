<?php
namespace Faid {
use \Faid\Configure\Configure;



class SimpleCache {
	const ConfigurePath = 'SimpleCache';
	protected static $basePath = '';

	protected static $instance = null;
	public static function getInstance() {
		if ( empty( self::$instance )) {
			self::factoryInstance();
		}
		return self::$instance;
	}

	/**
	 * @return \Faid\Cache\Engine\CacheEngineInterface
	 */
	protected static function factoryInstance() {
		$engineClass = Configure::read( self::ConfigurePath . '.Engine');
		self::$instance = new $engineClass();
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function get($key) {
		return self::getInstance()->get( $key );
	}
	public static function set($key, $value, $timeActual = 0 ) {
		return self::getInstance()->set( $key, $value, $timeActual );
	}
	public static function clear($key) {
		self::getInstance()->clear( $key );
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public static function isActual($key ) {
		return self::getInstance()->isActual( $key );
	}

	protected static function loadConfig( ) {
		if ( empty( self::$basePath )) {
			$config = Configure::read( self::ConfigurePath );
			self::$basePath = $config['BaseDir'];
		}
	}
}

}