<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
defined('_JEXEC') or die();

$disabled = '';
if (!empty($this->errormsg)) $disabled = ' disabled="disabled"';
?>
<form action="index.php" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_kunenaimporter" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="form" value="1" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>

<h1><?php echo JText::_('External Database Configuration'); ?></h1>

<table class="kunenaimporter">
	<tr valign="top">
		<td class="config">
			<?php echo $this->params->render('params'); ?>
		</td>
		<td class="info">
			<div class="info">
				<?php if( isset($this->messages) ) echo $this->messages; ?>
			</div>
		</td>
	</tr>
</table>

<br />
<h1><?php echo JText::_('Import Options'); ?></h1>

<table class="adminlist">
	<thead>
		<tr>
		<th class="x" width="1%"><input type="checkbox" name="toggle" value="" <?php echo $disabled; ?> onclick="checkAll(<?php echo count($this->options); ?>);" /></th>
			<th class="title" width="19%"><?php echo JText::_('Task'); ?></th>
			<th class="status" width="10%"><?php echo JText::_('Status'); ?></th>
<!--			<th class="action" width="15%"><?php echo JText::_('Action'); ?></th>-->
			<th class="notes" width="70%"><?php echo JText::_('Description'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td style="text-align: left;" colspan="5">
			&nbsp;
			</td>
		</tr>
	</tfoot>
	<tbody>
<?php if (isset($this->options)): ?>

<?php
$rowNum = 0;
$state = JRequest::getVar('cid', array(), 'post', 'array');
foreach($this->options as $item=>$option):

	if (isset($state[$option['name']])) echo $option['name'],$checked = 'checked="checked"';
	else $checked = '';

	if (!$option['status'] && $option['total']) $statusmsg = '<font color="red">'.$option['status'].' / '.$option['total'].'</font>';
	else if ($option['status'] < $option['total']) $statusmsg = '<font color="#b0b000">'.$option['status'].' / '.$option['total'].'</font>';
	else $statusmsg = '<font color="green">'.$option['total'].'</font>';

	$id = '<input type="checkbox" id="cb'.$rowNum.'" name="cid[]" value="'.$option['name'].'" onclick="isChecked(this.checked);" $checked />';
?>
		<tr class="row<?php echo $rowNum++ % 2; ?>">
			<td class="x"><?php echo $id; ?></td>
			<td class="title"><?php echo JText::_($option['task']); ?></td>
			<td class="action"><?php echo $statusmsg; ?></td>
			<td class="notes"><?php echo JText::_($option['desc']); ?></td>
		</tr>
<?php endforeach; ?>
<?php else: ?>
		<tr><td style="color: red; text-align: left;" colspan="5">Import is currently not possible because of the above errors.</td></tr>
<?php endif; ?>
	</tbody>
</table>
</form>