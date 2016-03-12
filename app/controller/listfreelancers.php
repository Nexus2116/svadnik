<?php

namespace Controller;
	
	class ListFreelancers extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function index() {
			$this->view->layout = 'index';
			// $this->seo('Главная страница', 'Цитадель', 'Цитадель');
			\App::view('hideServices', true);

			$serviceItem = \Model\Articles::where('url', $this->route->service)->first();
			\App::view('serviceItem', $serviceItem);

			$idsArrProduct = explode(',', $_GET['productid']);

			$catalog = \Model\Articles::where('url', 'services-catalog')->first();
			$catalog_parent = \Model\Articles::where('parent_id', $catalog->id)->get();
			\App::view('catalog', $catalog_parent);

			$serviceUser = \Model\Service::whereIn('tagid', $idsArrProduct)->where('deleted', null)->get();
			\App::view('serviceUser', $serviceUser);

			$article = \Model\Articles::where('url', 'services-catalog')->first();
			$articleServAll = \Model\Articles::where('parent_id', $article->id)->whereIn('id', $idsArrProduct)->get();
			foreach($articleServAll as $item){
				$art[$item['id']] = $item;
			}

			foreach($serviceUser as $item){
				$ids[] = $item->userid;
				$serviceUserData[$item->userid] = \Model\Service::where('userid', $item->userid)->where('deleted', null)->get();
			}

			$count = 0;
			$rating = [];
			$rating_count = [];
			$user = [];
			$review = [];
			if(!empty($ids)){
				$user = \Model\Users::whereIn('id', $ids)->get();
				$count = count($user);

				foreach($ids as $item){
					$rating[$item] = \Model\Review::where('userid', $item)->where('rating', '!=', 0)->get();
					$rating_count[$item] = \Model\Review::where('userid', $item)->where('rating', '!=', 0)->count();
					$review[$item] = \Model\Review::where('userid', $item)->count();
				}
			}
			\App::view('user', $user);
			\App::view('serviceUserData', $serviceUserData);
			\App::view('articleServAll', $art);
			\App::view('count', $count);
			\App::view('rating', $rating);
			\App::view('rating_count', $rating_count);
			\App::view('review', $review);

			$calendar = \Model\Reserve_day::whereIn('userid', $ids)->get();
			foreach($calendar as $item)
				$reserve[$item->userid][] = $item->date;

			if(!empty($reserve)){
				\App::view('reserve', $reserve);
			}

		}

		public function reviews(){

			$review = \Model\Review::where('userid', $_GET['id'])->orderBy('id', 'DESC')->get();
			\App::view('review', $review);
			\App::view('userid', $_GET['id']);
			\App::view('url', $_GET['url']);
			
			if(isset(\App::session('user')->id)){
				$user = \Model\Users::where('id', \App::session('user')->id)->first();
				\App::view('user', $user);
			}

			$this->view->render('reviews');
			exit;
		}

		public function reviewsAdd_post(){
				$valid = array('id'=>'', 'user_name'=>'Имя:', 'user_email'=>'E-mail:', 'content'=>'Текст комментария:', 'comment-rating'=>-1);
				foreach($_POST as $key => $item)
					if($_POST[$key] == $valid[$key])
						$arr[$key] = 'error';
					if(!empty($arr))
						echo json_encode($arr);

				if(empty($arr)){
						$review  = new \Model\Review();
						$review->userid = $_POST['id'];
						$review->name = $_POST['user_name'];
						$review->email = $_POST['user_email'];
						$review->text = $_POST['content'];
						$review->rating = $_POST['comment-rating'];
						$review->save();
				}
			\Core\Response::navigate($_POST['url']);
		}





	}

?>
