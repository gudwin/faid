<?php
namespace Faid {
	/**
	 * Class DBSimple
	 * @package Faid
	 */
	class DBSimple {
		protected static $isSelect = false;

		/**
		 *
		 * Простой Select * запрос
		 *
		 * @param string $table имя таблицы
		 * @param mixed $where  возможные условия выборки, может быть как строкой, так и массив, если как массив, то в формате ключ => значение, ключи комбинируются условием and
		 * @param string $order возможные условия сортировки
		 */
		public static function select($table, $where = '', $order = '') {
			DB::checkConnection();
			$condition = self::getCondition($where);
			$sql       = 'select SQL_CALC_FOUND_ROWS * from %s where %s ';
			$sql       = sprintf($sql, $table, $condition);
			if ( !empty($order) ) {
				$sql .= sprintf('order by %s', $order);
			}

			return DB::query($sql);
		}

		/**
		 *
		 * Enter description here ...
		 *
		 * @param string $table    имя таблицы
		 * @param mixed $condition возможные условия выборки, может быть как строкой, так и массив, если как массив, то в формате ключ => значение, ключи комбинируются условием and
		 */
		public static function get($table, $condition, $order = '') {
			self::$isSelect = true;
			DB::checkConnection();
			$condition = self::getCondition($condition);
			$sql       = 'select * from %s where %s ';
			$sql       = sprintf($sql, DB::$connection->real_escape_string($table), $condition);
			if ( !empty($order) ) {
				$sql .= $order;
			}
			$sql .= ' LIMIT 0,1';

			return DB::get($sql);
		}

		public static function insert($table, $data) {
			$pieces = array();
			foreach ($data as $key => $row) {
				if ( is_int($key) ) {
					$pieces[ ] = $row;
				} else {
					$pieces[ ] = sprintf(
						'`%s` = "%s" ',
						DB::$connection->real_escape_string($key),
						DB::$connection->real_escape_string($row)
					);
				}
			}
			$sql = 'insert %s set %s ';

			$sql = sprintf($sql, DB::$connection->real_escape_string($table), implode(',', $pieces));
			DB::post($sql);

			return DB::$connection->insert_id;
		}

		public static function update($table, $setCondition, $whereCondition) {
			DB::checkConnection();
			$setCondition   = self::getCondition($setCondition, ' , ', true);
			$whereCondition = self::getCondition($whereCondition);
			$sql            = 'update %s set %s where %s';
			$sql            = sprintf($sql, DB::$connection->real_escape_string($table), $setCondition, $whereCondition);
			DB::post($sql);
		}

		public static function delete($table, $whereCondition) {
			DB::checkConnection();
			$whereCondition = self::getCondition($whereCondition);
			$sql            = 'delete from %s where %s';
			$sql            = sprintf($sql, DB::$connection->real_escape_string($table), $whereCondition);
			DB::post($sql);
		}

		/**
		 *
		 * Возвращает кол-во рядов попадающих под запрос
		 * @param string $table    имя таблицы
		 * @param array $condition условие запроса
		 */
		public static function getRowsCount($table, $condition = array()) {
			$condition = self::getCondition($condition);
			$sql       = 'select count(*) as `count` from %s where %s';
			$sql       = sprintf($sql, DB::$connection->real_escape_string($table), $condition);

			return DB::getField($sql, 'count');
		}

		private static function getCondition($condition, $glue = ' and ', $updateCondition = false) {
			if ( is_array($condition) && !empty($condition) ) {
				$sqlCond = array();
				foreach ($condition as $key => $row) {
					// Если ключ является числом, то это означает, что данная строка
					// идет без имени колонки и должна быть просто вставлена в запрос
					if ( is_int($key) ) {
						$sqlCond[ ] = !$updateCondition
							? '(' . $row . ')'
							: $row;
					} else {
						$sqlCond[ ] = sprintf(
							'`%s` = "%s"',
							DB::$connection->real_escape_string($key),
							DB::$connection->real_escape_string($row)
						);
					}
				}

				return implode($glue, $sqlCond);
			} elseif ( empty($condition) ) {
				// Возвращаем тогда всегда верное условие
				return '1';
			}

			return $condition;
		}
	}
}
?>