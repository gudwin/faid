<?php
namespace Faid\Cache\Engine {

	interface CacheEngineInterface {
		public function get( $key );

		public function set( $key, $value, $timeActual = 0);

		public function clear( $key );

		public function isActual( $key );
	}
}