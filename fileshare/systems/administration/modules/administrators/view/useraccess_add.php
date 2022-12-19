<form method="post" action="<?= new shUrl('useraccess_save'); ?>">
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
    <td class="td_border_top_bottom" colspan="2">
    <?php if ($this->arguments->id) : ?>
    <b><?=Language::tag('TEXT_USERACCESS_EDIT');?></b>
    <input type="hidden" name="id" value="<?= $this->arguments->id; ?>" />
    <?php else : ?>
    <b><?=Language::tag('TEXT_USERACCESS_ADD');?></b>
    <?php endif; ?>
    </td>
</tr>
<?php if ( !$this->arguments->id ) : ?>
<tr>
	<td class="td_border_bottom" width="150"><?=Language::tag('TEXT_USER_ACCESS_KEY');?></td>
	<td class="td_border_bottom"><?= new shTextfield('access_key', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
</tr>
<?php endif; ?>
<tr>
	<td class="td_border_bottom" width="150"><?=Language::tag('TEXT_DESCRIPTION');?></td>
	<td class="td_border_bottom"><?= new shTextfield('description', NULL, $useraccess->description, array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="<?=Language::tag('ADMIN_TEXT_SAVE');?>" /></td>
</tr>
</table>
</form>