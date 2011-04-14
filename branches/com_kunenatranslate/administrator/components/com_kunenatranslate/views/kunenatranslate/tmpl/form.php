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
JToolBarHelper::title( JText::_( 'COM_KUNENATRANSLATE' ).': <small><small>'.JText::_('COM_KUNENATRANSLATE_EDIT').'</small></small>', 'generic.png' );
JToolBarHelper::save(); 
JToolBarHelper::cancel();?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="10">
				<?php echo JText::_( 'COM_KUNENATRANSLATE_NUM' ); ?>
			</th>
			<th class="title">
				<?php echo JText::_('COM_KUNENATRANSLATE_LABEL'); ?>
			</th>
			<?php foreach ($this->languages as $v):?>
				<th class="title">
					<?php echo $v; ?>
				</th>
			<?php endforeach;?>
		</tr>
	</thead>
	<tbody>
		<?php if(empty($this->labels)){
			require_once ( dirname(__FILE__).DS.'empty.php');
		} 
		$k = 0;
		foreach ($this->labels as $i=>$v):?>
			<tr class="row<?php echo $k;?>">
				<td><?php echo $i+1; ?></td>
				<td><?php echo $v->label; ?></td>
				<?php foreach ($this->languages as $val):
					$fvalue = '';
					$name = 'insert';
					if(!empty($v->lang)){
						foreach ($v->lang as $value){
							if($value->lang == $val)
								$fvalue = $value->translation;
								$name = 'update';
							}
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
<input type="hidden" name="task" value="" />
<input type="hidden" name="knownlanguages" value="<?php echo implode(',',$this->languages);?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
