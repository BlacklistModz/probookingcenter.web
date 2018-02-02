<?php

class Reports_Model extends Model{

    public function __construct() {
        parent::__construct();
    }
    /* REPORTS */
    public function listsBooking( $options=array() ){

        $data = array();
        $data['total_qty'] = 0;
        $data['total_receipt'] = 0;
        $data['total_master'] = 0;
        $data['total_balance'] = 0;
        $data['total'] = 0;

        $data['total_cancel'] = 0;
        $data['total_master_cancel'] = 0;
        $data['total_qty_cancel'] = 0;

        $field = "b.book_date
                  , b.book_code
                  , b.book_master_deposit
                  , b.book_master_full_payment
                  , b.book_receipt
                  , b.status

                  , ser.ser_name
                  , ser.ser_code

                  , (SELECT COALESCE(SUM(booking_list.book_list_qty),0) FROM booking_list WHERE booking_list.book_code=b.book_code AND booking_list.book_list_code IN ('1','2','3') ) as qty

                  , agen.agen_fname
                  , agen.agen_lname
                  , agen.agen_nickname

                  , ac.agen_com_name

                  , s.user_fname
                  , s.user_lname
                  , s.user_nickname";
        $table = "booking b 
                  LEFT JOIN period per ON b.per_id=per.per_id
                  LEFT JOIN series ser ON per.ser_id=ser.ser_id
                  LEFT JOIN agency agen ON b.agen_id=agen.agen_id
                  LEFT JOIN agency_company ac ON agen.agency_company_id=ac.agen_com_id
                  LEFT JOIN user s ON b.user_id=s.user_id";

        $options = array_merge( array(
            'date' => isset($_REQUEST["date"]) ? $_REQUEST["date"] : date("Y-m-d"),
        ),$options );

        $where_str = '';
        $where_arr = array();

        if( !empty($options["date"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "(book_date BETWEEN :s AND :e)";
            $where_arr[":s"] = date("Y-m-d 00:00:00", strtotime($options["date"]));
            $where_arr[":e"] = date("Y-m-d 23:59:59", strtotime($options["date"]));
        }
        if( !empty($options["country"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "ser.country_id=:country";
            $where_arr[":country"] = $options["country"];
        }
        if( !empty($options["series"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "per.ser_id=:series";
            $where_arr[":series"] = $options["series"];
        }
        if( !empty($options["sale"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "b.user_id=:sale";
            $where_arr[":sale"] = $options["sale"];
        }
        if( !empty($options["company"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "agen.agency_company_id=:company";
            $where_arr[":company"] = $options["company"];
        }
        if( !empty($options["agency"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "b.agen_id=:agency";
            $where_arr[":agency"] = $options["agency"];
        }
        if( $options["status"] != null ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "b.status=:status";
            $where_arr[":status"] = $options["status"];
        }

        $where_str = !empty($where_str) ? "WHERE {$where_str}" : "";
        $results = $this->db->select("SELECT {$field} FROM {$table} {$where_str}", $where_arr);
        foreach ($results as $key => $value) {
            $data['lists'][$key] = $value;
            $data['lists'][$key]['book_master'] = $value["book_master_deposit"] + $value["book_master_full_payment"];
            $data['lists'][$key]['book_balance'] = $data['lists'][$key]['book_master'] - $value["book_receipt"];
            $data['lists'][$key]['status_arr'] = $this->query('booking')->getStatus($value["status"]);

            $data['total_qty'] += $value["qty"];
            $data['total_receipt'] += $value["book_receipt"];
            $data['total_master'] += $data['lists'][$key]['book_master'];
            $data['total_balance'] += $data['lists'][$key]['book_balance'];

            if( $value["status"] == 40 ){
                $data['total_master_cancel'] += $data['lists'][$key]['book_master'];
                $data['total_qty_cancel'] += $value["qty"];
                $data['total_cancel']++;
            }

            $data['total']++;
        }

        $data['options'] = $options;
        return $data;
    }

    /* LIST FOR JSON */
    public function listsSeries( $country_id=null ){
    	$w = 'status IN (1,9)';
        $w_arr = array();

        if( !empty($country_id) ){
            $w .= !empty($w) ? " AND " : "";
            $w .= "country_id=:country";
            $w_arr[":country"] = $country_id;
        }

        $w = !empty($w) ? "WHERE {$w}" : "";

    	return $this->db->select("SELECT ser_id AS id , ser_name AS name , ser_code AS code FROM series {$w} ORDER BY ser_code ASC", $w_arr);
    }
    public function listsAgency($com_id){
        return $this->db->select("SELECT agen_id AS id, agen_fname AS fname, agen_lname AS lname, agen_nickname AS nickname FROM agency WHERE agency_company_id={$com_id}");
    }
}