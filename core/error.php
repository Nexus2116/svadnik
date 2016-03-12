<?php

namespace Core {

	class Error extends \Exception {

		public function __construct() {
			$this->register();
		}

	    public function register() {
	        set_exception_handler(array($this, 'handler'));
	    }
	    
	    public function handler(\Exception $e) {
			$e->process($e);
	    }

	    public function process(\Exception $e) {
	    	print_r($e);
	    }

	}

}

?>