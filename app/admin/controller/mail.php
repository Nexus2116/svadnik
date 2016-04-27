<?php

namespace Admin\Controller;

class Mail extends \Core\Controller
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
        $mails = \Model\Mail::all();


        $this->view->mails = $mails;
    }

    public function edit()
    {
        $file = $this->route->change;
        if($file == '')
            $file = 'params';
        $this->view->file = $file;

        $this->view->currentMail = \Model\Mail::find($this->route->id);
    }


    public function save_post()
    {
        if(!isset($_POST['id']) || $_POST['id'] == -1){
            unset($_POST['id']);
            $article = new \Model\Mail();
            foreach($_POST as $key => $value)
                $article->{$key} = $value;
            $article->save();

        } else{

            $article = \Model\Mail::find($_POST['id']);
            if($article == null)
                return false;

            unset($_POST['id']);
            foreach($_POST as $key => $value)
                $article->$key = $value;
            $article->save();
        }
        \Core\Response::json(array(
            'valid' => true,
            'message' => 'Успешно сохранено'
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
        foreach($_GET as $id)
            \Model\Mail::where('id', $id)->delete();

        \Model\Mail::destroy(array_values($_GET));
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
}

?>