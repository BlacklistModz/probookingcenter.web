<?php

class Series_Model extends Model{

    public function __construct() {
        parent::__construct();
    }
    private $_objType = "series";
    private $_field = "s.*
    				  , c.country_name
    				  , a.air_name
    				  , period.per_date_start AS periodStartDate
            		  , period.per_date_end AS periodEndDate
            		  , COUNT(*) AS periodCount";
    private $_table = "series s 
        LEFT JOIN country c ON s.country_id=c.country_id
        LEFT JOIN airline a ON s.air_id=a.air_id
        LEFT OUTER JOIN period ON s.ser_id=period.ser_id";

    private $_cutNamefield = "ser_";

    public function lists($options=array()){
    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'more' => true,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'create_date',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'DESC',
            
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,

        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "";
        $where_arr = array();

        if( isset($_REQUEST["country"]) ){
        	$options["country"] = $_REQUEST["country"];
        }
        if( !empty($options["country"]) ){
        	$where_str .= !empty($where_str) ? " AND " : "";
        	$where_str .= "s.country_id=:country";
        	$where_arr[":country"] = $options["country"];
        }

        if( isset($_REQUEST["status"]) ){
        	$options["status"] = $_REQUEST["status"];
        }
        if( !empty($options["status"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "s.status=:status";
            $where_arr[":status"] = $options["status"];
        }

        $groupby = ' GROUP BY s.ser_id HAVING periodCount>0';
        // $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( $options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$groupby} {$orderby} {$limit}", $where_arr ), $options );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        $arr['options'] = $options;

        return $arr;
    }
    public function buildFrag($results, $options=array()){
    	$data = array();
    	foreach ($results as $key => $value) {
    		if( empty($value) ) continue;
    		$data[] = $this->convert($value, $options);
    	}
    	return $data;
    }
    public function convert($data, $options=array()){
    	$data = $this->_cutFirstFieldName($this->_cutNamefield, $data);

    	if( !empty($options["period"]) ){
    		$data["period"] = $this->periodList($data['id']);
    	}

    	$data["permit"]["del"] = false;
    	return $data;
    }
    public function insert(&$data){
    	$data["create_date"] = date("c");
    	$data["update_date"] = date("c");
    	$this->db->insert($this->_objType, $data);
    }
    public function update($id, $data){
    	$data["update_date"] = date("c");
    	$this->db->update($this->_objType, $data, "{$this->_cutNamefield}id={$id}");
    }
    public function delete($id){
    	$this->db->delete($this->_objType, "{$this->_cutNamefield}id={$id}");
    }

    /* -- Period -- */
    private $_periodSelect = "
          p.per_id
        , p.per_date_start
        , p.per_date_end
        , p.per_price_1
        , p.per_price_2
        , p.per_price_3
        , p.per_price_4
        , p.per_price_5
        , p.single_charge
        
        , p.per_qty_seats

        , p.per_com_agency
        , p.per_com_company_agency

        , p.per_url_word
        , p.per_url_pdf

        , p.status
    ";
    private $_periodTable = "period p";
    public function periodList($id){

        $results = $this->db->select("SELECT {$this->_periodSelect} FROM {$this->_periodTable} WHERE p.ser_id=:id AND per_date_start>=:d ORDER BY per_date_start ASC", array(':id'=>$id, ':d'=>date('Y-m-d')));

        $data = array();
        foreach ($results as $key => $value) {
            $data[$key] = $this->_cutFirstFieldName('per_', $value);

            // $bus_list = 
            // per_id

            // 
            // $booking = 0;
            $data[$key]['booking'] = $booking = $this->seatBooked( $value['per_id'] );
            $data[$key]['seats'] = $value['per_qty_seats'];

            $data[$key]['balance'] = $value['per_qty_seats'] - $booking['booking'];
            // $data[$key]['bb'] = $rr;

            if( !empty($data[$key]['url_pdf']) ){
                $file = substr(strrchr($data[$key]['url_pdf'],"/"),1);

                if( file_exists(PATH_TRAVEL.$file) ){
                    $data[$key]['url_pdf'] = 'http://admin.probookingcenter.com/admin/upload/travel/'.$file;
                }
                else{
                    $data[$key]['url_pdf'] = '';
                }
            }


            $data[$key]['booking'] = $booking;

        }
        // print_r($data); die;
        return $data;
    }
    public function seatBooked($id)
    {
        $sth = $this->db->prepare("
            SELECT 
                  COALESCE(SUM(booking_list.book_list_qty),0) as booking
                , SUM(IF(booking.status=35, booking_list.book_list_qty, 0)) AS payed
                , SUM(IF(booking.status=10, booking_list.book_list_qty, 0)) AS invoice
            FROM booking_list LEFT JOIN booking ON booking.book_code=booking_list.book_code 
            WHERE 
                    booking.per_id=:id
                AND booking_list.book_list_code IN ('1','2','3')
                AND booking.status != 40
        ");
        $sth->execute( array( ':id'=> $id) );

        return $sth->fetch( PDO::FETCH_ASSOC );
        // print_r($fdata); die;
        // return !empty($fdata['booking']) ? $fdata['booking']: 0;
    }
}