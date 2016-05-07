<?php

namespace Controller;

class Customer extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function before()
    {
        \App::view('hideServices', true);
    }

    public function index()
    {

        $user = \App::session('user');

        if($user == null || $user->role != 1)
            throw new \Exception\PageNotFound;

        $user_id = \App::session('user')->id;

        $user = \Model\Users::where('id', $user_id)->
        with(['userMessagesInfo'])->
        first();

        \App::view('user', $user);

    }

    public function user_profile()
    {
        $user = \Model\Users::where('id', $this->route->user_id)->where('role', 1)->first();
        if($user == null)
            throw new \Exception\PageNotFound;

        \App::view('user', $user);

    }

    public function change_profile_post()
    {
        try{
            $user_id = \App::session('user')->id;
            $model = \Model\Users::find($user_id);
            foreach($_POST as $key => $item){
                if($key == 'phone')
                    $item = \Html::parsePhone($item);
                $model->{$key} = strip_tags($item);
            }
            if($model->save()){
                \App::session('user', $model);
                \Core\Response::json(array(
                    'status' => true,
                    'message' => 'Успешно сохранено'
                ));
            }

            throw new \Exception();
        } catch (\Exception $e){
            \Core\Response::json(array(
                'status' => false,
                'message' => 'Произошла лшибка'
            ));
        }
    }

    public function change_pswd_post()
    {
        $user_id = \App::session('user')->id;
        $model = \Model\Users::find($user_id);
        $model->password = md5($_POST['password']);
        if($model->save()){
            \App::session('user', $model);
            \Core\Response::json(array(
                'status' => true,
                'message' => 'Успешно сохранено'
            ));
        }
        \Core\Response::json(array(
            'status' => false,
            'message' => 'Произошла ошибка'
        ));
    }


    public function avatar_upload()
    {
        $user_id = \App::session('user')->id;
        $user = \Model\Users::where('id', $user_id)->first();

        \App::view('user', $user);

        \Core\Response::json($user->avatar);
    }

    public function avatar_upload_post()
    {
        $user_id = \App::session('user')->id;

        $this->service->uploadImage();
        $scales = \App::config('scales');
        $imageService = new \Service\Image;
        $imageService->resize($scales['articles'], $this->service->files);

        $this->view->image = $this->service->files[0];
        $model = \Model\Users::find($user_id);
        $model->avatar = $this->view->image['newname'];
        $model->save();

        exit;
    }

    public function send_message_post()
    {
        try{
            $user_obj = \App::session('user');
            $user_id = $user_obj->id;
            $order = \Model\UserToOrder::where('customer_id', $user_id)->
            where('executor_id', $_POST['executor_id'])->
            where('status', 'yes')->
            count();
            if($order == 0)
                throw new \Exception();

            $message = new \Model\UserMessagesInfo;
            $message->user_id = $_POST['executor_id'];
            $message->description = 'Сообщение от  <a target="_blank" href="/customer/' . $user_id . '">' . $user_obj->firstname . '</a> ' . strip_tags($_POST['description']);
            $message->save();
            \Core\Response::json(array(
                'status' => true,
                'message' => 'Сообщение успешно отправлено'
            ));

        } catch (\Exception $e){
            \Core\Response::json(array(
                'status' => false,
                'message' => 'Возможно исполнитель откланил заказ'
            ));
        }
    }

}

?>
