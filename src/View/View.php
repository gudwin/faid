<?php

namespace Faid\View {
	use \Faid\StaticObservable;

	class View extends StaticObservable {
		protected $rendered = false;

		/**
		 * @var array
		 */
		protected $helpers = array();

		/**
		 * @var array
		 */
		protected $viewVars = array();

		/**
		 * @var View
		 */
		protected $layout = NULL;

		/**
		 * @var string
		 */
		protected $viewPath = '';

		/**
		 * @param $filePath
		 */
		public function __construct($filePath) {
			//
			$filePath = $this->getFilePath($filePath);
			//
			$this->filePath = $filePath;
		}
		public function __isset( $key ) {
			$key = strtolower($key);
			foreach ($this->helpers as $helperName => $helper) {
				$isSame = strtolower($helperName) === $key;
				if ( $isSame ) {
					return true;
				}
			}
			return false;
		}
		/**
		 * @param $key
		 *
		 * @return mixed
		 * @throws Exception
		 */
		public function __get($key) {
			$key = strtolower($key);

			//
			foreach ($this->helpers as $helperName => $helper) {
				$isSame = strtolower($helperName) === $key;
				if ( $isSame ) {
					return $helper;
				}
			}
			//
			throw new Exception(sprintf('Helper class `%s` not found inside view', $key));
		}

		public function addHelper($helper, $helperName = '') {

			//
			if ( !is_object($helper) ) {
				//
				if ( empty($helperName) ) {
					$helperName = $helper;
				}
				if ( !class_exists($helper) ) {
					$error = sprintf('Helper class `%s` not found', $helper);
					throw new Exception($error);
				}
				$helper = new $helper();
			} else {
				if ( empty($helperName) ) {
					$helperName = get_class($helper);
				}
			}
			//
			$this->helpers[ $helperName ] = $helper;
		}

		/**
		 * @param $layoutFile
		 */
		public function setLayout($layoutFile) {
			$layoutFile   = is_string($layoutFile) ? new View($layoutFile) : $layoutFile;
			$this->layout = $layoutFile;
		}

		/**
		 *
		 */
		public function getLayout() {
			return $this->layout;
		}

		/**
		 * Returns path to file
		 * @return mixed
		 */
		public function getPath() {
			return $this->filePath;
		}

		/**
		 *
		 */
		public function getViewVars() {
			return $this->viewVars;
		}

		/**
		 *
		 */
		public function setViewVars($newViewVars) {
			$this->viewVars = $newViewVars;
		}

		/**
		 * @param $key
		 * @param $data
		 */
		public function set($key, $data = NULL) {
			if ( is_null($data) ) {
				if ( is_array($key) || is_object($key) ) {
					foreach ($key as $varName => $value) {
						$this->set($varName, $value);
					}
				} else {
					$this->viewVars[ $key ] = null;
				}
			} else {
				$this->viewVars[ $key ] = $data;
			}

		}

		/**
		 * @param $key
		 *
		 * @return mixed
		 */
		public function get($key) {
			if ( !isset($this->viewVars[ $key ]) ) {
				throw new Exception("Can`t find variable '$key' ");
			}

			return $this->viewVars[ $key ];
		}

		public function isRendered() {
			return $this->rendered;
		}

		/**
		 * @param array $vars
		 *
		 * @return string
		 */
		public function render() {
			//
			$this->beforeRender();
			//
			$oldContents = ob_get_contents();
			//
			if ( sizeof(ob_list_handlers()) > 0 ) {
				ob_clean();
			} else {
				ob_start();
			}
			//
			$content = $this->renderFile($this->viewVars, $this->filePath);
			//
			if ( !empty($this->layout) ) {
				$vars                         = $this->viewVars;
				$vars[ 'content_for_layout' ] = $content;
				$this->layout->set($vars);
				$content = $this->layout->render();
			}
			//
			print $oldContents;
			//
			$this->rendered = true;

			//
			return $content;
		}

		protected function renderFile($vars, $file) {
			extract($vars);
			include $file;
			$result = ob_get_contents();
			ob_clean();

			return $result;
		}

		/**
		 *
		 */
		protected function beforeRender() {
			// iterate over all helpers and call "beforeRender" method with $this as argument
			foreach ($this->helpers as $name => $helper) {
				$isCallable = is_callable(array($helper, 'beforeRender'));
				if ( $isCallable ) {
					$helper->beforeRender($this);
				}
				if ( !is_int($name) ) {
					$this->viewVars[ $name ] = $helper;
				}
			}
			// call event
			self::callEvent('View.render', $this);
		}

		/**
		 * @param $path
		 */
		protected function getFilePath($path) {
			if ( !file_exists($path) || !is_file($path) ) {
				$error = sprintf('Failed to find file - %s', $path);
				throw new Exception($error);
			}

			return realpath($path);
		}
	}
}
?>