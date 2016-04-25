<?php

namespace Admin\Controller;

class Projects extends Articles
{

    public function index()
    {
        $order = $this->route->order;
        $asc = $this->route->asc;

        $asc = $asc == -1 ? 'desc' : 'asc';

        $result = [];

        if($order == null){
            $order = 'id';
            $asc = 'desc';
        }

        $projects = \Model\Articles::where('url', 'projects')->first();
        $this->view->articles = \Model\Projects::orderBy($order, $asc)->get();


        \App::view('content', $projects);


        $this->view->period = $this->route->period;
        if($this->view->period == null)
            $this->view->period = 1;

        $users = \Model\Admin::all();
        $data = array();
        foreach($users as $user)
            $data[$user->id] = $user;
        $this->view->users = $data;
    }

    public function delete()
    {
        \Model\Projects::where('id', $this->route->id)->delete();
        \Core\Response::json(array('valid' => true));
    }

    public function options()
    {
        if($this->route->id){
            $this->view->article = \Model\Projects::find($this->route->id);
        }

    }

    public function save_post()
    {

        if(!isset($_POST['id'])){
            $article = \Model\Projects::create();
            $article->date = date('Y-m-d');
            $article->save();

            foreach($this->config->langs as $key => $value){
                $content = new \Model\Content;
                $content->articles_id = $article->id;
                $content->lang = $key;
                $content->save();
            }

            $tService = new \Admin\Service\Tree;
            \App::view('tree', $tService->getTree());

            \Core\Response::json(array(
                'valid' => true,
                'content' => $this->view->getContent('/articles/sidebar'),
                'page' => $article->toJson()
            ));
        } else{
            if(!\Model\Projects::save_data($_POST['id']))
                \Core\Response::json(array(
                    'valid' => false,
                    'error' => 'Запись не найдена'
                ));
            else
                \Core\Response::json(array(
                    'valid' => true,
                    'message' => 'Успешно сохранено'
                ));
        }
    }


}

?>