<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<tr class="krow<?php echo $this->k;?>">
	<?php if ($this->actions): ?>
		<td class="announcement-checkbox"><?php echo JHtml::_('kunenagrid.id', $this->row, $this->announcement->id) ?></td>
	<?php endif ?>
	<td class="center hidden-phone"><?php echo $this->displayField('id') ?></td>
	<td class="nowrap small hidden-phone"><?php echo $this->displayField('created', 'date_today') ?></td>
	<td class="nowrap has-context">
		<div class="overflow"><?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), $this->displayField('title'), null, 'follow') ?></div>
	</td>
	<?php if ($this->actions): ?>
		<td class="center">
			<div class="btn-group">
				<?php if ($this->canPublish()) echo JHtml::_('kunenagrid.published', $this->row, $this->announcement->published, '', true) ?>
			</div>
		</td>
		<td class="center">
			<?php if ($this->canEdit()) echo JHtml::_('kunenagrid.task', $this->row, 'tick.png', JText::_('COM_KUNENA_ANN_EDIT'), 'edit', '', true) ?>
		</td>
		<td class="center">
			<?php if ($this->canDelete()) echo JHtml::_('kunenagrid.task', $this->row, 'publish_x.png', JText::_('COM_KUNENA_ANN_DELETE'), 'delete', '', true) ?>
		</td>
		<td class="center">
			<?php echo $this->announcement->getAuthor()->username ?>
		</td>
	<?php endif; ?>
</tr>
