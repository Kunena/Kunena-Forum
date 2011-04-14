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
defined ( '_JEXEC' ) or die ( 'Restricted access' );
JToolBarHelper::save ( 'update', 'Overwrite' );
JToolBarHelper::cancel ();
?>
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_KUNENATRANSLATE_HOWTOUSE');?></legend>
	<p><?php echo JText::_('COM_KUNENATRANSLATE_EXIST_HOWTO'); ?></p>
	<p>Check the checkboxes if you want to use the new translation and overright the old
	one. Leave boxes unchecked if you want to keep the old one.</p>
</fieldset>
<form action="index.php" method="post" name="adminForm">
<table class="adminlist">
<thead>
	<tr>
		<th width="10">
			<?php echo JText::_ ( 'COM_KUNENATRANSLATE_NUM' ); ?>
		</th>
		<th class="title" width="10">
			<input type="checkbox" name="toggle" value="" 
				onclick="checkAll(<?php echo count ( $this->exist );?>);" />
		</th>
		<th class="title">
			<?php echo JText::_ ( 'COM_KUNENATRANSLATE_LABEL' ); ?>
		</th>
		<th class="title">
			<?php echo JText::_ ( 'COM_KUNENATRANSLATE_OLD' ); ?>
		</th>
		<th class="title">
			<?php echo JText::_ ( 'COM_KUNENATRANSLATE_NEW' ); ?>
		</th>
	</tr>
</thead>
<tbody>
	<?php foreach ($this->exist as $k=>$value) : ?>
		<tr>
			<td><?php echo $k+1 ?></td>
			<td><?php echo JHTML::_('grid.id', $k, $value['old']->labelid)?></td>
			<td><?php echo $value['old']->label; ?></td>
			<td><?php echo $value['old']->translation; ?></td>
			<td>
				<textarea name="new[<?php echo $value['old']->labelid; ?>]"
					readonly="readonly" cols="100" rows="1" 
					/><?php echo trim($value['new']);?></textarea>
			</td>
		</tr>
	<?php endforeach;?>
</tbody>
</table>
<input type="hidden" name="controller" value="import" />
<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="language" value="<?php echo $this->exist[0]['old']->lang; ?>" />
<?php echo JHTML::_ ( 'form.token' ); ?>
</form>
