<?php

namespace Controller;

use Symfony\Component\Config\Definition\Exception\Exception;

class Upload extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function image_post()
    {
        $article = \Model\Articles::find($_POST['article']);
        if($article == null)
            exit;

        $this->service->uploadImage();

        $scales = \App::config('scales');

        $imageService = new \Service\Image;
        $imageService->resize($scales[$_POST['scale']], $this->service->files);

        $this->view->image = $this->service->files[0];

        $key = $_POST['field'];
        $article->$key = $this->view->image['newname'];
        $article->save();

        $this->view->field = $key;
        $this->view->render('image.result');
        exit;
    }

    public function upImage_post()
    {

        if($_POST['type'] == 'photos'){

            $this->service->uploadImage();
            $scales = \App::config('scales');
            $imageService = new \Service\Image;
            $imageService->resize($scales['articles'], $this->service->files);

            foreach($_FILES['userfiles']['name'] as $key => $item){
                $this->view->image = $this->service->files[$key];

                $service = new \Model\Service;
                $service->typeserv = 'photos';
                $service->userid = \App::session('user')->id;
                $service->file_img = $this->view->image['newname'];
                $service->save();
            }

        } elseif($_POST['type'] == 'video'){
            $this->service->uploadImage();
            $scales = \App::config('scales');

            $imageService = new \Service\Image;
            $imageService->resize($scales['articles'], $this->service->files);

            $this->view->image = $this->service->files[0];

            $service = new \Model\Service;
            $service->typeserv = 'video';
            $service->text = htmlspecialchars($_POST['newvideo']);
            $service->userid = \App::session('user')->id;
            $service->file_img = $this->view->image['newname'];
            $service->save();

        } elseif($_POST['type'] == 'present'){


            foreach($_FILES['userfiles'] as $key => $item)
                $file[$key] = array_shift($_FILES['userfiles'][$key]);

            $arrName = explode('.', $file['name']);
            $ext = array_pop($arrName);
            $rand = rand(0, 999);
            $nameFile = $rand . $file['name'];
            if(in_array($ext, array('doc', 'ppt', 'pptx', 'docx', 'xls', 'pdf'))){
                move_uploaded_file($file['tmp_name'], DOCROOT . '/public/media/files/' . $nameFile);
            }

            $this->service->uploadImage();
            $scales = \App::config('scales');

            $imageService = new \Service\Image;
            $imageService->resize($scales['articles'], $this->service->files);

            $this->view->image = $this->service->files[0];

            $service = new \Model\Service;
            $service->typeserv = 'present';
            $service->file = $nameFile;
            $service->userid = \App::session('user')->id;
            $service->file_img = $this->view->image['newname'];
            $service->save();

        } elseif($_POST['type'] == 'avatar'){

            try{
                $this->service->uploadImage();
                $scales = \App::config('scales');

                $imageService = new \Service\Image;
                $imageService->resize($scales['articles'], $this->service->files);

                $this->view->image = $this->service->files[0];
                \Model\Users::where('id', \App::session('user')->id)->update(array('avatar' => $this->view->image['newname']));

            } catch (\Exception $e){

            }
        }
        \Core\Response::navigate('/edit');


        exit;
    }


    public function gallery()
    {
        $article = \Model\Articles::find($this->route->id);
        if($article == null)
            exit;

        $scales = \App::config('scales');
        $scale = $scales[$this->route->scale];

        $_FILES['userfiles'] = $_FILES['file'];
        unset($_FILES['file']);

        $this->service->uploadImage();
        $imageService = new \Admin\Service\Image;
        $imageService->resize($scale, $this->service->files);

        $picture = $this->service->files[0];

        if($this->route->scale == 'products'){
            foreach($this->service->files as $image){
                $path = DOCROOT . '/public/upload/gallery/' . $image['newname'];
                $imageService->watermark(DOCROOT . '/public/images/watermark.png', $path);
            }
        }

        $image = new \Model\Image;
        $image->path = $picture['newname'];
        $image->gallery = $article->gallery;
        $image->save();

        echo $image->path;
        exit;
    }

    public function redactor()
    {
        $path = DOCROOT . '/public/media/images/';
        $this->service->path = $path;
        $this->service->uploaded = &$_FILES['file'];

        $this->service->uploadImage($path);
        $file = array(
            'filelink' => '/public/media/images/' . $this->service->files[0]['newname']
        );

        \Core\Response::json($file);
    }

}

?>