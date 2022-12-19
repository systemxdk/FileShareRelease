<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        
        <title><?= Language::tag("TEXT_TITLE_ADMINISTRATION"); ?></title>
        <link rel="stylesheet" href="/css/administration.css?<?=time();?>" />
        
        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    </head>
    <body>
    
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="left"><b><?= Language::tag("TEXT_TITLE_ADMINISTRATION"); ?></b>&nbsp;(<?= Userassistant::get_user()->username; ?>@<?=$_SERVER['REMOTE_ADDR'];?>)</td>
            <td align="right"><a href=<?= new shUrl('auth/login/logout'); ?>><?=Language::tag('BUTTON_LOGOUT');?></a></td>
        </tr>
        <?php if ($error): ?>
        <tr>
            <td colspan="2" class="background-error"><?= $error; ?></td>
        </tr>
        <?php elseif ($warning): ?>
        <tr>
            <td colspan="2" class="background-warning"><?= $warning; ?></td>
        </tr>
        <?php elseif ($success): ?>
        <tr>
            <td colspan="2" class="background-success"><?= $success; ?></td>
        </tr>
        <?php endif; ?>
        <tr class="admin_bar">
            <td valign="middle"><?= $GLOBALS['_CONFIG']['draw_menu']; ?></td>
            <td align="right" class="developed" valign="middle"><?= sprintf(Language::tag("TEXT_WRITTEN_BY"), $GLOBALS['_CONFIG']['CREATOR']); ?></td>
        </tr>
        <tr>
            <td colspan="2" align="right" class="admin_submenu_bar">
                <?= isset($GLOBALS['_CONFIG']['draw_submenu']) ? $GLOBALS['_CONFIG']['draw_submenu'] : null; ?>&nbsp;
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 0px;">
                <?= $content; ?>
            </td>
        </tr>
    </table>
    
</body>
</html>