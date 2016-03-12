<?php

namespace Admin\Controller;
	
	class Upload extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function image_post() {
			$article = \Model\Articles::find($_POST['article']);
			if($article == null)
				exit;

			$this->service->uploadImage();

			$scales = \App::config('scales');

			$imageService = new \Admin\Service\Image;
			$imageService->resize($scales[$_POST['scale']], $this->service->files);

			$this->view->image = $this->service->files[0];

			$key = $_POST['field'];
			$article->$key = $this->view->image['newname'];
			$article->save();

			$this->view->field = $key;
			$this->view->render('image.result');
			exit;
		}


		public function gallery() {
			$article = \Model\Articles::find($this->route->id);
			if($article == null)
				exit;

			$scales = \App::config('scales');
			$scale = $scales[$this->route->scale];

			$_FILES['userfiles'] = $_FILES['file'];
			unset($_FILES['file']);

			$this->service->uploadImage();
			$imageService = new \Admin\Service\Image;
			$imageService->resize($scale, $this->service->files);

			$picture = $this->service->files[0];

			if($this->route->scale == 'products') {
				foreach ($this->service->files as $image) {
					$path = DOCROOT . '/public/upload/gallery/' . $image['newname'];
					$imageService->watermark(DOCROOT . '/public/images/watermark.png', $path);
				}
			}

			$image = new \Model\Image;
			$image->path = $picture['newname'];
			$image->gallery = $article->gallery;
			$image->save();

			echo $image->path;
			exit;
		}

		public function redactor() {
			$path = DOCROOT . '/public/media/images/';
			$this->service->path = $path;
			$this->service->uploaded = &$_FILES['file'];

			$this->service->uploadImage($path);
			$file = array(
		        'filelink' => '/public/media/images/' . $this->service->files[0]['newname']
		    );

		    \Core\Response::json($file);
		}
		
	}

?>