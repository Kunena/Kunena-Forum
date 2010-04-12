<?php
/**
 * @version $Id: moderate.php 1890 2010-02-04 23:24:48Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>

<form action="<?php
		echo CKunenaLink::GetPostURL ();
		?>"
	method="post" name="myform"><input type="hidden" name="do"
	value="domoderatecommon" /> <input type="hidden" name="id"
	value="<?php
		echo $this->id;
		?>" />
		<input type="hidden" name="catid"
	value="<?php
		echo $this->catid;
		?>" />

<p><?php
		echo JText::_ ( 'COM_KUNENA_GEN_TOPIC' );
		?>: <strong><?php
		echo kunena_htmlspecialchars ( $this->message->subject );
		?></strong> <br />
		<?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY'); ?> :<strong><?php
		echo kunena_htmlspecialchars ( $this->message->catname );
		?></strong> <br />

<br />

		<input id="modmergetopic" type="radio" name="moderation" value="modmergetopic" /><?php echo 'Merge topic'; ?> <br />
		<input id="modmergemessage" type="radio" name="moderation" value="modmergemessage" /><?php echo 'Merge Message'; ?> <br />
 		<input id="modmovetopic" type="radio" name="moderation" value="modmovetopic" ><?php echo 'Move Topic'; ?> <br />
 		<input id="modmovemessage" type="radio" name="moderation" value="modmovemessage" ><?php echo 'Move Message'; ?> <br />
 		<input id="modsplitmultpost" type="radio" name="moderation" value="modsplitmultpost" ><?php echo JText::_('COM_KUNENA_MODERATION_SPLIT_CHOOSE2'); ?> <br />
<br />
		<div id="modtopicslist"><?php
		echo JText::_ ( 'COM_KUNENA_POST_PROCEED_MODERATION_TOPIC' );
		?>: <br />

		<?php
		echo $this->selectlist;
		?></div> <br />

		<div id="modcategorieslist"><?php
		echo JText::_ ( 'COM_KUNENA_POST_PROCEED_MODERATION_CATEGORY' );
		?>: <br />

		<?php
		echo $this->selectlistmessage;
		?></div> <br />

		<?php
		echo JText::_ ( 'COM_KUNENA_MODERATE_FIELD_ID' );
		?>: <br />
		<input type="text" name="cattopicid" value="" /><br />

		<input type="checkbox" <?php if ($this->_config->boxghostmessage): ?> checked="checked" <?php endif; ?> name="leaveGhost"  value="<?php echo $this->_config->boxghostmessage ? '1' : '0'; ?>" /> <?php
		echo JText::_ ( 'COM_KUNENA_POST_MOVE_GHOST' );
		?>


<br />

<input type="submit" class="button"
	value="<?php
		echo JText::_ ( 'COM_KUNENA_POST_MODERATION_PROCEED' );
		?>" /></p>
</form>
