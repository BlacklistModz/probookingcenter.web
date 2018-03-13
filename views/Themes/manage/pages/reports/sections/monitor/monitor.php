<div id="mainContainer" class="clearfix" data-plugins="main">
	<div role="main">
		<div class="pal">
			<h3 class="fwb"><i class="icon-desktop mrs"></i>Recevied Monitor</h3>
			<div class="uiBoxWhite mts pam" data-plugins="reportDaily">
				<div class="clearfix">
					<ul class="lfloat">
						<li style="display:inline-block;">
							<label for="year" class="label fwb">เลือกปี</label>
							<select name="year" class="inputtext">
								<?php 
								for($i=0;$i<5;$i++){
									$sel = '';
									$year = date("Y")-$i;
									if( date("Y") == $year ) $sel = 'selected="1"';
									echo '<option '.$sel.' value="'.$year.'">'.($year + 543).'</option>';
								}
								?>
							</select>
						</li>
						<li style="display:inline-block;">
							<label for="country_id" class="label fwb">ประเทศ</label>
							<select name="country_id" class="inputtext">
								<?php 
								foreach ($this->country as $key => $value) {
									echo '<option value="'.$value["id"].'">'.$value["name"].'</option>';
								}
								?>
							</select>
						</li>
						<li style="display:inline-block;">
							<label for="ser_id" class="label fwb">ซีรีย์</label>
							<select name="ser_id" class="inputtext">
							</select>
						</li>
						<li style="display:inline-block;">
							<button class="btn btn-green js-search" style="margin-top: -1.5mm;"><i class="icon-search"></i></button>
						</li>
					</ul>
				</div>
			</div>
			<div class="mtm">
				<h3><i class="icon-list mrs"></i>รายงาน</h3>
			</div>
			<div class="uiBoxWhite mts pam">
				<div id="reportMonitor"><h3 class="tac fcr">-- กรุณาทำรายการ --</h3></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	
</script>