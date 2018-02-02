<?php

class User extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(){
    	$this->error();
    }

    public function add(){
    	//empty($this->me) ||
    	if( $this->format!='json' || empty($this->me) ) $this->error();

    	$this->view->setData('group', $this->model->group());
    	$this->view->setPage('path', 'Themes/manage/forms/users');
    	$this->view->render('add');
    }
    public function edit($id=null){
    	$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
    	if( empty($id) || empty($this->me) || $this->format!='json' ) $this->error();

    	$item = $this->model->get($id);
    	if( empty($item) ) $this->error();

    	$this->view->setData('item', $item);
    	$this->view->setData('group', $this->model->group());
    	$this->view->setPage('path', 'Themes/manage/forms/users');
    	$this->view->render('add');
    }
    public function save(){
        if( empty($_POST) ) $this->error();

        $id = isset($_POST["id"]) ? $_POST["id"] : null;
        if( !empty($id) ){
            $item = $this->model->get($id);
            if( empty($item) ) $this->error();
        }

        try{
            $form = new Form();
            $form   ->post('user_fname')->val('is_empty')
                    ->post('user_lname')
                    ->post('user_nickname')
                    ->post('user_email')->val('email')
                    ->post('user_tel')
                    ->post('user_line_id')
                    ->post('user_address')
                    ->post('user_name')->val('username')
                    ->post('group_id')->val('is_empty')
                    ->post('status');
            $form->submit();
            $postData = $form->fetch();

            if( empty($item) ){
                if( empty($_POST["user_password"]) ){
                    $arr['error']['user_password'] = "กรุณากรอกรหัสผ่าน";
                }
                elseif( strlen($_POST["user_password"]) < 4 ){
                    $arr['error']['user_password'] = 'รหัสผ่านต้องมีความยาว 4 ตัวอักษรขึ้นไป';
                }
                else{
                    $postData['user_password'] = $_POST["user_password"];
                }
            }

            $has_user = true;
            $has_email = true;
            if( !empty($item) ){
                if( $item['name'] == $postData['user_name'] ) $has_user = false;
                if( $item['email'] == $postData['user_email'] ) $has_email = false;
            }
            if( $this->model->is_user($postData['user_name']) && $has_user ){
                $arr['error']['user_name'] = 'มีชื่อผู้ใช้นี้อยู่ในระบบแล้ว';
            }
            if( $this->model->is_email($postData['user_email']) && $has_email ){
                $arr['error']['user_email'] = 'มีอีเมลนี้อยู่ในระบบแล้ว';
            }

            if( empty($arr['error']) ){
                if( !empty($id) ){
                    $this->model->update($id, $postData);
                }
                else{
                    $postData["create_user_id"] = $this->me['id'];
                    $this->model->insert($postData);
                }
                $arr['message'] = 'บันทึกเรียบร้อย';
                $arr['url'] = 'refresh';
            }

        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        } 
        echo json_encode($arr);
    }
    public function del($id=null){
        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
        if( empty($id) || empty($this->me) || $this->format!='json' ) $this->error();

        $item = $this->model->get($id);
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){
            if( !empty($item['permit']['del']) ){
                $this->model->delete($id);
                $arr['message'] = 'ลบข้อมูลเรียบร้อย';
                $arr['url'] = 'refresh';
            }
            else{
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }
            echo json_encode($arr);
        }
        else{
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/users');
            $this->view->render('del');
        }
    }
    public function change_password($id=null){
        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
        if( empty($id) || empty($this->me) || $this->format!='json' ) $this->error();

        $item = $this->model->get($id);
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){

        }
        else{
            $this->view->setPage('path', 'Themes/manage/forms/users');
            $this->view->render('change_password');
        }
    }
}