<?php
/**
 * @version $Id: default.php 4221 2011-01-18 09:52:30Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument ();
$document->addScriptDeclaration( "// <![CDATA[
document.addEvent('domready', function() {
	// Attach auto completer to the following ids:
	new Autocompleter.Request.JSON('kusername', '" . CKunenaLink::GetJsonURL('autocomplete', 'getuser', false) . "', { 'postVar': 'value' });
});
// ]]>");
?>
<div class="kblock kadvsearch">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler <?php //echo $advsearch_class; ?>" title="<?php //echo $advsearch_title ?>" rel="advsearch"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" id="searchform" name="adminForm">
	<table id="kforumsearch">
		<tbody id="advsearch"<?php //echo $advsearch_style; ?>>
			<tr class="krow1">
				<td class="kcol-first">
					<fieldset class="fieldset">
						<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?></legend>
						<label class="searchlabel" for="keywords"><?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS'); ?>:</label>
						<input id="keywords" type="text" class="ks input" name="q" size="30" />
						<select id="keywordfilter" class="ks" name="titleonly">
							<option value="0"><?php echo JText::_('COM_KUNENA_SEARCH_SEARCH_POSTS'); ?></option>
							<option value="1"><?php echo JText::_('COM_KUNENA_SEARCH_SEARCH_TITLES'); ?></option>
						</select>
					</fieldset>
				</td>
				<td class="kcol-mid">
					<fieldset class="fieldset">
						<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_USER'); ?></legend>
						<label class="searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_UNAME'); ?>:
							<input id="kusername" class="ks input" type="text" name="searchuser" />
						</label>
						<?php /*
						<select class="ks" name="starteronly">
							<option value="0"<?php if ($this->params['starteronly']==0) echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_USER_POSTED'); ?></option>
							<!--<option value="1"<?php if ($this->params['starteronly']==1) echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_USER_STARTED'); ?></option>
							<option value="2"<?php if ($this->params['starteronly']==2) echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_USER_ACTIVE'); ?></option>-->
						</select>
						*/ ?>
						<label class="searchlabel">
							<?php echo JText::_('COM_KUNENA_SEARCH_EXACT'); ?>:
							<input type="checkbox" name="exactname" value="1" />
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<div class="kheader">
						<span class="ktoggler" id="search_opt_status"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="advsearch_options"></a></span>
						<h2><span><?php echo JText::_('COM_KUNENA_SEARCH_OPTIONS'); ?></span></h2>
					</div>
				</th>
			</tr>
			<tr class="krow1" id="advsearch_options">
				<td class="kcol-first">
<?php /*
					<fieldset class="fieldset">
						<legend style="padding:0px">
							<?php echo JText::_('COM_KUNENA_SEARCH_FIND_WITH'); ?>
						</legend>

						<div>
							<select class="ks" name="replyless" style="width:150px">
								<option value="0"<?php if ($replyless==0) echo $selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_LEAST'); ?></option>
								<option value="1"<?php if ($replyless==1) echo $selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_MOST'); ?></option>
							</select>

							<input type="text" class="bginput" style="font-size:11px" name="replylimit" size="3" value="<?php echo $replylimit; ?>"/>
							<?php echo JText::_('COM_KUNENA_SEARCH_ANSWERS'); ?>
						</div>
					</fieldset>
*/ ?>

					<fieldset class="fieldset" id="search-posts-date">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?>
						</legend>
						<?php
						echo $this->searchdatelist;

						echo $this->beforeafterlist;
						?>
					</fieldset>

					<fieldset class="fieldset" id="search-posts-sort">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_SORTBY'); ?>
						</legend>
						<?php
						echo $this->sortbylist;

						?>
						<select class="ks" name="order">
							<option value="inc"><?php echo JText::_('COM_KUNENA_SEARCH_SORTBY_INC'); ?></option>
							<option value="dec"><?php echo JText::_('COM_KUNENA_SEARCH_SORTBY_DEC'); ?></option>
						</select>
					</fieldset>

					<fieldset class="fieldset" id="search-posts-start">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_START'); ?>
						</legend>
						<input class="ks input" type="text" name="limitstart" size="5" />
						<?php

						echo $this->limitlist;

						?>
					</fieldset>
				</td>
				<td class="kcol-mid">
					<fieldset class="fieldset">
						<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN'); ?></legend>
						<?php echo $this->categorylist; ?>
						<label id="childforums-lbl">
							<input type="checkbox" name="childforums" value="1" />
							<span onclick="document.adminForm.childforums.checked=(! document.adminForm.childforums.checked);"><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN'); ?></span>
						</label>
					</fieldset>
					<?php if ( CKunenaTools::isModerator($this->me->userid) ) : ?>
					<fieldset class="fieldset">
						<legend><?php echo JText::_('COM_KUNENA_SEARCH_SHOW'); ?></legend>
						<input id="show0" type="radio" name="show" value="0" />
						<label for="show0"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_NORMAL'); ?></label><br />
						<input id="show1" type="radio" name="show" value="1" />
						<label for="show1"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_UNAPPROVED'); ?></label><br />
						<input id="show2" type="radio" name="show" value="2" />
						<label for="show2"><?php echo JText::_('COM_KUNENA_SEARCH_SHOW_TRASHED'); ?></label><br />
					</fieldset>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="kcenter">
					<input class="kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?>"/>
					<input class="kbutton ks" type="reset" value="<?php echo JText::_('COM_KUNENA_SEARCH_CANCEL'); ?>" onclick="window.location='<?php echo CKunenaLink::GetKunenaURL();?>';"/>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_kunena" />
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="task" value="results" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
		</div>
	</div>
</div>
