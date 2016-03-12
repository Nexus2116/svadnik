<?php

namespace Admin\Controller;
	
	class Settings extends \Core\Controller {

		public $settings = array ();

		public function __construct() {
			parent::__construct();
		}

		public function before() {
			$this->view->activeTab = 'settings';

			$this->settings = array (
				'phone' => 'Телефон',
				'email' => 'Email для заявок',
				'fields' => 'Текст',
				'fb' => 'Facebook ссылка',
				'vk' => 'Vk ссылка',
				'ok' => 'Ok ссылка',
				'tw' => 'Twiter ссылка',
				'instagram' => 'Instagram ссылка',
			);
		}

		public function index() {
			$this->view->settings = $this->settings;
			$values = \Model\Settings::all();
			
			$data = array();
			foreach ($values as $val)
				$data[$val->key] = $val->value;
			$this->view->values = $data;
		}

		public function save_post() {
			foreach ($_POST as $key => $value) {
				\Model\Settings::where('key', $key)->delete();

				$option = new \Model\Settings;
				$option->key = $key;
				$option->value = $value;
				$option->save();
			}

			\Core\Response::json(array (
				'valid' => true,
				'message' => 'Успешно сохранено'
			));
		}
	}

?>