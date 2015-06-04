<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
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
	<?php echo JHtml::_( 'form.token' ); ?>
	<div class="col-lg-3 pull-right">
		<div class="input-group">
			<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_KUNENA_MENU_SEARCH'); ?>">
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit"><span class="icon glyphicon glyphicon-search"></span></button>
			</span>
		</div>
	</div>
</form>
