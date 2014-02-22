<?php
namespace Faid\Tests\FileStorage {
	use \Faid\FileStorageEngines\Base;

	class testStorage extends Base {
		public static $methodName = '';

		public static $argument1 = '';

		public static $argument2 = '';

		public static $argument3 = '';

		/**
		 * @param $path string
		 */
		public function get($path) {
			self::$methodName = __METHOD__;
			self::$argument1  = $path;
		}

		/**
		 * @param $path
		 * @param $contents
		 */
		public function upload($path, $contents, $config) {
			self::$methodName = __METHOD__;
			self::$argument1  = $path;
			self::$argument2  = $contents;
			self::$argument3  = $config;
		}

		/**
		 * @param $path string
		 */
		public function remove($path) {
			self::$methodName = __METHOD__;
			self::$argument1  = $path;
		}

		/**
		 * @param $from string
		 * @param $to   string
		 */
		public function copy($from, $to) {
		}

		/**
		 * @param $path string
		 */
		public function chmod($path, $rights) {
			self::$methodName = __METHOD__;
			self::$argument1  = $path;
			self::$argument2  = $rights;
		}

		/**
		 * @param $path string
		 */
		public function fileList($path) {
			self::$methodName = __METHOD__;
			self::$argument1  = $path;
		}
	}
}