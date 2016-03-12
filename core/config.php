<?php

namespace Core {

	class Config extends Magic {

		// array of sub applications names
		public $subApps = array ();

		// array of languages shortcats and defines
		public $langs = array ();

		// default language
		public $lang;

		// default environment
		public $environment = 'development';

		// database connections
		public $connections = array ();

		// default database connection
		public $default_connection;

	}

}

?>