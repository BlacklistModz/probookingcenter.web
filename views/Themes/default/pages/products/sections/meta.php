
<ul class="product-meta">
	<li class="">
		<strong>Code</strong>
		<span><?=$this->item['code']?></span>
	</li>
	<!-- <li class="product-category">
		<a>ฮ่องกง</a>, <a>ฮ่องกง</a>
		<strong></strong>
	</li> -->
	
	<li class="">
		<strong>ระยะเวลา</strong>
		<?php 
		$midDay = $this->fn->q('time')->DateDiff($this->item['first_start_date'], $this->item['first_end_date'])+1;
		$nightDay = $midDay - 1;
		?>
		<span><?=$midDay?> วัน <?=$nightDay?> คืน</span>
	</li>

	<li class="">
		<strong>ราคาเริ่มต้น</strong>
		<span><?=$this->item['price_str']?></span>
	</li>
	<li class="">
		<strong>สายการบิน</strong>
		<span><?=$this->item['air_name']?></span>
	</li>
</ul>
				