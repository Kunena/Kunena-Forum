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
<li class="announcements-row kbox-hover kbox-hover_list-row kbox-full">
	<dl class="announcements-list list-unstyled list-column">
		<dd class="announcements-id item-column">
			<div class="innerspacer-column">
				<?php echo $this->displayField('id') ?>
			</div>
		</dd>
		<dd class="announcements-date item-column">
			<div class="innerspacer-column">
				<?php echo $this->displayField('created') ?>
			</div>
		</dd>
		<dd class="announcements-author item-column">
			<div class="innerspacer-column">
				<?php echo $this->displayField('created_by') ?>
			</div>
		</dd>
		<dd class="announcements-title item-column">
			<div class="innerspacer-column">
				<?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), $this->displayField('title'), null, 'follow') ?>
			</div>
		</dd>
		<?php if ($this->actions): ?>
		<dd class="announcements-publish item-column">
			<div class="innerspacer-column">
				<?php if ($this->canPublish()) echo JHTML::_('kunenagrid.published', $this->row, $this->announcement->published) ?>
			</div>
		</dd>
		<dd class="announcements-edit item-column">
			<div class="innerspacer-column">
				<?php if ($this->canEdit()) echo JHTML::_('kunenagrid.task', $this->row, 'tick.png', JText::_('COM_KUNENA_ANN_EDIT'), 'edit') ?>
			</div>
		</dd>
		<dd class="announcements-delete item-column">
			<div class="innerspacer-column">
				<?php if ($this->canDelete()) echo JHTML::_('kunenagrid.task', $this->row, 'publish_x.png', JText::_('COM_KUNENA_ANN_DELETE'), 'delete') ?>
			</div>
		</dd>
		<?php endif ?>
		<?php if ($this->actions): ?>
			<dd class="announcements-checkbox item-column">
				<div class="innerspacer-column">
					<?php echo JHTML::_('kunenagrid.id', $this->row, $this->announcement->id) ?>
				</div>
			</dd>
		<?php endif ?>
	</dl>
</li>
