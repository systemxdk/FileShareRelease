<form method="post" action="<?= new shUrl('save'); ?>">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
        <?php if ($user) : ?>
		<td class="td_border_top_bottom" colspan="2"><b><?=Language::tag('TEXT_ADMINISTRATOR_EDITING');?></b></td>
        <?php else: ?>
		<td class="td_border_top_bottom" colspan="2"><b><?=Language::tag('TEXT_ADMINISTRATOR_ADDING');?></b></td>
        <?php endif; ?>
	</tr>
	<tr>
		<td class="td_border_bottom" width="150"><?=Language::tag('TEXT_USERNAME');?></td>
        <?php if ($user) : ?>
		<td class="td_border_bottom"><?= $user->username; ?></td>
        <?php else: ?>
        <td class="td_border_bottom"><?= new shTextfield('username', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
        <?php endif; ?>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_PASSWORD');?></td>
		<td class="td_border_bottom">
            <?= new shTextfield('password', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off'), TRUE); ?>
        </td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_ADMINISTRATOR_DEFAULT_PAGE');?></td>
		<td class="td_border_bottom"><?= new shTextfield('default_page', NULL, 'administration/users/index', array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td></td>
		<td>
            <?php if ($user) : ?>
            <?= Language::tag("TEXT_ADMINISTRATOR_FILL_ONLY"); ?><br /><br />
            <input type="hidden" name="id" value="<?= $user->id; ?>" />
            <?php endif; ?>
            
            <input type="submit" value="<?=Language::tag('TEXT_ADMINISTRATOR_SAVE');?>" />
        </td>
	</tr>
</table>
</form>