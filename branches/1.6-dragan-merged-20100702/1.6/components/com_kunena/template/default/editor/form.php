<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

global $topic_emoticons;

require_once (JPATH_COMPONENT . DS . 'lib' .DS. 'kunena.poll.class.php');
$kunena_poll = CKunenaPolls::getInstance();
$kunena_poll->call_javascript_form();
include_once (KUNENA_PATH_LIB . DS . 'kunena.bbcode.js.php');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
$document = JFactory::getDocument ();
$document->addScriptDeclaration('var kunena_anonymous_check_url = "'.CKunenaLink::GetJsonURL('anynomousallowed', '', false).'";');
$this->setTitle ( $this->escape($this->title) );

$this->k=0;
?>
<?php CKunenaTools::loadTemplate ( '/pathway.php' )?>

<form class="postform form-validate" id="postform" action="<?php echo CKunenaLink::GetPostURL()?>"
	method="post" name="postform" enctype="multipart/form-data" onsubmit="return myValidate(this);">
	<input type="hidden" name="action" value="<?php echo $this->action ?>" />
	<?php if (!isset($this->selectcatlist)) : ?>
	<input type="hidden" name="catid" value="<?php echo intval($this->catid) ?>" />
	<?php endif; ?>
	<input type="hidden" name="id" value="<?php echo intval($this->id) ?>" />
	<?php if (! empty ( $this->kunena_editmode )) : ?>
	<input type="hidden" name="do" value="editpostnow" />
	<?php endif; ?>
	<?php echo JHTML::_( 'form.token' ); ?>

<div class="k-bt-cvr1">
<div class="k-bt-cvr2">
<div class="k-bt-cvr3">
<div class="k-bt-cvr4">
<div class="k_bt_cvr5">
<table class="kblocktable<?php echo isset ( $msg_cat->class_sfx ) ? ' kblocktable' . $this->escape($msg_cat->class_sfx) : ''?>" id="kpostmessage">
	<thead>
		<tr>
			<th colspan="2">
				<div class="ktitle-cover km">
					<span class="ktitle kl">
						<?php echo $this->escape($this->title) ?>
					</span>
				</div>
			</th>
		</tr>
	</thead>

	<tbody id="kpost-message">
		<?php if (isset($this->selectcatlist)): ?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn"><strong><?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY')?></strong></td>
			<td class="ktopicicons"><?php echo $this->selectcatlist ?></td>
		</tr>
		<?php endif; ?>

		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn">
				<strong><?php echo JText::_('COM_KUNENA_GEN_NAME'); ?></strong>
			</td>
			<td>
				<input type="text" id="kauthorname" name="authorname" size="35" class="kinputbox postinput required" maxlength="35" value="<?php echo $this->escape($this->authorName);?>" <?php echo !$this->allow_name_change ? 'disabled="disabled" ' : ''; ?> />
			</td>
		</tr>

		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>" id="kanynomous_check" <?php if (!$this->allow_anonymous && $this->catid != 0 || !$this->cat_default_allow ): ?>style="display:none;"<?php endif; ?>>
			<td class="kleftcolumn">
				<strong><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?></strong>
			</td>
			<td>
				<input type="checkbox" id="kanonymous" name="anonymous" value="1" <?php if ($this->anonymous) echo 'checked="checked"'; ?> />
				<label for="kanonymous"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
			</td>
		</tr>

		<?php if ($this->config->askemail && !$this->my->id) : ?>
		<tr class = "ksectiontableentry<?php echo 1+ $this->k^=1 ?>">
			<td class = "kleftcolumn"><strong><?php echo JText::_('COM_KUNENA_GEN_EMAIL');?></strong></td>
			<td><input type="text" id="email" name="email"  size="35" class="kinputbox postinput required validate-email" maxlength="35" value="<?php echo !empty($this->email) ? $this->escape($this->email) : '' ?>" /></td>
		</tr>
		<?php endif; ?>

		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn">
				<strong><?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?></strong>
			</td>

			<td><input type="text" class="kinputbox postinput required" name="subject" id="subject" size="35"
				maxlength="<?php echo $this->escape($this->config->maxsubject); ?>" value="<?php echo $this->escape($this->resubject); ?>" />
			</td>
		</tr>

		<?php if ($this->parent == 0 && $this->config->topicicons) : ?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn">
				<strong><?php echo JText::_('COM_KUNENA_GEN_TOPIC_ICON'); ?></strong>
			</td>

			<td class="ktopicicons">
				<?php foreach ($topic_emoticons as $emoid=>$emoimg): ?>
				<input type="radio" name="topic_emoticon" value="<?php echo intval($emoid); ?>" <?php echo $this->emoid == $emoid ? ' checked="checked" ':'' ?> />
				<img src="<?php echo $this->escape($emoimg);?>" alt="" border="0" />
				<?php endforeach; ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php
		// Show bbcode editor
		CKunenaTools::loadTemplate('/editor/bbcode.php');
		?>

		<?php
		if ($this->config->allowfileupload || ($this->config->allowfileregupload && $this->my->id != 0)
			|| ($this->config->allowimageupload || ($this->config->allowimageregupload && $this->my->id != 0)
			|| CKunenaTools::isModerator ( $this->my->id, $this->catid ))) :
			//$this->document->addScript ( KUNENA_DIRECTURL . 'js/plupload/gears_init.js' );
			//$this->document->addScript ( KUNENA_DIRECTURL . 'js/plupload/plupload.full.min.js' );
			?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
			<td class="kleftcolumn">
				<strong><?php echo JText::_('COM_KUNENA_EDITOR_ATTACHMENTS') ?></strong>
			</td>
			<td>
				<div id="kattachment" class="kattachment">
					<span class="kattachment-id"></span>

					<input class="kfile-input-textbox" type="text" readonly="readonly" />
					<div class="hasTip kfile-hide" title="<?php echo $this->escape(JText::_('COM_KUNENA_FILE_EXTENSIONS_ALLOWED').' :: '. '<strong>'.$this->config->imagetypes.'</strong><br /><strong>'.$this->config->filetypes.'</strong>'); ?>" >
						<input type="button" value="<?php echo  JText::_('COM_KUNENA_EDITOR_ADD_FILE'); ?>" class="kfile-input-button kbutton" />
						<input id="kupload" class="kfile-input hidden" name="kattachment" type="file" onchange="javascript: document.getElementById('kfilename').value = this.value" />
					</div>
					<a href="#" class="kattachment-remove kbutton" style="display: none"><?php echo  JText::_('COM_KUNENA_GEN_REMOVE_FILE'); ?></a>
					<a href="#" class="kattachment-insert kbutton" style="display: none"><?php echo  JText::_('COM_KUNENA_EDITOR_INSERT'); ?></a>
				</div>

				<?php
				// Include attachments template if we have any
				if ( isset ( $this->attachments ) ) CKunenaTools::loadTemplate('/editor/attachments.php')
				?>
			</td>
		</tr>
		<?php endif; ?>

		<?php if (!empty($this->cansubscribe)) : ?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
			<td class="kleftcolumn">
				<strong><?php echo JText::_('COM_KUNENA_POST_SUBSCRIBE'); ?></strong>
			</td>
			<td>
				<?php if ($this->config->subscriptionschecked == 1) : ?>
				<input type="checkbox" name="subscribeMe" value="1" checked="checked" />
				<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i>
				<?php else : ?>
				<input type="checkbox" name="subscribeMe" value="1" />
				<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i>
				<?php endif; ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php
		//Begin captcha
		if ($this->hasCaptcha()) : ?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
			<td class="kleftcolumn">
				<strong><?php echo JText::_('COM_KUNENA_CAPDESC'); ?></strong>
			</td>
			<td align="left" valign="middle" height="35px">
				<?php $this->displayCaptcha() ?>
			</td>
		</tr>
		<?php endif;
		// Finish captcha
		?>
		<tr id="kpost-buttons_tr" class="ksectiontableentry1">
			<td id="kpost-buttons" colspan="2" style="text-align: center;">
				<input type="button" name="cancel" class="kbutton"
				value="<?php echo (' ' . JText::_('COM_KUNENA_GEN_CANCEL') . ' ');?>"
				onclick="javascript:window.history.back();"
				title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" />
				<input type="submit" name="ksubmit" class="kbutton"
				value="<?php echo (' ' . JText::_('COM_KUNENA_GEN_CONTINUE') . ' ');?>"
				title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" />
				</td>
		</tr>

		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
			<td colspan="2"><?php
			if ($this->config->askemail) {
				echo $this->config->showemail == '0' ? "<em>* - " . JText::_('COM_KUNENA_POST_EMAIL_NEVER') . "</em>" : "<em>* - " . JText::_('COM_KUNENA_POST_EMAIL_REGISTERED') . "</em>";
			}
			?>
			</td>
		</tr>

		<?php if ($this->hasThreadHistory ()) : ?>
		<tr class="ksectiontableentry2">
			<td colspan="2">
				<?php $this->displayThreadHistory (); ?>
			</td>
		</tr>
		<?php endif; ?>

	</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php
if (!$this->authorName) {
	echo '<script type="text/javascript">document.postform.authorname.focus();</script>';
} else if (!$this->resubject) {
	echo '<script type="text/javascript">document.postform.subject.focus();</script>';
} else {
	echo '<script type="text/javascript">document.postform.message.focus();</script>';
}
?>
</form>