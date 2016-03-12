<?php

namespace Exception;

	// 404 error
	class pageNotFound extends \Core\Error {

		public function process() {
			\Core\Response::setHeader(404);
			//echo "Page Not Found";
			\App::view()->renderFile('error/pagenotfound');
		}
	}
?>