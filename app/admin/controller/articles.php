<?php

namespace Admin\Controller;
	
	class Articles extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function before() {
			$this->view->activeTab = 'file';
		}

		public function index() {



			$order = $this->route->order;
			$asc = $this->route->asc;

			$asc = $asc == -1 ? 'desc' : 'asc';

			if($order == null) {
				$order = 'created_at';
				$asc = 'desc';
			}

			$this->view->articles = \Model\Articles::undel()
				// ->period($this->route->period)
				->orderBy($order, $asc)
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

		public function options() {
			if($this->route->id) {
				$this->view->article = \Model\Articles::find($this->route->id);

				$className = 'Model\Content';
				if($this->view->article->type != 'articles')
					$className = "Model\\" . $this->view->article->type;

				if($this->view->article->type != 'options') {
					$contentModel = new $className;
					$content = $contentModel->where('articles_id', $this->route->id)->get();

					$data = array();
					foreach ($content as $item) {
						$item->id = $item->articles_id;
						$data[$item->lang] = $item;
					}
					$this->view->content = $data;
				}
				$this->view->parent = $this->view->article->parent_id;
			}

			if($this->route->parent_id)
				$this->view->parent = $this->route->parent_id;
		}

		public function save_post() {
			if(!$this->service->validatePost())
				\Core\Response::json(array (
					'valid' => false,
					'errors' => \App::validator()->errors
				));

			if(!isset($_POST['id'])) {
				$article = \Model\Articles::create();
				$gallery = new \Model\Gallery;
				$gallery->save();
				$article->gallery = $gallery->id;
				$article->date = date('Y-m-d');
				$article->save();

				foreach ($this->config->langs as $key => $value) {
					$content = new \Model\Content;
					$content->articles_id = $article->id;
					$content->lang = $key;
					$content->save();
				}

				$tService = new \Admin\Service\Tree;
				\App::view('tree', $tService->getTree());

				\Core\Response::json(array (
					'valid' => true,
					'content' => $this->view->getContent('/articles/sidebar'),
					'page' => $article->toJson()
				));
			} else {
				if(!\Model\Articles::save_data($_POST['id']))
					\Core\Response::json(array (
						'valid' => false,
						'error' => 'Запись не найдена'
					));
				else 
					\Core\Response::json(array (
						'valid' => true,
						'message' => 'Успешно сохранено'
					));
			}
		}

		public function edit() {
			$article = \Model\Articles::find($this->route->id);
			if($article == null)
				throw new \Admin\Exception\PageNotFound;
			$this->view->article = $article;

			if($this->route->gallery)
				return $this->gallery();

			$this->view->file = 'content';
			$this->view->tabLanguage = 'ru';
			if($this->route->lng)
				$this->view->tabLanguage = $this->route->lng;

			$type = 'Content';
			if($article->type != 'articles')
				$type = $article->type;

			$this->view->content = $article->content($type)->where('lang', $this->view->tabLanguage)->first();
		}

		public function gallery() {
			$this->view->gallery = $this->view->article->gallery($this->view->article->gallery);
			$this->view->showGallery = true;
			$this->view->file = 'gallery';
		}

		public function edit_post() {
			unset($_POST['id']);

			$article = \Model\Articles::find($this->route->id);
			
			$className = 'Model\Content';
				if($article->type != 'articles')
					$className = "Model\\" . $article->type;

			$content = new $className;

			$content->saveContent($this->route->id, $_POST['lang'], $className);
			\Core\Response::json(array ('valid' => true));
		}

		public function delete() {
			$article = \Model\Articles::find($this->route->id);
			if($article->type == 'options')
				\Model\Options::where('article', $this->route->id)->delete();

			$treeService = new \Admin\Service\Tree;
			$treeService->delete($this->route->id);

			\Core\Response::json(array ('valid' => true));
		}

		public function publish($id) {
			$item = $this->route->id;
			$article = \Model\Articles::find($item);
			$article->published = $article->published >= 1 ? 0 : 1;
			$article->save();

			\Core\Response::json(array ('valid' => true));
		}

		public function publishall_post() {
			\Model\Content::whereIn('articles_id', $_POST)->update(array ('published' => $this->route->type));

			\Core\Response::json(array ('valid' => true));
		}

		public function contentpublish() {
			$item = $this->route->id;

			$article = \Model\Articles::find($item);
			$className = 'Model\Content';
			if($article->type != 'articles')
				$className = "Model\\" . $article->type;

			$content = $className::where('articles_id', $item)/*->where('lang', $this->route->lang)*/->first();
			$published = $content->published >= 1 ? 0 : 1;

			$className::where('articles_id', $item)/*->where('lang', $this->route->lang)*/->update(array ('published' => $published));

			$tree = new \Admin\Service\Tree;
			$subpages = $tree->subNodes($article->path);

			$ids = array ();
			foreach ($subpages as $page)
				$ids[] = $page->id;

			$className::whereIn('articles_id', $ids)/*->where('lang', $this->route->lang)*/->update(array ('published' => $published));

			\Core\Response::json(array ('valid' => true));
		}


		public function removeImage() {
			$field = $_GET['field'];
			$article = \Model\Articles::find($_GET['id']);
			$article->$field = '';
			$article->save();
			\Core\Response::json(array ('valid' => true));	
		}


		public function copy() {
			$id = $_GET['id'];
			$tService = new \Admin\Service\Tree;

			// копируем статью
			$item = \Model\Articles::find($id);
			$article = new \Model\Articles;
			foreach ($item['attributes'] as $key => $value)
				if($key != 'id')
					$article->$key = $value;
			$article->url = $article->url . '_copy';
			$article->name = $article->name . '_копия';
			$article->save();

			$tService->make($article);

			// копируем контент статьи
			$className = 'Model\Content';
			if($item->type != 'articles')
				$className = "Model\\" . $item->type;

			$content = $className::where('articles_id', $item->id)->where('lang', 'ru')->first();

			$newContent = new $className;
			foreach ($content['attributes'] as $field => $val)
				if($field != 'articles_id')
					$newContent->$field = $val;

			$newContent->caption = $newContent->caption . '_копия';
			$newContent->articles_id = $article->id;
			$newContent->save();

			// копируем вложенные статьи
			$options = \Model\Articles::where('parent_id', $item->id)->get();
			foreach ($options as $option) {
				$saveItem = new \Model\Articles;

				foreach ($option['attributes'] as $key => $value)
					if($key != 'id')
						$saveItem->$key = $value;

				$saveItem->parent_id = $article->id;
				$saveItem->url = $option->url . '_copy';
				$saveItem->name = $option->name . '_копия';
				$saveItem->save();
				$tService->make($saveItem);
				$id = $saveItem->id;

				// копируем контент вложенных статей
				$contentItem = \Model\Content::where('articles_id', $option->id)->where('lang', 'ru')->first();
				$newContentItem = new \Model\Content;
				foreach ($contentItem['attributes'] as $asd => $qwe)
					$newContentItem->$asd = $qwe;
				$newContentItem->articles_id = $saveItem->id;
				$newContentItem->save();

				// копируем свойства этажей
				$optionToCopy = \Model\Options::find($option->id);
				$saveItem = new \Model\Options;

				foreach ($optionToCopy['attributes'] as $key => $value)
					$saveItem->$key = $value;

				$saveItem->id = $id;
				$saveItem->article = $article->id;
				$saveItem->save();
			}


			\App::view('tree', $tService->getTree());
			\Core\Response::json(array (
				'valid' => true,
				'content' => $this->view->getContent('/articles/sidebar'),
				'page' => $article->toJson()
			));
		}

		public function search(){
			$q = $_GET['value'];
			$content = \Model\Content::where('title', 'LIKE', '%'.$q.'%')->
			orwhere('caption', 'LIKE', '%'.$q.'%')->
			orwhere('annotation', 'LIKE', '%'.$q.'%')->
			orwhere('text', 'LIKE', '%'.$q.'%')->
			orwhere('name', 'LIKE', '%'.$q.'%')->
			get();

			foreach($content as $item){
				if(!empty($item->articles_id))
					$ids[] = $item->articles_id;
			}
			if(!empty($ids)){
				$articles = \Model\Articles::whereIn('id', $ids)->get();

				foreach($content as $item){
					$result[$item->articles_id] = $item;
					$result[$item->articles_id]->id = $item->articles_id;
				}

				$users = \Model\Admin::all();
				$data = array ();
				foreach ($users as $user)
					$data[$user->id] = $user;

				\App::view('users', $data);
				\App::view('articles', $articles);
				\App::view('content', $result);
			}
			\Core\Response::render(true);
			exit;
		}
		
		
	}

?>