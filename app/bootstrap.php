<?php

class Bootstrap
{

    public function before()
    {
        $this->setAssets();
        $this->getSettings();
    }

    protected function setAssets()
    {
        \App::view()->css = array(
            'style' => 'style',
            'zebra_datepicker' => 'zebra_datepicker',
        );

        \App::view()->js = array(
            'jquery' => 'jquery-1.10.2.min',
            'zebra_datepicker' => 'zebra_datepicker',
            'jquery.columnizer' => 'jquery.columnizer',
            'jquery.geocomplete.min' => 'jquery.geocomplete.min',
            'functions' => 'functions',
        );
    }

    public function getSettings()
    {
        $settings = \Model\Settings::all();
        foreach($settings as $item)
            \App::config($item->key, $item->value);
    }

    public function Menu($url)
    {
        $menu = \Model\Articles::where('url', $url)->first();
        $allMenu = \Model\Articles::where('parent_id', $menu->id)->where('deleted_at', null)->get();
        foreach($allMenu as $item){
            ?><a href="/<?= $item->url == 'main' ? '' : $item->url ?>" class="menu-item"><?= $item->name ?></a><?
        }
    }


    public function userSession()
    {
        return \App::session('user');
    }


    public function services()
    {
        $services = \Model\Articles::where('url', 'services-catalog')->first();
        $allServices = \Model\Articles::where('parent_id', $services->id)->get();
        return $allServices;
    }

    public function news()
    {
        $news = \Model\Articles::where('url', 'news')->first();
        $allNews = \Model\Articles::getPageSortNews('parent_id', $news->id)->take(4)->get();
        return $allNews;
    }

    public function CountNews()
    {
        $news = \Model\Articles::where('url', 'news')->first();
        $CountNews = \Model\Articles::getPageSortNews('parent_id', $news->id)->count();
        return $CountNews;
    }

    public function city()
    {
        $city = \Model\Articles::where('url', 'city')->first();
        return \Model\Articles::where('parent_id', $city->id)->where('deleted_at', null)->get();
    }

    public function reserveDay($day)
    {
        $result = implode(',', $day);
        echo $result;
    }

    public function statusDay($arr)
    {
        if(array_search(date("d m Y"), $arr))
            echo 'Занят';
        else
            echo 'Свободен';
    }

    public function rating($rating, $count)
    {
        $num = 0;
        foreach($rating as $item)
            $num = $num + $item->rating;

        for($i = 1; $i <= $num / $count; $i++){
            ?>
            <div class="star active"></div>
        <? }
        for($i = 1; $i <= ceil(5 - $num / $count); $i++){
            ?>
            <div class="star"></div>
            <?
        }
    }

    public function listChatUser()
    {
        $user_obj = \App::session('user');
        $user_id = $user_obj->id;
        $column = $user_obj->role == 0 ? 'executor_id' : 'customer_id';
        $column2 = $user_obj->role == 1 ? 'executor_id' : 'customer_id';

        $messages = \Model\Message::where($column, $user_id)->groupBy($column2)->get();

        $user_ids = [];
        foreach($messages as $message){
            array_unshift($user_ids, $message->{$column2});

        }
        $get_users = [];
        if(!empty($user_ids))
            $get_users = \Model\Users::select('firstname', 'id')->whereIn('id', $user_ids)->get();
        $users = [];
        foreach($get_users as $user){
            $users[$user->id] = $user;
        }

        return [
            'messages' => $messages,
            'column' => $column2,
            'users' => $users
        ];
    }

    public function checkUserPro()
    {
        $user_id = \App::session('user')->id;
        $user = \Model\Users::find($user_id);
        if($user->date_end_pro >= Date("Y-m-d"))
            return true;
        else
            return false;
    }

    public function userGetRole()
    {
        $user_id = \App::session('user')->id;
        $user = \Model\Users::find($user_id);
        return $user->role;
    }

    public function checkAuthUser()
    {
        if(\App::session('user') == null)
            return false;
        else
            return true;
    }

    public function get_city()
    {
        $city = $_COOKIE['city'];
        if(empty($city))
            $city = 'moscow';

        return \Model\Articles::getPageCity('url', $city)->first();

    }


}

?>
