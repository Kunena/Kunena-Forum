<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
?>

<h2><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE'); ?></h2>
<table	class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : '' ?>" id="kflattable">
	<tbody>
		<tr class="ksectiontableentry2">
			<td class="td-0 km kcenter"><?php echo JText::_('COM_KUNENA_USER_ORDER'); ?></td>
			<td>
				<?php
				$mesordering[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
				$mesordering[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_USER_ORDER_ASC'));
				$mesordering[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_USER_ORDER_DESC'));
				echo JHTML::_('select.genericlist', $mesordering, 'messageordering', 'class="inputbox" size="1"', 'value', 'text', $this->escape($this->profile->ordering));
				?>
			</td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km kcenter"><?php echo JText::_('COM_KUNENA_USER_HIDEEMAIL'); ?></td>
			<td>
				<?php
				$hideEmail[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_A_NO'));
				$hideEmail[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_A_YES'));
				echo JHTML::_('select.genericlist', $hideEmail, 'hidemail', 'class="inputbox" size="1"', 'value', 'text', $this->escape($this->profile->hideEmail));
				?>
			</td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km kcenter"><?php echo JText::_('COM_KUNENA_USER_SHOWONLINE'); ?></td>
			<td>
				<?php
				$showonline[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_A_NO'));
				$showonline[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_A_YES'));
				echo JHTML::_('select.genericlist', $showonline, 'showonline', 'class="inputbox" size="1"', 'value', 'text', $this->escape($this->profile->showOnline));
				?>
			</td>
		</tr>
	</tbody>
</table>
