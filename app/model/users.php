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
        return $this->hasMany('\Model\UserPhotos', 'user_id')->orderBy('id', 'DESC');
    }

    public function userPresentations()
    {
        return $this->hasMany('\Model\UserPresentations', 'user_id')->orderBy('id', 'DESC');
    }

    public function userVideo()
    {
        return $this->hasMany('\Model\userVideo', 'user_id')->orderBy('id', 'DESC');
    }

    public function userSubscribeProject()
    {
        return $this->hasMany('\Model\UserSubscribeProject', 'user_id');
    }

    public function userMessagesInfo()
    {
        return $this->hasMany('\Model\UserMessagesInfo', 'user_id')->orderBy('id', 'DESC');
    }

    public function userToOrder()
    {
        return $this->hasMany('\Model\UserToOrder', 'executor_id')->orderBy('id', 'DESC');
    }

    public function userCalendarReserve()
    {
        return $this->hasMany('\Model\UserCalendarReserve', 'user_id');
    }

    public function userProjects()
    {
        return $this->hasMany('\Model\Projects', 'user_id');
    }

}

?>