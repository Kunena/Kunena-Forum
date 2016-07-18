<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// FIXME: change into JForm.

// TODO: Add generic form version

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('assets/js/jquery.caret.js');
$this->addScript('assets/js/jquery.atwho.js');
$this->addStyleSheet('assets/css/jquery.atwho.css');
$this->addScript('assets/js/search.js');

?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=search'); ?>" method="post" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
	<input type="hidden" name="task" value="results" />
	<?php if ($this->me->exists()) : ?>
		<input type="hidden" id="kurl_users" name="kurl_users" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>" />
	<?php endif; ?>
	<?php echo JHtml::_('form.token'); ?>

	<div class="btn-toolbar pull-right">
		<div class="btn-group">
			<button class="btn btn-default btn-small <?php echo KunenaIcons::collapse();?>" type="button" data-toggle="collapse" data-target="#search" aria-expanded="false" aria-controls="search"></button>
		</div>
	</div>
	<h1>
		<?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?>
	</h1>

	<div class="collapse in" id="search">
	<div class="well">
			<div class="row">
				<fieldset class="col-md-6">
					<legend>
						<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?>
					</legend>
					<div class="col-md-6">
						<label>
							<?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS'); ?>:
							<input type="text" name="query" class="form-control"
							       value="<?php echo $this->escape($this->state->get('searchwords')); ?>" />
						</label>
					</div>
					<div class="col-md-6">
						<br>
						<?php $this->displayModeList('mode'); ?>
					</div>
				</fieldset>

				<?php if (!$this->config->pubprofile && !JFactory::getUser()->guest || $this->config->pubprofile) : ?>
				<fieldset class="col-md-6">
					<legend>
						<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_USER'); ?>
					</legend>
					<label>
						<?php echo JText::_('COM_KUNENA_SEARCH_UNAME'); ?>:
						<input id="kusersearch" type="text" name="searchuser" class="form-control"
						       value="<?php echo $this->escape($this->state->get('query.searchuser')); ?>" />
					</label>
					<br>
					<label>
						<?php echo JText::_('COM_KUNENA_SEARCH_EXACT'); ?>:
						<input type="checkbox" name="exactname" value="1"
							<?php if ($this->state->get('query.exactname')) { echo $this->checked; } ?> />
					</label>
				</fieldset>
				<?php endif; ?>
			</div>
		</div>

		<button type="button" class="btn btn-default btn-small pull-right  <?php echo KunenaIcons::collapse();?>" data-toggle="collapse" data-target="#search-options" aria-expanded="false" aria-controls="search-options"></button>
		<h2>
			<?php echo JText::_('COM_KUNENA_SEARCH_OPTIONS'); ?>
		</h2>

		<div class="collapse in" id="search-options">
			<div class="well">
				<div class="row">
					<fieldset class="col-md-6">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?>
						</legend>
						<div class="col-md-6">
							<?php $this->displayDateList('date'); ?>
						</div>
						<div class="col-md-6">
							<?php $this->displayBeforeAfterList('beforeafter'); ?>
						</div>
					</fieldset>

					<fieldset class="col-md-6">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_SORTBY'); ?>
						</legend>
						<div class="col-md-6">
						<?php $this->displaySortByList('sort'); ?>
						</div>
						<div class="col-md-6">
						<?php $this->displayOrderList('order'); ?>
						</div>
					</fieldset>
				</div>

				<div class="row">
					<div class="col-md-6">
						<fieldset>
							<legend>
								<?php echo JText::_('COM_KUNENA_SEARCH_START'); ?>
							</legend>
							<div class="col-md-6">
							<input type="text" name="limitstart" class="form-control"
							       value="<?php echo $this->escape($this->state->get('list.start')); ?>" size="5" />
							</div>
							<div class="col-md-6">
								<?php $this->displayLimitlist('limit'); ?>
							</div>
						</fieldset>

						<?php if ($this->isModerator) : ?>
						<fieldset>
							<legend>
								<?php echo JText::_('COM_KUNENA_SEARCH_SHOW'); ?>
							</legend>
							<label class="radio">
								<input type="radio" name="show" value="0"
									<?php if ($this->state->get('query.show') == 0) { echo 'checked="checked"'; } ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_SHOW_NORMAL'); ?>
							</label>
							<label class="radio">
								<input type="radio" name="show" value="1"
									<?php if ($this->state->get('query.show') == 1) { echo 'checked="checked"'; } ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_SHOW_UNAPPROVED'); ?>
							</label>
							<label class="radio">
								<input type="radio" name="show" value="2"
									<?php if ($this->state->get('query.show') == 2) { echo 'checked="checked"'; } ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_SHOW_TRASHED'); ?>
							</label>
						</fieldset>
						<?php endif; ?>

					</div>

					<fieldset class="col-md-6">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN'); ?>
						</legend>
						<?php $this->displayCategoryList('categorylist', 'class="form-control" size="10" multiple="multiple"'); ?>
						<label>
							<input type="checkbox" name="childforums" value="1"
								<?php if ($this->state->get('query.childforums')) { echo 'checked="checked"'; } ?> />
							<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN'); ?>
						</label>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="center">
			<button type="submit" class="btn btn-primary">
				<?php echo KunenaIcons::search();?><?php echo(' ' . JText::_('COM_KUNENA_SEARCH_SEND') . ' '); ?>
			</button>
			<button type="reset" class="btn btn-default" onclick="window.history.back();">
				<?php echo KunenaIcons::cancel();?><?php echo(' ' . JText::_('COM_KUNENA_CANCEL') . ' '); ?>
			</button>
		</div>
	</div>
</form>
<div class="clearfix"></div>
