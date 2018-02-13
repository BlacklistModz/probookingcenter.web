<?php
//print_r($this->results['lists']); die;
$tr = "";
$tr_total = "";

if( !empty($this->results['lists']) ){ 
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) { 
       print_r($item);die;
        // $item = $item;
        //print_r($item);die;
        $file = substr(strrchr($item['url_file'],"/"),1);
        $cls = $i%2 ? 'even' : "odd";

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'">'.
            '<td class="status" stlye="text-align:center;">'.($i+1).' </td>'.
            '<td class="status_th" style="text-align:center;"><span class="book_status_'.$item['book_status']['id'].'">'.$item['book_status']['name'].'</span></td>'.
            '<td class="status_th" style="text-align:center;"><span class="agen_status_'.$item['status']['id'].'">'.$item['status']['name'].'</span></td>'.
            '<td class="status" style="text-align:center;"><a href="http://admin.probookingcenter.com/admin/upload/payment/'.$file.'"><i class="icon-file-pdf-o"></i><a></td>'.
             '<td class="status">'.date($item['time']).'</td>'.
             '<td>'.$item['received'].'</td>'.
            // '<td class="contact">'.$item['company_name'].'</td>'.
            // '<td class="email">'.$item['email'].'</td>'.
            // '<td class="phone" style="text-align:center;">'.$item['tel'].'</td>'.
            // '<td class="status fwb">'.strtoupper($item['role']).'</td>'.

            // '<td class="actions whitespace">'.
            //     '<span class="gbtn">
            //         <a data-plugins="dialog" href="'.URL.'/agency/password/'.$item['id'].'" class="btn btn-no-padding btn-green"><i class="icon-key"></i></a>
            //     </span>'.
            //     '<span class="gbtn">
            //         <a data-plugins="dialog" href="'.URL.'/agency/_edit/'.$item['id'].'" class="btn btn-no-padding btn-blue"><i class="icon-pencil"></i></a>
            //     </span>'.
            //     '<span class="gbtn">
            //         <a data-plugins="dialog" href="'.URL.'/agency/_del/'.$item['id'].'" class="btn btn-no-padding btn-red"><i class="icon-trash"></i></a>
            //     </span>'.
            '</td>'.
        '</tr>';
        
    }
  
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';