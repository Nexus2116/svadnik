<?php

	include('twitteroauth.php');

	class Module_Social_TW {

		public $userData = array();

		public function __construct($config) {
			$ck = $config['consumer_key'];
			$cs = $config['consumer_secret'];
			$ot = $config['oauth_token'];
			$ots = $config['oauth_token_secret'];
			$callback = $config['callback'];

			if(!isset($_SESSION['oauth_token']) && !isset($_SESSION['oauth_token_secret'])) {

	  			$connection = new TwitterOAuth($ck, $cs);

	  			$temporary_credentials = $connection->getRequestToken($callback);
	  			$_SESSION['oauth_token'] = $temporary_credentials['oauth_token'];
	  			$_SESSION['oauth_token_secret'] = $temporary_credentials['oauth_token_secret'];

	  			$redirect_url = $connection->getAuthorizeURL($temporary_credentials);
	  			Core_Util::navigate($redirect_url);
	  		}
	  		else {
	  			$oauth_token = $_SESSION['oauth_token'];
	  			$oauth_token_secret = $_SESSION['oauth_token_secret'];
	  			unset($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

	  			$connection = new TwitterOAuth($ck, $cs, $oauth_token, $oauth_token_secret);
				$token_credentials = $connection->getAccessToken($_REQUEST['oauth_verifier']);

				$connection = new TwitterOAuth($ck, $cs, $token_credentials['oauth_token'], $token_credentials['oauth_token_secret']);

				$account = $connection->get('account/verify_credentials');
				$account->profile_image_url = str_replace('normal', 'bigger', $account->profile_image_url);

				$replace = array(
					'id' => 'tw',
					'name' => 'name',
					'location' => 'city',
					'profile_image_url' => 'avatar'
				);

				foreach ($replace as $k => $v)
					$this->userData[$v] = $account->$k;
	  		}

		}
	}

?>