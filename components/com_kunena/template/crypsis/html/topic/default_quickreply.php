<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if ( $this->quickreply ) : ?>

<li id="kreply<?php echo $this->displayMessageField('id') ?>_form" class="kreply-form" style="display: none">
  <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic') ?>" method="post" enctype="multipart/form-data" name="postform" id="postform">
    <input type="hidden" name="task" value="post" />
    <input type="hidden" name="parentid" value="<?php echo $this->displayMessageField('id') ?>" />
    <input type="hidden" name="catid" value="<?php echo $this->displayCategoryField('id') ?>" />
    <?php echo JHtml::_( 'form.token' ) ?>
    <?php if ($this->me->exists() && $this->category->allow_anonymous): ?>
    <input type="text" name="authorname" size="35" class="kinputbox postinput" maxlength="35" value="<?php echo $this->escape($this->profile->getName()) ?>" />
    <br />
    <input type="checkbox" id="kanonymous<?php echo $this->displayMessageField('id') ?>" name="anonymous" value="1" class="kinputbox postinput" <?php if ($this->category->post_anonymous) echo 'checked="checked"'; ?> />
    <label for="kanonymous<?php echo intval($this->message->id) ?>"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></label>
    <br />
    <?php else: ?>
    <input type="hidden" name="authorname" value="<?php echo $this->escape($this->profile->getName()) ?>" />
    <?php endif; ?>
    <input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo intval($this->config->maxsubject); ?>" value="<?php echo $this->displayMessageField('subject') ?>" />
    <br />
    <textarea class="inputbox" name="message" rows="6" cols="60"></textarea>
    <br />
    <?php if ($this->topic->authorise('subscribe')) : ?>
    <input type="checkbox" name="subscribeMe" value="1" <?php echo ($this->config->subscriptionschecked == 1) ? 'checked="checked"' : '' ?> />
    <i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i> <br />
    <?php endif; ?>
    <input type="submit" class="btn kreply-submit" name="submit" value="<?php echo JText::_('COM_KUNENA_SUBMIT') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" />
    <input type="reset" class="btn kreply-cancel" name="cancel" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" />
    <small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE') ?></small>
  </form>
</li>
<?php endif ?>
