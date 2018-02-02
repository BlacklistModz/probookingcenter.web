<section id="product" class="module parallax product" style="padding-top: 180px; background-image: url(<?=IMAGES?>/demo/curtain/curtain-3.jpg)">

	<div class=" container clearfix">
		<div class="primary-content post">
			<div class="card">
				<header class="header clearfix">
					<h1 class="tac"><i class="icon-book"></i> Booking History</h1>
					<h3 class="tac">บริษัท : <?=$this->me['company_name']?></h3>
				</header>
				<ul class="rfloat mbm" ref="control">
					<li>
						<label for="status" class="label fwb">เซลล์ : </label>
						<select ref="selector" class="inputtext" name="agency" style="display:inline;">
							<option value="">-- ทั้งหมด --</option>
							<?php foreach ($this->sales['lists'] as $key => $value) {
								$sel = '';
								if( $this->agen_id == $value["id"] ) $sel = ' selected="1"';
								echo '<option'.$sel.' value="'.$value["id"].'">'.$value["fullname"].'</option>';
							} ?>
						</select>		
					</li>
				</ul>
				<div class="clearfix">
					<table class="table-bordered"  style="color:#000; overflow-x:auto; display: block; width: 100%;-webkit-overflow-scrolling: touch;  -ms-overflow-style: -ms-autohiding-scrollbar;">
						<thead>
							<tr style="color:#fff; background-color: #003;">
								<th width="10%">วันที่</th>
								<th width="8%">รหัส</th>
								<th width="5%">CODE</th>
								<th width="5%">ที่นั่ง</th>
								<th width="7%">ยอดสุทธิ</th>
								<th width="10%">Deposit Date</th>
								<th width="10%">Full Payment Date</th>
								<th width="10%">เซลล์</th>
								<th width="10%">สถานะ</th>
								<th width="10%">แจ้งโอนเงิน</th>
								<th width="10%">ยกเลิก</th>
							</tr>
						</thead>
						<tbody>
							<?php if( !empty($this->results["lists"]) ) { 
								foreach ($this->results["lists"] as $key => $value) {
									$dayStr = date("d", strtotime($value["book_date"]));
									$monthStr =  $this->fn->q('time')->month( date("n", strtotime($value["book_date"])) );
									$yearStr = date("Y", strtotime($value["book_date"])) + 543;

									$timeStr = date("H:i:s", strtotime($value["book_date"]));
									$dateTime = "{$dayStr} {$monthStr} {$yearStr}";
									//print_r($value); die;
									$fullPaymentStr = "-";
									$DepositStr = "-";
									$fullPaymenting ="";
									$deposited="";
									
									if( $value["book_due_date_full_payment"] != "0000-00-00 00:00:00" ){
										$fDaystr = date("d", strtotime($value['book_due_date_full_payment']));
										$fMonthStr = $this->fn->q('time')->month( date("n", strtotime($value["book_due_date_full_payment"])) );
										$fYearStr = date("Y", strtotime($value["book_due_date_full_payment"])) + 543;
										$fullPaymentStr = "{$fDaystr} {$fMonthStr} {$fYearStr}";
										
									}

									if( $value["book_due_date_deposit"] != "0000-00-00 00:00:00" ){
										$dDaystr = date("d", strtotime($value['book_due_date_deposit']));
										$dMonthStr = $this->fn->q('time')->month( date("n", strtotime($value["book_due_date_deposit"])) );
										$dYearStr = date("Y", strtotime($value["book_due_date_deposit"])) + 543;
										$DepositStr = "{$dDaystr} {$dMonthStr} {$dYearStr}";
									}
										$deposited = $value['book_master_deposit'];
										$fullPaymenting = $value["book_master_full_payment"];
								
									
									?>
									<tr>
										<td class="tac"><?=$dateTime?><br/><?=$timeStr?></td>
										<td class="tac"><a target="_blank" style="color:blue; text-decoration: none;" href="<?=URL?>booking/<?=$value["book_id"]?>"><?=$value["book_code"]?></a></td>
										<td class="tac">
											<a href="<?=URL?>tour/<?=$value["ser_id"]?>" style="color:blue; text-decoration: none;" target="_blank">
											<span class="fwb"><?=$value['ser_code']?></a>
										</td>
										<td class="tac"><?=$value["book_qty"]?></td>
										<td class="tar" style="padding-right: 2mm;"><?=number_format($value['book_amountgrandtotal'])?></td>
										<td class="tac"><?=$DepositStr?><?= $deposited ==0? "" :'<br>'.'<span class="fwb status_95">('. number_format($deposited).')</span>'   ?></td>
										<td class="tac"><?=$fullPaymentStr?><?=$fullPaymenting ==0? "" : '<br>'.'<span class="fwb status_96">('. number_format($fullPaymenting).')</span>'?> </td>
										<td class="tac"><?=$value['agen_fname']?></td>
										<td class="tac">
											<span class="fwb fz_11 status_<?=$value['status']?>"><?=$value["book_status"]['name']=="Full payment"?"FP":$value["book_status"]['name']?></span>
										</td>
										<td class="tac">
											<?php
											echo '<span>N/A</span>'; 
											// if( $value['status'] == 35 || $value['status'] == 40 || $value['status'] == 50 ){
											// 	echo '<a class="btn btn-blue disabled">LOCK</a>';
											// }
											// else{
											// 	echo '<a href="'.URL.'booking/payment/'.$value['book_id'].'" class="btn btn-blue" data-plugins="dialog">แจ้งโอนเงิน</a>';
											// }
											?>
										</td>
										<td class="tac">
											<?php 
											if( ($value['status'] == 0 || $value['status'] == 5) && $value['agen_id'] == $this->me['id'] ) {
												echo '<a href="'.URL.'booking/booking_cancel/'.$value['book_id'].'" class="btn btn-red" data-plugins="dialog">ยกเลิก</a>';
											}
											else{
												echo '<a class="disabled btn btn-red">LOCK</a>';
											}
											?>
										</td>
									</tr>
									<?php } 
								}
								else{
									echo '<tr><td colspan="11" style="color:red;" class="tac fwb">ไม่มีข้อมูลการจอง</td></tr>';
								} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript"> 
	$("[name=agency]").change(function(){
		// alert($(this).val());
		window.location.href = Event.URL + "profile/history/" + $(this).val();
	});
</script>