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
// TODO: add support for announcement RSS feed
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo $this->annTitle ?></dt>
						<dd class="tk-toggler">
						<a class="ktoggler close" rel="kannounce-detailsbox"></a>
						</dd>
					</dl>
				</li>
			</ul>

			<!-- a href="#" title="Announcement RSS Feed"><span class="krss-icon">Announcement RSS Feed</span></a -->

			<?php if ($this->canEdit) : ?>
			<div class="tksection-desc tk-ann-links">
				<?php echo CKunenaLink::GetAnnouncementLink( 'edit', $this->announcement->id, JText::_('COM_KUNENA_ANN_EDIT'), JText::_('COM_KUNENA_ANN_EDIT')); ?> |
				<?php echo CKunenaLink::GetAnnouncementLink( 'delete', $this->announcement->id, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?> |
				<?php echo CKunenaLink::GetAnnouncementLink( 'add',NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?> |
				<?php echo CKunenaLink::GetAnnouncementLink( 'show', NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?>
				<a style="float:right;" href="#" title="Announcement RSS Feed"><span class="krss-icon"></span></a>
			</div>
			<?php endif; ?>

			<div class="kdetailsbox" id="kannounce-detailsbox">
				<ul class="kheader-desc">
					<li class="kannounce-date"><?php echo $this->annDate->toKunena('date_today') ?></li>
					<li class="kannounce-desc"><p><?php echo $this->annDescription ?></p></li>
					<?php if ($this->annMoreURL) : ?>
					<li class="kannounce-desc kreadmore"><a href="<?php echo $this->annMoreURL ?>"><?php echo JText::_('COM_KUNENA_ANN_READMORE') ?></a></li>
					<?php endif ?>
				</ul>
			</div>

		<span class="corners-bottom"><span></span></span>
	</div>
</div>
<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>