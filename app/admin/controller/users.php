<?php

namespace Admin\Controller;
	
	class Users extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function create() {
			$this->view->partial = 'options';
		}

		public function create_post() {
			if(!$this->service->validateOptions())
				\Core\Response::json(array (
					'valid' => false,
					'errors' => \App::validator()->errors
				));

			$admin = new \Model\Users;
			$admin->login = $_POST['login'];
			$admin->email = $_POST['email'];
			$admin->name = $_POST['name'];
			$admin->save();

			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно сохранено',
				'login' => $admin->name,
				'url' => '/admin/user/edit/id/' . $admin->id
			));
		}

		public function settings() {

		}

		public function settings_post() {
			if(!$this->service->validatePasswords())
				\Core\Response::json(array (
					'valid' => false,
					'errors' => \App::validator()->errors
				));

			$admin = \Model\Users::find($_POST['id']);
			$admin->password = md5($this->config->salt . '_' . $_POST['password']);
			$admin->save();

			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно сохранено'
			));
		}

/*		public function options() {
			$this->view->options = \Model\Users::find($_SESSION['admin']->id);
		}

		public function options_post() {
			if(!$this->service->validateOptions())
				\Core\Response::json(array (
					'valid' => false,
					'errors' => \App::validator()->errors
				));

			$admin = \Model\Users::find($_SESSION['admin']->id);
			$admin->login = $_POST['login'];
			$admin->email = $_POST['email'];
			$admin->name = $_POST['name'];
			$admin->save();

			$_SESSION['admin']->login = $admin->login;

			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно сохранено',
				'login' => $admin->name
			));
		}*/

		public function index() {
			$users = \Model\Users::all();

			$admins = array ();
			foreach ($users as $admin) {
				$admin->active = \Model\Articles::where('author', $admin->id)
					->orderBy('updated_at', 'DESC')
					->pluck('updated_at');

				$admins[] = $admin;
			}
			$this->view->admins = $admins;
		}

		public function edit() {
			$file = $this->route->change;
			if($file == '')
				$file = 'params';
			$this->view->file = $file;

			$this->view->currentUser = \Model\Users::find($this->route->id);

			$servicesUser = \Model\Service::where('userid', $this->route->id)->where('typeserv','service')->where('deleted', null)->get();
			foreach($servicesUser as $item)
				$tagids[] = $item->tagid;
			if(isset($tagids)){
				$NameServices = \Model\Articles::whereIn('id', $tagids)->get();
				$this->view->NameServices = $NameServices;
			}
			
			$ServicesCat = \Model\Articles::where('url', 'services-catalog')->first();
			$Services = \Model\Articles::where('parent_id', $ServicesCat->id)->get();
			$this->view->services = $Services;
			$this->view->servicesUser = $servicesUser;

			if($file == 'access') {
				$tService = new \Admin\Service\Tree;
				\App::view('tree', $tService->getTree());
			}
		}


		public function save_post() {
			$getService = \Model\Service::where('userid', $this->route->id)->where('typeserv','service')->get();

			foreach($_POST['services'] as $key => $item){
				$service = \Model\Service::where('userid', $this->route->id)->where('tagid', $item)->where('typeserv','service')->first();
				if(empty($service)){
					$serviceAdd = new \Model\Service;
					$serviceAdd->userid = $this->route->id;
					$serviceAdd->tagid = $item;
					$serviceAdd->typeserv = 'service';
					$serviceAdd->save();
				}
			}

			foreach($getService as $item){
				$ids[] = $item->userid;
			\Model\Service::whereIn('userid', $ids)->where('typeserv','service')->update(array('deleted'=>1));

			}
			foreach($_POST['services'] as $item){
				\Model\Service::where('tagid', $item)->where('typeserv','service')->update(array('deleted'=>null));
			}

			array_pop($_POST);
			
			$user = \Model\Users::find($this->route->id);
			foreach ($_POST as $key => $value)
				$user->$key = $value;
			$user->save();

			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно сохранено',
				'login' => $user->name
			));
		}

		public function root() {
			$item = $this->route->id;
			$admin = \Model\Users::find($item);
			$admin->role = $admin->role >= 1 ? 0 : 1;
			$admin->save();

			\Core\Response::json(array ('valid' => true));
		}

		public function remove() {
			foreach($_GET as $item)
				\Model\Service::where('userid', $item)->delete();
				
			\Model\Users::destroy(array_values($_GET));
			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно удалено'
			));
		}


		public function removeImage() {
			\Model\Users::where('id', $_GET['id'])->update(array('avatar'=>null));
			\Core\Response::json(array ('valid' => true));
		}
	}

?>