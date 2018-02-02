<?php

class Office extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(){
    	header("location:".URL."office/settings");
    }
    public function settings($section='my',$tap=''){

    	$this->view->setPage('on', 'settings' );
        $this->view->setPage('title', 'ตั้งค่า');
        $this->view->setData('section', $section);
        if( !empty($tap) ) $this->view->setData('tap', $tap);

        if( $section == "my" ){
        	if( empty($tap) ) $tap = 'basic';

            $this->view->setData('section', 'my');
            $this->view->setData('tap', 'display');
            $this->view->setData('_tap', $tap);

    		// if( $tap=='basic' ){

    		// 	$this->view
    		// 	->js(  VIEW .'Themes/'.$this->view->getPage('theme').'/assets/js/bootstrap-colorpicker.min.js', true)
    		// 	->css( VIEW .'Themes/'.$this->view->getPage('theme').'/assets/css/bootstrap-colorpicker.min.css', true);

    		// 	$this->view->setData('prefixName', $this->model->query('system')->prefixName());
    		// }
        }
        elseif( $section == "users" ){
            if( $this->format=='json' ){
                $results = $this->model->query('user')->lists();
                $this->view->setData('results', $results);
                $render = "settings/sections/users/users/json";
            }
            else{
                $this->view->setData('status', $this->model->query('user')->status());
                $this->view->setData('group', $this->model->query('user')->group());
            }
        }
        else{
        	$this->error();
        }
        $this->view->render( !empty($render) ? $render : "settings/display");
    }

    public function reports($section="booking", $tap=""){
        $this->view->setPage('on', 'reports');
        $this->view->setPage('title', 'Reports - '.ucfirst($section));
        $this->view->setData('section', $section);
        if( !empty($tap) ) $this->view->setData('tap', $tap);

        if( $section == "booking" ){
            if( empty($tap) ) $tap = "daily";
            $this->view->setData('tap', $tap);
            if( $tap == "daily" ){

                $this->view->js('jquery/jquery-selector.min')
                           ->css('jquery-selector');

                $this->view->setData('country', $this->model->query('products')->categoryList());
                $this->view->setData('sales', $this->model->query('user')->lists( array('group'=>5, 'unlimit'=>true) ));
                $this->view->setData('company', $this->model->query('agency_company')->lists( array('unlimit'=>true, 'status'=>1,'sort'=>'com_name') ));
                $this->view->setData('status', $this->model->query('booking')->status());
            }
        }
        else{
            $this->error();
        }
        $this->view->render( !empty($render) ? $render : "reports/display" );
    }
}