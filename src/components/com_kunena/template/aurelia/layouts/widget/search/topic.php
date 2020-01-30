<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use function defined;

$this->addScript('assets/js/search.js');

$childforums = (int) (!isset($this->childforums) || $this->childforums);
?>
<div class="kunena-search search">
	<form role="search" action="<?php echo \Kunena\Forum\Libraries\Route\KunenaRoute::_(); ?>" method="post">
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
		<?php echo HTMLHelper::_('form.token'); ?>
		<div class="input-group">
			<label for="mod-search-searchword"></label>
			<input name="query" class="form-control" id="mod-search-searchword" type="search" maxlength="64"
				   placeholder="<?php echo Text::_('COM_KUNENA_MENU_SEARCH'); ?>">
			<span class="input-group-append">
				<button class="btn btn-light border" type="submit">
				<?php echo \Kunena\Forum\Libraries\Icons\Icons::search(); ?>
			</button>
			</span>
		</div>
	</form>
</div>
