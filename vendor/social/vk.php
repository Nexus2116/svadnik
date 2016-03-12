<?php
	
	class Module_Social_VK extends Module_Social_Social {

		public function __construct($params) {
			parent::__construct($params);
		}

		public function getData($responseData) {
			$this->token = json_decode($responseData);
			$this->userData['vk'] = $this->token->user_id;

			$params = array(
				'user_ids' => $this->userData['vk'],
				'fields' => 'sex,bdate,city,country,photo_200'
			);

			$this->userData = $this->request('users.get', $params);

			$this->userData->country = $this->request('database.getCountriesById', array('country_ids' => $this->userData->country))->name;
			$this->userData->city = $this->request('database.getCitiesById', array('city_ids' => $this->userData->city))->name;
			$date = $this->userData->bdate;

			$temp = explode('.', $date);
			$this->userData->bdate = join('-', array_reverse($temp));

			$replace = array(
				'uid' => 'vk',
				'first_name' => 'name',
				'last_name' => 'sername',
				'bdate' => 'date',
				'photo_200' => 'avatar'
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
		private function request($method, $params, $all = false) {
			$url = $this->API . $method . '?' . $this->buildRequest($params);

			$result = json_decode(file_get_contents($url));
			$data = $result->response;

			if($all == false)
				return $data[0];
			return $data;
		}

	}

?>