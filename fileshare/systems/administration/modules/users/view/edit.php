<form method="post" action="<?= new shUrl('save_edit'); ?>">
<input type="hidden" name="id" value="<?= $user->id; ?>" />
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" colspan="2"><b><?=Language::tag('TEXT_USER_EDITING');?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="150"><?=Language::tag('TEXT_USERNAME'); ?></td>
		<td class="td_border_bottom"><?= $user->username; ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_PASSWORD');?></td>
		<td class="td_border_bottom"><?= new shTextfield('password', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off'), TRUE); ?></td>
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
		<td class="td_border_bottom"><?=Language::tag('TEXT_EMAIL');?></td>
		<td class="td_border_bottom"><?= new shTextfield('email', NULL, $user->email, array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td></td>
		<td>
            <?= Language::tag("TEXT_ADMINISTRATOR_FILL_ONLY"); ?><br /><br />
            <input type="submit" value="<?=Language::tag('TEXT_SAVE');?>" />
        </td>
	</tr>
</table>
</form>