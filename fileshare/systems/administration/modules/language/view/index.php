<form method="post" action="<?= new shUrl("save"); ?>">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" colspan="<?= (count($keys) + 1); ?>"><b><?= Language::tag("TEXT_TRANSLATIONS"); ?></b></td>
	</tr>
    <?php foreach ($language[$language_key] as $tag_id => $translation): ?>
    <tr>
        <td><?= $tag_id; ?></td>
        <?php foreach ($keys as $key): ?>
        <td><input type="text" name="translation[<?= $key; ?>][<?= $tag_id; ?>]" value="<?= $language[$key][$tag_id]; ?>" /></td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td align="right" colspan="<?= (count($keys) + 1); ?>"><input type="submit" value="<?= Language::tag("TEXT_SAVE"); ?>" /></td>
    </tr>
</table>
</form>