<?php

namespace Admin\Exception;

	// 404 error
	class pageNotFound extends \Core\Error {

		public function process() {
			//\Core\Response::setHeader(404);
			if(\App::route('ajax'))
				\Core\Response::json(array ('pageNotFound' => true));

			\App::view()->renderFile('error/pagenotfound');
		}
	}

	// not auth exception
	class Auth extends \Core\Error {

		public function process() {
			$url = \HTML::url('/login');
			if(!\App::route('ajax'))
				\Core\Response::navigate($url);

			\Core\Response::json(array ('url' => $url));
		}
	}
?>