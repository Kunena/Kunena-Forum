<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
?>
<div class="ksection">
	<h2 class="kheader"><?php echo $this->category->exists() ? JText::_('COM_KUNENA_EDIT_CATEGORY') : JText::_('COM_KUNENA_NEW_CATEGORY') ?></h2>
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category') ?>" enctype="multipart/form-data" id="adminForm" name="adminForm" method="post" class="adminForm form-validate">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="catid" value="<?php echo $this->category->id ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<ul class="kposthead clearfix">
			<li><h3><?php echo JText::_('COM_KUNENA_BASIC_INFO') ?></h3></li>
		</ul>
		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="catname"><?php echo JText::_('COM_KUNENA_NAMEADD') ?></label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape ( $this->category->name ) ?>" maxlength="100" size="35" id="catname" name="name" class="kinputbox postinput required hasTip" title="Name :: Enter Category Name" />
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="catalias"><?php echo JText::_('COM_KUNENA_A_CATEGORY_ALIAS') ?></label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape ( $this->category->alias ) ?>" maxlength="100" size="35" id="catalias" name="alias" class="kinputbox postinput required hasTip" title="Alias :: Enter Category Alias" />
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="parent_id"><?php echo JText::_('COM_KUNENA_PARENT') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['categories'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="published"><?php echo JText::_('COM_KUNENA_PUBLISHED') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['published'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="kdescription"><?php echo JText::_('COM_KUNENA_DESCRIPTIONADD') ?></label><br/>
				</div>
				<div class="kform-field">
					<textarea cols="50" rows="10" id="kdescription" name="description" class="ktxtarea required hasTip" title="Description :: Enter category description"><?php echo $this->escape ( $this->category->description ) ?></textarea>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="kheaderdesc"><?php echo JText::_('COM_KUNENA_HEADERADD') ?></label>
				</div>
				<div class="kform-field">
					<textarea cols="50" rows="5" id="kheaderdesc" name="headerdesc" class="ktxtarea required hasTip" title="Category Header :: Enter Category Header"><?php echo $this->escape ( $this->category->headerdesc ) ?></textarea>
				</div>
			</li>
		</ul>

		<?php if ($this->me->isAdmin()) : ?>
		<ul class="kposthead clearfix">
			<li><h3><?php echo JText::_('COM_KUNENA_ACCESS') ?></h3></li>
		</ul>

		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="accesstype"><?php echo JText::_('COM_KUNENA_A_ACCESSTYPE') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['accesstypes'] ?>
				</div>
			</li>
			<?php foreach ($this->options ['accesslists'] as $accesstype=>$accesslist) foreach ($accesslist as $accessinput) : ?>
			<li class="kpostcategory-row krow-even kaccess kaccess-<?php echo $accesstype ?>" style="<?php echo $row->accesstype != $accesstype ? 'display:none' : '' ?>">
				<div class="kform-label">
					<label for="locked"><?php echo $accessinput['title'] ?></label>
				</div>
				<div class="kform-field">
					<?php echo $accessinput['input'] ?>
				</div>
			</li>
			<?php endforeach ?>
		</ul>
		<?php endif ?>

		<ul class="kposthead clearfix">
			<li><h3><?php echo JText::_('COM_KUNENA_SETTINGS') ?></h3></li>
		</ul>

		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="locked"><?php echo JText::_('COM_KUNENA_LOCKED1') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['forumLocked'] ?>
				</div>
			</li>
			<?php if (!$this->category->exists() || !$this->category->isSection()): ?>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="review"><?php echo JText::_('COM_KUNENA_REV') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['forumReview'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="allow_anonymous"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW'); ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['allow_anonymous'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="post_anonymous"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['post_anonymous'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="allow_polls"><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['allow_polls'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="channels"><?php echo JText::_('COM_KUNENA_CATEGORY_CHANNELS') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['channels'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="channels"><?php echo JText::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['topic_ordering'] ?>
				</div>
			</li>
			<?php endif ?>
		</ul>

		<ul class="kposthead clearfix">
			<li><h3><?php echo JText::_('COM_KUNENA_STYLES') ?></h3></li>
		</ul>

		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="class_sfx"><?php echo JText::_('COM_KUNENA_CLASS_SFX') ?></label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape ( $this->category->class_sfx ) ?>" maxlength="20" size="20" id="class_sfx" name="class_sfx" class="kinputbox postinput hasTip" title="Forum CSS Class Suffix :: Enter Forum CSS Class Suffix" />
				</div>
			</li>
		</ul>

		<?php if (!$this->category->exists() || !$this->category->isSection()): ?>
		<ul class="kposthead clearfix">
			<li><h3><?php echo JText::_('COM_KUNENA_MODERATION') ?></h3></li>
		</ul>

		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-odd">
				<ul class="ksubhead clearfix">
					<li><h4><?php echo JText::_('COM_KUNENA_MODSASSIGNED') ?></h4></li>
				</ul>

				<div class="kuserlist-items">
					<?php if (empty($this->moderators)) : ?>
					<?php echo JText::_('COM_KUNENA_NOMODS') ?>
					<?php else : ?>
					<?php
					foreach ( $this->moderators as $this->user ) {
						$this->action = '<label class="kuserlist-checkbox">'.JText::_('COM_KUNENA_THIS_MODERATOR_REMOVE').'<input type="checkbox" value="1" name="rmmod['.$this->user->userid.']" /></label>';
						$this->displayTemplateFile('category', 'edit', 'moderator');
					}
					?>
					<?php endif ?>
					<div class="clr">
						<button class="kbutton" type="submit" title="<?php echo JText::_('COM_KUNENA_MODERATOR_ADD_DESC') ?>"><?php echo JText::_('COM_KUNENA_MODERATOR_ADD') ?></button>
					</div>
				</div>
			</li>
		</ul>
		<?php endif ?>

		<div class="kpost-buttons">
			<button class="kbutton" onclick="javascript: submitbutton('apply')" title="<?php echo JText::_( 'COM_KUNENA_APPLY_DESC') ?>"><?php echo JText::_( 'COM_KUNENA_APPLY' ) ?></button>
			<button class="kbutton" onclick="javascript: submitbutton('save')" title="<?php echo JText::_( 'COM_KUNENA_SAVE_DESC') ?>"><?php echo JText::_( 'COM_KUNENA_SAVE' ) ?></button>
			<button class="kbutton" onclick="javascript: submitbutton('cancel')" title="<?php echo JText::_( 'COM_KUNENA_CANCEL_DESC') ?>"><?php echo JText::_( 'COM_KUNENA_CANCEL' ) ?></button>
		</div>
	</form>
</div>