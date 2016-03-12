<?php

namespace Admin\Service;
	
	class Image {

		public $path;

		public $quality = 95;

		public function  __construct() {
			$this->path = DOCROOT . '/public/upload/';
		}

		public function resize($scale, $images) {
			include_once DOCROOT . '/vendor/imagetoolkit/AcImage.php';
			\AcImage::setTransparency(true);
			\AcImage::setQuality($this->quality);


			foreach ($scale as $key => $value) {
				$path = $this->path . $key;
				@mkdir($path, 0755);

				foreach ($images as $image) {
					$img = \AcImage::createImage($this->path .'/original/'. $image['newname']);
					if($value[0] == null && $value[1] != null)
						$img->resizeByHeight($value[1]);
					else if ($value[0] != null && $value[1] == null)
						$img->resizeByWidth($value[1]);
					else
						$img->resize($value[0], $value[1]);
					$img->save($path .'/'. $image['newname']);
				}
			}
		}

		public function watermark($logo, $image) {
			include_once DOCROOT . '/vendor/imagetoolkit/AcImage.php';

			\AcImage::setTransparency(true);
			\AcImage::setQuality($this->quality);
			\AcImage::setRewrite(true);

			$img = \AcImage::createImage($image);
			$img->drawLogo($logo, \AcImage::CENTER);
			$img->save($image);
		}
	}
?>