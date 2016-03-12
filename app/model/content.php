<?php
	
namespace Model;

	class Content extends \Core\Model {

		public $primaryKey = array('articles_id', 'lang');
		public $incrementing = false;
		public $table = 'articles_content';

		public function article() {
	        return $this->belongsTo('Model\Articles', 'articles_id', 'id');
	    }

	    public function scopePage($query, $id, $lang) {
	    	return $query->where('articles_id', $id)->where('lang', $lang);
	    }

	    public function scopeGetContent($query, $id, $lang) {
	    	return $query->where('articles_id', $id)->where('lang', $lang)->where('published', 1);
	    }

	    public function saveContent($id, $lang, $modelClass) {
	    	$model = new $modelClass;
			$content = $model->page($id, $lang)->first();

			if($content == null) {
				$content = new $modelClass;
				$content->articles_id = $id;
				foreach ($_POST as $key => $value)
					$content->$key = $value;
				$content->author = \App::session()->admin->id;
				$content->save();
			} else {
				$data = array ();
				foreach ($_POST as $key => $value)
					$data[$key] = $value;
				$content->page($id, $lang)->update($data);
			}
		}

	}

?>