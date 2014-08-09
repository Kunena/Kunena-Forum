<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<tr class="krow<?php echo $this->k;?>">
			<?php if ($this->actions): ?>
			<td class="kcol-first announcement-checkbox"><?php echo JHtml::_('kunenagrid.id', $this->row, $this->announcement->id) ?></td>
			<?php endif ?>
			<td class="<?php echo $this->actions ? 'kcol-mid' : 'kcol-first'; ?> kcol-annid"><?php echo $this->displayField('id') ?></td>
			<td class="kcol-mid kcol-anndate"><?php echo $this->displayField('created', 'date_today') ?></td>
			<td class="kcol-mid kcol-anntitle">
				<div class="overflow"><?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), $this->displayField('title'), null, 'follow') ?></div>
			</td>
			<?php if ($this->actions): ?>
			<td class="kcol-mid kcol-annpublish">
				<?php if ($this->canPublish()) echo JHtml::_('kunenagrid.published', $this->row, $this->announcement->published) ?>
			</td>
			<td class="kcol-mid kcol-annedit">
				<?php if ($this->canEdit()) echo JHtml::_('kunenagrid.task', $this->row, 'tick.png', JText::_('COM_KUNENA_ANN_EDIT'), 'edit') ?>
			</td>
			<td class="kcol-mid kcol-anndelete">
				<?php if ($this->canDelete()) echo JHtml::_('kunenagrid.task', $this->row, 'publish_x.png', JText::_('COM_KUNENA_ANN_DELETE'), 'delete') ?>
			</td>
			<?php endif; ?>
		</tr>
