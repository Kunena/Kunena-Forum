<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
$this->document->addScriptDeclaration('// <![CDATA[
	function kunenaValidate(f) { return document.formvalidator.isValid(f); }
// ]]>');
?>

<div class="well well-small">
  <h2 class="page-header"><?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>: <?php echo $this->announcement->exists() ? JText::_('COM_KUNENA_ANN_EDIT') : JText::_('COM_KUNENA_ANN_ADD') ?></h2>
  <div class="row-fluid column-row">
    <div class="span12 column-item">
      <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement') ?>" method="post" name="editform" class="form-validate" id="editform" onsubmit="return kunenaValidate(this);">
        <input type="hidden" name="task" value="save" />
        <?php echo $this->displayInput('id') ?> <?php echo JHtml::_( 'form.token' ) ?>
        <div class="span12 column-item">
          <label class="span12"> <span><?php echo JText::_('COM_KUNENA_ANN_TITLE') ?>:</span> <span><?php echo $this->displayInput('title', 'class="klarge required" rows="80" cols="16"') ?></span> </label>
          <label> <span><?php echo JText::_('COM_KUNENA_ANN_SORTTEXT') ?>:</span> <span style="width:100%"><?php echo $this->displayInput('sdescription', 'class="ksmall required"') ?></span> </label>
        </div>
        <div class="span12 column-item">
          <label> <span><?php echo JText::_('COM_KUNENA_ANN_LONGTEXT') ?>:</span> <span><?php echo $this->displayInput('description', 'class="klarge" rows="80" cols="16"') ?></span> </label>
        </div>
        <div class="span12 column-item">
          <label> <span><?php echo JText::_('COM_KUNENA_ANN_DATE') ?>:</span> <span><?php echo $this->displayInput('created', 'addcreated', 'kanncreated') ?></span> </label>
          <label> <span><?php echo JText::_('COM_KUNENA_ANN_SHOWDATE') ?>:</span> <span><?php echo $this->displayInput('showdate') ?></span> </label>
          <label> <span><?php echo JText::_('COM_KUNENA_ANN_PUBLISH') ?>:</span> <span><?php echo $this->displayInput('published') ?></span> </label>
        </div>
        <input name="submit" class="btn" type="submit" value="<?php echo JText::_('COM_KUNENA_SAVE') ?>"/>
        <input onclick="javascript:window.history.back();" name="cancel" class="btn" type="button" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>"/>
      </form>
    </div>
  </div>
</div>
