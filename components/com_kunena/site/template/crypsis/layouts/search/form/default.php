<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
// FIXME: doesn't work in J!2.5
JHtml::_('dropdown.init');

if (!$this->me->exists()) {
	$this->addScriptDeclaration( "// <![CDATA[
window.addEvent('domready', function() {
	// Attach auto completer to the following ids:
	new Autocompleter.Request.JSON('kusername', '" . KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list&format=raw') . "', { 'postVar': 'search' });
});
// ]]>");
}
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post">
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="task" value="results" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<div class="btn btn-small pull-right" data-toggle="collapse" data-target="#search">X</div>
	<h2>
		<?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?>
	</h2>

	<div class="collapse in" id="search">
		<div class="well">
			<div class="row-fluid">
				<fieldset class="span6">
					<legend>
						<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?>
					</legend>
					<label>
						<?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS'); ?>:
						<input type="text" name="q" value="<?php echo $this->escape($this->state->get('searchwords')) ?>" />
					</label>
					<?php $this->displayModeList('mode') ?>
				</fieldset>

				<fieldset class="span6">
					<legend>
						<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_USER'); ?>
					</legend>
					<label>
						<?php echo JText::_('COM_KUNENA_SEARCH_UNAME'); ?>:
						<input type="text" name="searchuser" value="<?php echo $this->escape($this->state->get('query.searchuser')); ?>" />
					</label>

					<label>
						<?php echo JText::_('COM_KUNENA_SEARCH_EXACT'); ?>:
						<input type="checkbox" name="exactname" value="1" <?php if ($this->state->get('query.exactname')) echo $this->checked; ?> />
					</label>
				</fieldset>
			</div>
		</div>

		<div class="btn btn-small pull-right" data-toggle="collapse" data-target="#search-options">X</div>
		<h3>
			<?php echo JText::_('COM_KUNENA_SEARCH_OPTIONS'); ?>
		</h3>

		<div class="collapse in" id="search-options">
			<div class="well">
				<div class="row-fluid">
					<fieldset class="span6">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?>
						</legend>
						<?php $this->displayDateList('date') ?>
						<?php $this->displayBeforeAfterList('beforeafter') ?>
					</fieldset>

					<fieldset class="span6">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_SORTBY'); ?>
						</legend>
						<?php $this->displaySortByList('sort') ?>
						<?php $this->displayOrderList('order') ?>
					</fieldset>
				</div>

				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<legend>
								<?php echo JText::_('COM_KUNENA_SEARCH_START'); ?>
							</legend>
							<input type="text" name="limitstart" value="<?php echo $this->escape($this->state->get('list.start')); ?>" size="5" />
							<?php $this->displayLimitlist('limit') ?>
						</fieldset>

						<?php if ($this->isModerator) : ?>
						<fieldset>
							<legend>
								<?php echo JText::_('COM_KUNENA_SEARCH_SHOW'); ?>
							</legend>
							<label class="radio">
								<input type="radio" name="show" value="0" <?php if ($this->state->get('query.show') == 0) echo 'checked="checked"'; ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_SHOW_NORMAL'); ?>
							</label>
							<label class="radio">
								<input type="radio" name="show" value="1" <?php if ($this->state->get('query.show') == 1) echo 'checked="checked"'; ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_SHOW_UNAPPROVED'); ?>
							</label>
							<label class="radio">
								<input type="radio" name="show" value="2" <?php if ($this->state->get('query.show') == 2) echo 'checked="checked"'; ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_SHOW_TRASHED'); ?>
							</label>
						</fieldset>
						<?php endif; ?>
					</div>

					<fieldset class="span6">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN'); ?>
						</legend>
						<?php $this->displayCategoryList('categorylist', 'size="10" multiple="multiple"') ?>
						<label>
							<input type="checkbox" name="childforums" value="1" <?php if ($this->state->get('query.childforums')) echo 'checked="checked"'; ?> />
							<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN'); ?>
						</label>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="center">
			<input class="btn btn-primary" type="submit" value="<?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?>" />
			<input class="btn" type="reset" value="<?php echo JText::_('COM_KUNENA_CANCEL'); ?>" onclick="window.location='<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>';" />
		</div>
	</div>
</form>
