<?php if ($output): ?>
<table cellpadding="0" cellspacing="0" width="100%">
    <?php foreach ($output as $line): ?>
	<tr>
		<td class="td_border_bottom" colspan="2"><?= $line; ?>&nbsp;</td>
	</tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<form id="postform" method="post" action="<?= new shUrl("dig"); ?>">
<input type="hidden" name="perform" value="true" />
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" width="150" colspan="2"><b><?= Language::tag("TEXT_NETWORK_DIG"); ?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_NETWORK_HOST"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('target', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off', 'id' => 'target')); ?>&nbsp;<span id="loading" style="display: none;">HENTER..</span></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="<?=Language::tag('TEXT_DIG'); ?>" /></td>
	</tr>
</table>
</form>
<?php endif; ?>

<script type="text/javascript">
//JQuery fremvisning af et Ajax kald
$("#postform").submit(function(event) {
    $("#loading").html("HENTER..");
    $("#loading").show();
    
    $.ajax('<?= new shUrl("ajax_dig"); ?>', {
        type: 'post',
        data: $.param({ target: $("#target").val() }),
        success: function(response) {
            switch (response.status) { 
                case "failure": //We deal with errors inside http 200.
                    $("#loading").html(response.message);
                break;
                case "success":
                    alert(response.message);
                    $("#loading").hide();
                break;
            }
        }
    });
    
    event.preventDefault();
});
</script>