<?php

$url = URL .'projects/';


?><div class="pal"><div class="setting-header cleafix">

<div class="rfloat">

	<a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>add_email"><i class="icon-plus mrs"></i><span><?=$this->lang->translate('Add New')?></span></a>

</div>

<div class="setting-title"><?=$this->lang->translate('Email')?></div>
</div>

<section class="setting-section">
	<table class="settings-table admin"><tbody>
		<tr>
			<th class="name"><?=$this->lang->translate('Title')?></th>
			<th class="status_th"><?=$this->lang->translate('Status')?></th>
			<th class="actions"><?=$this->lang->translate('Action')?></th>
		</tr>

		<?php foreach ($this->data as $key => $item) { ?>
		<tr>
			<td class="name"><?=$item['title']?></td>
			<td class="status_th"><?=$item['status_arr']['name']?></td>

			<td class="actions whitespace">
				<span class="gbtn"><a data-plugins="dialog" href="<?=$url?>edit_email/<?=$item['id'];?>" class="btn btn-no-padding"><i class="icon-pencil"></i></a></span>
				<span class='gbtn'><a data-plugins="dialog" href="<?=$url?>del_email/<?=$item['id'];?>" class="btn btn-no-padding"><i class="icon-trash"></i></a></span>
			</td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
</div>