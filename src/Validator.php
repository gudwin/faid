<?php
namespace Faid {
	abstract class Validator {

		public function isValid() {
			return $this->test();
		}
		abstract protected function test();
	}
}

?>