<?php

class Agency_company_model extends Model  {

	public function __construct()
	{
		parent::__construct();
	}

	private $_objType = "agency_company";
	private $_table = "agency_company";
	private $_field = "*";
	private $_cutNamefield = "agen_";

	public function lists($options=array()){
    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'more' => true,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'com_id',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'ASC',
            
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,

        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "";
        $where_arr = array();

        if( !empty($options['q']) ){

            $where_str .= !empty( $where_str ) ? " AND ":'';
            $where_str .= "(agen_com_name LIKE :q OR agen_com_tel=:q)";
            $where_arr[':q'] = "%{$options['q']}%";;
            $where_arr[':qfull'] = $options['q'];
        }

        if( !empty($options["status"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "status=:status";
            $where_arr[":status"] = $options["status"];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( $this->_cutNamefield.$options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        if( !empty($options["unlimit"]) ) $limit = "";
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

        return $data;
    }

	public function insert($data)
	{
		$data['create_date'] = date('c');
		// $data['status'] = 1;
		// $data['agen_show'] = 1;

		$this->db->insert($this->_objType, $data);
		return $this->db->lastInsertId();
	}
	public function update($id, $data){
		$data['update_date'] = date("c");
		$this->db->update($this->_objType, $data, "{$this->_cutNamefield}id={$id}");
	}
	public function delete($id){
		$this->db->delete($this->_objType, "{$this->_cutNamefield}id={$id}");
	}

}