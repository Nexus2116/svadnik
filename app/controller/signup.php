<?php

namespace Controller;

class Signup extends \Core\Controller
{

   public function __construct()
   {
      parent::__construct();
   }

   public function reg()
   {

      echo json_encode(['stats' => 1]);
      exit;
   }


   public function reg_post()
   {
      $valid = Array(
            'firstname' => 'Имя*',
            'city' => 'Выберите город*:',
            'email' => 'E-mail:',
            'info' => 'О вас:',
            'lastname' => 'Фамилия',
            'password' => 'Пароль*:',
            'passconf' => 'Повторите пароль*:'
      );

      foreach($valid as $key => $item){
         if($_POST[$key] == $item)
            $_POST[$key] = null;
      }

      $user = new \Model\Users;
      $user->email = $_POST['email'];
      $user->password = md5($_POST['password']);
      $user->role = $_POST['role'];
      $user->firstname = $_POST['firstname'];
      $user->city = $_POST['city'];
      $user->lastname = $_POST['lastname'];
      $user->newsletter = $_POST['newsletter'];
      $user->info = $_POST['info'];

      if(!empty($_FILES['userfiles']['size'])){
         $upload = new \Service\Upload;
         $upload->uploadImage();
         $scales = \App::config('scales');
         $imageService = new \Service\Image;
         $imageService->resize($scales['avatar'], $upload->files);
         $user->avatar = $upload->files[0]['newname'];
      }
      $user->save();


      $auth = \Model\Users::where('email', $_POST['email'])->first();
      $obj = (object)array();
      $obj->id = $auth->id;
      $obj->role = $auth->role;
      $obj->firstname = $auth->firstname;
      \App::session('user', $obj);

      \Core\Response::navigate('/edit');
   }

   public function login_post()
   {

      if(preg_match('/@/', $_POST['login'])){
         $login = 'email';
      } else{
         $login = 'login';
      }
      $this->sess(\Model\Users::where($login, $_POST['login'])->where('password', md5($_POST['password']))->first());
      exit;
   }

   protected function sess($login)
   {
      if(!empty($login)){
         $obj = (object)array();
         $obj->id = $login->id;
         $obj->role = $login->role;
         $obj->firstname = $login->firstname;
         \App::session('user', $obj);
         echo json_encode(['stats' => 1]);
      } else
         echo json_encode(['stats' => 0]);
   }

   public function logout()
   {
      \App::session('user', null);
      \Core\Response::navigate('/');
   }

   public function ckeckEmail()
   {
      if(isset($_GET['regmail'])){
         $check = \Model\Users::where('email', $_GET['regmail'])->first();
         if(!isset($check->id))
            echo json_encode(['stats' => 1]);
         exit;
      }
      echo json_encode(['stats' => 0]);
      exit;
   }


}

?>
