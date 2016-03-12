<?php

namespace Core {
	
	if(!\App::config('eloquent')) {
		require_once SYSPATH . '/eloquent/autoload.php';

		$default = \App::config('default_connection');
		$connections = \App::config('connections');

		\Capsule\Database\Connection::make('default', $connections[$default], true);

		\App::config('eloquent', true);
	}


	class Model extends \Illuminate\Database\Eloquent\Model {

		public function __construct() {
			parent::__construct();
		}
	}
	
}
?>
