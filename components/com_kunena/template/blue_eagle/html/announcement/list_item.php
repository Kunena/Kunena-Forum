<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<tr class="krow<?php echo $this->k;?>">
			<td class="kcol-first kcol-annid"><?php echo intval($this->announcement->id); ?></td>
			<td class="kcol-mid kcol-anndate"><?php echo $this->announcement->getCreationDate()->toKunena('date_today'); ?></td>
			<td class="kcol-mid kcol-anntitle">
				<div class="overflow"><?php echo JHtml::_('kunenaforum.link', $this->announcement->getLayoutUrl('default', 'object'), KunenaHtmlParser::parseText ($this->announcement->title), KunenaHtmlParser::parseText ($this->announcement->title), 'follow'); ?></div>
			</td>
			<?php if ($this->actions): ?>
			<td class="kcol-mid kcol-annpublish">
				<?php echo $this->announcement->published ? JText::_('COM_KUNENA_ANN_PUBLISHED') : JText::_('COM_KUNENA_ANN_UNPUBLISHED') ?>
			</td>
			<td class="kcol-mid kcol-annedit">
				<?php if ($this->actions['edit']) echo JHtml::_('kunenaforum.link', $this->actions['edit'], JText::_('COM_KUNENA_ANN_EDIT'), JText::_('COM_KUNENA_ANN_EDIT')); ?>
			</td>
			<td class="kcol-mid kcol-anndelete">
				<?php if ($this->actions['delete']) echo JHtml::_('kunenaforum.link', $this->actions['delete'], JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?>
			</td>
			<?php endif; ?>
		</tr>
