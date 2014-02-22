<?php
//************************************************************//
//                                                            //
//              Базовый класс документа                       //
//       Copyright (c) 2006-2011  Extasy Team                 //
//       Email:   dmitrey.schevchenko@gmail.com               //
//                                                            //
//  Разработчик: Gisma (26.06.2007)                           //
//                                                            //
//************************************************************//
namespace Faid {

	abstract class Model extends StaticObservable {

		const ModelName = '';

		/**
		 * @var string Имя колонки, в которой хранится основной (primary) индекс таблицы БД
		 */
		protected $index = 'id';

		/**
		 *
		 */
		protected $columns = array();

		/**
		 *
		 * Enter description here ...
		 *
		 * @param unknown_type $initialData
		 */
		public function __construct($initialData = array()) {
			$this->setData($initialData);
		}

		/**
		 * Возвращает одно из данных документа
		 */
		public function __get($szKey) {
			return $this->attr($szKey);
		}

		public function __isset($name) {
			$name = strtolower( $name );
			return isset($this->columns[ $name ]);
		}

		/**
		 * Возвращает одно из данных документа
		 */
		public function __set($szKey, $szValue) {
			$this->setData(
				array(
					 $szKey => $szValue
				)
			);
		}

		/**
		 * Возвращает атрибут в виде объекта
		 */
		public function attr($columnName) {
			$columnName = strtolower( $columnName );
			if ( !isset($this->columns[ $columnName ]) ) {
				// Иначе бросаем исключение
				$szText = ('In document ("%s","%s") attribute `%s` not found');
				$szText = sprintf($szText, static::ModelName, $this->columns[ $this->index ]->getValue(), $columnName);
				throw new \Exception($szText);
			}

			return $this->columns[ $columnName ];

		}

		/**
		 * Returns current index value
		 * @return int
		 */
		public function getId() {
			return $this->columns[ $this->index ];
		}

		/**
		 * Returns index column name
		 * @return string
		 */
		public function getIndex() {
			return $this->index;
		}

		/**
		 * Данная функция вызывается перед каждым обновлением документа (перед insert и update) её задача - проверка
		 * документа на валидность
		 * Переопределяйте эту функцию в дочерних документах
		 */
		public function validate() {
			return true;
		}

		/**
		 *
		 * Enter description here ...
		 */
		public static function getModelName() {
			return static::ModelName;
		}

		/**
		 * @desc Возвращает внутренние данные документ
		 * @return
		 */
		public function getData() {
			return $this->columns;
		}

		/**
		 * @desc Устанавливает значение внутренних данных
		 * @return
		 */
		public function setData(array $newData) {
			foreach ($newData as $key => $row) {
				$key = strtolower( $key );
				$this->columns[ $key ] = $row;
			}
		}

		///////////////////////////////////////////////////////////////////////////
		// Abstract methods
		/**
		 *
		 * Enter description here ...
		 *
		 * @param unknown_type $index
		 */
		public abstract function get($index);

		/**
		 *
		 * Enter description here ...
		 */
		public abstract function insert();

		/**
		 *
		 * Enter description here ...
		 */
		public abstract function update();

		/**
		 *
		 * Enter description here ...
		 */
		public abstract function delete();

	}
}
?>