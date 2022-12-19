<form method="post" action="<?= new shUrl('save'); ?>">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" colspan="2"><b><?=Language::tag('TEXT_FILESHARE_ADDING');?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="150"><?=Language::tag('TEXT_USER_SELECT'); ?></td>
		<td class="td_border_bottom">
            <select name="user_id">
                <?php foreach ($users as $user): ?>
                <?php if (in_array($user->id, $users_with_accounts)) continue; ?>
                <option value="<?= $user->id; ?>"><?= $user->username; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="150"></td>
		<td class="td_border_bottom">
            <?=Language::tag('TEXT_FILESHARE_USERS'); ?>.<br /><br />
            <?=Language::tag('TEXT_FILESHARE_USERS_REACTIVATE'); ?>.
        </td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_PASSWORD');?></td>
		<td class="td_border_bottom">
            <?=Language::tag('TEXT_FILESHARE_PASSWORD_AUTOGENERATE'); ?><br /><br />
            <?=Language::tag('TEXT_FILESHARE_PASSWORD_NOT_STORED'); ?>
        </td>
	</tr>
	<tr>
		<td class="td_border_bottom"><?=Language::tag('TEXT_ACTIVE_DAYS');?></td>
		<td class="td_border_bottom"><?= new shTextfield('active_days', NULL, SettingAssistant::get("default_expiration_days"), array('class' => 'input_text', 'autocomplete' => 'off')); ?></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="<?=Language::tag('TEXT_SAVE');?>" /></td>
	</tr>
</table>
</form>