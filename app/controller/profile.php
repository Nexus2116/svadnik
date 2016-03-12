<?php

namespace Controller;
	
	class Profile extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function index() {
			$this->view->layout = 'index';
			$this->seo('Главная страница', 'Цитадель', 'Цитадель');
			\App::view('hideServices', true);

			$user = \Model\Users::where('id', $this->route->userId)->first();
			\App::view('user', $user);

			$servicesUserPhoto = \Model\Service::where('userid', $this->route->userId)->where('deleted', null)->where('typeserv', 'photos')->get();
			\App::view('servicesUserPhoto', $servicesUserPhoto);
			$servicesUserVideo = \Model\Service::where('userid', $this->route->userId)->where('deleted', null)->where('typeserv', 'video')->get();
			\App::view('servicesUserVideo', $servicesUserVideo);
			$servicesUserPresent = \Model\Service::where('userid', $this->route->userId)->where('deleted', null)->where('typeserv', 'present')->get();
			\App::view('servicesUserPresent', $servicesUserPresent);

			$article = \Model\Articles::where('url', 'services-catalog')->first();
			$articleServAll = \Model\Articles::where('parent_id', $article->id)->get();
			foreach($articleServAll as $item)
				$arr[$item['id']] = $item;
			\App::view('articleServAll', $arr);

			$servicesUser = \Model\Service::where('userid', $this->route->userId)->where('deleted', null)->where('typeserv', 'service')->get();
			\App::view('servicesUser', $servicesUser);



		}

	}

?>
