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
        $user_id = \App::session('user')->id;
        $user = \Model\Users::find($user_id);

        if($user == null || $user->role != 1)
            throw new \Exception\PageNotFound;

    }

    public function index()
    {
        \App::view('hideServices', true);

        $user_id = \App::session('user')->id;

        $user = \Model\Users::where('id', $user_id)->
        with(['userService', 'userPhotos', 'userPresentations', 'userVideo'])->
        first();


        \App::view('user', $user);

    }

    public function user_profile()
    {
        //  hide services list and news
        \App::view('hideServices', true);

        $user_id = $this->route->user_id;

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
                $model->{$key} = strip_tags($item);
            }
            if($model->save()){
                \Core\Response::json(array(
                    'valid' => true,
                    'message' => 'Успешно сохранено'
                ));
            }
            \Core\Response::json(array(
                'valid' => false,
                'message' => 'Произошла лшибка'
            ));

        } catch (\Exception $e){
            exit;
        }
    }

    public function change_pswd_post()
    {
        $user_id = \App::session('user')->id;
        $model = \Model\Users::find($user_id);
        $model->password = md5($_POST['password']);
        if($model->save()){
            \Core\Response::json(array(
                'valid' => true,
                'message' => 'Успешно сохранено'
            ));
        }
        \Core\Response::json(array(
            'valid' => false,
            'message' => 'Произошла лшибка'
        ));
    }


    public function avatar_upload()
    {
        $user_id = \App::session('user')->id;
        $user = \Model\Users::where('id', $user_id)->first();

        \App::view('user', $user);
        echo json_encode($user->avatar);
        exit;
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

}

?>
