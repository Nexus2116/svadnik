<?php

namespace Controller;

use Symfony\Component\Config\Definition\Exception\Exception;

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

        $catalogService = \Model\Articles::select('id')->where('url', 'services-catalog')->first();
        $services = \Model\Articles::where('parent_id', $catalogService->id)->where('deleted_at', null)->get();
        $services_key_id = [];
        foreach($services as $item){
            $services_key_id[$item->id] = $item;
        }
        \App::view('services', $services_key_id);

        $serviceUsers = [];
        $getServiceUsers = \Model\UserService::select('user_id', 'service_id')->get();
        foreach($getServiceUsers as $item){
            if(!isset($serviceUsers[$item->service_id]))
                $serviceUsers[$item->service_id] = [$item->user_id];
            if(isset($serviceUsers[$item->service_id]))
                if(!in_array($item->user_id, $serviceUsers[$item->service_id]))
                    array_push($serviceUsers[$item->service_id], $item->user_id);

        }
        \App::view('serviceUsers', $serviceUsers);

        $projects = \Model\Projects::take(16)->where('published', 1)->orderBy('id', 'DESC')->get();
        \App::view('projects', $projects);

        $offers = \Model\Articles::where('url', 'special-offers')->where('deleted_at', null)->first();
        $offersGet = \Model\Articles::getPage('parent_id', $offers->id)->get();
        \App::view('offers', $offersGet);
    }

    public function projectAdd_post()
    {
        try{
            $project = new \Model\Projects();
            $serviceIds = explode(',', $_POST['service']);
            unset($_POST['service']);

            foreach($_POST as $key => $item)
                $project->{$key} = strip_tags($item);
            if($project->save()){
                if(!empty($serviceIds))
                    foreach($serviceIds as $id){
                        $projectService = new \Model\projectsService;
                        $projectService->service_id = $id;
                        $projectService->project_id = $project->id;
                        $projectService->save();
                    }

                \Core\Response::json(array(
                    'valid' => true,
                    'message' => 'Успешно добавлен'
                ));
            }
            throw new Exception();

        } catch (\Exception $e){
            \Core\Response::json(array(
                'valid' => false,
                'message' => 'Не удалось сохранить'
            ));
        }
    }

    public function projectsInfo()
    {
        $project = \Model\Projects::where('id', $_GET['id'])->first();
        if(!\Bootstrap::checkUserPro()){
            $project->phone = null;
            $project->email = null;
        }
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

}

?>
