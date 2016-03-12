<?php

namespace Admin\Service;
	
	class Articles {

		public function validatePost() {
			$rules = array (
				'name' => array ('required', array('Поле "Название" обязательно для заполнения')),
				'url' => array ('required', array ('Поле "URL" обязательно для заполнения'))
			);

			if(!\App::validator()->validate($_POST, $rules))
				return false;

			return true;
		}
	}
?>