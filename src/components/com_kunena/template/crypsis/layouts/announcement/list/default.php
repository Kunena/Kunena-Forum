<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$options = $this->getOptions();
HTMLHelper::_('behavior.core');
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement'); ?>" method="post"
      id="adminForm" name="adminForm">
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>

	<h2>
		<?php echo Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>

		<?php if (!empty($options)) : ?>
			<div class="input-group pull-right">
				<div class="input-append">
					<?php echo HTMLHelper::_('select.genericlist', $options, 'task', '', 'value', 'text', 0, 'kchecktask'); ?>
					<input type="submit" name="kcheckgo" class="btn" value="<?php echo Text::_('COM_KUNENA_GO') ?>"/>
				</div>
				<a class="btn btn-primary" id="announcement-add-button"
				   href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement&layout=create'); ?>">
					<?php echo Text::_('COM_KUNENA_ANNOUNCEMENT_ACTIONS_LABEL_ADD'); ?>
				</a>
			</div>
		<?php endif; ?>
	</h2>

	<table class="table table-striped table-bordered">
		<thead>
		<tr>
			<th class="span1 hidden-phone">
				<?php echo Text::_('COM_KUNENA_ANN_DATE'); ?>
			</th>
			<th class="span5">
				<?php echo Text::_('COM_KUNENA_ANN_TITLE'); ?>
			</th>

			<?php if ($options) : ?>
				<th class="span1 center">
					<?php echo Text::_('COM_KUNENA_ANN_PUBLISH'); ?>
				</th>
				<th class="span1 center">
					<?php echo Text::_('COM_KUNENA_ANN_EDIT'); ?>
				</th>
				<th class="span1 center">
					<?php echo Text::_('COM_KUNENA_ANN_DELETE'); ?>
				</th>
			<?php endif; ?>
			<th class="span2">
				<?php echo Text::_('COM_KUNENA_ANNOUNCEMENT_AUTHOR'); ?>
			</th>
			<th class="span1 center hidden-phone">
				<?php echo Text::_('COM_KUNENA_ANN_ID'); ?>
			</th>

			<?php if ($options) : ?>
				<th class="span1 center">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
				</th>
			<?php endif; ?>
		</tr>
		</thead>

		<?php if ($this->pagination->pagesTotal > 1) : ?>
			<tfoot>
			<tr>
				<td colspan="<?php echo $options ? 8 : 3; ?>">
					<div class="pull-right">
						<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination); ?>
					</div>
				</td>
			</tr>
			</tfoot>
		<?php endif; ?>

		<tbody>
		<?php foreach ($this->announcements as $row => $announcement)
		{
			echo $this->subLayout('Announcement/List/Row')
				->set('announcement', $announcement)
				->set('row', $row)
				->set('checkbox', !empty($options));
		}
		?>
		</tbody>
	</table>
</form>
