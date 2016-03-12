<?php

	class Bootstrap {

		public function __construct () {
			$auth = new \Admin\Service\Auth;
			if(!$auth->checkAuth())
				throw new \Admin\Exception\Auth();
		}

		public function before() {
			\App::view('admin', @$_SESSION['admin']);
			$this->setAssets();
			\App::view('activeTab', 'statictics');
		}

		public function after() {
			if(\App::route('ajax'))
				\Core\Response::render(true);

			$tService = new \Admin\Service\Tree;
			\App::view('tree', $tService->getTree());

			\App::view('admins', \Model\Admin::all());
		}

		protected function setAssets() {
			\App::view()->css = array (
				'style' => 'admin/style',
				'redactor' => 'admin/redactor',
				'zebra' => 'admin/zebra_datepicker',
				'select2.min' => 'admin/select2.min'
			);

			\App::view()->js = array (
				'jquery' => 'admin/jquery.min',
				'jquery.ui' => 'admin/jquery.ui.min',
				'form' => 'form',
				'admin' => 'admin/admin',
				'page' => 'admin/page',
				'redactor' => 'admin/redactor',
				'functions' => 'admin/functions',
				'ru' => 'admin/ru',
				'zebra' => 'admin/zebra_datepicker.src',
				'knob' => 'admin/jquery.knob',
				'dropzone' => 'admin/dropzone',
				'select2.min' => 'admin/select2.min'
			);
		}
	}
?>
