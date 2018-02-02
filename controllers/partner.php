<?php

class partner extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(){

        $this->view->setPage('title', 'Partner');

        $results = $this->model->listsCompany( array('status'=>1) );
        
        $this->view->setData('results', $results);
        $this->view->render('partner/display');
    }
}