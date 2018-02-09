<?php

$this->count_nav = 0;

/* System */
$sub = array();
$sub[] = array('text' => 'Daily Booking Report','key' => 'daily','url' => $this->pageURL.'reports/booking/daily');
$sub[] = array('text' => 'Monthy Booking Report','key' => 'monthy','url' => $this->pageURL.'reports/booking/monthy');

// foreach ($sub as $key => $value) {
// 	if( empty($this->permit[$value['key']]['view']) ) unset($sub[$key]);
// }
if( !empty($sub) ){
	$this->count_nav+=count($sub);
	$menu[] = array('text' => 'Booking Reports', 'url' => $this->pageURL.'reports/daily', 'sub' => $sub);
}


$sub = array();
$sub[] = array('text' => 'Daily Payment Report','key' => 'pay_daily','url' => $this->pageURL.'reports/payment/pay_daily');
if( !empty($sub) ){
	$this->count_nav+=count($sub);
	$menu[] = array('text' => 'Payment Reports', 'url' => $this->pageURL.'reports/payment', 'sub' => $sub);
}
