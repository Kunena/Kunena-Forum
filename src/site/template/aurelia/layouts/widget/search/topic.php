<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$this->addScript('assets/js/search.js');

$childforums = (int) (!isset($this->childforums) || $this->childforums);
?>
<div class="kunena-search search">
	<form role="search" action="<?php echo KunenaRoute::_(); ?>" method="post">
		<input type="hidden" name="view" value="search" />
		<input type="hidden" name="task" value="results" />
		<?php if (isset($this->catid)) :
		?>
			<input type="hidden" name="catids[]" value="<?php echo $this->catid; ?>" />
		<?php endif; ?>

		<?php if (isset($this->id)) :
		?>
			<input type="hidden" name="ids[]" value="<?php echo $this->id; ?>" />
		<?php endif; ?>
		<?php echo HTMLHelper::_('form.token'); ?>
		<div class="input-group">
			<input name="query" class="form-control hasTooltip" id="mod-search-searchword" type="search" maxlength="64" placeholder="<?php echo Text::_('COM_KUNENA_MENU_SEARCH'); ?>" data-bs-toggle="tooltip" title="<?php echo Text::_('COM_KUNENA_WIDGET_SEARCH_INPUT_TOOLTIP'); ?>" />
			<button class="btn btn-outline-primary" type="submit">
				<?php echo KunenaIcons::search(); ?>
			</button>
		</div>
	</form>
</div>