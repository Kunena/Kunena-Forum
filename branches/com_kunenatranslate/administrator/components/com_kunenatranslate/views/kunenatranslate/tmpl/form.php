<?php 
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::save(); 
JToolBarHelper::cancel();?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="10">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th class="title">
				Label
			</th>
			<?php foreach ($this->languages as $v):?>
				<th class="title">
					<?php echo $v; ?>
				</th>
			<?php endforeach;?>
		</tr>
	</thead>
	<tbody>
		<?php $k = 0;
		foreach ($this->labels as $i=>$v):?>
			<tr class="row<?php echo $k;?>">
				<td><?php echo $i+1; ?></td>
				<td><?php echo $v->label; ?></td>
				<?php foreach ($this->languages as $val):
					$fvalue = '';
					$name = 'insert';
					foreach ($v->lang as $value){
						if($value->lang == $val)
							$fvalue = $value->translation;
							$name = 'update';
						}?>
					<td>
						<input type="text" name="<?php echo $val.'['.$v->id.']['.$name.']';?>"
						value="<?php echo $fvalue; ?>" />
					</td>
				<?php endforeach;?>
			</tr>
			<?php $k = 1- $k;
			endforeach;?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxedchecked" value="0" />
<input type="hidden" name="knownlanguages" value="<?php echo implode(',',$this->languages);?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
