<?php

namespace Admin\Controller;

/**
 * Class Articles_content_city
 * @package Admin\Controller
 */
class Articles_content_city extends Articles
{

    public function edit_post()
    {
        unset($_POST['id']);

        $article = \Model\Articles::find($this->route->id);

        if($_SESSION['admin']->role == null && $_SESSION['admin']->city != $article->url)
            \Core\Response::json(array('valid' => false));

        $className = 'Model\Content';
        if($article->type != 'articles')
            $className = "Model\\" . $article->type;

        $content = new $className;

        $content->saveContent($this->route->id, $_POST['lang'], $className);
        \Core\Response::json(array('valid' => true));
    }

}

?>