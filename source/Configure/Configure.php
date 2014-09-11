<?php
/**
 * Provides functions for storing and reading configuration data
 */
namespace Faid\Configure {
	use Faid\StaticObservable as StaticObservable;

	/**
	 * Class Configure
	 * If you want to listen when Configure options are changed you have to attach event listener to event
	 * <b>"Configure.write"</b>.
	 * <code>
	 * use \Faid\Configure
	 * Configure.addEventListener( 'Configure.write', function ( $key, $data ) {
	 *      print ('Property "%s% changed to new value:&lt;br&gt;%s', $key, print_r( $data, true) );
	 * });
	 * </code>
	 */
	class Configure extends StaticObservable {
		/**
		 * Map of all data
		 * @var array
		 */
		public static $data = array();

		/**
		 * Return data by given $key
		 *
		 * @param $key
		 *
		 * @return array
		 * @throws ConfigureException
		 */
		public static function read( $key ) {
			// split keys
			$keys = self::explode( $key );
			// read value
			$data = self::$data;

			foreach ( $keys as $part ) {
				if ( isset( $data[ $part ] ) ) {
					$data = $data[ $part ];
				} else {

					throw new ConfigureException( $key );
				}
			}
			return $data;
		}

		/**
		 * Writes key to database
		 *
		 * @param $key
		 * @param $newData array
		 *
		 * @throws ConfigureException
		 */
		public static function write( $key, $newData ) {
			// split keys
			$keys = self::explode( $key );
			// write values
			$data = & self::$data;
			foreach ( $keys as $part ) {
				if ( !isset( $data[ $part ] ) ) {
					$data[ $part ] = array();
				}
				$data = & $data[ $part ];
			}
			$data = $newData;
			self::callEvent( 'Configure.write', $key, $newData );
		}

		/**
		 * Explodes given string to array that contains path to data
		 *
		 * @param $key
		 *
		 * @return array
		 */
		private static function explode( $key ) {
			return explode( '.', $key );
		}
	}
}
?>