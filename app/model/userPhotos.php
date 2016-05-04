<?php

namespace Model;

class UserPhotos extends \Core\Model
{

    // public $incrementing = false;
    // public $timestamps = true;
    public $table = 'user_photos';
    protected $softDelete = true;
}

?>