<?php

namespace Admin\Controller;
	
	class Auth extends \Core\Controller {

		public function __construct() {
			parent::__construct();
		}

		public function login() {
			$this->view->layout = 'login';
			$this->view->css['login'] = 'admin/login';

			$this->view->js['form'] = 'form';
		}

		public function auth_post() {
			$this->service->validate();

			$login = $_POST['login'];
			$pass = md5($this->config->salt . '_' . $_POST['password']);
			$admin = \Model\Admin::where('login', $login)->where('password', $pass)->first();

			if($admin == null)
				\Core\Response::json(array (
						'valid' => false,
						'error' => 'Такого пользователя не существует'
					)
				);

			$_SESSION['admin'] = $admin;
			\App::view('admin', $admin);
			\Core\Response::json(array('url' => \HTML::url('')));
		}

		public function logout() {
			unset($_SESSION['admin']);
			session_destroy();
			\Core\Response::navigate(\HTML::url('/login'));
		}
	}
?>