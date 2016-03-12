<?php

namespace Admin\Controller;
	
	class User extends \Core\Controller {

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

			$admin = new \Model\Admin;
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

			$admin = \Model\Admin::find($_POST['id']);
			$admin->password = md5($this->config->salt . '_' . $_POST['password']);
			$admin->save();

			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно сохранено'
			));
		}

		public function options() {
			$this->view->options = \Model\Admin::find($_SESSION['admin']->id);
		}

		public function options_post() {
			if(!$this->service->validateOptions())
				\Core\Response::json(array (
					'valid' => false,
					'errors' => \App::validator()->errors
				));

			$admin = \Model\Admin::find($_SESSION['admin']->id);
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
		}

		public function index() {
			$users = \Model\Admin::all();

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

			$this->view->currentUser = \Model\Admin::find($this->route->id);

			if($file == 'access') {
				$tService = new \Admin\Service\Tree;
				\App::view('tree', $tService->getTree());
			}
		}


		public function save_post() {
			$user = \Model\Admin::find($this->route->id);
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
			$admin = \Model\Admin::find($item);
			$admin->role = $admin->role >= 1 ? 0 : 1;
			$admin->save();

			\Core\Response::json(array ('valid' => true));
		}

		public function remove() {
			\Model\Admin::destroy(array_values($_GET));
			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно удалено'
			));
		}
	}

?>