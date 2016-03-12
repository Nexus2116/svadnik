<?php

namespace Core {

	class i18n{

		private $params = array();

		public $lang;

		public function __construct() {
			$this->lang = \App::$state->lang;
			$this->params = include_once (APPPATH . '/i18n/' . $this->lang . '.php');
		}

		public function __get($param) {
			if (!isset($this->params[$param]))
				return $param;

			return $this->params[$param];
		}
	}
}

?>