<?php

class Plate extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
    	$this->error();
    }

    public function reports($section='daily'){
    	if( $section == "daily" ){
    		$date = isset($_REQUEST["date"]) ? $_REQUEST["date"] : date("Y-m-d");
    		$country = isset($_REQUEST["country"]) ? $_REQUEST["country"] : null;
    		$series = isset($_REQUEST["series"]) ? $_REQUEST["series"] : null;
    		$sale = isset($_REQUEST["sale"]) ? $_REQUEST["sale"] : null;
    		$company = isset($_REQUEST["company"]) ? $_REQUEST["company"] : null;
    		$agency = isset($_REQUEST["agency"]) ? $_REQUEST["agency"] : null;
    		$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;

    		$options = array(
    			"date"=>$date,
    			"country"=>$country,
    			"series"=>$series,
    			"sale"=>$sale,
    			"company"=>$company,
    			"agency"=>$agency,
    			"status"=>$status
    		);

    		$book = $this->model->query('reports')->listsBooking( $options );
    		$this->view->setData('book', $book);
    		$this->view->render("reports/daily");
    	}
    }
}