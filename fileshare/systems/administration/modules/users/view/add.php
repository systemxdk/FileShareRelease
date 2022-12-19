<form method="post" action="<?= new shUrl('save'); ?>">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" colspan="2"><b><?=Language::tag('TEXT_FILESHARE_ADDING');?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="150"><?=Language::tag('TEXT_USER_SELECT'); ?></td>
		<td class="td_border_bottom"><?= AccountAssistant::get_next_username(); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="150"></td>
		<td class="td_border_bottom"><?=Language::tag('TEXT_FILESHARE_USERS'); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_PASSWORD');?></td>
		<td class="td_border_bottom"><?= new shTextfield('password', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off'), TRUE); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_FIRSTNAME');?></td>
		<td class="td_border_bottom"><?= new shTextfield('firstname', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off'), TRUE); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_LASTNAME');?></td>
		<td class="td_border_bottom"><?= new shTextfield('lastname', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off'), TRUE); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_EMAIL');?></td>
		<td class="td_border_bottom"><?= new shTextfield('email', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off'), TRUE); ?></td>
	</tr>
	<tr>
		<td></td>
		<td>
            <?php if ($user) : ?>
            <?= Language::tag("TEXT_ADMINISTRATOR_FILL_ONLY"); ?><br /><br />
            <input type="hidden" name="id" value="<?= $user->id; ?>" />
            <?php endif; ?>
            
            <input type="submit" value="<?=Language::tag('TEXT_SAVE');?>" />
        </td>
	</tr>
</table>
</form>