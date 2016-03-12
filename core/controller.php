<?php

namespace Core {

	abstract class Controller {

		public function __construct() {

		}

		public function before() {

		}

		public function after() {

		}

		public function __get($key) {
			if(isset($this->$key))
				return $this->$key;

			$service = \App::get($key);
			if($service != null)
				return $service;

			return null;
		}

		public function seo($title, $keywords, $description) {
			\App::view('title', $title);
			\App::view('keywords', $keywords);
			\App::view('description', $description);
		}

	}
}

?>