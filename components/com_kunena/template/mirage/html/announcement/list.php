<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// FIXME: add pagination
?>
<div class="kmodule announcement-list">
	<div class="kbox-wrapper kbox-full">
		<div class="announcement-list-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header fl">
					<h2 class="header">
						<a class="section link-header2" title="<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>" rel="kannouncements-detailsbox"><?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?></a>
					</h2>
				</div>
				<div class="header fr">
					<?php if (!empty($this->actions['add'])) echo JHtml::_('kunenaforum.link', $this->actions['add'], JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD'), 'link') ?>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="announcements-list-detailsbox detailsbox kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="boxchecked" value="0" />
						<input type="hidden" name="task" value="" />
						<?php echo JHtml::_( 'form.token' ) ?>
						<ul class="announcements-list list-unstyled list-header">
							<li class="header kbox-hover_header-row">
								<dl class="announcements-list list-unstyled">
									<dd class="announcements-id">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_ANN_ID') ?></span>
										</div>
									</dd>
									<dd class="announcements-date">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_ANN_DATE') ?></span>
										</div>
									</dd>
									<dd class="announcements-author">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_ANN_AUTHOR') ?></span>
										</div>
									</dd>
									<dd class="announcements-title">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_ANN_TITLE') ?></span>
										</div>
									</dd>
									<?php if ($this->actions): ?>
									<dd class="announcements-publish">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_ANN_PUBLISH') ?></span>
										</div>
									</dd>
									<dd class="announcements-edit">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_ANN_EDIT') ?></span>
										</div>
									</dd>
									<dd class="announcements-delete">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_ANN_DELETE') ?></span>
										</div>
									</dd>
									<?php endif ?>
									<?php if ($this->actions): ?>
										<dd class="announcements-checkbox">
											<div class="innerspacer-header">
												<span class="bold"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->announcements ); ?>);" /></span>
											</div>
										</dd>
									<?php endif ?>
								</dl>
							</li>
						</ul>
						<ul class="list-unstyled announcements-list">
							<?php $this->displayItems() ?>
						</ul>
						<?php  if ( !empty($this->announcementActions) ) : ?>
						<div class="modbox-wrapper innerspacer-bottom">
							<div class="modbox">
								<?php echo JHtml::_('select.genericlist', $this->announcementActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
								<input type="submit" name="kcheckgo" class="kbutton" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
							</div>
						</div>
						<?php endif; ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
