<?php
//print_r($this->results['lists']); die;
$tr = "";
$tr_total = "";

if( !empty($this->results['lists']) ){ 
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) { 
        // print_r($item);die;
        // $item = $item;

        $period = '';
        if( !empty($item['period']) ){
            $num = 0;
            foreach ($item['period'] as $per) {
                $num++;
                $period .= '<tr class="no-hover">
                                <td>'.$num.'</td>
                                <td style="text-align:center;"></td>
                                <td style="text-align:center;">'.$this->fn->q('time')->str_event_date($per["date_start"], $per["date_end"]).'</td>
                                <td style="text-align:center; background-color:#51c6ea; color:#fff;" class="fwb">'.number_format($per['price_1']).'</td>
                                <td style="text-align:center;">'.number_format($per["qty_seats"]).'</td>
                                <td style="text-align:center;">'.number_format($per["booking"]["booking"]).'</td>
                                <td style="text-align:center;">'.number_format($per['balance']).'</td>
                                <td style="text-align:center;">'.number_format($per['booking']["payed"]).'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>';
            }
        }

        $cls = $i%2 ? 'even' : "odd";

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'">'.

            '<td class="name">
                <div style="color:#f532e5;" class="fwb">'.$item['code'].' - '.$item['name'].'</div>'.
                '<div class="mts uiBoxWhite pas">
                    <table class="table-bordered">
                        <thead>
                            <tr style="color:#fff; background-color:#003;">
                                <th style="width:1%;">#</th>
                                <th style="width:2%;">สถานะ</th>
                                <th style="width:10%;">เดินทาง</th>
                                <th style="width:2%;">ราคา</th>
                                <th style="width:2%;">ที่นั่ง</th>
                                <th style="width:2%;">จอง</th>
                                <th style="width:2%;">รับได้</th>
                                <th style="width:2%;">FP</th>
                                <th style="width:40%;">Booking</th>
                                <th style="width:20%;">W/L</th>
                                <th style="width:8%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            '.$period.'
                        </tbody>
                    </table>
                </div>'.
            '</td>'.

            // '<td class="email" style="text-align:center;"><span class="agen_status_'.$item['status'].'">'.$item['agen_status']['name'].'</span></td>'.
            // '<td class="username" style="text-align:center;">'.$item['user_name'].'</td>'.
            // '<td class="name">'.$item['fullname'].'</td>'.
            // '<td class="contact">'.$item['company_name'].'</td>'.
            // '<td class="email">'.$item['email'].'</td>'.
            // '<td class="phone" style="text-align:center;">'.$item['tel'].'</td>'.
            // '<td class="status fwb">'.strtoupper($item['role']).'</td>'.
        '</tr>';
        
    }
  
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';