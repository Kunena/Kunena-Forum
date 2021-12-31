<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Category
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category') ?>" method="post"
      name="kcategoryform" id="kcategoryform">
	<input type="hidden" name="userid" value="<?php echo $this->user->userid; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>

	<h3>
		<?php echo $this->escape($this->headerText); ?>
		<span class="badge badge-info"><?php echo (int) $this->pagination->total; ?></span>

		<?php if (!empty($this->actions) && !empty($this->categories)) : ?>
			<div class="form-group">
				<div class="input-append pull-right">
					<div class="input-group-btn">
						<?php echo HTMLHelper::_('select.genericlist', $this->actions, 'task', 'size="1"', 'value', 'text', 0,
							'kchecktask'); ?>
						<input type="submit" name="kcheckgo" class="btn"
						       value="<?php echo Text::_('COM_KUNENA_GO') ?>"/>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->embedded)) : ?>
			<div class="pull-right">
				<?php echo $this->subLayout('Widget/Pagination/List')
					->set('pagination', $this->pagination)
					->set('display', true); ?>
			</div>
		<?php endif; ?>
	</h3>

	<table class="table table-striped<?php echo KunenaTemplate::getInstance()->borderless(); ?>">

		<?php if (!empty($this->actions) && !empty($this->categories)) : ?>
			<thead>
			<tr>
				<th colspan="3"></th>
				<th class="center">
					<input class="kcheckallcategories" type="checkbox" name="toggle" value=""/>
				</th>
			</tr>
			</thead>
		<?php endif; ?>

		<?php if (empty($this->categories)) : ?>
			<tbody>
			<tr>
				<td>
					<?php echo Text::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?>
				</td>
			</tr>
			</tbody>
		<?php else : ?>
			<tbody>
			<?php
			foreach ($this->categories as $this->category)
			{
				echo $this->subLayout('Category/List/Row')
					->set('category', $this->category)
					->set('config', $this->config)
					->set('checkbox', !empty($this->actions));
			}
			?>
			</tbody>
		<?php endif; ?>

	</table>
</form>
