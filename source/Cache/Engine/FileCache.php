<?php
namespace Faid\Cache\Engine {
	use \Faid\Cache\Exception;
	use \Faid\Configure\Configure;

	class FileCache implements CacheEngineInterface {

		const ConfigurePath = 'SimpleCache.FileCache';


		protected $basePath = '';

		protected $lastLoadedFile = '';
		protected $lastLoadedData = array();

		/**
		 * Создает кеш и сохраняет его на файловую систему
		 *
		 * @param string $key  ключ хеша
		 * @param mixed  $data данные для хеширования
		 */
		public function set( $key, $data, $timeActual = 0 ) {
			// Проверяем ключ на пустоту

			if ( empty( $key ) or preg_match( '#..\/.\/#', $key ) ) {
				throw new Exception( 'Invalid cache name' );
			}

			// Создаем файл
			$path = $this->getPath( $key );

			$data = array(
				'expire' => time() + $timeActual,
				'data'     => $data
			);
			$data = serialize( $data );
			//

			file_put_contents( $path, $data, LOCK_EX );

//			Trace::addMessage( 'SimpleCache', 'Cache `' . $key . '` created' );
		}

		/**
		 * Синоним метода load
		 *
		 * @param string $key
		 */
		public function get( $key ) {
			$path = $this->getPath( $key );
			if ( $path == $this->lastLoadedFile ) {
				return $this->lastLoadedData['data'];
			}
			$this->loadData( $path );
			if ( !$this->testIfCurrentCacheActual()) {
				throw new Exception('Cache "'.$key.'" not actual');
			}
//			Trace::addMessage( 'SimpleCache', 'Cache `' . $key . '` loaded' );
			return $this->lastLoadedData[ 'data' ];
		}

		protected function loadData( $path ) {
			$validator = new \Faid\Validators\FileInSecuredFolder( $this->basePath );
			if ( !$validator->isValid( $path ) ) {
				throw new Exception( 'File restricted by security settings: ' . $path );
			}

			$data                 = file_get_contents( $path );
			$this->lastLoadedData = unserialize( $data );
			$this->lastLoadedFile = $path;
		}

		/**
		 * Удаляет кеш
		 *
		 * @param string $key
		 */
		public function clear( $key ) {
			$path = self::getPath( $key );
			if ( file_exists( $path ) && is_file( $path ) ) {
				unlink( $path );
			} else {
				throw new Exception( 'Path `' . $key . '` isn`t file' );
			}
//			Trace::addMessage( 'SimpleCache', 'Cache `' . $key . '` cleared' );
		}

		/**
		 * Проверяет время последнего обновления кеша
		 *
		 * @param string $key
		 * @param int    $time
		 */
		public function cacheOlder( $key, $time ) {
			// Если время отрицательное, то воспринимаем его как смещение от текущего момента
			// т.е. оно равно = текущеее время - abs($time)
			if ( $time < 0 ) {
				$time = time() + $time;
			}
			$path = self::getPath( $key );
			if ( !file_exists( $path ) ) {
				throw new Exception( 'Uknown path="' . $path . '"' );
			}
			$timeModified = filemtime( $path );
			if ( $timeModified < $time ) {
				$message = sprintf( 'Cache `%s` older than %s', $key, date( 'Y-m-s H:i:s', $time ) );
//				Trace::addMessage( 'SimpleCache', $message );
				return true;
			} else {
				$message = sprintf( 'Cache `%s` still actual', $key );
//				Trace::addMessage( 'SimpleCache', $message );
				return false;
			}
		}

		public function isActual( $key ) {
			$path = $this->getPath( $key );
			if ( $path == $this->lastLoadedFile ) {
				return $this->lastLoadedData;
			}
			try {
				$this->loadData( $path );
			} catch (Exception $e ) {
				return false;
			}
			return $this->testIfCurrentCacheActual();

		}
		protected function testIfCurrentCacheActual() {
			if ( time() >= $this->lastLoadedData[ 'expire' ] ) {
				return false;
			}
			return true;
		}
		protected function getPath( $key ) {
			$path = $this->basePath . $key;

			return $path;
		}

		public function __construct() {
			$key            = self::ConfigurePath . '.BaseDir';
			$this->basePath = Configure::read( $key );
		}
	}

}
?>