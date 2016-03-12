<?php

namespace Core {
	
	class Response {

		// class instance
		private static $instance = null;

		// state data for ajax-response
		public static $data = array (
			'valid' => true
		);

		private static $status = array (
			'404' => "HTTP/1.0 404 Not Found"
		);

		public function __construct() {}

		// send any data
		public static function sendMessage($message) {
			echo $message;
			exit();
		}

		// send ajax-response as JSON data
		public static function send($valid = true) {
			self::getInstance();

			if($valid == false)
				self::$instance->data['valid'] = false;

			echo json_encode(self::$instance->data);
			exit();
		}

		public static function json($data) {
			echo json_encode($data);
			exit();
		}

		// render response
		// when $renderFile == true, Response will contain View::render()
		public static function render($renderFile = false) {
			$View = \App::view();

			if($renderFile == true) {
				$View->render();
				exit();
			}

			$View->renderLayout();
			exit();
		}

		public static function navigate($url) {
			header('Location: ' . $url);
		}

		public static function __callStatic($name, $args) {
			return self::call($name, $args); 
		}

		public function __call($name, $args) {
			return self::call($name, $args); 
		}

		private static function call($name, $args) {
			self::getInstance();
			self::$instance->data[$name] = self::checkArgs($args);
			return self::$instance;
		}

		private static function checkArgs($args) {
			if(count($args) == 1)
				return $args[0];

			if(count($args) > 1)
				return $args;
		}

		private static function getInstance() {
			if(self::$instance == null)
				self::$instance = new self;
		}

		public static function setHeader($code) {
			header(self::$status[$code]);
		}
	}

}

?>