<?php

namespace Admin\Controller;
	
	class Help extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function before() {
			$this->view->activeTab = 'help';
		}


		public function index() {
			$this->view->selected = 2;
		}

		public function faq() {
			$this->view->selected = 1;	
		}

		public function after() {
			if($this->route->ajax)
				\Core\Response::render(true);
		}
	}

?>