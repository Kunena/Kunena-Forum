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
  <td><?php echo JHtml::_('kunenagrid.id', $this->row, $this->announcement->id) ?></td>
  <?php endif ?>
  <td><?php echo $this->displayField('id') ?></td>
  <td><?php echo $this->displayField('created', 'date_today') ?></td>
  <td>
    <div><?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), $this->displayField('title'), null, 'follow') ?></div>
  </td>
  <?php if ($this->actions): ?>
  <td>
    <?php if ($this->canPublish()) echo JHtml::_('kunenagrid.published', $this->row, $this->announcement->published) ?>
  </td>
  <td>
    <?php if ($this->canEdit()) echo JHtml::_('kunenagrid.task', $this->row, 'tick.png', JText::_('COM_KUNENA_ANN_EDIT'), 'edit') ?>
  </td>
  <td>
    <?php if ($this->canDelete()) echo JHtml::_('kunenagrid.task', $this->row, 'publish_x.png', JText::_('COM_KUNENA_ANN_DELETE'), 'delete') ?>
  </td>
  <?php endif; ?>
</tr>
