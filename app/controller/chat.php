<?php

namespace Controller;
	
	class Chat extends \Core\Controller {

		public function __construct() {
			parent::__construct();
			if(!isset(\App::session('user')->id))
				exit;
		}

		public function index() {
			$this->view->layout = 'index';
			$this->seo('Главная страница', 'Цитадель', 'Цитадель');
			\App::view('hideServices', true);

			exit;
		}

		public function index_post() {
			exit;
		}

		public function sendMessage_post() {
				if(isset($_POST['text'])){
					$message = new \Model\Message;
					$message->userid = \App::session('user')->id;
					$message->touserid = $_POST['id'];
					$message->text = $_POST['text'];
					$message->save();
				}
			exit;
		}

		public function messages_post() {
			$getMessage = \Model\Message::
			where('userid', \App::session('user')->id)
			->where('touserid', $_POST['id'])
			->orWhere('userid', $_POST['id'])
			->where('touserid', \App::session('user')->id)
			->get();
			$userids = Array(
				$getMessage[0]['userid'],
				$getMessage[0]['touserid'],
				);
			$users = \Model\Users::whereIn('id', $userids)->get();
			foreach($users as $item){
				$usersArr[$item->id] = Array(
						'id'=>htmlspecialchars($item->id),
						'firstname'=>htmlspecialchars($item->firstname),
						'lastname'=>htmlspecialchars($item->lastname),
						'avatar'=>htmlspecialchars($item->avatar)
					);
			}
			foreach($getMessage as $item){
				$arr[] = array(
					'id'=>htmlspecialchars($item['userid']),
					'text'=>htmlspecialchars($item['text']),
					'date'=>$item['created_at']
					);
			}
			if(isset($arr[0]['text'])){
				array_unshift($arr, $usersArr);
				echo json_encode($arr);
			}
			exit;
		}







	}

?>
