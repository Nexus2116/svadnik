<?php

namespace Model;

class Users extends \Core\Model
{

    public $incrementing = true;
    protected $softDelete = true;
    public $table = 'users';

    public function target()
    {
        return $this->morphTo();
    }

    public function userService()
    {
        return $this->hasMany('\Model\UserService', 'user_id');
    }

    public function userPhotos()
    {
        return $this->hasMany('\Model\UserPhotos', 'user_id');
    }

    public function userPresentations()
    {
        return $this->hasMany('\Model\UserPresentations', 'user_id');
    }

    public function userVideo()
    {
        return $this->hasMany('\Model\userVideo', 'user_id');
    }

    public function userProjects()
    {
        return $this->hasMany('\Model\UserPhotos', 'user_id');
    }

}

?>