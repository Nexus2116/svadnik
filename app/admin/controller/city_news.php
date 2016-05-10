<?php

namespace Admin\Controller;

/**
 * Class Articles_content_city
 * @package Admin\Controller
 */
class City_news extends Articles
{

    public function edit()
    {
        $article = \Model\Articles::find($this->route->id);
        if($article == null)
            throw new \Admin\Exception\PageNotFound;
        $this->view->article = $article;

        if($this->route->gallery)
            return $this->gallery();

        $this->view->file = 'content';
        $this->view->tabLanguage = 'ru';
        if($this->route->lng)
            $this->view->tabLanguage = $this->route->lng;

        $type = 'Content';
        if($article->type != 'articles')
            $type = $article->type;

        $this->view->content = $article->content($type)->where('lang', $this->view->tabLanguage)->first();

        $get_news = \Model\Articles::where('url', 'news')->first();
        $news = \Model\Articles::getPage('parent_id', $get_news->id)->get();
        $this->view->news = $news;

    }

    public function edit_post()
    {

        unset($_POST['id']);

        $news_ids = join(',', $_POST['news']);
        $_POST['news'] = $news_ids;

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