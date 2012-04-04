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
<li class="announcements-row kbox-hover kbox-hover_list-row">
	<dl class="list-unstyled">
		<?php if ($this->actions): ?>
		<dd class="announcement-checkbox">
			<div class="innerspacer-column">
				<?php echo JHTML::_('kunenagrid.id', $this->row, $this->announcement->id) ?>
			</div>
		</dd>
		<?php endif ?>
		<dd class="announcement-id">
			<div class="innerspacer-column">
				<?php echo $this->displayField('id') ?>
			</div>
		</dd>
		<dd class="announcement-date">
			<div class="innerspacer-column">
				<?php echo $this->displayField('created') ?>
			</div>
		</dd>
		<dd class="announcement-author">
			<div class="innerspacer-column">
				<?php echo $this->displayField('created_by') ?>
			</div>
		</dd>
		<dd class="announcement-title">
			<div class="innerspacer-column">
				<?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), $this->displayField('title'), null, 'follow') ?>
			</div>
		</dd>
		<?php if ($this->actions): ?>
		<dd class="announcement-publish">
			<div class="innerspacer-column">
				<?php if ($this->canPublish()) echo JHTML::_('kunenagrid.published', $this->row, $this->announcement->published) ?>
			</div>
		</dd>
		<dd class="announcement-edit">
			<div class="innerspacer-column">
				<?php if ($this->canEdit()) echo JHTML::_('kunenagrid.task', $this->row, 'tick.png', JText::_('COM_KUNENA_ANN_EDIT'), 'edit') ?>
			</div>
		</dd>
		<dd class="announcement-delete">
			<div class="innerspacer-column">
				<?php if ($this->canDelete()) echo JHTML::_('kunenagrid.task', $this->row, 'publish_x.png', JText::_('COM_KUNENA_ANN_DELETE'), 'delete') ?>
			</div>
		</dd>
		<?php endif ?>
	</dl>
</li>
