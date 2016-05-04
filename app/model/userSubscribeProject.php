<?php

namespace Model;

class UserSubscribeProject extends \Core\Model
{

    // public $incrementing = false;
    // public $timestamps = true;
    public $table = 'user_subscribe_project';
    protected $softDelete = true;

    public function project()
    {
        return $this->hasOne('\Model\Projects', 'id');
    }
}

?>