<?php

namespace Controller;

class Edit extends \Core\Controller
{

    public function __construct()
    {
        parent::__construct();
        if(!isset(\App::session('user')->id))
            \Core\Response::navigate('/');
    }

    public function index()
    {
        \App::view('hideServices', true);

        if(\App::session('user')->role){
            $customer = new \Service\Customer;
            $customer->index();
            $this->view->partial = 'customer';
        } else{
            $freelance = new \Service\Freelance;
            $freelance->index();
            $this->view->partial = 'freelance';
        }

        $this->view->renderLayout();


    }

    public function index_post()
    {

        if(\App::session('user')->role){
            $customer = new \Service\Customer;
            $customer->index_post();
            $this->view->partial = 'customer';
        } else{
            $freelance = new \Service\Freelance;
            $freelance->index_post();
            $this->view->partial = 'freelance';
        }


    }

    public function calendar()
    {
        $freelance = new \Service\Freelance;
        $freelance->calendar();
    }

    public function delFile_post()
    {
        $freelance = new \Service\Freelance;
        $freelance->delFile_post();
    }


}

?>
