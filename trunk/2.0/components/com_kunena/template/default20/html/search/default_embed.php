<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
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
		<div class="ksearch-adv">
			<h2 class="kheader"><a title="Advanced Search" rel="kadvsearch-detailsbox"><?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH') ?></a></h2>
			<div class="kdetailsbox kadvsearch" id="kadvsearch-detailsbox">

				<form name="adminForm" id="searchform" method="post" action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=search') ?>">
					<input type="hidden" value="com_kunena" name="option" />
					<input type="hidden" value="search" name="view" />
					<input type="hidden" value="results" name="task" />
					<?php echo JHTML::_( 'form.token' ); ?>

					<div class="ksearchform-leftcol">
						<fieldset class="fieldset">
							<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD') ?></legend>
							<label for="keywords" class="searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS') ?>:</label><br />
							<input type="text" value="<?php echo $this->escape($this->state->get('searchwords')) ?>" size="30" name="q" class="ks input" id="keywords" />
							<select name="titleonly" class="ks" id="keywordfilter">
								<option value="0"<?php if ($this->state->get('query.titleonly')==0) echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_SEARCH_POSTS') ?></option>
								<option value="1"<?php if ($this->state->get('query.titleonly')==1) echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_SEARCH_TITLES') ?></option>
							</select>
						</fieldset>
					</div>

					<div class="ksearchform-rightcol">
						<fieldset class="fieldset">
							<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_USER') ?></legend>
							<label class="searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_UNAME') ?>:
								<input type="text" value="<?php echo $this->escape($this->state->get('query.searchuser')); ?>" name="searchuser" class="ks input kautocomplete-off" id="kusername" />
							</label>
							<br />
							<label class="searchlabel">
								<?php echo JText::_('COM_KUNENA_SEARCH_EXACT'); ?>:
								<input type="checkbox" value="1" name="exactname" <?php if ($this->state->get('query.exactname')) echo $this->checked ?> />
							</label>
						</fieldset>
					</div>

					<div class="clrline"></div>

					<div class="ksearchform-leftcol">
						<fieldset id="search-posts-date" class="fieldset">
							<legend><?php echo JText::_('COM_KUNENA_SEARCH_FIND_POSTS') ?></legend>
							<?php echo $this->searchdatelist ?>
							<?php echo $this->beforeafterlist ?>
						</fieldset>
						<fieldset id="search-posts-sort" class="fieldset">
							<legend><?php echo JText::_('COM_KUNENA_SEARCH_SORTBY'); ?></legend>
							<?php echo $this->sortbylist ?>
							<select name="order" class="ks">
								<option value="inc"<?php if ($this->state->get('query.order')=='inc') echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_SORTBY_INC') ?></option>
								<option value="dec"<?php if ($this->state->get('query.order')=='dec') echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_SORTBY_DEC') ?></option>
							</select>
						</fieldset>

						<fieldset id="search-posts-start" class="fieldset">
							<legend><?php echo JText::_('COM_KUNENA_SEARCH_START'); ?></legend>
							<input type="text" size="5" value="<?php echo $this->escape($this->state->get('list.start')); ?>" name="limitstart" class="ks input" />
							<?php echo $this->limitlist ?>
						</fieldset>
					</div>

					<div class="ksearchform-rightcol">
						<fieldset class="fieldset">
							<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN'); ?></legend>
							<?php echo $this->categorylist; ?>
							<br />
							<label id="childforums-lbl">
								<input type="checkbox" value="1" name="childforums" <?php if ($this->state->get('query.childforums')) echo 'checked="checked"'; ?> />
								<?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN'); ?>
							</label>
						</fieldset>

						<?php if ( $this->me->isModerator() ) : ?>
						<fieldset class="fieldset">
							<legend><?php echo JText::_('COM_KUNENA_SEARCH_SHOW'); ?></legend>
							<input id="show0" type="radio" name="show" value="0" <?php if ($this->state->get('query.show') == 0) echo 'checked="checked"'; ?> />
							<label for="show0"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_NORMAL'); ?></label><br />
							<input id="show1" type="radio" name="show" value="1" <?php if ($this->state->get('query.show') == 1) echo 'checked="checked"'; ?> />
							<label for="show1"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_UNAPPROVED'); ?></label><br />
							<input id="show2" type="radio" name="show" value="2" <?php if ($this->state->get('query.show') == 2) echo 'checked="checked"'; ?> />
							<label for="show2"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_TRASHED'); ?></label><br />
						</fieldset>
						<?php endif; ?>
					</div>
					<div class="clr"></div>

					<div class="kpost-buttons">
						<button title="Click here to search" type="submit" class="kbutton"><?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?></button>
						<button onclick="window.location='<?php echo CKunenaLink::GetKunenaURL();?>';" title="Click here to cancel" type="button" class="kbutton"><?php echo JText::_('COM_KUNENA_SEARCH_CANCEL'); ?></button>
					</div>
				</form>

			</div>
			<div class="clr"></div>
		</div>
		<?php $this->displaySearchResults() ?>