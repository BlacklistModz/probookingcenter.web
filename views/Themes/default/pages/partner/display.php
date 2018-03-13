<section id="product" class="module parallax product" style="padding-top: 180px; background-image: url(<?=IMAGES?>/demo/curtain/curtain-3.jpg)">
	<div class="container">
		<?php $i=1; foreach($this->results['lists'] as $key => $item) { ?>
		<div class="span5 mts mbs" style="width: 550px">
			<div class="card">

				<?php
				// เพิ่ม logo
				if (
					(!empty($item['logo_img']))
					&& ($item['logo_img'] != "undefined")
				) {
					// ถ้าเจอรูปให้โหลดรุปนี้
					$image = substr(strrchr($item['logo_img'],"/"),1);
					$logo_path = "http://localhost/probookingcenter.admin/admin/upload/company_agency/".$image;
				}
				else {
					//ถ้าไม่ได้กำหนดรูปให้โหลดรูปนี้
					$logo_path ="http://localhost/probookingcenter.admin/admin/upload/company_agency/probookingcenter_logo.png";
				}
				?>

				<img src="<?= $logo_path ?>" style="height: 100px; width: 100px; border-radius: 5px;"/>

				<h3 class="fwb">
					<i class="icon-handshake-o"></i> <?=$item['name']?>
				</h3>
			</div>
		</div>
		<?php $i++; } ?>
	</div>
</section>
