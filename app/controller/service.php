<?php

namespace Controller;


class Service extends \Core\Controller
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
        if(!empty($_GET)){

            $this->handler_executors();

        } else{

            $service = \Model\Articles::where('url', $this->route->service)->first();
            if($service == null){
                throw new \Exception\PageNotFound;
            }

            \App::view('service', $service);

            $user_get_ids = \Model\UserService::select('user_id')->
            where('service_id', $service->id)->
            get();

            if(count($user_get_ids)){
                $user_ids = [];

                foreach($user_get_ids as $id)
                    array_unshift($user_ids, $id->user_id);
                $users = \Model\Users::whereIn('id', $user_ids)->
                with(['userPhotos', 'userService'])->
                orderBy('id', 'DESC')->
                get();
                \App::view('users', $users);
            }


            $this->Services();
        }
    }

    public function list_executors()
    {
        $this->handler_executors();
        $this->view->render('list_executors');
        exit;
    }

    public function handler_executors()
    {
        $service = \Model\Articles::where('url', $this->route->service)->first();
        if($service == null || empty($_GET)){
            throw new \Exception\PageNotFound;
        }

        $type_price = 'price_project';
        if($_GET['type_price'] == 'hour'){
            $type_price = 'price_h';
        }

        $get_user_id = \Model\UserService::select('user_id')->
        where($type_price, '>=', $_GET['price_start'])->
        where($type_price, '<=', $_GET['price_end'])->
        where('service_id', $service->id)->
        get();

        \App::view('service', $service);

        if(count($get_user_id)){
            $user_ids = [];
            foreach($get_user_id as $id)
                array_unshift($user_ids, $id->user_id);

            $users = \Model\Users::whereIn('id', $user_ids)->
            with(['userPhotos', 'userService', 'userCalendarReserve'])->
            orderBy('id', 'DESC')->
            get();

            \App::view('users', $users);
        }

        $this->Services();
    }

    public function Services()
    {
        $catalogService = \Model\Articles::where('url', 'services-catalog')->first();
        $services = \Model\Articles::where('parent_id', $catalogService->id)->get();
        $services_key_id = [];
        foreach($services as $item){
            $services_key_id[$item->id] = $item;
        }

        \App::view('services', $services_key_id);
    }


    public function reviews()
    {
        $review = \Model\Review::where('userid', $_GET['id'])->orderBy('id', 'DESC')->get();
        \App::view('review', $review);
        \App::view('userid', $_GET['id']);
        \App::view('url', $_GET['url']);

        if(isset(\App::session('user')->id)){
            $user = \Model\Users::where('id', \App::session('user')->id)->first();
            \App::view('user', $user);
        }

        $this->view->render('reviews');
        exit;
    }

    public function reviewsAdd_post()
    {
        $valid = array('id' => '', 'user_name' => 'Имя:', 'user_email' => 'E-mail:', 'content' => 'Текст комментария:', 'comment-rating' => -1);
        foreach($_POST as $key => $item)
            if($_POST[$key] == $valid[$key])
                $arr[$key] = 'error';
        if(!empty($arr))
            echo json_encode($arr);

        if(empty($arr)){
            $review = new \Model\Review();
            $review->userid = $_POST['id'];
            $review->name = $_POST['user_name'];
            $review->email = $_POST['user_email'];
            $review->text = $_POST['content'];
            $review->rating = $_POST['comment-rating'];
            $review->save();
        }
        \Core\Response::navigate($_POST['url']);
    }

    public function list_executors_count()
    {
        $type_price = 'price_project';
        if($_GET['type_price'] == 'hour'){
            $type_price = 'price_h';
        }

        $service = \Model\Articles::select('id')->where('url', $this->route->service)->first();
        $get_ids = \Model\UserService::select('user_id')->
        where('service_id', $service->id)->
        where($type_price, '>=', $_GET['price_start'])->
        where($type_price, '<=', $_GET['price_end'])->
        get()->
        toArray();

        $ids = [];
        foreach($get_ids as $item)
            $ids[] = join(',', $item);

        echo json_encode(
            [
                'user_ids' => $ids,
            ]
        );

        exit;
    }

}

?>
