<?php

namespace Admin\Service;

class Projects
{

    public function validatePost()
    {
        $rules = array(
            'title' => array('required', array('Поле "Заголовок" обязательно для заполнения')),
            'text' => array('required', array('Поле "Текст" обязательно для заполнения'))
        );

        if(!\App::validator()->validate($_POST, $rules))
            return false;

        return true;
    }
}

?>