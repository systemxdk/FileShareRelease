<form method="post" action="<?= new shUrl('save'); ?>">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" colspan="2"><b><?=Language::tag('TEXT_ACCOUNT_EDITING');?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="150"><?=Language::tag('TEXT_USERNAME'); ?></td>
		<td class="td_border_bottom"><?= $user->username; ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_FIRSTNAME');?></td>
		<td class="td_border_bottom"><?= new shTextfield('firstname', NULL, $user->firstname, array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_LASTNAME');?></td>
		<td class="td_border_bottom"><?= new shTextfield('lastname', NULL, $user->lastname, array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="<?=Language::tag('TEXT_SAVE');?>" /></td>
	</tr>
</table>
</form>