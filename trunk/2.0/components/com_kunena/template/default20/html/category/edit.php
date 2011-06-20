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

JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
?>
<form enctype="multipart/form-data" name="adminForm" method="post" id="categoryform" class="adminForm form-validate" action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category&layout=manage') ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="catid" value="<?php echo $this->category->id ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<div class="ksection">
		<h2 class="kheader"><?php echo $this->category->id? JText::_('COM_KUNENA_EDIT_CATEGORY') : JText::_('COM_KUNENA_NEW_CATEGORY') ?></h2>
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
					<label for="parent_id"><?php echo JText::_('COM_KUNENA_PARENT') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['categories'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="published"><?php echo JText::_('COM_KUNENA_PUBLISHED') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['published']; ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="kdescription"><?php echo JText::_('COM_KUNENA_DESCRIPTIONADD') ?></label><br/>
				</div>
				<div class="kform-field">
					<textarea cols="50" rows="10" id="kdescription" name="description" class="ktxtarea required hasTip" title="Description :: Enter category description"><?php echo $this->escape ( $this->category->description ) ?></textarea>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="kheaderdesc"><?php echo JText::_('COM_KUNENA_HEADERADD') ?></label>
				</div>
				<div class="kform-field">
					<textarea cols="50" rows="5" id="kheaderdesc" name="headerdesc" class="ktxtarea required hasTip" title="Category Header :: Enter Category Header"><?php echo $this->escape ( $this->category->headerdesc ) ?></textarea>
				</div>
			</li>
		</ul>

		<ul class="kposthead clearfix">
			<li><h3><?php echo JText::_('COM_KUNENA_ACCESS') ?></h3></li>
		</ul>

		<ul class="kform kpostcategory clearfix">
			<?php if ($this->category->accesstype != 'none') : ?>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="locked"><?php echo JText::_('COM_KUNENA_A_ACCESSTYPE') ?></label>
				</div>
				<div class="kform-field">
					<?php echo JText::_('COM_KUNENA_INTEGRATION_'.strtoupper($this->category->accesstype)); ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="locked"><?php echo JText::_('COM_KUNENA_A_ACCESS') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['access']; ?>
				</div>
			</li>
			<?php elseif ($this->me->isAdmin() && $this->category->accesstype == 'none') : ?>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="pub_access"><?php echo JText::_('COM_KUNENA_PUBACC') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['pub_access'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="pub_recurse"><?php echo JText::_('COM_KUNENA_CGROUPS') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['pub_recurse'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="admin_access"><?php echo JText::_('COM_KUNENA_ADMINLEVEL') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['admin_access'] ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="admin_recurse"><?php echo JText::_('COM_KUNENA_CGROUPS') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['admin_recurse']; ?>
				</div>
			</li>
			<?php endif ?>
		</ul>

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
			<?php if (!$this->category->id || !$this->category->isSection()): ?>
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
					<?php echo $this->options ['allow_polls']; ?>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="channels"><?php echo JText::_('COM_KUNENA_CATEGORY_CHANNELS') ?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->options ['channels']; ?>
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
					<input type="text" value="<?php echo $this->escape ( $this->category->class_sfx ); ?>" maxlength="20" size="20" id="class_sfx" name="class_sfx" class="kinputbox postinput hasTip" title="Forum CSS Class Suffix :: Enter Forum CSS Class Suffix" />
				</div>
			</li>
		</ul>

		<?php if (!$this->category->id || !$this->category->isSection()): ?>
		<ul class="kposthead clearfix">
			<li><h3><?php echo JText::_('COM_KUNENA_MODERATION'); ?></h3></li>
		</ul>

		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-odd">
				<ul class="ksubhead clearfix">
					<li><h4><?php echo JText::_('COM_KUNENA_MODSASSIGNED') ?></h4></li>
				</ul>

				<div class="kuserlist-items">
					<?php if (count ( $this->moderators ) == 0) : ?>
					<?php echo JText::_('COM_KUNENA_NOMODS') ?>
					<?php else : ?>
					<?php
					foreach ( $this->moderators as $this->user ) {
						$this->action = '<label class="kuserlist-checkbox">'.JText::_('COM_KUNENA_THIS_MODERATOR_REMOVE').'<input type="checkbox" value="1" name="rmmod['.$this->user->userid.']" /></label>';
						echo $this->loadTemplate('moderator');
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
	</div>
</form>