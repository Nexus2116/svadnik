<?php
	
	class Module_Social_FB extends Module_Social_Social {

		public $scope = 'user_birthday';

		public function __construct($params) {
			parent::__construct($params);
		}

		public function getData($responseData) {
			$this->token = new StdClass();

			$temp = explode('&', $responseData);
			foreach ($temp as $item) {
				$data = explode('=', $item);
				$key = $data[0];
				$value = $data[1];
				$this->token->$key = $value;
			}

			$keys = array(
				'fields' => 'birthday,hometown,first_name,last_name,gender',
				'locale' => 'ru_RU'
			);

			$this->userData = $this->request($keys);

			$this->userData->avatar = 'http://graph.facebook.com/'.$this->userData->id.'/picture?type=large';

			$temp = explode('/', $this->userData->birthday);
			$date = @$temp[2] . '-' . @$temp[0] . '-' . @$temp[1];

			$this->userData->birthday = $date;
			$this->userData->hometown = $this->userData->hometown->name;

			if($this->userData->gender == 'мужской')
				$this->userData->gender = 2;
			else
				$this->userData->gender = 1;

			$replace = array (
				'id' => 'fb',
				'first_name' => 'name',
				'last_name' => 'sername',
				'birthday' => 'date',
				'hometown' => 'city',
				'gender' => 'sex'
			);

			$user = array ();
			foreach ($this->userData as $k => $v) {
				if(isset($replace[$k]))
					$user[$replace[$k]] = $v;
				else 
					$user[$k] = $v;
			}
			$this->userData = $user;
		}

		// send request
		private function request($keys) {
			$url = $this->API . '?' . $this->buildRequest($keys);
			return json_decode(file_get_contents($url));
		}

	}
?>