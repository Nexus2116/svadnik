<?php

namespace Core {

	class Dispatcher {

		private $controller;

		public function __construct() {
			//session_start();
			\App::session()->start(\App::route()->host);
			$this->run();
		}

		protected function run() {
			$controller = \App::route()->controller;
			$action = \App::route()->action;

			$View = \App::view();
			\App::$state->_sessionId = Session::$id;

			include_once (APPPATH . '/bootstrap.php');
			$class = '\bootstrap';
			$bootstrap = new $class;

			$clazz = 'Controller\\' . $controller;
			$service = 'Service\\' . $controller;
			if(\App::$state->app != '') {
				$clazz = \App::$state->app . '\\' . $clazz;
				$service = \App::$state->app . '\\' . $service;
			}

			$servicePath = strtolower(str_replace('\\', '/', $service));
			$servicePath = strtolower(str_replace('service', 'services', $servicePath));
			$servicePath = APP .'/'. $servicePath . '.php';

			if(file_exists($servicePath)) {
				$serviceObject = new $service();
				\App::set('service', $serviceObject);
			} else 
				\App::set('service', new \StdClass);

			try {
				$this->controller = new $clazz();
			} catch(Exception $e) {
				$errorClass = \App::$state->app . '\Exception\PageNotFound';
				throw new $errorClass();
			}

			if(method_exists($bootstrap, 'before'))
				$bootstrap->before();

			if(method_exists(\App::service(), $action . '_before'))
				\App::service()->{$action . '_before'}();
			$this->controller->before();

			if(!method_exists($this->controller, $action)) {
				$errorClass = \App::$state->app . '\Exception\PageNotFound';
				throw new $errorClass();
			}
			$this->controller->{$action}();

			$this->controller->after();
			if(method_exists(\App::service(), $action . '_after'))
				\App::service()->{$action . '_after'}();

			if(method_exists($bootstrap, 'after'))
				$bootstrap->after();

			Response::render();
		}

	}

}

?>
