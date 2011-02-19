<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined( '_JEXEC' ) or die();
?>

<div class="kblock keditsettings">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : '';
	?>" id="kflattable">
	<tbody>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_USER_ORDER'); ?></td>
			<td class="kcol-mid">
				<?php
				$mesordering[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
				$mesordering[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_USER_ORDER_ASC'));
				$mesordering[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_USER_ORDER_DESC'));
				echo JHTML::_('select.genericlist', $mesordering, 'messageordering', 'class="inputbox" size="1"', 'value', 'text', $this->escape($this->profile->ordering));
				?>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_USER_HIDEEMAIL'); ?></td>
			<td class="kcol-mid">
				<?php
				$hideEmail[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_A_NO'));
				$hideEmail[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_A_YES'));
				echo JHTML::_('select.genericlist', $hideEmail, 'hidemail', 'class="inputbox" size="1"', 'value', 'text', $this->escape($this->profile->hideEmail));
				?>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_USER_SHOWONLINE'); ?></td>
			<td class="kcol-mid">
				<?php
				$showonline[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_A_NO'));
				$showonline[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_A_YES'));
				echo JHTML::_('select.genericlist', $showonline, 'showonline', 'class="inputbox" size="1"', 'value', 'text', $this->escape($this->profile->showOnline));
				?>
			</td>
		</tr>
	</tbody>
</table>
		</div>
	</div>
</div>