<?php
 
 class Booking extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($book=null) {

        if( empty($this->me) || empty($book) ) $this->error();
        // print_r($this->me); die;
        // $this->error();

        $book = $this->model->get( $book );
        if( empty($book) ) $this->error();
        // print_r($book); die;

        $period = $book['per_id'];
        $item = $this->model->query('products')->period( $period );
        if( empty($item) ) $this->error();
        // print_r($item); die;
        $this->view->setData( 'busList', $this->model->query('products')->busList( $period ) );
        $this->view->setData( 'salesList', $this->model->query('products')->salesList( $period ) );

        // จำนวน ที่นั่ง ที่จองไปแล้ว
        $seatBooked = $this->model->query('products')->seatBooked( $period );
        $this->view->setData( 'seatBooked', $seatBooked );

        $this->view->setData( 'item', $item );
        $this->view->setData( 'book', $book );
        $this->view->render("booking/display");
    }

    public function register($period=null) {

        $period = isset($_REQUEST['period'])? $_REQUEST['period']: $period;
        if( empty($this->me) || empty($period) ) $this->error();

        $item = $this->model->query('products')->period( $period );
        if( empty($item) ) $this->error();
        // print_r($item); die;

        // จำนวน ที่นั่ง ที่จองไปแล้ว
        $seatBooked = $this->model->query('products')->seatBooked( $period );
        $availableSeat = $item['per_qty_seats']-$seatBooked['booking'];

        $settings = array(
            'trave' => array(
                'date' => date('Y-m-d', strtotime($item['per_date_start']))
            ),
            'deposit' => array(
                'date' => date('Y-m-d'),
                'price' => $item['ser_deposit'],
            ),
            
        );

        $DayOfGo = $this->fn->q('time')->DateDiff( date("Y-m-d"), $item['per_date_start'] );
        if( $DayOfGo > 30 ){
            $settings['deposit']['date'] = date("Y-m-d", strtotime("+2 day"));
        }

        $settings['trave']['date'] = date('Y-m-d', strtotime("-1 day", strtotime($settings['trave']['date'])));

        $settings['fullPayment']['date'] = date('Y-m-d', strtotime("-21 day", strtotime($settings['trave']['date'])));

        if( strtotime($settings['fullPayment']['date']) < strtotime(date('Y-m-d')) ){
            $settings['fullPayment']['date'] = date("Y-m-d", strtotime('tomorrow'));
            $settings['deposit']['date'] = '-';
            $settings['deposit']['price'] = 0;
        }

        if( !empty($_POST) ){

            $totalQty = 0;
            $status = $availableSeat<=0 ? '05': '00'; // 00 = จอง, 05=รอ
            $_SUM = array('subtotal'=>0, 'discount'=>0, 'total'=>0); $seats = array(); $n = 0;
            foreach ($_POST['seat'] as $key => $value) {
                $n ++;
                if( empty($value) ) $value = 0;
                // if( empty($value) ) continue;

                switch ($key) {
                    case 'adult': $name='Adult'; $price=$item['per_price_1']; break;
                    case 'child': $name='Child'; $price=$item['per_price_2']; break;
                    case 'child_bed': $name='Child No bed'; $price=$item['per_price_3']; break;
                    case 'infant': $name='Infant'; $price=$item['per_price_4']; break;
                    case 'joinland': $name='Joinland'; $price=$item['per_price_5']; break;
                    
                    default: $name=''; $price=0; break;
                }
                $total = $value * $price;
                $seats[] = array(
                    'book_list_code' => $n,
                    'book_list_name' => $name,
                    'book_list_price' => $price,
                    'book_list_qty' => $value,
                    'book_list_total' => $total,
                );

                if( in_array($key, array('adult', 'child', 'child_bed', 'joinland')) ){
                    $totalQty += $value;
                }

                $_SUM['subtotal'] += $total;
            }


            if( $totalQty>$availableSeat && $status=='00' ){
                $arr['error'] = 1;
                $arr['message'] = array('text'=>'ใส่จำนวนคนไม่ถูกต้อง!', 'auto'=>1, 'load'=>1, 'bg'=>'red') ;
            }
            else if( empty($seats) ){
                $arr['error'] = 1;
                $arr['message'] = array('text'=>'Please, Input seat!', 'auto'=>1, 'load'=>1, 'bg'=>'red') ;
            }
            else{

                /*-- get: prefixnumber --*/
                $prefixNumber = $this->model->prefixNumber();

                $booking = !empty($prefixNumber['pre_booking'])? intval($prefixNumber['pre_booking']): 1;
                $invoice = !empty($prefixNumber['pre_invoice'])? intval($prefixNumber['pre_invoice']): 1;
                $year = !empty($prefixNumber['pre_year'])? intval($prefixNumber['pre_year']): date('Y');
                $month = !empty($prefixNumber['pre_month'])? intval($prefixNumber['pre_month']): date('m');
                
               
                $running_booking = sprintf("%04s", $booking);
                $running_invoice = sprintf("%04s", $invoice);
                $month = sprintf("%02d", $month);
                $bookCode = "B{$year}/{$month}{$running_booking}";

                
                if( !empty($_POST['room']['single']) ){
                    $_SUM['subtotal'] += $_POST['room']['single']*$item['single_charge'];
                }

                $comOffice = $item['per_com_company_agency']*$totalQty;
                $comAgency = $item['per_com_agency']*$totalQty;

                $_SUM['discount'] = $comOffice + $comAgency;
                $_SUM['total'] = $_SUM['subtotal'] - $_SUM['discount'];

                $settings['deposit']['price'] *= $totalQty;

                /*-- insert: booking --*/
                $book = array(
                    "book_code"=>$bookCode, // running_booking
                    "book_date"=>date('c'), // date now
                    "invoice_code"=>"I{$year}/{$month}{$running_invoice}", // running_invoice
                    "invoice_date"=>date('c'), // date now
                    "agen_id"=>$this->me['id'], // login: id
                    "user_id"=>$_POST['sale_id'], // POST: sale_id
                    "per_id"=>$period, // period: id
                    "bus_no"=> isset($_POST['bus']) ? $_POST['bus']: 1,  // POST: bus

                    "book_total"=>$_SUM['total'], // SUM: total

                    "book_master_deposit"=>$settings['deposit']['price'], // จำนวนเงินที่ต้องมัดจำ Master
                    "book_due_date_deposit"=>$settings['deposit']['date'], // กำหนดจ่ายเงินมัดจำ
                    "book_master_full_payment"=>$_SUM['total']-$settings['deposit']['price'], // จำนวนเงินที่ต้องจ่ายเต็ม Master
                    "book_due_date_full_payment"=>$settings['fullPayment']['date'], // กำหนดจ่ายเงิน Full payment

                    "status"=> $status,
                    // "book_discount"=>'', // 0
                    "book_amountgrandtotal"=> $_SUM['total'], 
                    "book_comment"=>$_POST['comment'], // POST: comment

                    "book_com_agency_company"=>$comOffice,  // period: per_com_company_agency
                    "book_com_agency"=>$comAgency, // period: per_com_agency

                    "book_room_twin"=>$_POST['room']['twin'], 
                    "book_room_double"=>$_POST['room']['double'], 
                    "book_room_triple"=>$_POST['room']['triple'], 
                    "book_room_single"=>$_POST['room']['single'], 

                    "create_date"=>date('c'),
                );
                // print_r($book); die;
                $this->model->insert($book);


                /*-- insert: booking_list --*/
                foreach ($seats as $key => $value) {
                    $value['book_code'] = $bookCode;
                    $value['create_date'] = date('c');

                    $this->model->detailInsert($value);
                }

                /* -- update: prefixnumber -- */
                $this->model->prefixNumberUpdate( 1, array(
                    'pre_booking' => $booking+1,
                    'pre_invoice' => $invoice+1
                ) );


                $arr['message'] = 'Thank You.';
                $arr['url'] = URL.'booking/'.$book['id'];
                // $arr['url'] = URL;
            }

           echo json_encode( $arr );
           die;
        }
        else{
            $this->view->setData( 'busList', $this->model->query('products')->busList( $period ) );
            $this->view->setData( 'salesList', $this->model->query('products')->salesList( $period ) );

            
            $this->view->setData( 'seatBooked', $seatBooked );
            
            // print_r($seatBooked); die;

            $this->view->setData( 'item', $item );
            $this->view->setData( 'settings', $settings );


            $this->view->setPage('title', 'จองทัวร์ - ' .  $item['name']);
            $this->view->render("booking/register");
        }

    }


    public function save()
    {
        $arr['message'] = 'Saved.';
        $arr['url'] = 'refresh';
        echo json_encode($arr);
    }

    public function booking_cancel($id=null){
        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
        if( empty($this->me) || empty($id) || $this->format!='json' ) $this->error();

        $item = $this->model->get($id);
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){
            
            $this->model->update($id, array('status'=>40));
            $this->model->updateWaitingList( $item['per_id'] );

            if( $item['permit']['cancel'] ){
                $arr['message'] = 'ยกเลิกการจองเรียบร้อย';
                $arr['url'] = 'refresh';
            }
            else{
                $arr['message'] = 'ไม่สามารถยกเลิกได้ กรุณาติดต่อทาง ProbookingCenter';
            }
            echo json_encode($arr);
        }
        else{
            $this->view->setData('item', $item);
            $this->view->render('forms/booking/cancel');
        }
    }

}