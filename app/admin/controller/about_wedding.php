<?php

namespace Admin\Controller;

class About_wedding extends Articles
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

        $this->view->articles = \Model\AboutWedding::orderBy($order, $asc)->get();
        \App::view('content', []);

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
        \Model\AboutWedding::where('id', $this->route->id)->delete();
        \Core\Response::json(array('valid' => true));
    }

    public function options()
    {
        if($this->route->id){
            $this->view->article = \Model\AboutWedding::find($this->route->id);
        }

    }

    public function save_post()
    {

        if(!isset($_POST['id'])){
            $article = \Model\AboutWedding::create();
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
            if(!\Model\AboutWedding::save_data($_POST['id']))
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

    public function publish()
    {
        $item = $this->route->id;
        $project = \Model\AboutWedding::find($item);
        $project->published = $project->published >= 1 ? 0 : 1;
        $project->save();

        \Core\Response::json(array('valid' => true));
    }


}

?>