<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
	<tr class="krow<?php echo $this->k;?>">
		<?php if (!empty($this->actions)): ?>
		<td class="kcol-first"><?php echo JHTML::_('grid.id', intval($this->announcement->id), intval($this->announcement->id)) ?></td>
		<td class="kcol-mid kcol-annid"><?php echo intval($this->announcement->id) ?></td>
		<?php else : ?>
		<td class="kcol-first kcol-annid"><?php echo intval($this->announcement->id) ?></td>
		<?php endif ?>
		<td class="kcol-mid kcol-anndate"><?php echo $this->announcement->getCreationDate()->toKunena('date_today') ?></td>
		<td class="kcol-mid"><?php echo $this->announcement->getAuthor()->getLink() ?></td>
		<td class="kcol-mid kcol-anntitle">
			<div class="overflow"><?php echo JHtml::_('kunenaforum.link', $this->announcement->getLayoutUrl('default', 'object'), KunenaHtmlParser::parseText ($this->announcement->title), KunenaHtmlParser::parseText ($this->announcement->title), 'follow') ?></div>
		</td>
		<?php if (!empty($this->actions)): ?>
		<td class="kcol-mid kcol-annpublish">
			<?php if ($this->announcement->published) : ?>
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($this->announcement->id) ?>','unpublish')"><img src="<?php echo $this->ktemplate->getImagePath('tick.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_PUBLISHED') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_PUBLISHED') ?>" /></a>
			<?php else : ?>
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($this->announcement->id) ?>','publish')"><img src="<?php echo $this->ktemplate->getImagePath('publish_x.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_UNPUBLISHED') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_UNPUBLISHED') ?>" /></a>
			<?php endif ?>
		</td>
		<td class="kcol-mid kcol-annedit">
			<?php if (!empty($this->actions['edit'])) : ?>
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($this->announcement->id) ?>','edit')"><img src="<?php echo $this->ktemplate->getImagePath('tick.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_EDIT') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_EDIT') ?>" /></a>
			<?php endif ?>
		</td>
		<td class="kcol-mid kcol-anndelete">
			<?php if (!empty($this->actions['delete'])) : ?>
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo intval($this->announcement->id) ?>','delete')"><img src="<?php echo $this->ktemplate->getImagePath('publish_x.png') ?>" alt="<?php echo JText::_('COM_KUNENA_ANN_EDIT') ?>" title="<?php echo JText::_('COM_KUNENA_ANN_DELETE') ?>" /></a>
			<?php endif ?>
		</td>
		<?php endif ?>
	</tr>
