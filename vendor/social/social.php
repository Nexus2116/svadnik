<?php
	
	class Module_Social_Social {

		protected $authUrl; // auth url
		protected $tokenUrl; // get token by url with code
		protected $API; // url to social API methods
		protected $client_id; // application id
		protected $client_secret; // application secret code
		protected $redirect_uri; // redirect to after auth
		protected $response_type = 'code';
		protected $token; // token response object

		public $userData = array(); // user's data form social
		public $code;

		// setting params
		public function __construct($params = array()) {
			foreach ($params as $k => $v)
				$this->$k = $v;

			$this->checkCode();
		}

		// if array assoc or not
		protected function is_assoc(array $array) {
			$keys = array_keys($array);
			return array_keys($keys) !== $keys;
		}

		public function checkCode() {
			if(!isset($_GET['code']))
				$this->sendData();
			else
				$this->getToken();
		}

		// build URI for request
		protected function buildRequest($params = null) {
			$data = array();

			if($params != null) {
				$flag = false;
				if($this->is_assoc($params))
					$flag = true;
				foreach ($params as $k => $v) {
					if($flag == false) {
						if(!isset($this->$v) || empty($this->$v))
							continue;
						$data[] = "$v=" . $this->$v;
					}
					else
						$data[] = "$k=" . $v;
				}
			}

			if(!empty($this->token))
				$data[] = 'access_token=' . $this->token->access_token;

			return join('&', $data);
		}

		// send auth info
		protected function sendData() {
			$keys = array ('client_id', 'redirect_uri', 'response_type', 'scope');
			$params = $this->buildRequest($keys);
			Core_Util::navigate($this->authUrl .'?'. $params);
		}

		// get token from auth response
		protected function getToken() {
			$this->code = $_GET['code'];
			$keys = array ('client_id', 'client_secret', 'redirect_uri', 'code');
			$params = $this->buildRequest($keys);

			$data = file_get_contents($this->tokenUrl .'?'. $params);
			$this->getData($data);
		}

		
	}

?>