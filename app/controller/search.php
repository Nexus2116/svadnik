<?php

namespace Controller;

use Model\Articles;

class Search extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function before()
    {
        \App::view('hideServices', true);
        \App::view('indexPage', false);
    }

    public function index()
    {
        $this->seo('Главная страница', 'Цитадель', 'Цитадель');

        $services = [];
        $allNews = [];
        $weddingArticles = [];

        if(!empty($_GET['value'])){
            $news = Articles::where('url', 'news')->first();
            $allNews = Articles::where('parent_id', $news->id)->
            where('deleted_at', null)->
            where('name', 'like', '%' . $_GET['value'] . '%')->
            get();

            $service = Articles::where('url', 'services-catalog')->first();
            $services = Articles::where('parent_id', $service->id)->
            where('deleted_at', null)->
            where('name', 'like', '%' . $_GET['value'] . '%')->
            get();

            $weddingArticles = \Model\AboutWedding::where('title', 'like', '%' . $_GET['value'] . '%')->get();
        }

        \App::view('services', $services);
        \App::view('news', $allNews);
        \App::view('articles', $weddingArticles);

    }

    public function users()
    {
        $this->seo('Главная страница', 'Цитадель', 'Цитадель');
        $columns = ['id', 'email'];
        $users = [];
        foreach($_GET as $key => $item){
            if(in_array($key, $columns)){
                $query = \Model\Users::orderBy('id', 'DESC');
                if($key == 'id'){
                    $query->whereIn($key, explode(',', $item));
                } else
                    $query->Where($key, 'like', $item . '%');
                $users = $query->with(['userPhotos', 'userService', 'userCalendarReserve'])->
                get();
            }
        }

        \App::view('users', $users);


        $catalogService = \Model\Articles::where('url', 'services-catalog')->first();
        $services = \Model\Articles::where('parent_id', $catalogService->id)->get();
        $services_key_id = [];
        foreach($services as $item){
            $services_key_id[$item->id] = $item;
        }

        \App::view('services', $services_key_id);
    }


}

?>
