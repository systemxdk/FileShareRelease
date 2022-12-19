<form method="post" action="<?= new shUrl("save"); ?>">
<input type="hidden" name="" value="" />
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" width="150" colspan="2"><b><?= Language::tag("TEXT_SETTINGS_FILESHARE"); ?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?= Language::tag("TEXT_SETTINGS_FILESHARE_DEFAULT_EXPIRATION_DAYS"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('setting[default_expiration_days]', NULL, (isset($settings["default_expiration_days"]) ? $settings["default_expiration_days"] : null), array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="<?=Language::tag('TEXT_SAVE');?>" /></td>
	</tr>
</table>
</form>