<form method="post" action="<?= new shUrl("save"); ?>">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" width="150" colspan="2"><b><?= Language::tag("TEXT_SETTINGS_EMAIL"); ?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_SETTINGS_EMAIL_OUTGOING_SMTP"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('setting[outgoing_smtp]', NULL, (isset($settings["outgoing_smtp"]) ? $settings["outgoing_smtp"] : null), array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_SETTINGS_EMAIL_OUTGOING_PORT"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('setting[outgoing_smtp_port]', NULL, (isset($settings["outgoing_smtp_port"]) ? $settings["outgoing_smtp_port"] : null), array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_SETTINGS_EMAIL_OUTGOING_EMAIL_FROM"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('setting[outgoing_email_name]', NULL, (isset($settings["outgoing_email_name"]) ? $settings["outgoing_email_name"] : null), array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_SETTINGS_EMAIL_OUTGOING_EMAIL"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('setting[outgoing_email]', NULL, (isset($settings["outgoing_email"]) ? $settings["outgoing_email"] : null), array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_SETTINGS_EMAIL_OUTGOING_PASSWORD"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('setting[outgoing_email_password]', NULL, (isset($settings["outgoing_email_password"]) ? $settings["outgoing_email_password"] : null), array('class' => 'input_text', 'autocomplete' => 'off'), true); ?></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="<?=Language::tag('TEXT_SAVE');?>" /></td>
	</tr>
</table>
</form>
