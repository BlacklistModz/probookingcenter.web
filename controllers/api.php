<?php
class api extends Controller {

    public function __construct() {
        parent::__construct();
    }
    public function index(){
    	$this->error();
    }
    public function series(){
    	$token = isset($_REQUEST["token"]) ? $_REQUEST["token"] : null;
    	if( empty($token) ) $arr['error'] = 'Token Undefined';

    	if( empty($arr['error']) ){
    		$arr['token'] = $token;
    	}
    	echo json_encode($arr);
    }
}