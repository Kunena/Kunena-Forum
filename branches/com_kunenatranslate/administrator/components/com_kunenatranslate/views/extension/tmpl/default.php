<?php
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010-2011 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::addNewX();
JToolBarHelper::deleteListX();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="10">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th class="title" width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->extension); ?>);" />
			</th>
			<th class="title">
				<?php echo JText::_('COM_KUNENATRANSLATE_EXT_NAME');?>
			</th>
			<th class="title">
				<?php echo JText::_('COM_KUNENATRANSLATE_EXT_FILENAME');?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JText::_('COM_KUNENATRANSLATE_ID');?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php $k = 0;
		fb($this->extension);
		if(!empty($this->extension)):
			foreach ($this->extension as $i=>$v):
				?>
				<tr class="row<?php echo $k;?>">
					<td><?php echo $i+1; ?></td>
					<td><?php echo JHTML::_('grid.id', $i, $v['id'])?></td>
					<td><?php echo $v['name']; ?></td>
					<td><?php echo $v['filename']; ?></td>
					<td><?php echo $v['id']; ?></td>
				</tr>
				<?php $k = 1- $k;
				endforeach;
			endif;?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="controller" value="extension" />
<input type="hidden" name="view" value="extension" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
