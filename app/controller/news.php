<?php

namespace Controller;

use \Model\Articles;

class News extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $dataItem = Articles::getPage('url', $this->route->news)->first();
        \App::view('dataItem', $dataItem);

    }

    public function getlastNews()
    {
        $news = Articles::where('url', 'news')->first();
        $allNews = Articles::getPageSortNews('parent_id', $news->id)->skip($this->route->number)->take(4)->get();
        if($this->route->number >= 12){
            $allNews = Articles::getPageSortNews('parent_id', $news->id)->skip(8)->take(4)->get();
        }
        \App::view('news', $allNews);
        $this->view->render('getlastnews');
        exit;
    }

}

?>
