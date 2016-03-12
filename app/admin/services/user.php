<?php

namespace Admin\Service;
	
	class User {

		public function validatePasswords() {
			$rules = array (
				'password' => array ('required', array('Поле "Пароль" обязательно для заполнения')),
				'passrepeat' => array ('required', array ('Поле "Повторите пароль" обязательно для заполнения'))
			);

			if(!\App::validator()->validate($_POST, $rules))
				return false;

			if($_POST['password'] != $_POST['passrepeat']) {
				\App::validator('errors', array ('passrepeat', 'Пароли не совпадают'));
				return false;
			}

			return true;
		}

		public function validateOptions() {
			$rules = array (
				'login' => array ('required', array('Поле "Логин" обязательно для заполнения')),
				'name' => array ('required', array('Поле "Имя" обязательно для заполнения')),
				'email' => array ('type:email', array ('Поле "email" заполнено некорректно'))
			);

			if(!\App::validator()->validate($_POST, $rules))
				return false;
			return true;
		}
	}
?>