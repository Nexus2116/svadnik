<?php

namespace Model;

class Projects extends \Core\Model
{

    // public $incrementing = false;
    // public $timestamps = true;

    public static function save_data($id)
    {
        unset($_POST['id']);

        $article = static::find($id);
        if($article == null)
            return false;

        foreach($_POST as $key => $value)
            $article->$key = $value;

        $article->save();

        return true;
    }

}

?>