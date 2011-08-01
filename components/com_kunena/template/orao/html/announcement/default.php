<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo KunenaHtmlParser::parseText($this->announcement->title) ?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>


			<?php if ($this->canEdit) : ?>
			<div class="tksection-desc">
				<?php echo CKunenaLink::GetAnnouncementLink( 'edit', $this->announcement->id, JText::_('COM_KUNENA_ANN_EDIT'), JText::_('COM_KUNENA_ANN_EDIT')); ?> |
				<?php echo CKunenaLink::GetAnnouncementLink( 'delete', $this->announcement->id, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?> |
				<?php echo CKunenaLink::GetAnnouncementLink( 'add',NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?> |
				<?php echo CKunenaLink::GetAnnouncementLink( 'show', NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?>
			</div>
			<?php endif; ?>
			<div class="kdetailsbox" id="kannounce-detailsbox">
				<ul class="kheader-desc">
					<?php if ($this->announcement->showdate > 0) : ?>
					<li class="kannounce-date"><?php echo KunenaDate::getInstance($this->announcement->created)->toKunena('date_today') ?></li>
					<?php endif; ?>
					<li class="kannounce-desc"><p><?php echo !empty($this->announcement->description) ? KunenaHtmlParser::parseBBCode($this->announcement->description) : KunenaHtmlParser::parseBBCode($this->announcement->sdescription); ?></p></li>
				</ul>
			</div>

		<span class="corners-bottom"><span></span></span>
	</div>
</div>
		<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>