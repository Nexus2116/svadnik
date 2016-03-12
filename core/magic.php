<?php

namespace Core {

	abstract class Magic {

		protected $containerData = array ();

		public function set($key, $value) {
			$this->containerData[$key] = $value;
		}

		public function get($key) {
			if(!isset($this->containerData[$key]))
				return null;
			return $this->containerData[$key];
		}

		public function __set($key, $value) {
			$this->set($key, $value);
		}

		public function __get($key) {
			return $this->get($key);
		}
	}

}

?>