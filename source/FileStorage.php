<?php
namespace Faid;

/**
 * This is class configurable class (@class \Faid\Config)
 *                Example of DEFAULT Config in  :
 *    'FileStorage' => array(
 *        'configs' => array(
 *            'default' => array(
 *                'class'   => '\Faid\StorageEngines\LocalFile',
 *                // place storage specific data here:
 *                baseDir' => ''
 *            )
 *            'active' => 'default'
 *        ),
),
),
'active' => 'default'
 *
 *
 * Class FileStorage
 * @package       Faid
 */
class FileStorage extends StaticObservable {
	const ConfigureKey = 'FileStorage';
	/**
	 * @var \Faid\FileStorageEngines\Base
	 */
	protected static $instance;

	public static function get($path) {
	}

	public static function copy($path, $pathTo) {
	}

	public static function remove($path) {
	}

	public static function fileList($path) {
	}

	public static function upload($path, $fileData, $config = array()) {

	}

	public static function chmod($path, $value, $config = array()) {

	}

	/**
	 *
	 */
	public static function setupInstance($instance) {
		self::$instance = $instance;
	}

	/**
	 * This method flushes all internal data
	 */
	public static function clear( ) {
		self::$instance = null;
	}
	/**
	 *
	 */
	public static function checkInstance() {
		/**
		 *
		 */
		if ( empty(self::$instance) ) {
			// try to initialize  current
			self::initInstance();
		}
	}
	/**
	 *
	 */
	protected static function initInstance() {
		//
	}
}