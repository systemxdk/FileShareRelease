<select <?= implode(" ", $htmlProps) ?>>
<?php foreach($options as $key => $val): ?>
	<option <?= (string)$selected === (string)$key ? "selected='selected' " : " " ?> value='<?= $key ?>'><?= $val ?></option>
<?php endforeach; ?>
</select>
