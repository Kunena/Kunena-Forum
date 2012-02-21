<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius box-shadow">
		<div id="announce" class="block">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header">
						<a href="" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST') ?>" rel="kannounce-detailsbox"><?php echo KunenaHtmlParser::parseText($this->announcement->title) ?>
					</a>
					</h2>
					<?php if ($this->canEdit) : ?>
						<div class="kactions">
							<?php echo CKunenaLink::GetAnnouncementLink( 'edit', $this->announcement->id, JText::_('COM_KUNENA_ANN_EDIT'), JText::_('COM_KUNENA_ANN_EDIT')); ?> |
							<?php echo CKunenaLink::GetAnnouncementLink( 'delete', $this->announcement->id, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?> |
							<?php echo CKunenaLink::GetAnnouncementLink( 'add',NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?> |
							<?php echo CKunenaLink::GetAnnouncementLink( 'show', NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="header fr">	
					<?php if ($this->canEdit) : ?>
						<a class="link" href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement&layout=create') ?>" class="kheader-link"><?php echo JText::_('COM_KUNENA_ANN_ADD') ?></a>
					<?php endif ?>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="detailsbox innerspacer box-full box-hover box-border box-border_radius box-shadow" id="announce-detailsbox">
					<ul class="details-desc">
						<?php if ($this->announcement->showdate > 0) : ?>
						<li class="kannounce-date"><?php echo KunenaDate::getInstance($this->announcement->created)->toKunena('date_today') ?></li>
						<?php endif; ?>
						<li class="kannounce-desc"><?php echo !empty($this->announcement->description) ? KunenaHtmlParser::parseBBCode($this->announcement->description) : KunenaHtmlParser::parseBBCode($this->announcement->sdescription); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>