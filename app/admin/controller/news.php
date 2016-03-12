<?php

namespace Admin\Controller;
	
	class News extends Articles {
		
		public function index() {

			$order = $this->route->order;
			$asc = $this->route->asc;

			$asc = $asc == -1 ? 'desc' : 'asc';

			if($order == null) {
				$order = 'date';
				// $order = 'created_at';
				$asc = 'desc';
			}

			$news = \Model\Articles::where('url', 'news')->first();
			$this->view->articles = \Model\Articles::where('parent_id', $news->id)->undel()
				// ->period($this->route->period)
				// ->orderBy($order, $asc)
				->orderBy($order, 'asc')
				->get();

			foreach($this->view->articles as $item)
				$ids[] = $item->id;
			if(!empty($ids))
			$content = \Model\Content::whereIn('articles_id',$ids)->get();

			foreach($content as $item){
				$result[$item->articles_id] = $item;
				$result[$item->articles_id]->id = $item->articles_id;
				}
			\App::view('content', $result);



			$this->view->period = $this->route->period;
			if($this->view->period == null)
				$this->view->period = 1;

			$users = \Model\Admin::all();
			$data = array ();
			foreach ($users as $user)
				$data[$user->id] = $user;
			$this->view->users = $data;
		}




	}

?>