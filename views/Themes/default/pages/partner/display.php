<section id="product" class="module parallax product" style="padding-top: 180px; background-image: url(<?=IMAGES?>/demo/curtain/curtain-3.jpg)">
	<div class="container">
		<?php $i=1; foreach($this->results['lists'] as $key => $item) { ?>
		<div class="span5 mts mbs" style="width: 550px">
			<div class="card">
				<h3 class="fwb"><i class="icon-handshake-o"></i> <?=$item['name']?></h3>
			</div>
		</div>
		<?php $i++; } ?>
	</div>
</section>