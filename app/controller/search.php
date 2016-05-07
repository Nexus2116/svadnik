<?php

namespace Controller;

class Search extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        \App::view('hideServices', true);
        \App::view('indexPage', false);

        $this->view->layout = 'index';
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
