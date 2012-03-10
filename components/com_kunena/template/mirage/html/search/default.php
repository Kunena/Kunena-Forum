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
	new Autocompleter.Request.JSON('kusername', '" . KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list&format=raw') . "', { 'postVar': 'search' });
});
// ]]>");
}

$this->setTitle(JText::_('COM_KUNENA_SEARCH_ADVSEARCH'));
?>
<div class="kmodule">
	<div class="box-wrapper">
		<div class="search-adv kbox box-color box-border box-border_radius box-border_radius-child box-shadow">
			<div class="headerbox-wrapper">
				<div class="header">
					<h2 class="header"><a title="Advanced Search" rel="kadvsearch-detailsbox"><?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH') ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="advsearch-detailsbox detailsbox">

					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=search') ?>" name="adminForm" id="adminForm" method="post">
						<input type="hidden" name="task" value="results" />
						<?php echo JHTML::_( 'form.token' ); ?>

						<div class="searchform-leftcol">
							<fieldset class="fieldset">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD') ?></legend>
								<label for="keywords" class="display-hidden searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS') ?>:</label>
								<input type="text" value="<?php echo $this->escape($this->state->get('searchwords')) ?>" size="30" name="q" class="box-width form-vertical form-field_simple" id="keywords" placeholder="<?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS') ?>" />
								<?php $this->displayModeList('mode', 'class="ks"') ?>
							</fieldset>
						</div>

						<div class="searchform-rightcol">
							<fieldset class="fieldset">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_USER') ?></legend>
								<label class="display-hidden searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_UNAME') ?>:</label>
								<input type="text" value="<?php echo $this->escape($this->state->get('query.searchuser')) ?>" name="searchuser" class="box-width form-vertical form-field_simple kautocomplete-off" id="kusername" placeholder="<?php echo JText::_('COM_KUNENA_SEARCH_UNAME') ?>" />
								<label class="display-hidden searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_EXACT') ?>:</label>
								<input type="checkbox" value="1" name="exactname" <?php if ($this->state->get('query.exactname')) echo $this->checked ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_EXACT') ?>
							</fieldset>
						</div>

						<div class="clrline"></div>

						<div class="searchform-leftcol">
							<fieldset id="search-posts-date" class="fieldset">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_FIND_POSTS') ?></legend>
								<?php $this->displayDateList('date', 'class="ks"') ?>
								<?php $this->displayBeforeAfterList('beforeafter', 'class="ks"') ?>
							</fieldset>
							<fieldset id="search-posts-sort" class="fieldset">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SORTBY'); ?></legend>
								<?php $this->displaySortByList('sort', 'class="ks"') ?>
								<?php $this->displayOrderList('order', 'class="ks"') ?>
							</fieldset>

							<fieldset id="search-posts-start" class="fieldset">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_START') ?></legend>
								<input type="text" size="5" value="<?php echo $this->escape($this->state->get('list.start')) ?>" name="limitstart" class="box-width form-vertical form-field_simple" />
								<?php $this->displayLimitlist('limit', 'class="ks"') ?>
							</fieldset>
						</div>

						<div class="searchform-rightcol">
							<fieldset class="fieldset">
								<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN') ?></legend>
								<?php $this->displayCategoryList('categorylist', 'class="inputbox" size="8" multiple="multiple"') ?>
								<br />
								<label id="childforums-lbl">
									<input type="checkbox" value="1" name="childforums" <?php if ($this->state->get('query.childforums')) echo 'checked="checked"' ?> />
									<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN') ?>
								</label>
							</fieldset>

							<?php if ( $this->me->isModerator() ) : ?>
							<fieldset class="fieldset">
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
						<div class="clr"></div>

						<div class="kost-buttons">
							<button title="Click here to search" type="submit" class="kbutton"><span><?php echo JText::_('COM_KUNENA_SEARCH_SEND') ?></span></button>
							<button onclick="window.location='<?php echo KunenaRoute::_ ( 'index.php?option=com_kunena' ) ?>';" title="Click here to cancel" type="button" class="kbutton"><span><?php echo JText::_('COM_KUNENA_SEARCH_CANCEL') ?></span></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
<?php $this->displaySearchResults() ?>
