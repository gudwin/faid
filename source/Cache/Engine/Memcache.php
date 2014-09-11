<?php
namespace Faid\Cache\Engine {
	use \Memcache as PeclMemcache;
	use \Faid\Cache\Exception;

	class Memcache implements CacheEngineInterface {
		const ConfigurePath = 'SimpleCache.Memcache';
		protected $config = null;
		/**
		 * @var PeclMemcache
		 */
		protected $instance = null;

		public function __construct() {
			$this->autoloadConfig();
			$this->instance = new PeclMemcache();
			foreach ( $this->config[ 'servers' ] as $row ) {
				$result = $this->instance->pconnect( $row[ 'host' ], !empty( $row[ 'port' ] ) ? $row[ 'port' ] : null );
				if ( !$result ) {
					throw new Exception( 'Failed to connect to server:' . print_r( $row, true ) );
				}
			}
		}



		public function get( $key ) {
			$flags = null;
			$result = $this->instance->get( $key, $flags );
			// $flags stays untouched if $key was not found on the server
			// @see http://php.net/manual/ru/memcache.get.php#112056
			if ( empty( $result ) && empty( $flags ) ) {
				throw new Exception( 'Cache "' . $key . '" not found' );
			}
			return $result;
		}

		public function set( $key, $value, $timeActual = null ) {
			$this->instance->set( $key, $value, 0, $timeActual );
		}

		public function clear( $key ) {
			$this->instance->delete( $key );
		}

		public function isActual( $key ) {
			$flags  = 0;
			$result = $this->instance->get( $key, $flags );
			// $flags stays untouched if $key was not found on the server
			// @see http://php.net/manual/ru/memcache.get.php#112056
			if ( empty( $result ) && empty( $flags ) ) {
				return false;
			}
			return true;
		}
		public function getInstance() {
			return $this->instance;
		}
		protected function autoloadConfig() {
			$this->config = \Faid\Configure\Configure::read( self::ConfigurePath );
			$valid        = isset( $this->config[ 'servers' ] ) && is_array( $this->config[ 'servers' ] );
			if ( !$valid ) {
				throw new Exception( 'Memcache config not valid' );
			}
		}
	}
}