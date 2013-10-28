<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$childforums = (int) (!isset($this->childforums) || $this->childforums);
?>

<form action="<?php echo KunenaRoute::_(); ?>" method="post" class="form-search">
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="task" value="results" />

	<?php if (isset($this->catid)) : ?>
	<input type="hidden" name="catids[]" value="<?php echo $this->catid; ?>" />
	<?php endif; ?>

	<?php if (isset($this->id)) : ?>
	<input type="hidden" name="ids[]" value="<?php echo $this->id; ?>" />
	<?php endif; ?>

	<input type="hidden" name="childforums" value="<?php echo $childforums; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<div class="input-append">
		<label>
			<input class="input-medium search-query" type="text" name="q" value="" placeholder="" />
		</label>

		<button type="submit" class="btn"><?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?></button>
	</div>
</form>
