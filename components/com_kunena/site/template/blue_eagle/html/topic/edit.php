<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');

$this->document->addScriptDeclaration('config_attachment_limit = '.(int) $this->config->attachment_limit );

$editor = KunenaBbcodeEditor::getInstance();
$editor->initialize('id');

include_once (KPATH_SITE.'/lib/kunena.bbcode.js.php');
include_once (KPATH_SITE.'/lib/kunena.special.js.php');

$this->k=0;
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="postform form-validate" id="postform" name="postform" enctype="multipart/form-data" onsubmit="return myValidate(this);">
	<input type="hidden" name="view" value="topic" />
	<?php if ($this->message->exists()) : ?>
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="mesid" value="<?php echo intval($this->message->id) ?>" />
	<?php else: ?>
	<input type="hidden" name="task" value="post" />
	<input type="hidden" name="parentid" value="<?php echo intval($this->message->parent) ?>" />
	<?php endif; ?>
	<?php if (!isset($this->selectcatlist)) : ?>
	<input type="hidden" name="catid" value="<?php echo intval($this->message->catid) ?>" />
	<?php endif; ?>
	<?php echo JHtml::_( 'form.token' ); ?>

<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo $this->escape($this->title)?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table class="kblocktable<?php echo !empty ( $this->category->class_sfx ) ? ' kblocktable' . $this->escape($this->category->class_sfx) : ''?>" id="kpostmessage">
	<tbody id="kpost-message">
		<?php if (isset($this->selectcatlist)): ?>
		<tr id="kpost-category" class="krow<?php echo 1 + $this->k^=1 ?>">
			<td class="kcol-first"><strong><?php echo JText::_('COM_KUNENA_CATEGORY')?></strong></td>
			<td class="kcol-mid"><?php echo $this->selectcatlist?></td>
		</tr>
		<?php endif; ?>

		<?php if ($this->message->userid) : ?>
		<tr class="krow<?php echo 1 + $this->k^=1 ?>" id="kanynomous-check" <?php if (!$this->category->allow_anonymous): ?>style="display:none;"<?php endif; ?>>
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?></strong>
			</td>
			<td class="kcol-mid">
				<input type="checkbox" id="kanonymous" name="anonymous" value="1" <?php if ($this->post_anonymous) echo 'checked="checked"'; ?> />
				<label for="kanonymous"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
			</td>
		</tr>
		<?php endif; ?>

		<tr class="krow<?php echo 1 + $this->k^=1 ?>" id="kanynomous-check-name"
		<?php if ( $this->me->userid && !$this->category->allow_anonymous ): ?>style="display:none;"<?php endif; ?>>
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_GEN_NAME'); ?></strong>
			</td>
			<td class="kcol-mid">
				<input type="text" id="kauthorname" name="authorname" size="35" class="kinputbox postinput required" maxlength="35" value="<?php echo $this->escape($this->message->name);?>" />
			</td>
		</tr>

		<?php if ($this->config->askemail && !$this->me->userid) : ?>
		<tr class = "krow<?php echo 1+ $this->k^=1 ?>">
			<td class = "kcol-first"><strong><?php echo JText::_('COM_KUNENA_GEN_EMAIL');?></strong></td>
			<td class="kcol-mid">
				<input type="text" id="email" name="email"  size="35" class="kinputbox postinput required validate-email" maxlength="35" value="<?php echo !empty($this->message->email) ? $this->escape($this->message->email) : '' ?>" />
				<br />
				<?php echo $this->config->showemail == '0' ? JText::_('COM_KUNENA_POST_EMAIL_NEVER') : JText::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?>
			</td>
		</tr>
		<?php endif; ?>

		<tr id="kpost-subject" class="krow<?php echo 1 + $this->k^=1 ?>">
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?></strong>
			</td>

			<td class="kcol-mid"><input type="text" class="kinputbox postinput required" name="subject" id="subject" size="35"
				maxlength="<?php echo $this->escape($this->config->maxsubject); ?>" value="<?php echo $this->escape($this->message->subject); ?>" tabindex="1" />
			</td>
		</tr>

		<?php if (!empty($this->topicIcons)) : ?>
		<tr id="kpost-topicicons" class="krow<?php echo 1 + $this->k^=1 ?>">
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_GEN_TOPIC_ICON'); ?></strong>
			</td>

			<td class="kcol-mid">
				<?php foreach ($this->topicIcons as $id=>$icon): ?>
				<span class="kiconsel">
				<input type="radio" name="topic_emoticon" value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ':'' ?> />
				<img src="<?php echo $this->ktemplate->getTopicIconIndexPath($icon->id, true);?>" alt="" border="0" />
				</span>
				<?php endforeach; ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php
		// Show bbcode editor
		$this->displayTemplateFile('topic', 'edit', 'editor');
		?>

		<?php if ($this->allowedExtensions) : ?>
		<tr id="kpost-attachments" class="krow<?php echo 1 + $this->k^=1;?>">
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_EDITOR_ATTACHMENTS') ?></strong>
			</td>
			<td class="kcol-mid">
				<div id="kattachment-id" class="kattachment">
					<span class="kattachment-id-container"></span>

					<input class="kfile-input-textbox" type="text" readonly="readonly" />
					<div class="kfile-hide hasTip" title="<?php echo JText::_('COM_KUNENA_FILE_EXTENSIONS_ALLOWED')?>::<?php echo $this->escape(implode(', ', $this->allowedExtensions)) ?>" >
						<input type="button" value="<?php echo  JText::_('COM_KUNENA_EDITOR_ADD_FILE'); ?>" class="kfile-input-button kbutton" />
						<input id="kupload" class="kfile-input" name="kattachment" type="file" />
					</div>
					<a href="#" class="kattachment-remove kbutton" style="display: none"><?php echo  JText::_('COM_KUNENA_GEN_REMOVE_FILE'); ?></a>
					<a href="#" class="kattachment-insert kbutton" style="display: none"><?php echo  JText::_('COM_KUNENA_EDITOR_INSERT'); ?></a>
				</div>

				<?php $this->displayAttachments($this->message); ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($this->config->keywords && $this->me->isModerator ( $this->topic->getCategory() ) ) : ?>
		<tr id="kpost-tags" class="krow<?php echo 1 + $this->k^=1;?>">
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS') ?></strong>
			</td>
			<td class="kcol-mid">
				<input type="text" class="kinputbox postinput" name="tags" id="tags" size="35" maxlength="100" value="<?php echo $this->escape($this->topic->getKeywords(false, ', ')); ?>" />
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($this->config->userkeywords && $this->me->userid) : ?>
		<tr id="kpost-tags" class="krow<?php echo 1 + $this->k^=1;?>">
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_OWN') ?></strong>
			</td>
			<td class="kcol-mid">
				<input type="text" class="kinputbox postinput" name="mytags" id="mytags" size="35" maxlength="100" value="<?php echo $this->escape($this->topic->getKeywords($this->me->userid, ', ')); ?>" />
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($this->canSubscribe()) : ?>
		<tr id="kpost-subscribe" class="krow<?php echo 1 + $this->k^=1;?>">
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_POST_SUBSCRIBE'); ?></strong>
			</td>
			<td class="kcol-mid">
				<input type="checkbox" name="subscribeMe" id="subscribeMe" value="1" <?php if ($this->subscriptionschecked == 1) echo 'checked="checked"' ?> />
				<label for="subscribeMe"><i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i></label>
			</td>
		</tr>
		<?php endif; ?>
		<?php if (!empty($this->captchaHtml)) : ?>
		<tr id="kpost-captcha" class="krow<?php echo 1 + $this->k^=1;?>">
			<td class="kcol-first">
				<strong><?php echo JText::_('COM_KUNENA_CAPDESC'); ?></strong>
			</td>
			<td class="kcol-mid">
				<?php echo $this->captchaHtml ?>
			</td>
		</tr>
		<?php endif; ?>
		<tr id="kpost-buttons" class="krow1">
			<td id="kpost-buttons" colspan="2">
				<input type="submit" name="ksubmit" class="kbutton"
				value="<?php echo (' ' . JText::_('COM_KUNENA_SUBMIT') . ' ');?>"
				title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" tabindex="4" />
				<input type="button" name="preview" class="kbutton"
				onclick="kToggleOrSwapPreview('kbbcode-preview-bottom')"
				value="<?php echo (' ' . JText::_('COM_KUNENA_PREVIEW') . ' ');?>"
				title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEW'));?>:: "tabindex="3" />
				<input type="button" name="cancel" class="kbutton"
				value="<?php echo (' ' . JText::_('COM_KUNENA_CANCEL') . ' ');?>"
				onclick="window.history.back();"
				title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" tabindex="5" />
			</td>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
<?php
if (!$this->message->name) {
	echo '<script type="text/javascript">document.postform.authorname.focus();</script>';
} else if (!$this->topic->subject) {
	echo '<script type="text/javascript">document.postform.subject.focus();</script>';
} else {
	echo '<script type="text/javascript">document.postform.message.focus();</script>';
}
?>
</form><?php if ($this->hasThreadHistory ()) : ?>
<?php $this->displayThreadHistory (); ?>
<?php endif; ?>
