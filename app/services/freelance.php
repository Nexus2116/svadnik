<?php

namespace Service;

class Freelance
{

    public function index()
    {

        $user_id = \App::session('user')->id;

        $user = \Model\Users::find($user_id)->
        with(['userService', 'userPhotos', 'userPresentations'])->
        first();
        \App::view('user', $user);

        $catalogService = \Model\Articles::where('url', 'services-catalog')->first();
        $services = \Model\Articles::where('parent_id', $catalogService->id)->get();
        \App::view('services', $services);


//        print_r($user);
//
//
//        exit;
//
//        $user = \Model\Users::where('id', $user_id)->first();
//        \App::view('user', $user);
//
//        $catalogService = \Model\Articles::where('url', 'services-catalog')->first();
//        $services = \Model\Articles::where('parent_id', $catalogService->id)->get();
//        \App::view('services', $services);
//
//        $servicesUser = \Model\UserService::where('user_id', $user_id)->where('deleted', null)->get();
//        foreach($servicesUser as $key => $item){
//            $ids[] = $item['service_id'];
//        }
//
//        if(!empty($ids)){
//            $services = \Capsule\Db::table('services')
//                ->join('articles', 'services.tagid', '=', 'articles.id')
//                ->where('user_id', $user_id)
//                ->where('services.deleted_at', null)
//                ->get();
//            \App::view('servicesUser', $services);
//        }
//
//        $user_photo = \Model\Service::where('user_id', $user_id)->where('deleted_at', null)->get();
//        \App::view('user_photo', $user_photo);
//        $user_videos = \Model\Service::where('user_id', $user_id)->where('deleted', null)->get();
//        \App::view('user_videos', $user_videos);
//        $user_presentations = \Model\Service::where('user_id', $user_id)->where('deleted', null)->get();
//        \App::view('user_presentations', $user_presentations);
//
//
//        $project_arr = explode(',', $user->projects);
//        $project = \Model\Projects::whereIn('id', $project_arr)->get();
//        \App::view('project', $project);
//
//        $calendar = \Model\Reserve_day::where('userid', $user->id)->get();
//        foreach($calendar as $item)
//            $cal[] = $item->date;
//        if(!empty($cal)){
//            $calendarStr = implode(',', $cal);
//            \App::view('calendarStr', $calendarStr);
//        }


    }

    public function index_post()
    {
        $user_id = \App::session('user')->id;
        if(isset($_POST['mode'])){
            if($_POST['mode'] == 2){
                $update = array('firstname' => $_POST['firstname']);

                $user = (object)array();
                $user->id = $user_id;
                $user->firstname = $_POST['firstname'];
                $user->role = 0;
                \App::session('user', $user);
            } elseif($_POST['mode'] == 3)
                $update = array('password' => md5($_POST['new-password']));
        }
        if(isset($_POST['data'])){
            $count = 0;
            $iterator = 1;
            $group = array();
            foreach($_POST['data'] as $item){
                if($count > 5){
                    $arr[] = $item;
                    if($iterator == 3){
                        $price['price_h'] = $arr[0]['value'];
                        $price['price_project'] = $arr[1]['value'];
                        $price['service_id'] = $arr[2]['value'];
                        $group[] = $price;
                        $arr = array();
                        $iterator = 0;
                    }
                    $iterator++;
                } else{
                    $update[$item['name']] = $item['value'];
                }
                $count++;
            }
            array_shift($update);

            foreach($group as $item){
                if($item['price_h'] != 'Цена за час (рублей)' &&
                    $item['price_project'] != 'Цена за проект (рублей)'
                ){
                    $service = \Model\Service::where('user_id', $user_id)->
                    where('service_id', $item['service_id'])->
                    first();

                    if($service == null){
                        $service = new \Model\UserService();
                        $service->user_id = $user_id;
                        $service->service_id = $item['service_id'];
                    }
                    $service->price_h = $item['price_h'];
                    $service->price_project = $item['price_project'];
                    $service->save();
                }
            }

        }

        \Model\Users::where('id', $user_id)->update($update);
        if(isset($_POST['mode'])){
            \Core\Response::navigate('/edit');
        }

        \Core\Response::json(array(
            'valid' => true,
            'message' => 'Успешно сохранено'
        ));
    }

    public function calendar()
    {
        if(isset($_GET['calendar']))
            $calendar = \Model\Reserve_day::where('userid', \App::session('user')->id)->where('date', $_GET['calendar'])->count();
        if($calendar){
            \Model\Reserve_day::where('userid', \App::session('user')->id)->where('date', $_GET['calendar'])->delete();
            echo json_encode($_GET['calendar']);
        } else{
            $calendar = new \Model\Reserve_day;
            $calendar->userid = \App::session('user')->id;
            $calendar->date = $_GET['calendar'];
            $calendar->save();
        }

        exit;
    }

    public function delFile_post()
    {
        if(isset(\App::session('user')->id))
            \Model\Service::where('userid', \App::session('user')->id)->where('id', $_POST['id'])->delete();
        exit;
    }

}

?>