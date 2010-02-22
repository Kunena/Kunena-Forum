<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
$kunena_config = & CKunenaConfig::getInstance ();
$catid = JRequest::getInt ( 'catid', 0 );
?>
<div class="kunena-msgtitle left"><?php
					if ($this->my->id && $this->prevCheck < $this->kunena_message->time && ! in_array ( $this->kunena_message->thread, $this->read_topics )) {
						$msgtitle = 'msgtitle_new';
					} else {
						$msgtitle = 'msgtitle';
					} ?>
	<span class="<?php echo $msgtitle; ?>">
		<?php echo $this->msg_html->subject; ?>
	</span>
	<span class="msgdate" title="<?php echo CKunenaTimeformat::showDate($this->kunena_message->time, 'config_post_dateformat_hover'); ?>">
		<?php echo CKunenaTimeformat::showDate($this->kunena_message->time, 'config_post_dateformat'); ?>
	</span>
	<span class="msgkarma">
		<?php if (isset ( $this->msg_html->karma )) { echo $this->msg_html->karma;
			if (isset ( $this->msg_html->karmaplus )) echo '&nbsp;&nbsp;' . $this->msg_html->karmaplus . ' ' . $this->msg_html->karmaminus;
			} else { echo '&nbsp;'; } ?>
	</span>
</div>
<div>
	<div class="msgtext">
		<?php echo $this->msg_html->text; ?>
	</div>
</div>
<?php
// Include attachments template if we have any
if ( isset ( $this->msg_html->attachments ) ) {
	CKunenaTools::loadTemplate('/view/message.attachements.php');
} ?>
<div>
	<span><?php if (isset ( $this->msg_html->signature )) {
		echo '<div class="msgsignature">';
		echo $this->msg_html->signature;
		echo '</div>'; } ?>
	</span>
</div>
<?php if ( isset ( $this->msg_html->quickreply ) ) { ?>
<div id="kreply<?php echo $this->msg_html->id; ?>_form" class="kreply_form" style="display: none">
	<?php
		//contruct the reply subject
		$resubject = kunena_htmlspecialchars ( JString::strtolower ( JString::substr ( $this->msg_html->subject, 0, JString::strlen ( JText::_('COM_KUNENA_POST_RE') ) ) ) == JString::strtolower ( JText::_('COM_KUNENA_POST_RE') ) ? $this->msg_html->subject : JText::_('COM_KUNENA_POST_RE') . ' ' . $this->msg_html->subject );
	?>
	<form action="<?php echo CKunenaLink::GetPostURL(); ?>" method="post" name="postform" enctype="multipart/form-data">
		<input type="hidden" name="parentid" value="<?php echo $this->msg_html->id; ?>" />
		<input type="hidden" name="catid" value="<?php echo $catid; ?>" />
		<input type="hidden" name="action" value="post" />
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo $kunena_config->maxsubject; ?>" value="<?php echo html_entity_decode ( $resubject ); ?>" /><br />
		<textarea class="inputbox" name="message" rows="6" cols="60"></textarea><br />
		<input type="reset" class="kbutton kreply_cancel" name="cancel" value="<?php @print (JText::_('COM_KUNENA_CANCEL')) ; ?>" />
		<input type="submit" class="kbutton kreply_submit" name="submit" value="<?php @print (JText::_('COM_KUNENA_GEN_CONTINUE')) ; ?>" />
		<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE')?></small>
	</form>
</div>
<?php } ?>
<div class="kmessage_editMarkUp_cover"><?php
	if ($this->kunena_message->modified_by) {
		echo '<span class="kmessage_editMarkUp" title="'.CKunenaTimeformat::showDate($this->kunena_message->modified_time, 'config_post_dateformat_hover').'">' . JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' .
					CKunenaTimeformat::showDate($this->kunena_message->modified_time, 'config_post_dateformat' ) . ' ' . JText::_('COM_KUNENA_BY') . ' ' .
					($kunena_config->username ? $this->kunena_message->modified_username : $this->kunena_message->modified_name) . '.';
	if ($this->kunena_message->modified_reason) {
		echo JText::_('COM_KUNENA_REASON') . ': ' . kunena_htmlspecialchars ( stripslashes ( $this->kunena_message->modified_reason ) ); }
	echo '</span>'; }
	if ($kunena_config->reportmsg && $this->my->id > 1) {
	echo '<span class="kmessage_informMarkUp">' . CKunenaLink::GetReportMessageLink ( $catid, $this->msg_html->id, JText::_('COM_KUNENA_REPORT') ) . '</span>'; }
	//Check that the user is an admin to display the ip in messages
	$check = '0';
	if($kunena_config->hide_ip){
		$check = CKunenaTools::isAdmin ();
	} else {
		$check = CKunenaTools::isModerator($this->my->id);
	}
	if (isset ( $this->msg_html->ip ) && $check) {
	echo '<span class="kmessage_informMarkUp">' . CKunenaLink::GetMessageIPLink ( $this->msg_html->ip ) . '</span>'; } ?>
</div>