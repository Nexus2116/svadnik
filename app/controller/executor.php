<?php

namespace Controller;

class Executor extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    private $message = [
        0 => 'Успешно сохранено',
        1 => 'Успешно удалено',
    ];

    public function index()
    {
        if(\App::session('user')->role != 0){
            throw new \Exception\PageNotFound;
        }

        //  hide services list and news
        \App::view('hideServices', true);

        $user_id = \App::session('user')->id;
        $userCheck = \Model\Users::find($user_id);
        if($userCheck == null)
            throw new \Exception\PageNotFound;


        $user = \Model\Users::where('id', $user_id)->
        with(['userService',
            'userPhotos',
            'userPresentations',
            'userVideo',
            'userSubscribeProject',
            'userMessagesInfo',
            'userToOrder',
            'userCalendarReserve'
        ])->
        first();

        $userSubscribeProject_ids = [];
        foreach($user->userSubscribeProject as $item)
            array_unshift($userSubscribeProject_ids, $item->project_id);

        $projects = [];
        if(!empty($userSubscribeProject_ids))
            $projects = \Model\Projects::whereIn('id', $userSubscribeProject_ids)->get();

        $catalogService = \Model\Articles::where('url', 'services-catalog')->first();
        $services = \Model\Articles::where('parent_id', $catalogService->id)->where('deleted_at', null)->get();
        $services_key_id = [];
        foreach($services as $item){
            $services_key_id[$item->id] = $item;
        }

        $customer_ids = [];
        foreach($user->userToOrder as $item){
            array_unshift($customer_ids, $item->customer_id);
        }
        $users = [];
        if(!empty($customer_ids)){
            $get_users = \Model\Users::select('id', 'firstname', 'lastname')->whereIn('id', $customer_ids)->get();
            foreach($get_users as $item){
                $users[$item->id] = $item;
            }
        }

        \App::view('projects', $projects);
        \App::view('user', $user);
        \App::view('users', $users);
        \App::view('services', $services_key_id);

    }

    public function user_profile()
    {
        //  hide services list and news
        \App::view('hideServices', true);

        $user_id = $this->route->user_id;

        $checkUser = \Model\Users::where('id', $user_id)->where('role', 0)->count();
        if($checkUser == 0)
            throw new \Exception\PageNotFound;

        $user = \Model\Users::find($user_id)->
        with(['userService', 'userPhotos', 'userPresentations', 'userVideo'])->
        first();

        $catalogService = \Model\Articles::where('url', 'services-catalog')->first();
        $services = \Model\Articles::where('parent_id', $catalogService->id)->get();
        $services_key_id = [];
        foreach($services as $item){
            $services_key_id[$item->id] = $item;
        }

        \App::view('user', $user);
        \App::view('services', $services_key_id);

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
                    'valid' => true,
                    'message' => $this->message[0]
                ));
            }
            throw new \Exception();
        } catch (\Exception $e){
            \Core\Response::json(array(
                'valid' => false,
                'message' => 'Произошла лшибка'
            ));
        }
    }

    public function change_pswd_post()
    {
        $user_id = \App::session('user')->id;
        $model = \Model\Users::find($user_id);
        $model->password = md5($_POST['new-password']);
        if($model->save()){
            \Core\Response::json(array(
                'valid' => true,
                'message' => $this->message[0]
            ));
        }
        \Core\Response::json(array(
            'valid' => false,
            'message' => 'Произошла лшибка'
        ));
    }

    public function services_save_post()
    {
        $user_id = \App::session('user')->id;
        $servicesData = [];
        foreach($_POST['services'] as $item){
            preg_match('/(?<id>\d.+)](?<name>\w.+)/', $item['name'], $matches);
            $id = $matches['id'];
            $name = $matches['name'];
            $servicesData[$id][$name] = $item['value'];
        }

        foreach($servicesData as $id => $item){
            $service = \Model\UserService::where('user_id', $user_id)->
            where('service_id', $id)->
            first();
            if($service == null){
                $service = new \Model\UserService;
                $service->user_id = $user_id;
                $service->service_id = $id;
            }
            $service->price_h = $item['price_h'];
            $service->price_project = $item['price_project'];
            $service->save();
        }

        \Core\Response::json(array(
            'valid' => true,
            'message' => $this->message[0]
        ));
    }

    public function service_delete_post()
    {
        $id = $_POST['id'];
        $user_id = \App::session('user')->id;

        $res = \Model\UserService::where('service_id', $id)->where('user_id', $user_id)->delete();

        if($res == 1){
            \Core\Response::json(array(
                'valid' => true,
                'message' => 'Успешно удален'
            ));
        }

        \Core\Response::json(array(
            'valid' => true,
            'message' => 'Не удалось найту услугу'
        ));
    }

    public function photo_upload()
    {
        $user_id = \App::session('user')->id;
        $photos = \Model\UserPhotos::where('user_id', $user_id)->get();

        \App::view('photos', $photos);
        $this->view->render('ajax_photo');
        exit;
    }

    public function photo_upload_post()
    {
        $this->service->uploadImage();
        $scales = \App::config('scales');
        $imageService = new \Service\Image;
        $imageService->resize($scales['articles'], $this->service->files);

        foreach($_FILES['userfiles']['name'] as $key => $item){
            $this->view->image = $this->service->files[$key];

            $model = new \Model\UserPhotos;
            $model->image = $this->view->image['newname'];
            $model->user_id = \App::session('user')->id;
            $model->save();
        }

        exit;
    }

    public function photo_delete_post()
    {
        $model = \Model\UserPhotos::find($_POST['id']);
        if($model != null){
            $model->delete();
            \Core\Response::json(array(
                'valid' => true,
                'message' => $this->message[1]
            ));
        }
    }

    public function service_inputs()
    {
        $service = \Model\Articles::find($this->route->id);
        \App::view('service', $service);
        $this->view->render('ajax_service_inputs');

        exit;
    }

    public function upload_video()
    {
        $user_id = \App::session('user')->id;
        $video = \Model\UserVideo::where('user_Id', $user_id)->get();
        \App::view('video', $video);
        $this->view->render('ajax_video');
        exit;
    }

    public function upload_video_post()
    {
        $this->service->uploadImage();
        $scales = \App::config('scales');
        $imageService = new \Service\Image;
        $imageService->resize($scales['articles'], $this->service->files);
        $this->view->image = $this->service->files[0];
        $model = new \Model\UserVideo();
        $model->image = $this->view->image['newname'];
        $model->video_code = strip_tags($_POST['video_code']);
        $model->user_id = \App::session('user')->id;
        $model->save();
        exit;
    }

    public function delete_video_post()
    {
        $user_id = \App::session('user')->id;
        $result = \Model\UserVideo::where('user_id', $user_id)->where('id', $_POST['id'])->delete();
        if($result == 1){
            \Core\Response::json(array(
                'valid' => true,
                'message' => $this->message[1]
            ));
        }

        \Core\Response::json(array(
            'valid' => false,
            'message' => 'Не удалось найти данные'
        ));
    }

    public function upload_presentation()
    {
        $user_id = \App::session('user')->id;
        $present = \Model\UserPresentations::where('user_Id', $user_id)->get();
        \App::view('userPresentations', $present);
        $this->view->render('ajax_presentation');
        exit;
    }

    public function upload_presentation_post()
    {
        $doc = $_FILES['doc'];
        $info = pathinfo($doc['name']);
        $user_id = \App::session('user')->id;

        $nameFile = md5(time() . $info['filename']) . '.' . $info['extension'];
        if(in_array($info['extension'], array('doc', 'ppt', 'pptx', 'docx', 'xls', 'pdf'))){
            move_uploaded_file($doc['tmp_name'], DOCROOT . '/public/media/files/' . $nameFile);
        } else{
            \Core\Response::json(array(
                'valid' => false,
                'message' => 'Не верный формат документа'
            ));
        }

        $this->service->uploadImage();
        $scales = \App::config('scales');
        $imageService = new \Service\Image;
        $imageService->resize($scales['articles'], $this->service->files);
        $this->view->image = $this->service->files[0];
        $model = new \Model\UserPresentations;
        $model->user_id = $user_id;
        $model->image = $this->view->image['newname'];
        $model->document = $nameFile;
        if($model->save()){
            \Core\Response::json(array(
                'valid' => true,
                'message' => $this->message[0]
            ));
        }

        exit;
    }

    public function delete_presentation_post()
    {
        $user_id = \App::session('user')->id;
        $result = \Model\UserPresentations::where('user_id', $user_id)->where('id', $_POST['id'])->delete();
        if($result == 1){
            \Core\Response::json(array(
                'valid' => true,
                'message' => $this->message[1]
            ));
        }

        \Core\Response::json(array(
            'valid' => false,
            'message' => 'Не верный id'
        ));
    }

    public function upload_avatar()
    {
        $user_id = \App::session('user')->id;
        $user = \Model\Users::where('id', $user_id)->first();
        \Core\Response::json($user->avatar);
    }

    public function upload_avatar_post()
    {
        $user_id = \App::session('user')->id;
        $this->service->uploadImage();
        $scales = \App::config('scales');
        $imageService = new \Service\Image;
        $imageService->resize($scales['articles'], $this->service->files);
        $this->view->image = $this->service->files[0];
        $model = \Model\Users::where('id', $user_id)->first();
        $model->avatar = $this->view->image['newname'];
        if($model->save()){
            \App::session('user', $model);
            \Core\Response::json(array(
                'valid' => true,
                'message' => $this->message[0]
            ));
        }

        exit;
    }

    public function reserve_post()
    {
        $user_id = \App::session('user')->id;
        $model = new \Model\UserToOrder;
        foreach($_POST as $key => $value){
            $model->{$key} = strip_tags($value);
        }
        $model->customer_id = $user_id;
        if($model->save()){
            \Core\Response::json(array(
                'status' => true,
                'message' => 'Пользователь оповещен'
            ));
        }
        \Core\Response::json(array(
            'status' => false,
            'message' => 'Возникала ошибка, повторите попытку'
        ));
    }

    public function to_order_status_post()
    {
        $user_obj = \App::session('user');
        $user_id = $user_obj->id;
        $id = intval($_POST['id']);
        $status = $_POST['status'];
        $model = \Model\UserToOrder::where('id', $id)->where('executor_id', $user_id)->first();
        if($status == 'no'){
            $model->delete();
            \Core\Response::json(array(
                'status' => true,
                'message' => 'Удалено'
            ));
        }
        $model->status = $_POST['status'];
        $model->save();
        if($model->status == 'yes')
            $this->confirm_order_send_message($model, $user_obj->firstname);

        \Core\Response::json(array(
            'status' => true,
            'message' => 'Статус сохранен'
        ));
    }


    public function confirm_order_send_message($model, $firstname)
    {
        $message = new \Model\UserMessagesInfo;
        $message->description = <<<HTML
Пользователь
<a class="modal_window_send_message" data-user_id="$model->executor_id" href="#">$firstname</a>
заинтересован в вашем заказе.
Вы можете отправить ему сообщение кликнув на его имя.
HTML;

        $message->user_id = $model->customer_id;
        return $message->save();
    }

    public function calendar_reserve_post()
    {
        $user_id = \App::session('user')->id;
        \Model\UserCalendarReserve::where('user_id', $user_id)->delete();
        foreach($_POST['dates'] as $date){
            if(Date('Y-m-d') > $date)
                continue;
            $model = new \Model\UserCalendarReserve;
            $model->user_id = $user_id;
            $model->date = $date;
            $model->save();
        }

        \Core\Response::json(array(
            'status' => true,
            'message' => 'Данные обновлены'
        ));
    }

}

?>
