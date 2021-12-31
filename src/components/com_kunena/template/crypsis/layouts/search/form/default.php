<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// FIXME: change into JForm.

// TODO: Add generic form version

HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('dropdown.init');

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('jquery.caret.js');
$this->addScript('jquery.atwho.js');
$this->addStyleSheet('jquery.atwho.css');

echo $this->subLayout('Widget/Datepicker');
$this->addScript('assets/js/search.js');
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=search'); ?>" method="post">
	<input type="hidden" name="task" value="results"/>
	<?php if ($this->me->exists())
		:
		?>
		<input type="hidden" id="kurl_users" name="kurl_users"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>"/>
	<?php endif; ?>
	<?php echo HTMLHelper::_('form.token'); ?>

	<div class="btn-toolbar pull-right">
		<div class="btn-group">
			<div class="btn btn-small" data-toggle="collapse" data-target="#search"></div>
		</div>
	</div>
	<h1>
		<?php echo Text::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?>
	</h1>

	<div class="collapse in" id="search">
		<div class="well">
			<div class="row-fluid">
				<fieldset class="span6">
					<legend>
						<?php echo Text::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?>
					</legend>
					<label>
						<?php echo Text::_('COM_KUNENA_SEARCH_KEYWORDS'); ?>:
						<input type="text" name="query"
						       value="<?php echo $this->escape($this->state->get('searchwords')); ?>"/>
					</label>
					<?php $this->displayModeList('mode'); ?>
				</fieldset>

				<?php if (!$this->config->pubprofile && !Factory::getUser()->guest || $this->config->pubprofile)
					:
					?>
					<fieldset class="span6">
						<legend>
							<?php echo Text::_('COM_KUNENA_SEARCH_SEARCHBY_USER'); ?>
						</legend>
						<label>
							<?php echo Text::_('COM_KUNENA_SEARCH_UNAME'); ?>:
							<input id="kusersearch" data-provide="typeahead" type="text" name="searchuser"
							       autocomplete="off"
							       value="<?php echo $this->escape($this->state->get('query.searchuser')); ?>"/>
						</label>

						<label>
							<?php echo Text::_('COM_KUNENA_SEARCH_EXACT'); ?>:
							<input type="checkbox" name="exactname" value="1"
								<?php if ($this->state->get('query.exactname'))
								{
									echo ' checked="checked" ';
								} ?> />
						</label>
					</fieldset>
				<?php endif; ?>
			</div>
		</div>

		<div class="btn btn-small pull-right" data-toggle="collapse" data-target="#search-options"></div>
		<h2>
			<?php echo Text::_('COM_KUNENA_SEARCH_OPTIONS'); ?>
		</h2>

		<div class="collapse in" id="search-options">
			<div class="well">
				<div class="row-fluid">
					<fieldset class="span4">
						<legend>
							<?php echo Text::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?>
						</legend>
						<?php $this->displayDateList('date'); ?>
						<?php $this->displayBeforeAfterList('beforeafter'); ?>
					</fieldset>

					<fieldset class="span4">
						<legend>
							<?php echo Text::_('COM_KUNENA_SEARCH_SORTBY'); ?>
						</legend>
						<?php $this->displaySortByList('sort'); ?>
						<?php $this->displayOrderList('order'); ?>
					</fieldset>

					<fieldset class="span4">
						<legend>
							<?php echo Text::_('COM_KUNENA_SEARCH_AT_A_SPECIFIC_DATE'); ?>
						</legend>
						<div id="searchatdate">
							<div class="input-append date">
								<input type="text" name="searchatdate" data-date-format="yyyy-dd-mm"
								       value="">
								<span class="add-on"><?php echo KunenaIcons::calendar(); ?></span>
							</div>
						</div>
					</fieldset>
				</div>

				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<legend>
								<?php echo Text::_('COM_KUNENA_SEARCH_START'); ?>
							</legend>
							<input type="text" name="limitstart"
							       value="<?php echo $this->escape($this->state->get('list.start')); ?>" size="5"/>
							<?php $this->displayLimitlist('limit'); ?>
						</fieldset>

						<?php if ($this->isModerator)
							:
							?>
							<fieldset>
								<legend>
									<?php echo Text::_('COM_KUNENA_SEARCH_SHOW'); ?>
								</legend>
								<label class="radio">
									<input type="radio" name="show" value="0"
										<?php if ($this->state->get('query.show') == 0)
										{
											echo 'checked="checked"';
										} ?> />
									<?php echo Text::_('COM_KUNENA_SEARCH_SHOW_NORMAL'); ?>
								</label>
								<label class="radio">
									<input type="radio" name="show" value="1"
										<?php if ($this->state->get('query.show') == 1)
										{
											echo 'checked="checked"';
										} ?> />
									<?php echo Text::_('COM_KUNENA_SEARCH_SHOW_UNAPPROVED'); ?>
								</label>
								<label class="radio">
									<input type="radio" name="show" value="2"
										<?php if ($this->state->get('query.show') == 2)
										{
											echo 'checked="checked"';
										} ?> />
									<?php echo Text::_('COM_KUNENA_SEARCH_SHOW_TRASHED'); ?>
								</label>
							</fieldset>
						<?php endif; ?>

					</div>

					<fieldset class="span6">
						<legend>
							<?php echo Text::_('COM_KUNENA_SEARCH_SEARCHIN'); ?>
						</legend>
						<?php $this->displayCategoryList('categorylist', 'size="10" multiple="multiple"'); ?>
						<label>
							<input type="checkbox" name="childforums" value="1"
								<?php if ($this->state->get('query.childforums'))
								{
									echo 'checked="checked"';
								} ?> />
							<?php echo Text::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN'); ?>
						</label>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="center">
			<button type="submit" class="btn btn-primary">
				<?php echo KunenaIcons::search() . ' ' . Text::_('COM_KUNENA_SEARCH_SEND'); ?>
			</button>
			<button type="reset" class="btn" onclick="window.history.back();">
				<?php echo KunenaIcons::cancel() . ' ' . Text::_('COM_KUNENA_CANCEL'); ?>
			</button>
		</div>
	</div>
</form>
