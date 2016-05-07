<?php

namespace Controller;

class Projects extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    private $message = [
        0 => 'Данный заказ был добавлен ранее',
        1 => 'Заказ добавлен',
        2 => 'Успешно удалено',
        3 => 'Не удалось удалить',
        4 => 'Данные заказ был добавлен ранне',
        5 => 'СПАСИБО, ЗАЯВКА БУДЕТ<br>ОПУБЛИКОВАНА ПОСЛЕ<br>МОДЕРАЦИИ',
        6 => 'Не удалось сохранить',
        7 => 'Исполнители оповещены',
    ];

    public function subscribe_project()
    {
        $user_obj = \App::session('user');
        $user_id = $user_obj->id;
        $model = \Model\UserSubscribeProject::where('user_id', $user_id)->where('project_id', $_GET['id'])->first();

        if($model != null)
            \Core\Response::json(array(
                'status' => false,
                'message' => $this->message[0]
            ));
        else
            $model = new \Model\UserSubscribeProject;
        $model->user_id = $user_id;
        $model->project_id = intval($_GET['id']);
        if($model->save()){
            $project = \Model\Projects::find($_GET['id']);
            $message = new \Model\UserMessagesInfo;
            $message->description = 'Пользователь <a target="_blank" href="/executor/' . $user_id . '">' . $user_obj->firstname . '</a> откликнулся на ваш заказ #' . $project->id . '.';
            $message->user_id = $project->user_id;
            $message->save();
        }
        \Core\Response::json(array(
            'status' => true,
            'message' => $this->message[1]
        ));
    }

    public function user_remove_project_post()
    {
        $user = \App::session('user');

        $project = \Model\Projects::find($_POST['id']);
        $result = \Model\UserSubscribeProject::where('project_id', $_POST['id'])->where('user_id', $user->id)->delete();
        if($result == 1){
            $message = new \Model\UserMessagesInfo;
            $message->description = 'Пользователь <a target="_blank" href="/executor/' . $user->id . '">' . $user->firstname . '</a> удалил заказ #' . $project->id . '.';
            $message->user_id = $project->user_id;
            $message->save();

            \Core\Response::json(array(
                'status' => true,
                'message' => $this->message[2]
            ));
        } else
            \Core\Response::json(array(
                'status' => false,
                'message' => $this->message[3]
            ));
    }

    public function get_information()
    {
        $project = \Model\Projects::where('id', $_GET['id'])->first();
        if(!\Bootstrap::checkUserPro()){
            $project->phone = null;
            $project->email = null;
        }

        $arr = Array(
            'title' => $project->title,
            'content' => $project->text,
            'date_add_n' => Date($project->created_at),
            'user_phone' => $project->phone,
            'user_email' => $project->email,
            'attr_id' => $project->id,
        );
        \Core\Response::json($arr);
    }

    public function add_project_post()
    {
        try{
            $user_obj = \App::session('user');
            $user_id = null;
            if($user_obj != null)
                $user_id = $user_obj->id;
            else{
                $get_user = \Model\Users::select('id')->where('email', $_POST['email'])->first();
                if($get_user != null)
                    $user_id = $get_user->id;
            }

            $project_hash = md5($_POST['title'] . $_POST['text'] . $_POST['email']);
            if($this->find_project($project_hash)){
                \Core\Response::json(array(
                    'status' => false,
                    'message' => $this->message[4]
                ));
            }

            $project = new \Model\Projects();
            $serviceIds = explode(',', $_POST['service']);
            unset($_POST['service']);

            foreach($_POST as $key => $item){
                if($key == 'phone')
                    $item = \Html::parsePhone($item);
                $project->{$key} = strip_tags($item);
            }
            $project->user_id = $user_id;
            $project->project_hash = $project_hash;
            if($project->save()){
                if(!empty($serviceIds))
                    foreach($serviceIds as $id){
                        $projectService = new \Model\projectsService;
                        $projectService->service_id = $id;
                        $projectService->project_id = $project->id;
                        $projectService->save();
                    }

                \Core\Response::json(array(
                    'status' => true,
                    'message' => $this->message[1]
                ));
            }
            throw new \Exception();

        } catch (\Exception $e){
            \Core\Response::json(array(
                'status' => false,
                'message' => $this->message[6]
            ));
        }
    }

    public function to_order_post()
    {
        $user_obj = \App::session('user');
        $user_id = $user_obj->id;
        $idsData = explode(',', $_POST['id']);
        foreach($idsData as $id){
            if(intval($id) == 0)
                continue;
            $message = new \Model\UserMessagesInfo;
            $message->description = 'Пользователь <a target="_blank" href="/customer/' . $user_id . '">' . $user_obj->firstname . '</a> вами заинтересовался.';
            $message->user_id = intval($id);
            $message->save();
        }
        \Core\Response::json(array(
            'status' => true,
            'message' => $this->message[7]
        ));
    }

    private function find_project($project_hash)
    {
        $project_count = \Model\Projects::where('project_hash', $project_hash)->count();
        if($project_count == 0)
            return false;
        else
            return true;
    }

}

?>
