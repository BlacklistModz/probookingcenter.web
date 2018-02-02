<?php

class Booking_Model extends Model {


	public function __construct()
	{
		parent::__construct();
	}

    private $_objName = "booking";
    private $_table = "booking b 
                       LEFT JOIN agency ag ON b.agen_id=ag.agen_id
                       LEFT JOIN period per ON b.per_id=per.per_id
                       LEFT JOIN series ser ON per.ser_id=ser.ser_id";
    private $_field = "b.*
                       , ag.agen_fname
                       , ag.agen_lname
                       , ag.agen_position

                       , per.per_date_start
                       , per.per_date_end

                       , ser.ser_id
                       , ser.ser_name
                       , ser.ser_code";
    private $_cutNamefield = "book_";

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

        if( !empty($options["company"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "ag.agency_company_id=:company";
            $where_arr[":company"] = $options["company"];
        }

        if( !empty($options["agency"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "b.agen_id=:agency";
            $where_arr[":agency"] = $options["agency"];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        if( !empty($options["unlimit"]) ) $limit = '';
        $orderby = $this->orderby( $options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr ), $options );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        $arr['options'] = $options;

        return $arr;
    }

	public function prefixNumber()
	{
		$sth = $this->db->prepare("SELECT * FROM prefixnumber LIMIT 1");
        $sth->execute();

        return $sth->fetch( PDO::FETCH_ASSOC );
	}
	public function prefixNumberUpdate($id, $data) {
		$this->db->update('prefixnumber', $data, "`prefix_id`={$id}");
	}


	public function get($id, $options=array())
	{
		$sth = $this->db->prepare("SELECT * FROM booking WHERE book_id=:id LIMIT 1");
        $sth->execute( array( ':id' => $id ) );

        if( $sth->rowCount()==1 ){
            return $this->convert( $sth->fetch( PDO::FETCH_ASSOC ), $options );
        } return array();
	}
	public function insert(&$data){
    	$this->db->insert('booking', $data);
    	$data['id'] = $this->db->lastInsertId();
    }
    public function update($id, $data){
        $this->db->update($this->_objName, $data, "{$this->_cutNamefield}id={$id}");
    }
    public function buildFrag($results, $options=array()) {
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert( $value, $options );
        }

        return $data;
    }
    public function convert($data, $options=array()){

    	// $data = $this->_cutFirstFieldName($this->_cutNamefield, $data);
        
        $total_qty = 0;
        $booking_list = $this->db->select("SELECT * FROM booking_list WHERE `book_code`=:code ORDER BY book_list_code ASC", array(':code'=>$data['book_code']));
        $items = array();
        foreach ($booking_list as $key => $value) {
        	$items[$value['book_list_code']] = $value;
            if( $value['book_list_code'] == 4 || $value['book_list_code'] == 5 ) continue;
            $total_qty += $value["book_list_qty"];
        }
        $data['book_qty'] = $total_qty;
        $data['book_status'] = $this->getStatus($data['status']);
        $data['items'] = $items;

        $data["permit"]["cancel"] = false;
        if( $data["status"] == 0 || $data["status"] == 5 ){
            $data["permit"]["cancel"] = true;
        }

        return $data;
    }


    public function detailInsert(&$data){
    	$this->db->insert('booking_list', $data);
    	$data['id'] = $this->db->lastInsertId();
    }

    /* STATUS */
    public function status(){
        $a[] = array('id'=>0, 'name'=>'จอง', 'detail'=>"จอง");
        $a[] = array('id'=>10, 'name'=>'แจ้ง Quotation', 'detail'=>"แจ้ง quatation");
        $a[] = array('id'=>20, 'name'=>'มัดจำ(บางส่วน)', 'detail'=>"มัดจำบางส่วน");
        $a[] = array('id'=>25, 'name'=>'มัดจำ', 'detail'=>"มัดจำต็มจำนวน");
        $a[] = array('id'=>30, 'name'=>'ชำระเต็มจำนวน (บางส่วน)', 'detail'=>"ชำระเต็มจำนวน บางส่วน");
        $a[] = array('id'=>35, 'name'=>'ชำระเต็มจำนวน', 'detail'=> "ชำระเต็มจำนวน แบบเต็มจำนวน");
        $a[] = array('id'=>40, 'name'=>'ยกเลิก', "detail"=> "Cancel");
        $a[] = array('id'=>50, 'name'=>'จอง/WL', "detail"=> "จอง/Waiting");
        $a[] = array('id'=>5, 'name'=>'Waiting List', 'detail'=>"Waiting List");
        $a[] = array('id'=>55, 'name'=>'แจ้งชำระเงิน', 'detail'=>"แจ้งชำระเงิน");

        return $a;
    }
    public function getStatus($id){
        $data = array();
        foreach ($this->status() as $key => $value) {
            if( $id == $value['id'] ){
                $data = $value;
                break;
            }
        }
        return $data;
    }

    public function updateWaitingList($per_id){
        /* GET Waiting List */
        $waiting = $this->db->select("SELECT book_id,user_id,COALESCE(SUM(booking_list.book_list_qty)) AS qty FROM booking LEFT JOIN booking_list ON booking.book_code=booking_list.book_code WHERE per_id={$per_id} AND status=5 ORDER BY booking.create_date ASC");
        if( !empty($waiting) ){
            /* จำนวนทีนั่งทั้งหมด */
            $seats = $this->db->select("SELECT per_qty_seats FROM period WHERE per_id={$per_id} LIMIT 1");

            /* จำนวนคนจองทั้งหมด (ตัด Waiting กับ ยกเลิกแล้ว) */
            $book = $this->db->select("SELECT COALESCE(SUM(booking_list.book_list_qty),0) as qty FROM booking_list
                    LEFT JOIN booking ON booking_list.book_code=booking.book_code
                  WHERE booking.per_id={$per_id} AND booking.status!=5 AND booking.status!=40");
            $BalanceSeats = $seats[0]["per_qty_seats"] - $book[0]["qty"];
            if( $BalanceSeats > 0 ){
                foreach ($waiting as $key => $value) {
                    if( !empty($BalanceSeats) ){
                        if( $value["qty"] <= $BalanceSeats ){
                            /* SET STATUS BOOKING */
                            $this->db->update("booking", array("status"=>"00"), "book_id={$value["book_id"]}");
                            $BalanceSeats -= $value["qty"];
                        }
                    }
                    else{
                        if( $BalanceSeats > 0 ){
                            /* SET STATUS BOOKING */
                            $this->db->update("booking", array("status"=>"00"), "book_id={$value["book_id"]}");

                            /* SET ALERT FOR SALE */
                            $alert = array(
                                "user_id"=>$value["user_id"],
                                "book_id"=>$value["book_id"],
                                "detail"=>"ที่นั่งไม่เพียงพอ",
                                "source"=>"150booking",
                                "log_date"=>date("c")
                            );
                            $this->db->insert("alert_msg", $alert);

                            /* EXIT LOOP */
                            $BalanceSeats = 0;
                            break;
                        }
                    }
                }
            }
        }
    }
}