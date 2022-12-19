<?php
$controller = ucfirst( strtolower($module) ) . 'Controller';
echo <<<EOCODE
	<?php \$obj = new {$controller}(); ?>
EOCODE;
 ?>

<table>
	<tr>
		<?php
		foreach($tableDesc as $key => $val){
			$lowModule = strtolower($module);
			echo <<<EOCODE
		<td>

			<b><?= new shLink('{$lowModule}/select','{$key}','order_by={$key}&orderrule='.\$orderrule) ?></b> 
		</td>
EOCODE;
		}
		?>
		<td>
			&nbsp;
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<form action='create' method='post'>
		<?php foreach($tableDesc as $key => $val):?>
		<td>
			<?php
			if($val['PrimaryKey'] == 'PRI'){
			echo "&nbsp;";
			}
			elseif( preg_match("/^enum\((.+)\)/",$val['Type'],$match) ){
				echo <<<EOCODE
				<?= new shSelectbox(\$obj->{$key}Enum(),'{$key}');?>
EOCODE;

			} else {
			?>
			<input type='text' name='<?= $key?>'>
			<?php
			}
			?>
		</td>	
		<?php endforeach; ?>
		<td>
			<input type='submit' value='Create'>
		</td>	
		</form>
		<td>
			&nbsp;
		</td>
	</tr>
	<?php
	echo <<<EOCODE
	<?php foreach(\$result as \$key => \$values): ?>
EOCODE;
	?>
	<tr>
		<form action='update' method='post'>
<?php
	foreach($tableDesc as $key => $val){
		if($val['PrimaryKey'] == 'PRI'){
			
			echo <<<EOCODE
			<td>
				<input type='hidden' name='{$key}' value='<?= \$values->{$key} ?>'>
				<?= \$values->{$key} ?>
			</td>
EOCODE;
		} elseif( preg_match("/^enum\((.+)\)/",$val['Type'],$match) ){
				echo <<<EOCODE
			<td>
				<?= new shSelectbox(\$obj->{$key}Enum(),'{$key}',\$values->$key);?>
			</td>
EOCODE;
		} else {
			echo <<<EOCODE
			<td>
				<input type='text' name='{$key}' value='<?= \$values->{$key} ?>'>
			</td>
EOCODE;
		}
	}
			
?>
		<td>
			<input type='submit' value='Update'>
		</td>
		</form>
		<form action='delete' method='post'>
		<td>
<?php
		foreach($tableDesc as $key => $val){
			if($val['PrimaryKey'] == 'PRI'){
				echo <<<EOCODE
			<input type='hidden' name='{$key}' value='<?= \$values->{$key} ?>'>
EOCODE;
			}
		}
?>			
			<input type='submit' value='Delete' onClick="javascript:return confirm('are you sure you want ot delete the row?')">
		</td>
		</form>
	</tr>
<?php
	echo <<<EOCODE
	<?php endforeach; ?>
EOCODE;
	?>
</table>