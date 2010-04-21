<?php
/**
 * Joomla! 1.5 component: Kunena Forum Importer
 *
 * @version $Id$
 * @author Kunena Team
 * @package Joomla
 * @subpackage Kunena Forum Importer
 * @license GNU/GPL
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */

defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php" method="post" name="adminForm">
    <input type="hidden" name="option" value="com_kunenaimporter" />
    <input type="hidden" name="task" value="" />

<h1><?php echo JText::_('User mapping'); ?></h1>

<table class="adminlist">
	<thead>
		<tr>
			<th class="x" width="1%"><?php echo "[ X ]"; ?></th>
			<th class="title" width="19%"><?php echo JText::_('Task'); ?></th>
			<th class="status" width="10%"><?php echo JText::_('Progress'); ?></th>
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
$disabled = '';
if (!empty($this->errormsg)) $disabled = ' disabled="disabled"';
foreach($this->options as $option):

	if (JRequest::getBool($option['name'], 0)) $checked = ' checked="checked"';
	else $checked = '';

	if (!$option['status'] && $option['total']) $statusmsg = '<font color="red">0 %</font>';
	else if ($option['status'] < $option['total']) $statusmsg = '<font color="#b0b000">'.(int)(100*$option['status']/$option['total']).' %</font>';
	else $statusmsg = '<font color="green">100 %</font>';
?>
	   <tr>
		<td class="x"><input type="checkbox" name="<?php echo $option['name']; ?>" <?php echo $disabled.$checked; ?> /></td>
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

