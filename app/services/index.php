<?php

namespace Service;
	
	class Index {

		public function trailers($object) {
			\App::view('articles', \Model\Trailer::childs($object['id']));
		}

		public function panoramas($object) {
			foreach (\App::view('articles') as $key => $value) {
				if($value['url'] == 'panoramas') {
					\App::view('pano', \Model\Articles::childs($value['id'], 1, 50));
					break;
				}
			}
		}

		public function video($object) {
			foreach (\App::view('articles') as $key => $value) {
				if($value['url'] == 'video') {
					\App::view('video', \Model\Video::childs($value['id']));
					break;
				}
			}
		}

		public function gallery($object) {
			foreach (\App::view('articles') as $key => $value) {
				if($value['url'] == 'photo') {
					\App::view('album', \Model\Articles::gallery($value['id']));
					break;
				}
			}

			$this->video($object);
			$this->panoramas($object);
		}

	}
?>