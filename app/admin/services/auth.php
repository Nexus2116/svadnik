<?php

namespace Admin\Service;
	
	class Auth {

		public function validate() {
			$rules = array (
				'login' => array ('required', array('Поле Логин обязательно для заполнения')),
				'password' => array ('required', array ('Поле Пароль обязательно для заполнения'))
			);

			if(!\App::validator()->validate($_POST, $rules)) {
				\Core\Response::json(array (
					'valid' => false,
					'errors' => \App::validator()->errors
				));
			}
		}

		public function checkAuth() {
			if(\App::route()->controller != 'Auth') {
				if(!isset($_SESSION['admin']) || empty($_SESSION['admin']))
					return false;

				return true;
			}
			return true;
		}
	}
?>