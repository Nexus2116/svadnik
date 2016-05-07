<?php

namespace Controller;

class Allabout extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->layout = 'index';
        $this->seo('Главная страница', 'Цитадель', 'Цитадель');

        $dataItem = \Model\Articles::getPage('url', $this->route->urlParts[0])->first();
        \App::view('dataItem', $dataItem);

        $articles = \Model\AboutWedding::take(4)->where('published', 1)->orderBy('id', 'DESC')->get();
        \App::view('articles', $articles);

        $articles_count = \Model\AboutWedding::where('published', 1)->count();
        \App::view('articles_count', $articles_count);
    }

    public function create_article_post()
    {
        try{
            $user_obj = \App::session('user');
            $user_id = $user_obj->id;


            if($user_obj->role != 0 || !\Bootstrap::checkUserPro())
                throw new \Exception();

            $model = new \Model\AboutWedding;
            foreach($_POST as $key => $value)
                $model->{$key} = strip_tags($value);
            $model->user_id = $user_id;
            if($model->save()){
                \Core\Response::json(array(
                    'status' => true,
                    'message' => 'Статья отправлена на модерацию'
                ));
            }

            throw new \Exception();

        } catch (\Exception $e){
            \Core\Response::json(array(
                'status' => false,
                'message' => 'У вас нет доступа'
            ));
        }
    }

    public function page_article()
    {
        $articles_count = \Model\AboutWedding::where('published', 1)->count();
        \App::view('articles_count', $articles_count);

        $articles = \Model\AboutWedding::take(4)->where('published', 1)->orderBy('id', 'DESC')->get();
        \App::view('articles', $articles);

        $article = \Model\AboutWedding::where('id', $this->route->id)->first();
        \App::view('article', $article);
    }

    public function get_articles()
    {
        $articles = \Model\AboutWedding::take(4)->
        offset($this->route->offset)->
        where('published', 1)->
        orderBy('id', 'DESC')->
        get();
        \App::view('articles', $articles);

        $this->view->render('ajax_get_articles');
        exit;
    }

}

?>
