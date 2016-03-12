<?php

namespace Admin\Controller;
	
	class Options extends \Admin\Controller\Articles {

		public function __construct() {
			parent::__construct();
		}

		public function before() {
			$this->view->activeTab = 'file';
		}

		public function edit() {
			$article = \Model\Articles::find($this->route->id);
			if($article == null)
				throw new \Admin\Exception\PageNotFound;
			$this->view->article = $article;

			$this->view->option = \Model\Options::find($article->id);

			$this->view->tabLanguage = 'ru';
			if($this->route->lng)
				$this->view->tabLanguage = $this->route->lng;

			\Model\Settings::floorOptions();
		}

		public function edit_post() {
			$option = \Model\Options::find($_POST['id']);

			if($option == null) {
				$option = new \Model\Options;
				$option->article = $_POST['parent_id'];
				$option->id = $_POST['id'];
			}

			$option->caption = $_POST['caption'];
			$option->options = json_encode($_POST['options']);
			$option->save();

			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно сохранено'
			));
		}
	}

?>