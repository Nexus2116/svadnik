<?php

namespace Admin\Controller;

/**
 * Class Articles_content_city
 * @package Admin\Controller
 */
class City_banners extends Articles
{

    public function edit_post()
    {
        unset($_POST['id']);

        $article = \Model\Articles::find($this->route->id);

        $className = 'Model\Content';
        if($article->type != 'articles')
            $className = "Model\\" . $article->type;

        $content = new $className;

        $content->saveContent($this->route->id, $_POST['lang'], $className);
        \Core\Response::json(array('valid' => true));
    }

}

?>