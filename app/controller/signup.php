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

        $checkmail = \Model\Users::where('email', $_POST['email'])->first();
        if(!empty($checkmail)){
            \Core\Response::navigate('/');
        }

        $defaultValue = Array(
            'firstname' => 'Имя*',
            'city' => 'Выберите город*:',
            'email' => 'E-mail:',
            'info' => 'О вас:',
            'lastname' => 'Фамилия',
            'password' => 'Пароль*:',
            'passconf' => 'Повторите пароль*:'
        );

        foreach($defaultValue as $key => $item){
            if($_POST[$key] == $item)
                $_POST[$key] = null;
        }

        $user = new \Model\Users;
        $user->email = strip_tags($_POST['email']);
        $user->password = md5($_POST['password']);
        $user->role = strip_tags($_POST['role']);
        $user->firstname = strip_tags($_POST['firstname']);
        $user->city = strip_tags($_POST['city']);
        $user->lastname = strip_tags($_POST['lastname']);
        $user->newsletter = strip_tags($_POST['newsletter']);
        $user->info = strip_tags($_POST['info']);

        $months = \App::config('pro_account');
        $user->date_end_pro = date('Y-m-d', strtotime('+' . $months . ' months'));

        if(!empty($_FILES['userfiles']['size'])){
            $upload = new \Service\Upload;
            $upload->uploadImage();
            $scales = \App::config('scales');
            $imageService = new \Service\Image;
            $imageService->resize($scales['avatar'], $upload->files);
            $user->avatar = $upload->files[0]['newname'];
        }
        $user->save();

        $this->addUser_idInProject($user->email, $user->id);
        $auth = \Model\Users::where('email', $_POST['email'])->first();
        \App::session('user', $auth);

        if($auth->role == 1)
            \Core\Response::navigate('/customer');
        else
            \Core\Response::navigate('/executor');

    }

    public function login_post()
    {
        $user = \Model\Users::where('email', $_POST['login'])->where('password', md5($_POST['pass']))->first();
        if($user != null){
            \App::session('user', $user);
        }
        \Core\Response::navigate($_SERVER["HTTP_REFERER"]);
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
            if(!empty($check)){
                echo json_encode(['stats' => 1]);
                exit;
            }
        }
        echo json_encode(['stats' => 0]);
        exit;
    }

    public function respassword_post()
    {
        $user = \Model\Users::where('email', $_POST['forgot-mail'])->first();
        if($user !== null){
            $password = rand(999999, 9999999);
            $user->password = md5($password);
            $user->save();

            include_once(MODPATH . '/PHPMailer/class.phpmailer.php');
            $mail = new \PHPMailer();

            $mail->CharSet = 'utf-8';
            $mail->From = 'svadnik.ru';
            $mail->FromName = 'Svadnik';
            $mail->addAddress($user->email);
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Востонавление пароля Svadnik';
            $mail->Body = <<< HTML
<p>Ваш новый пароль: {$password}</p>
HTML;
            $mail->send();
        }
        header("Location: {$_SERVER['HTTP_REFERER']}");
    }

    private function addUser_idInProject($email, $user_id)
    {
        $project = \Model\Projects::where('email', $email)->first();
        if($project != null){
            $project->user_id = $user_id;
            $project->save();
        }
    }


}

?>
