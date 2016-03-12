<?php

namespace Admin\Controller;
	
	class Products extends \Admin\Controller\Articles {

		public function __construct() {
			parent::__construct();
		}

		public function before() {
			$this->view->activeTab = 'file';
		}		
	}

?>