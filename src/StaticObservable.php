<?php
/**
 *
 * Pattern observable
 * @date 18.02.2012
 * @author Gisma
 *
 */
namespace Faid {
	class StaticObservable {
		const eventCallEvent = 'faid::callEvent';
		const eventAddEventListener = 'faid::addEventListener';
		/**
		 *
		 * Enter description here ...
		 * @var unknown_type
		 */
		protected static $eventMap = array();

		///////////////////////////////////////////////////////////////////////////
		// Public static methods
		/**
		 *
		 * Enter description here ...
		 * @param string $event
		 * @param callable $callback
		 */
		public static function addEventListener( $event, $callback) {
			$event = strtolower( $event );
			//
			if (!isset( self::$eventMap[$event] )) {
				self::$eventMap[$event] = array();
			}
			self::$eventMap[$event][] = $callback;
			self::callEvent(self::eventAddEventListener);
		}
		///////////////////////////////////////////////////////////////////////////
		// Protected static methods
		/**
		 *
		 * Enter description here ...
		 * @param string $event
		 */
		protected static function callEvent( $event ) {
			$event = strtolower( $event );
			$arguments = func_get_args();
			array_shift($arguments);

			if (isset(self::$eventMap[$event])) {
				foreach ( self::$eventMap[$event] as $callback ) {
					call_user_func_array($callback, $arguments);
				}
				if (self::eventCallEvent != $event ) {
					self::callEvent(self::eventCallEvent,$event,$arguments);
				}
			}
		}
		/**
		 *
		 * Enter description here ...
		 * @param string $event
		 */
		protected static function callFilter( $event, $data ) {
			$event = strtolower( $event );
			//
			if (isset(self::$eventMap[$event])) {
				foreach ( self::$eventMap[$event] as $callback ) {
					$data = call_user_func($callback, $data );
				}
			}
			return $data;
		}
	}
}
?>