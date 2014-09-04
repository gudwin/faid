<?php
namespace Faid {
	use \Faid\Configure\Configure;

	class DB extends StaticObservable
	{
		/**
		 *
		 * В данной версии хранится последний выполненный SQL-запрос
		 * @var unknown_type
		 */
		public static $lastSQL;
		/**
		 * Mysql connection handler
		 * @var \mysqli
		 */
		public static $connection;

		/**
		 * Check if connection with mysql server was established or not
		 */
		public static function checkConnection()
		{
			if (!empty( self::$connection )) {
				// connection established, exit function
			} else {
				// no, we have to connect Mysql-server
				$data = Configure::read( 'DB' );
				self::$connection = new \mysqli( $data['host'], $data['user'], $data['password'], $data['database'] );
				if (self::$connection->connect_error) {
					$msg = sprintf( 'DB failed to connect mysql server. Mysql respone - %s', self::$connection->connect_error );
					throw new \Exception( $msg );
				}
				self::$connection->set_charset( 'utf8' );
			}
		}

		static public function get( $sql )
		{
			self::checkConnection();
			self::$lastSQL = $sql;
			self::callEvent( 'DB::before_get', $sql );
			$result = self::$connection->query( $sql );
			if (!$result) {
				throw new \Exception( 'DB <span style="color:Darkred">' . self::$connection->error . '</span> Query:<br/><pre>' . $sql . '</pre>' );
			}
			return $result->fetch_assoc();
		}

		static public function post( $sql, $bIgnore = false )
		{
			self::checkConnection();
			self::$lastSQL = $sql;
			self::callEvent( 'DB::before_post', $sql );
			$result = self::$connection->query( $sql );

			if (!$result) {
				if ($bIgnore) {
					return null;
				} else {
					throw new \Exception( 'DB <span style="color:Darkred">' . self::$connection->error . '</span> Query:<br/><pre>' . $sql . '</pre>' );

				}

			}
			return false;
		}

		static public function query( $sql, $bUserFetchAssoc = true )
		{
			self::checkConnection();
			self::$lastSQL = $sql;
			self::callEvent( 'DB::before_query', $sql );
			$result = self::$connection->query( $sql );
			if (!$result) {
				throw new \Exception( 'DB <span style="color:Darkred">' . self::$connection->error . '</span> Query:<br/><pre>' . $sql . '</pre>' );
			}
			$method = $bUserFetchAssoc ? MYSQL_ASSOC : MYSQL_NUM;
			if (is_callable( array( $result, 'fetch_all' ) )) {
				$result = $result->fetch_all( $method );
			} elseif ( is_object( $result )) {
				$rows = array();
				$methodName = $bUserFetchAssoc ? 'fetch_assoc' : 'fetch_row';
				while ($row = $result->$methodName()) {
					$rows[] = $row;
				}
				;
				$result = $rows;
			}

			return $result;

		}

		/**
		 * Возвращает только одно поле из результат запроса
		 * @param string $sql
		 * @param string $field
		 */
		static function getField( $sql, $field )
		{
			self::$lastSQL = $sql;
			self::callEvent( 'DB::before_getField', $sql );
			$result = self::get( $sql );
			if (!isset( $result[$field] )) {
				throw new \Exception ( 'Field `' . $field . '` not found' );
			}
			return $result[$field];
		}

		/**
		 *
		 * Возвращает последний вставленный индекс
		 */
		static function getInsertId()
		{
			return self::$connection->insert_id;
		}

		static public function getAutoIncrement( $table )
		{
			self::checkConnection();
			$table = self::$connection->real_escape_string( $table );
			$rows = self::query( "SHOW TABLE STATUS LIKE '$table'", self::$connection );
			return $rows[0]['Auto_increment'];
		}

		/**
		 *
		 */
		static public function escape( $value )
		{
			if ( is_array( $value )) {
				throw new \InvalidArgumentException('Can`t be an array');
			}
			self::checkConnection();
			return self::$connection->real_escape_string( $value );
		}

		/**
		 *
		 */
		static public function setConnection( $connection )
		{
			self::$connection = $connection;
		}

		static public function getConnection()
		{
			return self::$connection;
		}
	}

	/**
	 *
	 * Простой хелпер для примитивных запросов в бд (заебало ошибаться в мелочах ;)
	 * @author Gisma
	 *
	 */
}
?>