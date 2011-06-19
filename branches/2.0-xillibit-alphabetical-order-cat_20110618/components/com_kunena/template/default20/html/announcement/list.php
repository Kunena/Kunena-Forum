<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document=JFactory::getDocument();
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);
//FIXME: announcement show only 5 ann. in table
?>
		<div id="kannouncements">
			<?php if ($this->canEdit) : ?>
			<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement&layout=create') ?>" class="kheader-link"><?php echo JText::_('COM_KUNENA_ANN_ADD') ?> &raquo;</a>
			<?php endif ?>
			<h2 class="kheader">
				<a title="<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>" rel="kannouncements-detailsbox">
					<?php echo $this->app->getCfg('sitename') . ' ' . JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>
				</a>
			</h2>

			<div class="kdetailsbox kannouncements-details" id="kannouncements-detailsbox" >
				<table class="kannouncement">
					<tbody id="kannouncement_body">
						<tr class="ksth">
							<th class="kcol-annid"><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
							<th class="kcol-anndate"><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?></th>
							<th class="kcol-anntitle"><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?></th>
							<?php if ($this->canEdit): ?>
								<th class="kcol-annpublish"><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></th>
								<th class="kcol-annedit"><?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?></th>
								<th class="kcol-anndelete"><?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?></th>
							<?php endif; ?>
						</tr>

						<?php
						$k=0;
						if (!empty($this->announcements))
							foreach ($this->announcements as $ann) :
								$k=1 - $k;
						?>
						<tr class="krow<?php echo $k;?>">
							<td class="kcol-first kcol-annid"><?php echo intval($ann->id); ?></td>
							<td class="kcol-mid kcol-anndate"><?php echo KunenaDate::getInstance($ann->created)->toKunena('date_today'); ?></td>
							<td class="kcol-mid kcol-anntitle">
								<div class="overflow"><?php echo CKunenaLink::GetAnnouncementLink('read', intval($ann->id), KunenaHtmlParser::parseText ($ann->title), KunenaHtmlParser::parseText ($ann->title), 'follow'); ?></div>
							</td>
							<?php if ($this->canEdit): ?>
							<td class="kcol-mid kcol-annpublish">
								<?php
								if ($ann->published > 0) {
									echo JText::_('COM_KUNENA_ANN_PUBLISHED');
								} else {
									echo JText::_('COM_KUNENA_ANN_UNPUBLISHED');
								}
								?>
							</td>
							<td class="kcol-mid kcol-annedit">
								<?php echo CKunenaLink::GetAnnouncementLink('edit', intval($ann->id), JText::_('COM_KUNENA_ANN_EDIT'),JText::_('COM_KUNENA_ANN_EDIT')); ?>
							</td>
							<td class="kcol-mid kcol-anndelete">
								<?php echo CKunenaLink::GetAnnouncementLink('delete', intval($ann->id), JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?>
							</td>
							<?php endif; ?>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
	</div>
	<div class="clr"></div>
</div>