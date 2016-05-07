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

}

?>
