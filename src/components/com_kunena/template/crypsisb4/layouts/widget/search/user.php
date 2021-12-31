<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$me    = KunenaUserHelper::getMyself();
$state = $this->state;
?>

<div class="kunena-search">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list'); ?>" method="post"
		  name="usrlform" id="usrlform">
		<input type="hidden" name="view" value="user"/>
		<?php if ($me->exists())
		:
			?>
			<input type="hidden" id="kurl_users" name="kurl_users"
				   value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>"/>
		<?php endif; ?>
		<?php echo HTMLHelper::_('form.token'); ?>
		<div class="input-group search">
			<label for="kusersearch"></label>
			<input id="kusersearch" class="form-control input-sm search-query" type="text" name="search"
				   value="<?php echo $this->escape($state); ?>"
				   placeholder="<?php echo Text::_('COM_KUNENA_USRL_SEARCH'); ?>"/>
			<span class="input-group-append">
					<button class="btn btn-light border" type="submit">
						<?php echo KunenaIcons::search(); ?>
					</button>
				</span>
		</div>
	</form>
</div>
