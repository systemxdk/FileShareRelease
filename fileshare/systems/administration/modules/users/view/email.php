<form method="post" action="<?= new shUrl("email_send"); ?>">
<input type="hidden" name="user_id" value="<?= $user->id; ?>" />
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" width="150" colspan="2"><b><?= Language::tag("TEXT_EMAIL_SEND"); ?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_EMAIL_USER"); ?></td>
		<td class="td_border_bottom"><?= $user->firstname . " " . $user->lastname; ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_EMAIL_USER_EMAIL"); ?></td>
		<td class="td_border_bottom"><?= $user->email; ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_EMAIL_SUBJECT"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('subject', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_EMAIL_BODY"); ?></td>
		<td class="td_border_bottom"><?= new shTextarea('body', 75, 10); ?></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="<?=Language::tag('TEXT_EMAIL_SEND');?>" /></td>
	</tr>
</table>
</form>
