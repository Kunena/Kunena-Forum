<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Announcement
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$row = $this->row;
$announcement = $this->announcement;
?>

<tr>
	<td class="nowrap hidden-phone">
		<?php echo $announcement->displayField('created', 'date_today'); ?>
	</td>

	<td class="nowrap">
		<div class="overflow">
			<?php echo JHtml::_(
	'kunenaforum.link', $announcement->getUri(), $announcement->displayField('title'),
null, null, ''); ?>
		</div>
	</td>

	<?php if ($this->checkbox) : ?>
	<td class="center">
		<?php if ($this->canPublish()) { echo JHtml::_('kunenagrid.published', $row, $announcement->published, '', true); } ?>
	</td>
	<td class="center">
		<?php if ($this->canEdit()) { echo JHtml::_(
	'kunenagrid.task', $row, 'tick.png', JText::_('COM_KUNENA_ANN_EDIT'),
'edit', '', true); } ?>
	</td>
	<td class="center">
		<?php if ($this->canDelete()) { echo JHtml::_(
	'kunenagrid.task', $row, 'publish_x.png',
JText::_('COM_KUNENA_ANN_DELETE'), 'delete', '', true); } ?>
	</td>
	<?php endif; ?>
	<td>
		<?php if (KunenaConfig::getInstance()->username) :?>
			<?php echo $announcement->getAuthor()->username; ?>
		<?php else :?>
			<?php echo $announcement->getAuthor()->name; ?>
		<?php endif; ?>
	</td>

	<td class="center hidden-phone">
		<?php echo $announcement->displayField('id'); ?>
	</td>

	<?php if ($this->checkbox) : ?>
	<td class="center">
		<?php echo JHtml::_('kunenagrid.id', $row, $announcement->id); ?>
	</td>
	<?php endif; ?>

</tr>
