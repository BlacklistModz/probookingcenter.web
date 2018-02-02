<?php
$html = '';
$content = '<!doctype html><html lang="th"><head><title id="pageTitle">plate</title><meta charset="utf-8" /></head><body>'.$html.'</body></html>';

// echo $content;
$mpdf = new mPDF('th', 'A4', '0');
$mpdf->debug = true;
$mpdf->allow_output_buffering = true;

$mpdf->charset_in='UTF-8';
$mpdf->allow_charset_conversion = true;
$mpdf->list_indent_first_level = 0;

// $stylesheet = file_get_contents(CSS . 'bootstrap.css');
// $mpdf->WriteHTML($stylesheet,1);

// $stylesheet2 = file_get_contents(VIEW.'Themes/plate/assess/css/main.css');
// $mpdf->WriteHTML($stylesheet2,1);

// $content = iconv('UTF-8', 'windows-1252', $content);
// $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

ob_clean();
$mpdf->SetTitle('Report-Booking-Daily');
$mpdf->WriteHTML( $content );
$mpdf->Output('report-booking-'.$this->date.'.pdf','I');