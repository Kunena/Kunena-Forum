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

$document=JFactory::getDocument();
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->escape($this->config->board_title));
?>
<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo $this->app->getCfg('sitename'); ?> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?> | <?php echo CKunenaLink::GetAnnouncementLink('add', NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table class="kannouncement">
	<tbody id="kannouncement_body">
		<tr class="ksth">
			<th class="kcol-annid"><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
			<th class="kcol-anndate"><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?></th>
			<th class="kcol-anntitle"><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?></th>
			<th class="kcol-annpublish"><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></th>
			<th class="kcol-annedit"><?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?></th>
			<th class="kcol-anndelete"><?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?></th>
		</tr>

		<?php
		$k=0;
		if (!empty($this->announcements))
			foreach ($this->announcements as $ann) :
				$k=1 - $k;
		?>
		<tr class="krow<?php echo $k;?>">
			<td class="ktd-kcol-first kcol-annid"><?php echo intval($ann->id); ?></td>
			<td class="ktd-kcol-other kcol-anndate"><?php echo CKunenaTimeformat::showDate($ann->created, 'date_today'); ?></td>
			<td class="ktd-kcol-other kcol-anntitle">
				<div class="overflow"><?php echo CKunenaLink::GetAnnouncementLink('read', intval($ann->id), KunenaParser::parseText ($ann->title), KunenaParser::parseText ($ann->title), 'follow'); ?></div>
			</td>
			<td class="ktd-kcol-other kcol-annpublish">
				<?php
				if ($ann->published > 0) {
					echo JText::_('COM_KUNENA_ANN_PUBLISHED');
				} else {
					echo JText::_('COM_KUNENA_ANN_UNPUBLISHED');
				}
				?>
			</td>
			<td class="ktd-kcol-other kcol-annedit">
				<?php echo CKunenaLink::GetAnnouncementLink('edit', intval($ann->id), JText::_('COM_KUNENA_ANN_EDIT'),JText::_('COM_KUNENA_ANN_EDIT')); ?>
			</td>
			<td class="ktd-kcol-other kcol-anndelete">
				<?php echo CKunenaLink::GetAnnouncementLink('delete', intval($ann->id), JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
        </div>
	</div>
</div>