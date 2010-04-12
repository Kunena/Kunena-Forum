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

CKunenaPolls::call_javascript_form();
include_once (KUNENA_PATH_LIB . DS . 'kunena.bbcode.js.php');
JHTML::_('behavior.formvalidation');

$this->setTitle ( $this->title );

$this->k=0;
?>
<?php CKunenaTools::loadTemplate ( '/pathway.php' )?>

<form class="postform form-validate" id="postform" action="<?php echo CKunenaLink::GetPostURL()?>"
	method="post" name="postform" enctype="multipart/form-data" onsubmit="return myValidate(this);">
	<input type="hidden" name="action" value="<?php echo $this->action?>" />
	<?php if (!isset($this->selectcatlist)) : ?>
	<input type="hidden" name="catid" value="<?php echo $this->catid?>" />
	<?php endif; ?>
	<input type="hidden" name="id" value="<?php echo $this->id?>" />
	<?php if (! empty ( $this->kunena_editmode )) : ?>
	<input type="hidden" name="do" value="editpostnow" />
	<?php endif; ?>
	<?php echo JHTML::_( 'form.token' ); ?>

<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class="kblocktable<?php echo isset ( $msg_cat->class_sfx ) ? ' kblocktable' . $msg_cat->class_sfx : ''?>" id="kpostmessage">
	<thead>
		<tr>
			<th colspan="2">
				<div class="ktitle_cover km">
					<span class="ktitle kl">
						<?php echo $this->title?>
					</span>
				</div>
			</th>
		</tr>
	</thead>

	<tbody id="kpost_message">
		<?php if (isset($this->selectcatlist)): ?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn"><strong><?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY')?></strong></td>
			<td class="k-topicicons"><?php echo $this->selectcatlist?></td>
		</tr>
		<?php endif; ?>

		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn"><strong><?php
			echo JText::_('COM_KUNENA_GEN_NAME');
			?></strong></td>

			<td>
				<input type="text" id="kauthorname" name="authorname" size="35" class="kinputbox postinput required" maxlength="35" value="<?php echo $this->authorName;?>" <?php if (!$this->allow_name_change) echo 'disabled="disabled" '; ?>/>
			</td>
		</tr>

		<?php if ($this->allow_anonymous): ?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn"><strong><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?></strong></td>

			<td>
			<input type="checkbox" id="kanonymous" name="anonymous" value="1" <?php if ($this->anonymous) echo 'checked="checked"'; ?> /> <label for="kanonymous"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
			</td>
		</tr>
		<?php endif; ?>

		<?php
		if ($this->_config->askemail && !$this->my->id) {
		?>
		<tr class = "ksectiontableentry<?php echo 1+ $this->k^=1 ?>">
			<td class = "kleftcolumn"><strong><?php echo JText::_('COM_KUNENA_GEN_EMAIL');?></strong></td>
			<td><input type="text" id="email" name="email"  size="35" class="kinputbox postinput required validate-email" maxlength="35" value="<?php if ( !empty($this->emai) ) { echo $this->email; } ?>" /></td>
		</tr>
		<?php
		}
		?>

		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn"><strong><?php
				echo JText::_('COM_KUNENA_GEN_SUBJECT');
				?></strong></td>

			<td><input type="text"
				class="kinputbox postinput required"
				name="subject" id="subject" size="35"
				maxlength="<?php
				echo $this->_config->maxsubject;
				?>"
				value="<?php
				echo $this->resubject;
				?>" /></td>
		</tr>

		<?php if ($this->id == 0) : ?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1 ?>">
			<td class="kleftcolumn"><strong><?php
			echo JText::_('COM_KUNENA_GEN_TOPIC_ICON');
			?></strong></td>

			<td class="k-topicicons">
				<?php foreach ($topic_emoticons as $emoid=>$emoimg): ?>
					<input type="radio" name="topic_emoticon" value="<?php echo $emoid; ?>"
						<?php echo $this->emoid == $emoid ? ' checked="checked" ':'' ?> />
					<img src="<?php echo $emoimg;?>" alt="" border="0" />
				<?php endforeach; ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php
		// Now show bbcode editor
		CKunenaTools::loadTemplate('/editor/bbcode.php');
		?>

		<?php
		if ($this->_config->allowfileupload || ($this->_config->allowfileregupload && $this->my->id != 0) || ($this->_config->allowimageupload || ($this->_config->allowimageregupload && $this->my->id != 0) || CKunenaTools::isModerator ( $this->my->id, $this->catid ))) {
			//$this->document->addScript ( KUNENA_DIRECTURL . 'js/plupload/gears_init.js' );
			//$this->document->addScript ( KUNENA_DIRECTURL . 'js/plupload/plupload.full.min.js' );
			?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
			<td class="kleftcolumn"><strong><?php
			echo JText::_('COM_KUNENA_EDITOR_ATTACHMENTS');
			?></strong></td>
		<td>
			<div id="kattachment"><input id="kupload" name="kattachment" type="file" /><a href="#" style="display: none">Delete</a></div>
			<div id="kattachmentsnote"></div>

		<?php
		// Include attachments template if we have any
		if ( isset ( $this->msg_html->attachments ) ) {
			CKunenaTools::loadTemplate('/view/message.attachments.php');
		} ?>

		</td>
		</tr>

		<?php
		}

		if (!empty($this->cansubscribe)) {
			?>

		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
			<td class="kleftcolumn"><strong><?php
			echo JText::_('COM_KUNENA_POST_SUBSCRIBE');
			?></strong></td>

			<td><?php
			if ($this->_config->subscriptionschecked == 1) {
				?>

			<input type="checkbox" name="subscribeMe" value="1" checked="checked" /> <i><?php
				echo JText::_('COM_KUNENA_POST_NOTIFIED');
				?></i>

			<?php
			} else {
				?> <input type="checkbox" name="subscribeMe" value="1" /> <i><?php
				echo JText::_('COM_KUNENA_POST_NOTIFIED');
				?></i> <?php
			}
			?></td>
		</tr>
		<?php
		}
		//Begin captcha
		if ($this->hasCaptcha()) : ?>
		<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
			<td class="kleftcolumn"><strong><?php
			echo JText::_('COM_KUNENA_CAPDESC');
			?></strong></td>
			<td align="left" valign="middle" height="35px">
				<?php $this->displayCaptcha() ?>
			 </td>
		</tr>
		<?php
		endif;
		// Finish captcha
		?>
		<tr id="kpost_buttons_tr" class="ksectiontableentry1">
			<td id="kpost_buttons" colspan="2" style="text-align: center;">
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
			if ($this->_config->askemail) {
				echo $this->_config->showemail == '0' ? "<em>* - " . JText::_('COM_KUNENA_POST_EMAIL_NEVER') . "</em>" : "<em>* - " . JText::_('COM_KUNENA_POST_EMAIL_REGISTERED') . "</em>";
			}
			?>
			</td>
		</tr>

		<?php if ($this->hasThreadHistory ()) : ?>
		<tr>
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