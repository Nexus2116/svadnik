<?php

namespace Controller;
	
	class Sitemap extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function index() {
			$this->view->layout = 'index';
			$this->seo('Главная страница', 'Цитадель', 'Цитадель');

			$dataItem = \Model\Articles::getPage('url', $this->route->urlParts[0])->first();
			\App::view('dataItem', $dataItem);


		}

	}

?>
