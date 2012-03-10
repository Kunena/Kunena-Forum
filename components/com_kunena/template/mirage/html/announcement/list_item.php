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
?>
	<li class="announcements-row box-hover box-hover_list-row">
		<dl class="list-unstyled">
			<?php if ($this->actions): ?>
			<dd class="announcement-checkbox">
				<?php echo JHTML::_('kunenagrid.id', $this->row, $this->announcement->id) ?>
			</dd>
			<?php endif ?>
			<dd class="announcement-id">
				<?php echo $this->announcement->displayField('id') ?>
			</dd>
			<dd class="announcement-date">
				<?php echo $this->announcement->displayField('created') ?>
			</dd>
			<dd class="announcement-author">
				<?php echo $this->announcement->displayField('created_by') ?>
			</dd>
			<dd class="announcement-title">
				<?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), $this->announcement->displayField('title'), null, 'follow') ?>
			</dd>
			<?php if ($this->actions): ?>
			<dd class="announcement-publish">
				<?php if ($this->canPublish()) echo JHTML::_('kunenagrid.published', $this->row, $this->announcement->published) ?>
			</dd>
			<dd class="announcement-edit">
				<?php if ($this->canEdit()) echo JHTML::_('kunenagrid.task', $this->row, 'tick.png', JText::_('COM_KUNENA_ANN_EDIT'), 'edit') ?>
			</dd>
			<dd class="announcement-delete">
				<?php if ($this->canDelete()) echo JHTML::_('kunenagrid.task', $this->row, 'publish_x.png', JText::_('COM_KUNENA_ANN_DELETE'), 'delete') ?>
			</dd>
			<?php endif; ?>
		</dl>
	</li>
