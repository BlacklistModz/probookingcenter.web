<?php

$tr = "";
$tr_total = "";
$url = URL .'student/';
if( !empty($this->results['lists']) ){ 

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) { 

        // $item = $item;
        $cls = $i%2 ? 'even' : "odd";
        // set Name

        $subtext = '';

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'">'.

            // '<td class="check-box"><label class="checkbox"><input id="toggle_checkbox" type="checkbox" value="'.$item['id'].'"></label></td>'.

            // '<td class="bookmark"><a class="ui-bookmark js-bookmark'.( $item['bookmark']==1 ? ' is-bookmark':'' ).'" data-value="" data-id="'.$item['id'].'" stringify="'.URL.'customers/bookmark/'.$item['id']. (!empty($this->hasMasterHost) ? '?company='.$this->company['id']:'') .'"><i class="icon-star yes"></i><i class="icon-star-o no"></i></a></td>'.

            '<td class="name">'.

                '<div class="anchor clearfix">'.
                    
                    '<div class="content"><div class="spacer"></div><div class="massages">'.

                        '<div class="fullname"><a class="fwb">'. $item['name'].'</a></div>'.

                        '<div class="subname fsm fcg meta">'.$subtext.'</div>'.

                        // '<div class="fss fcg whitespace">Last update: '.$this->fn->q('time')->live( $item['updated'] ).'</div>'.
                    '</div>'.
                '</div></div>'.

            '</td>'.

            '<td class="actions whitespace">
                <span class="gbtn"><a data-plugins="dialog" href="'.$url.'edit_faculty/'.$item['id'].'" class="btn btn-no-padding"><i class="icon-pencil"></i></a></span>
                <span class="gbtn"><a data-plugins="dialog" href="'.$url.'del_faculty/'.$item['id'].'" class="btn btn-no-padding"><i class="icon-trash"></i></a></span>
            </td>'.
              
        '</tr>';
        
    }
}

$table = '<table class="settings-table"><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';