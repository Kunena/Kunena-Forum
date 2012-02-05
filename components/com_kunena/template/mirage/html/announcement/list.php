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

//FIXME: announcement show only 5 ann. in table
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius box-shadow">
		<div id="announcements" class="announcements block">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header">
						<a title="<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>" rel="kannouncements-detailsbox"><?php echo $this->app->getCfg('sitename') . ' ' . JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?></a>
					</h2>
				</div>
				<div class="header fr">
					<?php if ($this->canEdit) : ?>
						<a class="link" href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement&layout=create') ?>" class="kheader-link"><?php echo JText::_('COM_KUNENA_ANN_ADD') ?></a>
					<?php endif ?>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="announcements-details detailsbox box-full box-hover box-border box-border_radius box-shadow" id="announcements-detailsbox" >
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
						<ul class="announcement-list">
							<li class="header">
								<dl>
									<dd class="announcement-checkbox">
										#
									</dd>
									<dd class="announcement-id">
										<span><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></<span>
									</dd>
									<dd class="announcement-date">
										<span><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?></<span>
									</dd>
									<dd class="announcement-author">
										<span><?php echo JText::_('COM_KUNENA_ANN_AUTHOR'); ?></<span>
									</dd>
									<dd class="announcement-title">
										<span><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?></<span>
									</dd>
									<?php if ($this->canEdit): ?>
										<dd class="announcement-publish">
											<span><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></<span>
										</dd>
										<dd class="announcement-edit">
											<span><?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?></<span>
										</dd>
										<dd class="announcement-delete">
											<span><?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?></<span>
										</dd>
									<?php endif; ?>
								</dl>
							</li>
						</ul>
						<ul class="announcement-list">
						<ul class="announcement-list">
							<?php
							$k=0;
							if (!empty($this->announcements))
							foreach ($this->announcements as $ann) :
							$k=1 - $k;
							?>
								<li class="announcements-row box-hover">
									<dl>
										<dd class="announcement-checkbox">
											<?php echo JHTML::_('grid.id', intval($ann->id), intval($ann->id)) ?>
										</dd>
										<dd class="announcement-id">
											<?php echo intval($ann->id); ?>
										</dd>
										<dd class="announcement-date">
											<?php echo KunenaDate::getInstance($ann->created)->toKunena('date_today'); ?>
										</dd>
										<dd class="announcement-author">
											<?php echo CKunenaLink::GetProfileLink($ann->created_by); ?>
										</dd>
										<dd class="announcement-title">
											<?php echo CKunenaLink::GetAnnouncementLink('read', intval($ann->id), KunenaHtmlParser::parseText ($ann->title), KunenaHtmlParser::parseText ($ann->title), 'follow'); ?>
										</dd>
										<?php if ($this->canEdit): ?>
											<dd class="announcement-publish">
											<?php
											if ($ann->published > 0) { ?>
												<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($ann->id); ?>','unpublish')"><img src="<?php echo $this->ktemplate->getImagePath('tick.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_PUBLISHED') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_PUBLISHED') ?>" /></a>
											<?php } else { ?>
												<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($ann->id);
												?>','publish')"><img src="<?php echo $this->ktemplate->getImagePath('publish_x.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_UNPUBLISHED') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_UNPUBLISHED') ?>" /></a>
											<?php }
											?>
											</dd>
											<dd class="announcement-edit">
												<?php echo CKunenaLink::GetAnnouncementLink('edit', intval($ann->id), JText::_('COM_KUNENA_ANN_EDIT'),JText::_('COM_KUNENA_ANN_EDIT')); ?>
											</dd>
											<dd class="announcement-delete">
												<?php echo CKunenaLink::GetAnnouncementLink('delete', intval($ann->id), $this->ktemplate->getImage('publish_x.png'), JText::_('COM_KUNENA_ANN_DELETE')); ?>
											</dd>
										<?php endif; ?>
									</dl>
								</li>
							<?php endforeach; ?>
						</ul>
					<input type="hidden" name="view" value="announcement" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" name="task" value="" />
					<?php echo JHTML::_( 'form.token' ); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>