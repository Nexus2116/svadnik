<?php

namespace Controller;
	
	class Calc extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function index() {
			$this->view->layout = 'index';
			$this->seo('Главная страница', 'Цитадель', 'Цитадель');

			if($_GET['hour'])
				$typePriceService = 'price';
			else
				$typePriceService = 'projprice';



			echo \Model\Service::
				where('tagid', $_GET['id'])
				->where($typePriceService, '>', $_GET['price_start'])
				->where($typePriceService, '<', $_GET['price_end'])
				->where('deleted', null)
				->count();
			exit;
		}

		public function index_post() {
			if(isset($_POST['id'])){
				if($_POST['hour'])
					$typePriceService = 'price';
				else
					$typePriceService = 'projprice';

				$catalog = \Model\Articles::where('url', 'services-catalog')->first();
				$catalog_parent = \Model\Articles::where('parent_id', $catalog->id)->get();
				\App::view('catalog', $catalog_parent);

				$serviceItem = \Model\Articles::where('id', $_POST['id'])->first();
				\App::view('serviceItem', $serviceItem);

				$serviceUser = \Model\Service::where('tagid', $_POST['id'])-> where($typePriceService, '>', $_POST['price_start'])-> where($typePriceService, '<', $_POST['price_end'])-> where('deleted', null)->get();
				\App::view('serviceUser', $serviceUser);
				\App::view('serviceId', $_POST['id']);
					
				$article = \Model\Articles::where('url', 'services-catalog')->first();
				$articleServAll = \Model\Articles::where('parent_id', $article->id)->get();

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
				\App::view('rating', $rating);
				\App::view('rating_count', $rating_count);
				\App::view('review', $review);
				\App::view('user', $user);
				\App::view('serviceUserData', $serviceUserData);
				\App::view('articleServAll', $art);
				\App::view('count',  $count);

				if(!empty($ids)){
					$calendar = \Model\Reserve_day::whereIn('userid', $ids)->get();
				
					foreach($calendar as $item)
						$reserve[$item->userid][] = $item->date;
					if(!empty($reserve)){
						\App::view('reserve', $reserve);
					}
				}

				$this->view->render('index');
				exit;
			}





	}
}

?>
