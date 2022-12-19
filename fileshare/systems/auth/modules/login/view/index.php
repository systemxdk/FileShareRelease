<div id="container">
	<h1><?=Language::tag('TEXT_TITLE');?></h1>
	
	<div class="login"><?=Language::tag('TEXT_LOGIN_BELOW');?></div>
    
    <?php if ($error): ?>
    <span class="error"><?= $error; ?></span>
    <?php elseif ($success): ?>
    <span class="success"><?= $success; ?></span>
    <?php endif; ?>
    
	<form method="post" action="<?= new shUrl('auth/login/perform'); ?>">
		<input class="input" placeholder="<?=Language::tag('TEXT_TYPE_USERNAME');?>" type="text" name="username" value="" autofill="no" /><br />
		<input class="input" placeholder="<?=Language::tag('TEXT_TYPE_PASSWORD');?>" type="password" name="password" value="" autofill="no" /><br />
        <select name="lang">
            <?php foreach ($languages as $language): ?>
            <option value="<?= $language; ?>"><?= Language::tag(sprintf("TEXT_TRANSLATE_LANG_%s", strtoupper($language))); ?></option>
            <?php endforeach; ?>
        </select>
        <br />
		<input type="submit" value="<?=Language::tag('TEXT_LOGIN');?>" />
	</form>
</div>
