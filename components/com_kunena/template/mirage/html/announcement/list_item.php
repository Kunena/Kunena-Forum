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
				<?php echo JHTML::_('grid.id', intval($this->announcement->id), intval($this->announcement->id)) ?>
			</dd>
			<?php endif ?>
			<dd class="announcement-id">
				<?php echo intval($this->announcement->id) ?>
			</dd>
			<dd class="announcement-date">
				<?php echo $this->announcement->getCreationDate()->toKunena('date_today') ?>
			</dd>
			<dd class="announcement-author">
				<?php echo $this->announcement->getAuthor()->getLink() ?>
			</dd>
			<dd class="announcement-title">
				<?php echo JHtml::_('kunenaforum.link', $this->announcement->getLayoutUrl('default', 'object'), KunenaHtmlParser::parseText ($this->announcement->title), KunenaHtmlParser::parseText ($this->announcement->title), 'follow') ?>
			</dd>
			<?php if ($this->actions): ?>
			<dd class="announcement-publish">
			<?php if ($this->announcement->published) : ?>
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($this->announcement->id) ?>','unpublish')"><img src="<?php echo $this->ktemplate->getImagePath('tick.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_PUBLISHED') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_PUBLISHED') ?>" /></a>
			<?php else : ?>
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($this->announcement->id) ?>','publish')"><img src="<?php echo $this->ktemplate->getImagePath('publish_x.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_UNPUBLISHED') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_UNPUBLISHED') ?>" /></a>
			<?php endif ?>
			</dd>
			<dd class="announcement-edit">
			<?php if (!empty($this->actions['edit'])) : ?>
				<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($this->announcement->id) ?>','edit')"><img src="<?php echo $this->ktemplate->getImagePath('tick.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_EDIT') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_EDIT') ?>" /></a>
			<?php endif ?>
			</dd>
			<dd class="announcement-delete">
			<?php if (!empty($this->actions['delete'])) : ?>
				<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($this->announcement->id) ?>','delete')"><img src="<?php echo $this->ktemplate->getImagePath('publish_x.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_EDIT') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_DELETE') ?>" /></a>
			<?php endif ?>
			</dd>
			<?php endif; ?>
		</dl>
	</li>
