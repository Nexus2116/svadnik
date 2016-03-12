<?php
	
namespace Model;

	class Options extends \Core\Model {

		public $table = 'options';

		public function get($key) {
			$data = json_decode($this->options);
			if(isset($data->$key))
				return $data->$key;

			return null;
		}

		protected function getPage($field, $value, $op = '=') {
			return \Capsule\Db::table('articles')
            ->join($this->table, 'articles.id', '=', $this->table.'.id')
            ->where($field, $op, $value)
            ->where('deleted_at', null);
		}

	}

?>