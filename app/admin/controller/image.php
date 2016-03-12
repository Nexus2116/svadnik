<?php

namespace Admin\Controller;
	
	class Image extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function publish() {
			$item = $this->route->id;
			$image = \Model\Image::find($item);
			$image->published = $image->published >= 1 ? 0 : 1;
			$image->save();
		}

		public function delete() {
			$image = \Model\Image::find($this->route->id);

			$scales = \App::config('scales');
			$scale = $scales[$this->route->scale];
			
			unlink(DOCROOT . '/public/upload/original/' . $image->path);
			foreach ($scale as $key => $value)
				unlink(DOCROOT . '/public/upload/' . $key . "/" . $image->path);


			$image->delete();
			exit;
		}

		public function comment_post() {
			$image = \Model\Image::find($_POST['id']);
			$image->alt = json_encode($_POST['comment']);
			$image->center = $_POST['center'];
			$image->save();
			
			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно сохранено'
			));
		}

		public function media() {
			$path = DOCROOT . '/public/media/images/';
			$images = array ();
			
			if (is_dir($path)) {
			    if ($dh = opendir($path)) {
			        while (($file = readdir($dh)) !== false) {
			        	if($file == '.' || $file == '..')
			        		continue;
			        	$data = array ();
			        	$data['image'] = '/public/media/images/' . $file;
			        	$data['thumb'] = '/public/media/images/' . $file;
			            $images[] = $data;
			        }
			        closedir($dh);
			    }
			}

			\Core\Response::json($images);
		}

		public function sort() {

			foreach ($_GET as $id => $sort) {
				$image = \Model\Image::find($id);
				$image->sort = $sort;
				$image->save();
			}

			\Core\Response::json(array('valid' => true));
		}
	}

?>