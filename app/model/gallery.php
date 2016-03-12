<?php
	
namespace Model;

	class Gallery extends \Core\Model {

		public function images() {
			return $this->hasMany('Model\Image');
		}
	}

?>