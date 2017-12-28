<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$me = KunenaUserHelper::getMyself();
$state = $this->state;
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list'); ?>" method="post"
	  name="usrlform" id="usrlform" class="form-search pull-right">
	<input type="hidden" name="view" value="user"/>
	<?php if ($me->exists()) : ?>
		<input type="hidden" id="kurl_users" name="kurl_users" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>"/>
	<?php endif; ?>
	<?php echo JHtml::_('form.token'); ?>

	<div class="input-append">
		<label>
			<input id="kusersearch" class="input-medium search-query" type="text" name="search"
				   value="<?php echo $this->escape($state); ?>" placeholder="<?php echo JText::_('COM_KUNENA_USRL_SEARCH'); ?>"/>
		</label>

		<button type="submit" class="btn"><?php echo KunenaIcons::search();?></button>
	</div>
</form>
