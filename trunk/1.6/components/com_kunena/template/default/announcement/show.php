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
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . stripslashes($this->config->board_title));
?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
	<h1><?php echo $this->app->getCfg('sitename'); ?> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?> | <?php echo CKunenaLink::GetAnnouncementLink('add', NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?></h1>
	<table class="kblocktable" id="kannouncement">
		<tbody id="announcement_tbody">
			<tr class="ksth ks">
				<th class="th-1 ksectiontableheader"  width="1%" align="center"> <?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
				<th class="th-2 ksectiontableheader" width="15%" align="left"> <?php echo JText::_('COM_KUNENA_ANN_DATE'); ?></th>
				<th class="th-3 ksectiontableheader" width="54%" align="left"> <?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?></th>
				<th class="th-4 ksectiontableheader" width="10%"  align="center"> <?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></th>
				<th class="th-5 ksectiontableheader"  width="10%"  align="center"> <?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?></th>
				<th class="th-6 ksectiontableheader" width="10%"  align="center"> <?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?></th>
			</tr>

			<?php
			$tabclass=array
			(
				"sectiontableentry1",
				"sectiontableentry2"
			);
			$k=0;

			if (!empty($this->announcements))
				foreach ($this->announcements as $ann) :
					$k=1 - $k;
			?>

			<tr class="k<?php echo $tabclass[$k];?>">
				<td class="td-1">#<?php echo $ann->id; ?></td>
				<td class="td-2"><?php echo CKunenaTimeformat::showDate($ann->created, 'date_today'); ?></td>
				<td class="td-3">
					<?php echo CKunenaLink::GetAnnouncementLink('read', $ann->id, KunenaParser::parseText ($ann->title), KunenaParser::parseText ($ann->title), 'follow'); ?>
				</td>
				<td class="td-4">
					<?php
					if ($ann->published > 0) {
						echo JText::_('COM_KUNENA_ANN_PUBLISHED');
					} else {
						echo JText::_('COM_KUNENA_ANN_UNPUBLISHED');
					}
					?>
				</td>
				<td class="td-5">
					<?php echo CKunenaLink::GetAnnouncementLink('edit', $ann->id, JText::_('COM_KUNENA_ANN_EDIT'),JText::_('COM_KUNENA_ANN_EDIT')); ?>
				</td>
				<td class="td-6">
					<?php echo CKunenaLink::GetAnnouncementLink('delete', $ann->id, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
</div>
</div>
</div>
</div>