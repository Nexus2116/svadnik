<?php

namespace Model;

	class Articles extends \Core\Model {

		public function content($type) {
			return $this->hasMany('Model\\' . $type);
		}

		public function gallery($id) {
			return \Model\Image::where('gallery', $id)->orderBy('sort', 'ASC')->get();
		}

		public function image() {
			return $this->hasOne('Model\Image');
		}

		// scopes

		public function scopeUndel($query) {
			return $query->where('deleted_at', null);
		}

		public function scopeUrl($query, $url) {
			return $query->where('published', 1)->where('deleted_at', null)->where('url', 'about');
		}

		public function byUrl($url) {
			return (object) \Model\Articles::getPage('url', $url)->first();
		}

		public function byId($id) {
			if(is_array($id))
				return (object) \Model\Articles::getPage('article.id', $id, 'IN')->get();

			return (object) \Model\Articles::getPage('article.id', $id)->first();
		}

		public function childs($id, $page = 1, $perPage = 10, $order = null, $direct = 'asc') {
			$offset = ($page - 1) * $perPage;
			$data = \Model\Articles::getPage('parent_id', $id)->skip($offset)->take($perPage);
			if($order != null)
				$data->orderBy($order, $direct);
			return $data->get();
		}

		protected function getPage($field, $value, $op = '=') {
			return \Capsule\Db::table('articles')
            ->join('articles_content', 'articles.id', '=', 'articles_content.articles_id')
            ->where($field, $op, $value)
            ->where('deleted_at', null)
            ->where('lang', \App::$state->lang)
            ->where('articles_content.published', 1)
            ->orderBy('sort', 'ASC');
		}

		protected function getPageCity($field, $value, $op = '=') {
			return \Capsule\Db::table('articles')
            ->join('articles_content_city', 'articles.id', '=', 'articles_content_city.articles_id')
            ->where($field, $op, $value)
            ->where('deleted_at', null)
            ->where('lang', \App::$state->lang)
            ->where('articles_content_city.published', 1)
            ->orderBy('sort', 'ASC');
		}

		protected function getPageCityBanner($field, $value, $op = '=') {
			return \Capsule\Db::table('articles')
            ->join('city_banners', 'articles.id', '=', 'city_banners.articles_id')
            ->where($field, $op, $value)
            ->where('deleted_at', null)
            ->where('lang', \App::$state->lang)
            ->where('city_banners.published', 1)
            ->orderBy('sort', 'ASC');
		}

		protected function getPageCityNews($field, $value, $op = '=') {
			return \Capsule\Db::table('articles')
            ->join('city_news', 'articles.id', '=', 'city_news.articles_id')
            ->where($field, $op, $value)
            ->where('deleted_at', null)
            ->where('lang', \App::$state->lang)
            ->where('city_news.published', 1)
            ->orderBy('sort', 'ASC');
		}

		protected function getPageSortNews($field, $value, $op = '=') {
			return \Capsule\Db::table('articles')
            ->join('articles_content', 'articles.id', '=', 'articles_content.articles_id')
            ->where($field, $op, $value)
            ->where('deleted_at', null)
            ->where('lang', \App::$state->lang)
            ->where('articles_content.published', 1)
            ->orderBy('date', 'DESC');
		}

		protected function getPageIn($field, $values, $op = '=') {
			return \Capsule\Db::table('articles')
            ->join('articles_content', 'articles.id', '=', 'articles_content.articles_id')
            ->whereIn($field, $values)
            //->where('articles.published', 1)
            ->where('deleted_at', null)
            ->where('lang', \App::$state->lang)
            ->where('articles_content.published', 1)
            ->orderBy('sort', 'ASC');
		}

		public function scopePeriod($query, $period = null) {
			$today = date('Y-m-d');
			$dateRanges = array (
				date('Y-m-d', strtotime($today. ' - 1 year')),
				date('Y-m-d', strtotime($today. ' - 1 month')),
				date('Y-m-d', strtotime($today. ' - 7 days')),
				date('Y-m-d', strtotime($today. ' - 1 days'))
			);

			if($period == null)
				$period = 1;

			return $query->where('created_at', '>=', $dateRanges[$period]);
		}

		public static function create() {
			$article = new Articles;
			foreach ($_POST as $key => $value)
				$article->$key = $value;

			$article->author = \App::session()->admin->id;
			$article->save();

			$tService = new \Admin\Service\Tree;
			$tService->make($article);

			return $article;
		}

		public static function save_data($id) {
			unset($_POST['id']);

			$article = static::find($id);
			if($article == null)
				return false;

			foreach ($_POST as $key => $value)
				$article->$key = $value;

			$article->author = \App::session()->admin->id;
			$article->save();

			return true;
		}

	}

?>