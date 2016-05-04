<?php

namespace Model;

class UserProjects extends \Core\Model
{

    // public $incrementing = false;
    // public $timestamps = true;
    public $table = 'user_projects';
    protected $softDelete = true;

    public function userProjects()
    {
        return $this->hasMany('\Model\Projects', 'project_id');
    }


}

?>