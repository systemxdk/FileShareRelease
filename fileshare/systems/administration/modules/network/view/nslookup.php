<?php if ($output): ?>
<table cellpadding="0" cellspacing="0" width="100%">
    <?php foreach ($output as $line): ?>
	<tr>
		<td class="td_border_bottom" colspan="2"><?= $line; ?>&nbsp;</td>
	</tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<form id="form" onsubmit="return false;">
<input type="hidden" name="perform" value="true" />
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="td_border_top_bottom" width="150" colspan="2"><b><?= Language::tag("TEXT_NETWORK_NSLOOKUP"); ?></b></td>
	</tr>
	<tr>
		<td class="td_border_bottom" width="200"><?= Language::tag("TEXT_NETWORK_HOST"); ?></td>
		<td class="td_border_bottom"><?= new shTextfield('target', NULL, NULL, array('class' => 'input_text', 'autocomplete' => 'off', 'id' => 'target')); ?>&nbsp;<span id="loading" style="display: none;">HENTER..</span></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="<?=Language::tag('TEXT_NSLOOKUP'); ?>" /></td>
	</tr>
</table>
</form>
<?php endif; ?>

<script type="text/javascript">

    function submit() {        
        document.getElementById("loading").innerHTML = "HENTER..";
        document.getElementById("loading").style.display = "inline";

        var http = new XMLHttpRequest();
        var url = '<?= new shUrl("ajax_nslookup"); ?>';
        var params = "target=" + document.getElementById("target").value;
        http.open('POST', url, true);

        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        http.onreadystatechange = function() {//Called when state changes
            if(http.readyState == 4 && http.status == 200) {
                console.log(http.responseText);
                var response = JSON.parse(http.responseText);

                switch (response.status) { 
                    case "failure": //We deal with errors inside http 200.
                        document.getElementById("loading").innerHTML = response.message;
                        document.getElementById("loading").style.display = "inline";
                    break;
                    case "success":
                        alert(response.message);
                        document.getElementById("loading").style.display = "none";
                    break;
                }
            }
        }
        http.send(params);
    }
    
    var form = document.getElementById('form');
    form.addEventListener('submit', submit);
</script>