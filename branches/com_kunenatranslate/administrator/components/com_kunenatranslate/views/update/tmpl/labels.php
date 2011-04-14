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
if( JRequest::getVar('task') == 'old'){
	JToolBarHelper::deleteList();
	$add = 0;
	$arr = 'old';
	$text = 'Here you can choose which language labels, which are in the database but not found in the files, you want to delete from the database.';
}else{
	JToolBarHelper::save();
	$add = 1;
	$arr = 'new';
	$text = 'Here you can choose which language labels you want to save to the database.'; 
}
JToolBarHelper::cancel();?>

<fieldset class="adminform">
	<legend><?php echo JText::_('COM_KUNENATRANSLATE_HOWTOUSE');?></legend> 
	<p><?php //Comment for the script to find the correct Labels 
		//JText::_('COM_KUNENATRANSLATE_OLD);
		//JText::_('COM_KUNENATRANSLATE_NEW);
		echo JText::_('COM_KUNENATRANSLATE_'.$arr); ?></p>
</fieldset>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="10">
				<?php if( $arr == 'old')
					echo JText::_('COM_KUNENATRANSLATE_ID');
				else 
					echo JText::_( 'COM_KUNENATRANSLATE_NUM' ); ?>
			</th>
			<th class="title" width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->labels[$arr]); ?>);" />
			</th>
			<th class="title">
				<?php echo JText::_('COM_KUNENATRANSLATE_LABEL'); ?>
			</th>
			<th class="title">
				<?php echo JText::_('COM_KUNENATRANSLATE_CLIENT'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->labels[$arr] as $k=>$v):?>
		<tr>
			<td><?php echo $k+$add; ?></td>
			<td><?php echo JHTML::_('grid.id', $k, $k ); ?></td>
			<td><?php echo $v; ?></td>
			<td><?php echo $this->client; ?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="update" />
<input type="hidden" name="labels" value="<?php echo implode(";",$this->labels['new']); ?>" />
<input type="hidden" name="client" value="<?php echo $this->client; ?>" />
<input type="hidden" name="extension" value="<?php echo $this->extension; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
