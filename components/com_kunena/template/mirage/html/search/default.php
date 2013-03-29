<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

if ($this->me->exists()) {
	$this->document->addScriptDeclaration( "// <![CDATA[
document.addEvent('domready', function() {
	// Attach auto completer to the following ids:
	new Autocompleter.Request.JSON('kusername', " . json_encode(KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list&format=raw')) . ", { 'postVar': 'search' });
});
// ]]>");
}

?>
<div class="kmodule search-default">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=search') ?>" name="adminForm" id="adminForm" method="post">
		<input type="hidden" name="task" value="results" />
		<?php echo JHtml::_( 'form.token' ); ?>
		<div class="kbox-wrapper kbox-full">
			<div class="search_adv-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
				<div class="headerbox-wrapper kbox-full">
					<div class="header">
						<h2 class="header"><a title="Advanced Search" rel="kadvsearch-detailsbox"><?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH') ?></a></h2>
					</div>
				</div>
				<div class="detailsbox-wrapper innerspacer kbox-full">
					<div class="advsearch-detailsbox detailsbox">
						<div class="searchform-leftcol">
							<fieldset class="fieldset kbox-hover kbox-border kbox-border_radius">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD') ?></legend>
								<label for="keywords" class="display-hidden searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS') ?>:</label>
								<input type="text" value="<?php echo $this->escape($this->state->get('searchwords')) ?>" size="30" name="q" class="kbox-width form-vertical form-field_simple" id="keywords" placeholder="<?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS') ?>" />
								<?php $this->displayModeList('mode', 'class=""') ?>
							</fieldset>
						</div>

						<div class="searchform-rightcol">
							<fieldset class="fieldset kbox-hover kbox-border kbox-border_radius">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_USER') ?></legend>
								<label class="display-hidden searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_UNAME') ?>:</label>
								<input type="text" value="<?php echo $this->escape($this->state->get('query.searchuser')) ?>" name="searchuser" class="kbox-width form-vertical form-field_simple kautocomplete-off" id="kusername" placeholder="<?php echo JText::_('COM_KUNENA_SEARCH_UNAME') ?>" />
								<label class="display-hidden searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_EXACT') ?>:</label>
								<input type="checkbox" value="1" name="exactname" <?php if ($this->state->get('query.exactname')) echo $this->checked ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_EXACT') ?>
							</fieldset>
						</div>
						<div class="searchform-leftcol">
							<fieldset id="search-posts-date" class="fieldset kbox-hover kbox-border kbox-border_radius">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_FIND_POSTS') ?></legend>
								<?php $this->displayDateList('date', 'class="form-horizontal"') ?>
								<?php $this->displayBeforeAfterList('beforeafter', 'class="form-horizontal"') ?>
							</fieldset>
							<fieldset id="search-posts-sort" class="fieldset kbox-hover kbox-border kbox-border_radius">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SORTBY'); ?></legend>
								<?php $this->displaySortByList('sort', 'class="form-horizontal"') ?>
								<?php $this->displayOrderList('order', 'class="form-horizontal"') ?>
							</fieldset>

							<fieldset id="search-posts-start" class="fieldset kbox-hover kbox-border kbox-border_radius">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_START') ?></legend>
								<input type="text" size="5" value="<?php echo $this->escape($this->state->get('list.start')) ?>" name="limitstart" class="kbox-width form-vertical form-field_simple" />
								<?php $this->displayLimitlist('limit', 'class="form-vertical"') ?>
							</fieldset>

							<fieldset id="search-in-a-specific-topic" class="fieldset kbox-hover kbox-border kbox-border_radius">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_ENTER_TOPICID'); ?></legend>
								<input type="text" value="<?php echo $this->escape($this->state->get('list.topic_id')); ?>" size="30" name="topic_id" class="kbox-width form-vertical form-field_simple" />
							</fieldset>
						</div>

						<div class="searchform-rightcol">
							<fieldset class="fieldset kbox-hover kbox-border kbox-border_radius">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN') ?></legend>
								<?php $this->displayCategoryList('categorylist', 'class="kbox-width form-vertical" size="8" multiple="multiple"') ?>
								<br />
								<label id="childforums-lbl">
									<input type="checkbox" value="1" name="childforums" <?php if ($this->state->get('query.childforums')) echo 'checked="checked"' ?> />
									<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN') ?>
								</label>
							</fieldset>

							<?php if ($this->isModerator) : ?>
							<fieldset class="fieldset kbox-hover kbox-border kbox-border_radius">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SHOW') ?></legend>
								<input id="show0" type="radio" name="show" value="0" <?php if ($this->state->get('query.show') == 0) echo 'checked="checked"' ?> />
								<label for="show0"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_NORMAL') ?></label><br />
								<input id="show1" type="radio" name="show" value="1" <?php if ($this->state->get('query.show') == 1) echo 'checked="checked"' ?> />
								<label for="show1"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_UNAPPROVED') ?></label><br />
								<input id="show2" type="radio" name="show" value="2" <?php if ($this->state->get('query.show') == 2) echo 'checked="checked"' ?> />
								<label for="show2"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_TRASHED') ?></label><br />
							</fieldset>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="footerkbox-wrapper innerspacer-bottom">
					<div class="footerkbox">
						<ul class="list-unstyled buttonbar buttons-category hcenter">
							<li class="item-button">
								<button title="Click here to search" type="submit" class="kbutton button-type-comm"><span><?php echo JText::_('COM_KUNENA_SEARCH_SEND') ?></span></button>
							</li>
							<li class="item-button">
								<button onclick="window.location='<?php echo KunenaRoute::_ ( 'index.php?option=com_kunena' ) ?>';" title="Click here to cancel" type="button" class="kbutton button-type-standard"><span><?php echo JText::_('COM_KUNENA_CANCEL') ?></span></button>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<?php $this->displaySearchResults() ?>
