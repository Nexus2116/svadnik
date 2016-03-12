<?php

namespace Service;
	
	class Customer {

		public function index() {

			$user = \Model\Users::where('id', \App::session('user')->id)->first();
			\App::view('user', $user);
				
		}

		public function index_post() {
				print_r($_POST);
			if($_POST['mode'] == 1){
					\Model\Users::where('id' , \App::session('user')->id)->update(array('phone'=>$_POST['user-phone'], 'email'=>$_POST['user-mail'] ));
			}elseif($_POST['mode'] == 3){
				if($_POST['new-pass'] == $_POST['repeat-pass'])
					\Model\Users::where('id' , \App::session('user')->id)->update(array('password'=>md5($_POST['repeat-pass'])));
			}elseif($_POST['mode'] == 2){
					\Model\Users::where('id' , \App::session('user')->id)->update(array('firstname'=>$_POST['new-name']));
					$update = array('firstname' => $_POST['new-name']);
					$user = (object) array();
					$user->id = \App::session('user')->id;
					$user->firstname = $_POST['new-name'];
					$user->role = 1;
					\App::session('user', $user);
			}






			

			\Core\Response::navigate('/edit');
		}

		

	}
?>