<?php

namespace Controller;

class Chat extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
        if(!isset(\App::session('user')->id))
            exit;
    }

    public function index()
    {
        $this->view->layout = 'index';
        $this->seo('Главная страница', 'Цитадель', 'Цитадель');
        \App::view('hideServices', true);

        exit;
    }

    public function get_messages()
    {
        $user_id = \App::session('user')->id;
        $user_obj = \Model\Users::find($user_id);
        $user_id = $user_obj->id;

        $column = $user_obj->role == 0 ? 'executor_id' : 'customer_id';
        $column2 = $user_obj->role == 1 ? 'executor_id' : 'customer_id';

        $messages = [];
        $user_interlocutor = [];

        if(\Bootstrap::checkUserPro()){
            $messages = \Model\Message::where($column, $user_id)->where($column2, $this->route->id)->orderBy('id', 'DESC')->get();
            $user_interlocutor = \Model\Users::find($this->route->id);
        }

        \App::view('this_user', $user_obj);
        \App::view('user_interlocutor', $user_interlocutor);
        \App::view('messages', $messages);
        $this->view->render('ajax_get_messages');

        exit;
    }

    public function send_message_post()
    {
        $user_obj = \App::session('user');
        $user_id = $user_obj->id;

        $column = $user_obj->role == 0 ? 'executor' : 'customer';
        $column2 = $user_obj->role == 1 ? 'executor' : 'customer';

        if(empty($_POST['text']))
            \Core\Response::json(array(
                'status' => false,
                'message' => 'Сообщение не должно быть пустым'
            ));

        try{
            $model = new \Model\Message;
            $model->sender_user_id = $user_id;
            $model->{$column . '_id'} = $user_id;
            $model->{$column2 . '_id'} = $_POST['user_id'];
            $model->text = strip_tags($_POST['text']);
            if($model->save()){
                \Core\Response::json(array(
                    'status' => true,
                    'message' => 'Сообщение успешно отправлено'
                ));
            }

            throw new \Exception();
        } catch (\Exception $e){
            \Core\Response::json(array(
                'status' => false,
                'message' => 'Не удалось отправить сообщение'
            ));
        }
    }

    public function close_chat_post()
    {
        $user_obj = \App::session('user');
        $user_id = $user_obj->id;

        $column = $user_obj->role == 0 ? 'executor_id' : 'customer_id';
        $column2 = $user_obj->role == 1 ? 'executor_id' : 'customer_id';

        \Model\Message::where($column, $user_id)->where($column2, $_POST['id'])->delete();

        exit;
    }


}

?>
