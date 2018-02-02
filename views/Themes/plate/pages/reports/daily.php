<?php

$tbody = '';

$total_master = 0;
$total_receipt = 0;
$total_balance = 0;
$sum = 0;

foreach ($this->book['lists'] as $key => $value) {

	$sum++;

	$date = date("d", strtotime($value["book_date"]));
	$month = $this->fn->q('time')->month( date("n", strtotime($value["book_date"])) );
	$year = date("Y", strtotime($value["book_date"]))+543;

	$total_master += $value["book_master"];
	$total_receipt += $value["book_receipt"];
	$total_balance += $value["book_balance"];

	$dateStr = "{$date} {$month} {$year}";
	$tbody .= '<tr>
					<td style="text-align:center;">'.$dateStr.'</td>
					<td style="text-align:center;">'.$value["book_code"].'</td>
					<td style="text-align:center;">'.$value["ser_code"].'</td>
					<td style="text-align:center;">'.$value["qty"].'</td>
					<td style="text-align:center;">'.$value["agen_com_name"].' '.'('.$value["agen_fname"].' '.$value["agen_lname"].')'.'</td>
					<td style="text-align:center;">'.number_format($value["book_master"]).'</td>
					<td style="text-align:center;">'.number_format($value["book_receipt"]).'</td>
					<td style="text-align:center;">'.number_format($value["book_balance"]).'</td>
					<td style="text-align:center;">'.$value["user_fname"].' '.$value["user_lname"].'</td>
					<td style="text-align:center;">'.$value["status_arr"]["name"].'</td>
				</tr>';
}

$html = '<h3 class="fwb tac">รายงาน</h3>
		<div class="clearfix">
			<table class="table-standard" width="100%">
				<thead>
					<tr style="background-color:#003;">
						<th style="color:#fff; text-align:center;" width="10%">วันที่จอง</th>
						<th style="color:#fff; text-align:center;" width="7%">รหัส</th>
						<th style="color:#fff; text-align:center;" width="7%">CODE</th>
						<th style="color:#fff; text-align:center;" width="6%">จำนวน</th>
						<th style="color:#fff; text-align:center;" width="15%">Agency</th>
						<th style="color:#fff; text-align:center;" width="10%">รวม</th>
						<th style="color:#fff; text-align:center;" width="10%">จ่ายแล้ว</th>
						<th style="color:#fff; text-align:center;" width="10%">คงเหลือ</th>
						<th style="color:#fff; text-align:center;" width="15%">เซลล์</th>
						<th style="color:#fff; text-align:center;" width="10%">สถานะ</th>
					</tr>
				</thead>
				<tbody>
					'.$tbody.'
				</tbody>
			</table>
 		 </div>';

$content = '<!doctype html><html lang="th"><head><title id="pageTitle">plate</title><meta charset="utf-8" /></head><body style="font-size: 10pt;">'.$html.'</body></html>';

// echo $content;
$mpdf = new mPDF('th', 'A4-L', '0');
$mpdf->debug = true;
$mpdf->allow_output_buffering = true;

$mpdf->charset_in='UTF-8';
$mpdf->allow_charset_conversion = true;
$mpdf->list_indent_first_level = 0;

// $stylesheet = file_get_contents(CSS . 'bootstrap.css');
// $mpdf->WriteHTML($stylesheet,1);

$stylesheet2 = file_get_contents(VIEW.'Themes/plate/assets/css/main.css');
$mpdf->WriteHTML($stylesheet2,1);

// $content = iconv('UTF-8', 'windows-1252', $content);
// $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

ob_clean();
$mpdf->SetDisplayMode('fullpage');
$mpdf->SetTitle('Report-Booking-Daily');
$mpdf->WriteHTML( $content );
$mpdf->Output('report-booking-'.date("d_m_Y", strtotime($this->book["options"]["date"])).'.pdf','I');