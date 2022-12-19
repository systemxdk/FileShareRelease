<?php if ($output): ?>
<table cellpadding="0" cellspacing="0" width="100%">
    <?php foreach ($output as $line): ?>
	<tr>
		<td class="td_border_bottom" colspan="2"><?= $line; ?>&nbsp;</td>
	</tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<form method="post" action="<?= new shUrl("ping"); ?>">
<input type="hidden" name="perform" value="true" />
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" width="150" colspan="2"><b><?= Language::tag("TEXT_NETWORK_PING"); ?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_NETWORK_HOST"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('target', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="<?=Language::tag('TEXT_PING'); ?>" /></td>
	</tr>
</table>
</form>
<?php endif; ?>