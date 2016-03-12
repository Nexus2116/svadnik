<?php
	
namespace Model;

	class Image extends \Core\Model {

		public function getAltAttribute($value) {
	        return json_decode($value);
	    }

	    public function scopeGallery($query, $id) {
	    	return $query->where('gallery', $id)->where('published', 1)->orderBy('sort', 'ASC');
	    }

	}

?>