<?php

namespace Service;
	
	class Upload {

		public $path;

		// refactored files array
		public $files;

		// &$_FILES
		public $uploaded;

		// this files are denied to upload
		protected $denied = array ('php', 'php3', 'pl', 'exe', 'sql', 'js', 'rb', 'py', 'cgi');

		public $errors = array ();

		public $mime = array (
			'images' => array ('image/jpeg','image/pjpeg','image/gif','image/png','image/bmp')
		);

		public function __construct() {
			$this->path = DOCROOT . '/public/upload/';
			$this->uploaded = &$_FILES['userfiles'];
		}

		// check and upload images
		public function uploadImage($path = null) {
			$this->path .= '/original/';
			if($path != null)
				$this->path = $path;
			$this->getFiles();

			foreach ($this->files as $image) {
				$size = getimagesize($image['tmp_name']);
			    if(!$size) {
			    	$this->errors[] = 'Файл '.$image['name'].' не является изображением';
			        continue;
			    }

				if($this->checkMime($image, 'images') && !$this->denied($image)) {
					if(!$this->uploadFile($image))
						$this->errors[] = 'Невозможно загрузить файл ' . $image['name'] . ': '.$image['error'];
				} else
					$this->errors[] = 'Файл '.$image['name'].' не является изображением';
			}
		}

		// get array with files properties
		protected function getFiles() {
		    if(!is_array($this->uploaded['name'])) {
		    	$this->files[0] = $this->uploaded;
		    	$this->files[0]['ext'] = $this->getExt($this->uploaded['name']);

		    	$newFileName = md5(uniqid($this->uploaded['tmp_name'])).'.'.$this->files[0]['ext'];

		    	$this->files[0]['newname'] = $newFileName;
		    	return;
		    }

			$file_count = count($this->uploaded['name']);
		    $file_keys = array_keys($this->uploaded);

		    for ($i = 0; $i < $file_count; $i++)
			    foreach ($file_keys as $key) {
		            $this->files[$i][$key] = $this->uploaded[$key][$i];
		            $this->files[$i]['ext'] = $this->getExt($this->uploaded['name'][$i]);

		            $newFileName=md5(uniqid($this->uploaded['tmp_name'][$i])).'.'.$this->files[$i]['ext'];
		    		$this->files[$i]['newname'] = $newFileName;
		        }
		}

		protected function checkMime($file, $type) {
			if(in_array($file['type'], $this->mime[$type]))
				return true;

			return false;
		}

		protected function denied($file) {
			if(in_array($file['ext'], $this->denied))
				return true;

			return false;
		}

		// get file extension
		protected function getExt($filename) {
			return end((explode(".", $filename)));
		}

		// upload file to server
		protected function uploadFile($file) {
			return move_uploaded_file($file['tmp_name'], $this->path . $file['newname']);
		}
	}
?>