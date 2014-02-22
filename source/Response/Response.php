<?php
/**
 * @package faid
 */
namespace Faid\Response {
	use \Faid\StaticObservable;
	abstract class Response extends StaticObservable {
		/**
		 * @var bool
		 */
		protected $sent = false;
		/**
		 * Sets data for response
		 */
		public function setData( ) {
			self::callEvent('response.setData', $this, func_get_args());
		}
		/**
		 * Returns response data
		 */
		public abstract function getData();
		/**
		 * Outputs response to user
		 */
		public function send() {
			$this->sent = true;
		}
		public function isSent( ) {
			return $this->sent;
		}
	}
}
?>