<?php

class Me extends Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        
        // print_r($this->me); die;
        $this->error();
        // header('location:'.URL.'manage/products');
    }

    public function navTrigger() {
        if( $this->format!='json' ) $this->error();
        

        if( isset($_REQUEST['status']) ){

            Session::init();                          
            Session::set('isPushedLeft', $_REQUEST['status']);
        }
    }

    /* updated */
    /**/
    public function updated($avtive='') {
        if( empty($_POST) || $this->format!='json' || $avtive=="" ) $this->error();
        
        /**/
        /* account */
        if( $avtive=='account' ){
            try {
                $form = new Form();
                $form   ->post('user_name')->val('username');

                $form->submit();
                $dataPost = $form->fetch();

                if( $this->model->query('user')->is_user( $dataPost['user_name'] ) && $this->me['name']!=$dataPost['user_name'] ){
                    $arr['error']['user_name'] = 'ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว';
                }

                // Your username must be longer than 4 characters.

                if( empty($arr['error']) ){

                    $this->model->query('user')->update( $this->me['id'], $dataPost );
  
                    $arr['url'] = 'refresh';
                    $arr['message'] = 'Thanks, your settings have been saved.';
                }
                
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
            exit;
        }
        /**/
        /* basic */
        else if( $avtive=='basic' ){

            try {
                $form = new Form();
                $form   ->post('user_fname')->val('maxlength', 45)->val('is_empty')
                        ->post('user_lname')
                        ->post('user_nickname')
                        ->post('user_email')
                        // ->post('user_color')
                        ->post('user_mode');

                $form->submit();
                $dataPost = $form->fetch();

                if( empty($arr['error']) ){

                    $this->model->query('user')->update( $this->me['id'], $dataPost );
  
                    $arr['url'] = 'refresh';
                    $arr['message'] = 'Thanks, your settings have been saved.';
                }
                
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
            exit;
        }

        /**/
        /* password */
        if( $avtive=='password' ){

            $data = $_POST;
            $arr = array();
            if( !$this->model->query('users')->login($this->me['name'], $data['password_old']) ){
                $arr['error']['password_old'] = "รหัสผ่านไม่ถูกต้อง";
            } elseif ( strlen($data['password_new']) < 6 ){
                $arr['error']['password_new'] = "รหัสผ่านสั้นเกินไป อย่างน้อย 6 ตัวอักษรขึ้นไป";

            } elseif ($data['password_new'] == $data['password_old']){
                $arr['error']['password_new'] = "รหัสผ่านต้องต่างจากรหัสผ่านเก่า";

            } elseif ($data['password_new'] != $data['password_confirm']){
                $arr['error']['password_confirm'] = "คุณต้องใส่รหัสผ่านที่เหมือนกันสองครั้งเพื่อเป็นการยืนยัน";
            }

            if( !empty($arr['error']) ){
                $this->view->error = $arr['error'];
            }
            else{
                $this->model->query('user')->update($this->me['id'], array(
                    'user_password' => substr(md5(trim($data['password_new'])),0,20),
                ));

                $arr['url'] = 'refresh';
                $arr['message'] = 'Thanks, your settings have been saved.';
            }

            echo json_encode($arr);
            exit;
        }

        $this->error();
    }

}