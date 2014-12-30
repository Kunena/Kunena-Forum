<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

if ($this->me->exists()) {
	$this->document->addScriptDeclaration( "// <![CDATA[
window.addEvent('domready', function() {
	// Attach auto completer to the following ids:
	new Autocompleter.Request.JSON('kusername', " . json_encode(KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list&format=raw')) . ", { 'postVar': 'search' });
});
// ]]>");
}
?>
<div class="kblock kadvsearch">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler <?php echo $this->search_class; ?>" title="<?php echo $this->search_title ?>" rel="advsearch"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="searchform" id="searchform">
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="task" value="results" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<table id="kforumsearch">
		<tbody id="advsearch"<?php echo $this->search_style; ?>>
			<tr class="krow1">
				<td class="kcol">
					<fieldset class="fieldset">
						<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?></legend>
						<label class="searchlabel" for="keywords"><?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS'); ?>:</label>
						<input id="keywords" type="text" class="ks input" name="query" size="30" value="<?php echo $this->escape($this->state->get('searchwords')) ?>" />
						<?php $this->displayModeList('mode', 'class="ks"') ?>
					</fieldset>
				</td>
				<td class="kcol">
					<fieldset class="fieldset">
						<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_USER'); ?></legend>
						<label class="searchlabel"><?php echo JText::_('COM_KUNENA_SEARCH_UNAME'); ?>:
							<input id="kusername" class="ks input" type="text" name="searchuser" value="<?php echo $this->escape($this->state->get('query.searchuser')); ?>" />
						</label>
						<?php /*
						<select class="ks" name="starteronly">
							<option value="0"<?php if ($this->state->get('query.starteronly')==0) echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_USER_POSTED'); ?></option>
							<!--<option value="1"<?php if ($this->state->get('query.starteronly')==1) echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_USER_STARTED'); ?></option>
							<option value="2"<?php if ($this->state->get('query.starteronly')==2) echo $this->selected;?>><?php echo JText::_('COM_KUNENA_SEARCH_USER_ACTIVE'); ?></option>-->
						</select>
						*/ ?>
						<label class="searchlabel">
							<?php echo JText::_('COM_KUNENA_SEARCH_EXACT'); ?>:
							<input type="checkbox" name="exactname" value="1" <?php if ($this->state->get('query.exactname')) echo $this->checked; ?> />
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
				<td class="kcol">
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
						<?php $this->displayDateList('date', 'class="ks"') ?>
						<?php $this->displayBeforeAfterList('beforeafter', 'class="ks"') ?>
					</fieldset>

					<fieldset class="fieldset" id="search-posts-sort">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_SORTBY'); ?>
						</legend>
						<?php $this->displaySortByList('sort', 'class="ks"') ?>
						<?php $this->displayOrderList('order', 'class="ks"') ?>
					</fieldset>

					<fieldset class="fieldset" id="search-posts-start">
						<legend>
							<?php echo JText::_('COM_KUNENA_SEARCH_START'); ?>
						</legend>
						<input class="ks input" type="text" name="limitstart" value="<?php echo $this->escape($this->state->get('list.start')); ?>" size="5" />
						<?php $this->displayLimitlist('limit', 'class="ks"') ?>
					</fieldset>
				</td>
				<td class="kcol">
					<fieldset class="fieldset">
						<legend><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN'); ?></legend>
						<?php $this->displayCategoryList('categorylist', 'class="inputbox" size="8" multiple="multiple"') ?>
						<label id="childforums-lbl">
							<input type="checkbox" name="childforums" value="1" <?php if ($this->state->get('query.childforums')) echo 'checked="checked"'; ?> />
							<span onclick="document.searchform.childforums.checked=(! document.searchform.childforums.checked);"><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN'); ?></span>
						</label>
					</fieldset>
					<?php if ($this->isModerator) : ?>
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
				</td>
			</tr>
			<tr>
				<td colspan="2" class="kcenter">
					<input class="kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?>"/>
					<input class="kbutton ks" type="reset" value="<?php echo JText::_('COM_KUNENA_CANCEL'); ?>" onclick="window.location='<?php echo KunenaRoute::_ ( 'index.php?option=com_kunena' ) ?>';"/>
				</td>
			</tr>
		</tbody>
	</table>
</form>
		</div>
	</div>
</div>

<?php if($this->results): ?>
<div class="kblock ksearchresult">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="ksearchresult"></a></span>
		<h2>
			<span>
				<?php echo JText::_('COM_KUNENA_SEARCH_RESULTS'); ?>
			</span>
		</h2>
		<div class="ksearchresult-desc km">
			<span><?php echo JText::sprintf ('COM_KUNENA_FORUM_SEARCH', $this->escape($this->state->get('searchwords')) ); ?></span>
		</div>
	</div>
	<div class="kcontainer" id="ksearchresult">
		<div class="kbody">
	<?php if ($this->error) : ?>
		<div>
			<?php echo $this->error; ?>
		</div>
	<?php endif; ?>

<table>
	<tbody>
		<tr>
			<td>
				<?php $this->displayRows() ?>
			</td>
		</tr>
		<tr class="ksth">
			<th colspan="3">
			<?php
			$resStart = $this->state->get('list.start') + 1;
			$resStop = $this->state->get('list.start') + count ( $this->results );
			if ($resStart < $resStop)
				$resStartStop = ( string ) ($resStart) . ' - ' . ( string ) ($resStop);
			else
				$resStartStop = '0';
			printf ( JText::_('COM_KUNENA_FORUM_SEARCHRESULTS'), $resStartStop, intval($this->total) );
			?>

			<?php if ($this->total > $this->state->get('list.limit')) : ?>
			<?php echo $this->getPagination(5); ?>
			<?php endif; ?>
			</th>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
<?php endif; ?>
