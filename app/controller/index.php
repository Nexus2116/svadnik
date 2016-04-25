<?php

namespace Controller;

class Index extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->layout = 'index';
        $this->seo('Главная страница', 'Цитадель', 'Цитадель');
        \App::view('indexPage', true);
        // \App::view('hideServices', true);

        $catalog = \Model\Articles::where('url', 'services-catalog')->first();

        $catalog_parent = \Model\Articles::where('parent_id', $catalog->id)->get();
        \App::view('catalog', $catalog_parent);

        foreach($catalog_parent as $item)
            $serviceUserCount[$item['id']] = \Model\Service::where('tagid', $item['id'])->where('deleted', null)->count();

        \App::view('serviceUserCount', $serviceUserCount);


        $catalog_parent = \Model\Articles::where('parent_id', $catalog->id)->take(2)->get();
        \App::view('catalog_index', $catalog_parent);


        //Примерный бюджет вашего мероприятия
        $offers = \Model\Articles::where('url', 'special-offers')->first();
        $offersGet = \Model\Articles::getPage('parent_id', $offers->id)->get();
        \App::view('offers', $offersGet);


        $projects = \Model\Projects::get();
        \App::view('projects', $projects);

    }

    public function projectAdd_post()
    {
        foreach($_POST as $key => $item){
            $_POST[$key] = htmlspecialchars($item);
        }
        $project = new \Model\Projects();
        $project->title = $_POST['req-title'];
        $project->date_start = $_POST['req-date-since'];
        $project->date_end = $_POST['req-date-to'];
        $project->text = $_POST['req-text'];
        $project->phone = $_POST['req-phone'];
        $project->email = $_POST['req-mail'];
        $project->budget = $_POST['req-budget'];
        $project->services = $_POST['service-arr'];
        $project->visible = $_POST['req-chb'];
        if($project->save()){
            echo json_encode(['status' => 1]);
            exit;
        }
        echo json_encode(['status' => 0]);
        exit;
    }

    public function projectsInfo()
    {
        $project = \Model\Projects::where('id', $_GET['id'])->first();
        $arr = Array(
            'title' => $project->title,
            'content' => $project->text,
            'date_add_n' => $project->created_at,
            'user_phone' => $project->phone,
            'user_email' => $project->email,
            'attr_id' => $project->id,
        );
        echo json_encode($arr);
        exit;
    }

    public function projectOfferAdd()
    {
        $user = \Model\Users::where('id', \App::session('user')->id)->first();
        $arr = explode(',', $user->projects);

        if(!array_search($_GET['id'], $arr)){
            array_push($arr, $_GET['id']);
            $result = implode(',', $arr);
            \Model\Users::where('id', \App::session('user')->id)->update(array('projects' => $result));
            echo 'Проект добавлен';
        } else{
            echo 'У вас уже имеется данный проект';
        }

        exit;
    }

    public function search()
    {
        \App::view('hideServices', true);


    }


}

?>
