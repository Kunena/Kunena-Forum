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

$childforums = (int) (!isset($this->childforums) || $this->childforums);
?>

<form action="<?php echo KunenaRoute::_(); ?>" method="post" class="form-search pull-right">
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="task" value="results" />

	<?php if (isset($this->catid)) : ?>
	<input type="hidden" name="catids[]" value="<?php echo $this->catid; ?>" />
	<?php endif; ?>

	<?php if (isset($this->id)) : ?>
	<input type="hidden" name="ids[]" value="<?php echo $this->id; ?>" />
	<?php endif; ?>

	<input type="hidden" name="childforums" value="<?php echo $childforums; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="search">
		<input type="text" class="form-control input-sm" maxlength="64" name="query" value="" placeholder="<?php echo JText::_('COM_KUNENA_MENU_SEARCH'); ?>" />
		<button class="btn btn-default" type="submit">
			<?php echo KunenaIcons::search(); ?>
		</button>
	</div>
</form>
