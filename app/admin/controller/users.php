<?php

namespace Admin\Controller;

class Users extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function create()
    {
        $this->view->partial = 'options';
    }

    public function create_post()
    {
        if(!$this->service->validateOptions())
            \Core\Response::json(array(
                'valid' => false,
                'errors' => \App::validator()->errors
            ));

        $admin = new \Model\Users;
        $admin->login = $_POST['login'];
        $admin->email = $_POST['email'];
        $admin->name = $_POST['name'];
        $admin->save();

        \Core\Response::json(array(
            'valid' => true,
            'message' => 'Успешно сохранено',
            'login' => $admin->name,
            'url' => '/admin/user/edit/id/' . $admin->id
        ));
    }

    public function settings()
    {

    }

    public function settings_post()
    {
        if(!$this->service->validatePasswords())
            \Core\Response::json(array(
                'valid' => false,
                'errors' => \App::validator()->errors
            ));

        $admin = \Model\Users::find($_POST['id']);
        $admin->password = md5($this->config->salt . '_' . $_POST['password']);
        $admin->save();

        \Core\Response::json(array(
            'valid' => true,
            'message' => 'Успешно сохранено'
        ));
    }


    public function index()
    {

        if(empty($_GET)){
            $_GET['executor'] = true;
            $_GET['customer'] = true;
        }

        $usersModel = \Model\Users::orderBy('id', 'ASC');

        if($_GET['executor'] == 'true'){
            $usersModel->where('role', 0);
        }

        $this->view->params = $_GET;

//        if($_GET['customer'] == 'true'){
//            $usersModel->where('role', 1);
//        }

        $this->view->users = $usersModel->get();
        $this->view->mails = \Model\Mail::all();
    }

    public function edit()
    {
        $file = $this->route->change;
        if($file == '')
            $file = 'params';
        \App::view('file', $file);

        $this->view->currentUser = \Model\Users::where('id', $this->route->id)->
        with(['userService', 'userPhotos', 'userPresentations', 'userVideo'])->
        first();

        $catalogService = \Model\Articles::where('url', 'services-catalog')->first();
        $services = \Model\Articles::where('parent_id', $catalogService->id)->where('deleted_at', null)->get();
        \App::view('services', $services);
    }


    public function save_post()
    {
        $model = \Model\Users::where('id', $this->route->id)->first();
        foreach($_POST as $key => $item){
            $model->{$key} = $item;
        }
        $model->save();

        \Core\Response::json(array(
            'valid' => true,
            'message' => 'Успешно сохранено',
        ));
    }

    public function root()
    {
        $item = $this->route->id;
        $admin = \Model\Users::find($item);
        $admin->role = $admin->role >= 1 ? 0 : 1;
        $admin->save();

        \Core\Response::json(array('valid' => true));
    }

    public function remove()
    {
        foreach($_GET as $item)
            \Model\Service::where('userid', $item)->delete();

        \Model\Users::destroy(array_values($_GET));
        \Core\Response::json(array(
            'valid' => true,
            'message' => 'Успешно удалено'
        ));
    }


    public function removeImage()
    {
        \Model\Users::where('id', $_GET['id'])->update(array('avatar' => null));
        \Core\Response::json(array('valid' => true));
    }

    public function send_mail()
    {
        exit;
    }

    public function send_mail_post()
    {

        include_once(MODPATH . '/phpmailer/class.phpmailer.php');

        $maildata = \Model\Mail::where('id', $_POST['mail_id'])->first();
        $users = \Model\Users::whereIn('id', $_POST['user_ids'])->get();

        if(!empty($users))
            foreach($users as $user){

                $mail = new \PHPMailer();
                $mail->CharSet = 'utf-8';
                $mail->From = 'svadnik.ru';
                $mail->FromName = 'Svadnik';
                $mail->addAddress($user->email);
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $maildata->title;
                $mail->Body = $maildata->text;
                $mail->send();

            }
        \Core\Response::json(array(
            'valid' => true,
            'message' => 'Успешно отправлено'
        ));

    }
}

?>