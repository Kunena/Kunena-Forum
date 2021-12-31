<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$childforums = (int) (!isset($this->childforums) || $this->childforums);
?>

<form action="<?php echo KunenaRoute::_(); ?>" method="post" class="form-search pull-right">
	<input type="hidden" name="view" value="search"/>
	<input type="hidden" name="task" value="results"/>

	<?php if (isset($this->catid))
		:
		?>
		<input type="hidden" name="catids[]" value="<?php echo $this->catid; ?>"/>
	<?php endif; ?>

	<?php if (isset($this->id))
		:
		?>
		<input type="hidden" name="ids[]" value="<?php echo $this->id; ?>"/>
	<?php endif; ?>

	<input type="hidden" name="childforums" value="<?php echo $childforums; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
	<div class="search">
		<input type="text" class="form-control input-sm hasTooltip" maxlength="64" name="query" value=""
		       placeholder="<?php echo Text::_('COM_KUNENA_MENU_SEARCH'); ?>" data-original-title="<?php echo Text::_('COM_KUNENA_WIDGET_SEARCH_INPUT_TOOLTIP'); ?>" />
		<button class="btn btn-default" type="submit">
			<?php echo KunenaIcons::search(); ?>
		</button>
	</div>
</form>
