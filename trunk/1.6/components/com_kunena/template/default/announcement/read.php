<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

$document = JFactory::getDocument();
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . stripslashes($this->config->board_title));
?>
<h1><?php echo $this->app->getCfg('sitename'); ?> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?></h1>
<table class="kblocktable" id="kannouncement">
	<tbody id="announcement_tbody">
		<tr class="ksth ks">
			<th class="th-1 ksectiontableheader" align="left" >
				<?php
				if ($this->canEdit) {
					echo CKunenaLink::GetAnnouncementLink('edit', $this->id, JText::_('COM_KUNENA_ANN_EDIT'), JText::_('COM_KUNENA_ANN_EDIT')).' | ';
					echo CKunenaLink::GetAnnouncementLink('delete', $this->id, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')).' | ';
					echo CKunenaLink::GetAnnouncementLink('add', NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')).' | ';
					echo CkunenaLink::GetAnnouncementLink('show', NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL'));
				}
				?>
			</th>
		</tr>

		<tr>
			<td class="kanndesc" valign="top">
				<h3> <?php echo $this->title; ?> </h3>
				<?php if ($this->showdate > 0) : ?>
				<div class="anncreated ks" title="<?php echo CKunenaTimeformat::showDate($this->created, 'ago'); ?>">
					<?php echo CKunenaTimeformat::showDate($this->created, 'date_today'); ?>
				</div>
				<?php endif; ?>
				<div class="anndesc"><?php echo !empty($this->description) ? $this->description : $this->sdescription; ?></div>
			</td>
		</tr>
	</tbody>
</table>
