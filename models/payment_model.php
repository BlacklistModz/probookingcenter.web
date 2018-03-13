<?php

class Payment_Model extends Model{

    public function __construct() {
        parent::__construct();
    }
    private $_objType = "payment";
    private $_table = "payment p
                       LEFT JOIN booking b on p.book_id=b.book_id
                       LEFT JOIN period per on per.per_id=b.per_id
                       LEFT JOIN series s on per.ser_id = s.ser_id 
                                ";
    private $_field = "p.*
                    ,per.per_id
                    ,per.per_date_start
                    ,per.per_date_end
                    ,s.ser_code        
                    ,b.invoice_code    
                        ";
    private $_cutNamefield = "pay_";

    public function insert(&$data){
    	$data["create_date"] = date("c");
    	$this->db->insert($this->_objType, $data);
    	$data["id"] = $this->db->lastInsertId();
    }
    public function update($id, $data){
    	$data["update_date"] = date("c");
    	$this->db->update($this->_objType, $data, "{$this->_cutNamefield}id={$id}");
    }
    public function delete($id){
    	$this->db->delete($this->_objType, "{$this->_cutNamefield}id={$id}");
    }

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

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( 'p.'.$options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr ), $options );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        $arr['options'] = $options;

        return $arr;
    }
    public function buildFrag($results, $options=array()) {
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert( $value, $options );
        }

        return $data;
    }
    public function get($id, $options=array()){
        
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE {$this->_cutNamefield}id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        if( $sth->rowCount()==1 ){
            return $this->convert( $sth->fetch( PDO::FETCH_ASSOC ), $options );
        } return array();
    }
    public function convert($data, $options=array()){
        $data = $this->_cutFirstFieldName($this->_cutNamefield, $data);
        $data['permit']['del'] = true;
        $data['status'] = $this->getStatus($data['status']);
        $data['book_status'] = $this->getBookStatus($data['book_status']);
        $data['useraction'] = $this->getUser($data['user_action']);
        return $data;
    }

    public function status(){
        $a[] = array('id'=>0, 'name'=>'รอการตรวจสอบ');
        $a[] = array('id'=>1, 'name'=>'ผ่านการตรวจสอบ');
        $a[] = array('id'=>9, 'name'=>'ไม่ผ่านการตรวจสอบ');

        return $a;
    }
    public function getStatus($id){
        $data = array();
        foreach ($this->status() as $key => $value) {
            if( $id == $value["id"] ){
                $data = $value;
                break;
            }
        }
        return $data;
    }
    public function getBookStatus($id){
        $data = array();
        foreach ($this->Bookstatus() as $key => $value) {
            if( $id == $value["id"] ){
                $data = $value;
                break;
            }
        }
        return $data;
    }
    public function Bookstatus(){
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
    public function getUser($text){
        $sth = $this->db->prepare("SELECT user_nickname, user_fname, user_lname FROM user WHERE user_name=:username LIMIT 1");
        //return($sth);die;
        $sth->execute( array(
            ':username' => $text
        ) );
        
        $fdata = $sth->fetch( PDO::FETCH_ASSOC ) ;

        if( $sth->rowCount()==1 ){
            return "{$fdata["user_fname"]} {$fdata["user_lname"]}";
        } return "";
    }
    public function getAgency($text){
        $sth = $this->db->prepare("SELECT agen_fname,agen_nickname, agen_lname FROM agency WHERE agen_user_name=:username LIMIT 1");
        $sth->execute( array(
            ':username' => $text
        ) );

        $fdata = $sth->fetch( PDO::FETCH_ASSOC ) ;

        if( $sth->rowCount()==1 ){
            return "{$fdata["agen_fname"]} {$fdata["agen_lname"]}";
        } return "";
    }
    public function getAgencyCompany($text){
      $sth = $this->db->prepare("SELECT agen_com_name FROM agency_company WHERE agen_com_id = :id LIMIT 1");
      $sth->execute(
          array(
              ':id'=>$text
          )
        );

      $fdata = $sth->fetch( PDO::FETCH_ASSOC );

          if( $sth->rowCount()==1 ){
            return $fdata["agen_com_name"];
        } return "";
    } 
    
    
}
