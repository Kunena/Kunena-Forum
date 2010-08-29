<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */
defined('_JEXEC') or die;
?>
<table class="kinstaller">
	<tr>
		<th colspan="3"><?php echo JText::_('COM_KUNENA_INSTALL_REQ_CHECK') ?>: <font color="red"><?php echo JText::_('COM_KUNENA_INSTALL_REQ_FAILED') ?></font> -
		<a href="http://docs.kunena.com/index.php/Technical_Requirements"><?php echo JText::_('COM_KUNENA_INSTALL_REQ_FAILED_DESC') ?></a></th>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_INSTALL_REQ_PHP') ?>:</td>
		<td><font
			color="<?php echo isset($this->requirements->fail['php'])?'red':'green'; ?>"><?php echo $this->requirements->php; ?></font></td>
		<td>(<?php echo JText::_('COM_KUNENA_INSTALL_REQUIRED') ?> &gt;= <?php echo KUNENA_MIN_PHP; ?>)</td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_INSTALL_REQ_MYSQL') ?>:</td>
		<td><font
			color="<?php echo isset($this->requirements->fail['mysql'])?'red':'green'; ?>"><?php echo $this->requirements->mysql; ?></font></td>
		<td>(<?php echo JText::_('COM_KUNENA_INSTALL_REQUIRED') ?> &gt;= <?php echo KUNENA_MIN_MYSQL; ?>)</td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_INSTALL_REQ_JOOMLA') ?>:</td>
		<td><font
			color="<?php echo isset($this->requirements->fail['joomla'])?'red':'green'; ?>"><?php echo $this->requirements->joomla; ?></font></td>
		<td>(<?php echo JText::_('COM_KUNENA_INSTALL_REQUIRED') ?> &gt;= <?php echo KUNENA_MIN_JOOMLA; ?>)</td>
	</tr>
</table>
