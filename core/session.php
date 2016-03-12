<?php

namespace Core {

	class Session {

		public static $id;

		public static function start($server) {
			session_name('sessid');

			session_start();
			self::$id = session_id();
		}

		public static function end() {
			session_destroy();
		}

		public static function commit() {
			session_commit();
		}

		public static function get($name) {
			if (isset($_SESSION[$name]))
				return $_SESSION[$name];

			return null;
		}

		public static function set($name, $value) {
			$_SESSION[$name] = $value;
		}

		public static function remove($name) {
			if (isset($_SESSION[$name]))
				unset($_SESSION[$name]);
		}

		public function __get($key) {
			return self::get($key);
		}

		public function __set($key, $value) {
			$this->set($key, $value);
		}

	}
}

?>
