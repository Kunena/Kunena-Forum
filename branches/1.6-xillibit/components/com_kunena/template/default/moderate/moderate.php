<?php
/**
 * @version $Id$
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

<div class="kbt_cvr1">
<div class="kbt_cvr2">
<div class="kbt_cvr3">
<div class="kbt_cvr4">
<div class="kbt_cvr5">
<h1><?php echo JText::_('COM_KUNENA_TITLE_MODERATE_TOPIC'); ?>: <?php echo kunena_htmlspecialchars ( $this->message->subject ); ?></h1>
	<div id="kmod-container">
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

<div>
		<?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY'); ?> :<strong><?php
		echo kunena_htmlspecialchars ( $this->message->catname );
		?></strong></div>
<div>

<?php
if (empty($this->moderateMultiplesChoices)) {
	if ($this->moderateTopic) :	?>
		<input id="modmergetopic" type="radio" name="moderation" value="modmergetopic" /><?php echo 'Merge topic'; ?> <br />
		<input id="modmovetopic" type="radio" name="moderation" value="modmovetopic" ><?php echo 'Move Topic'; ?> <br />
<?php else :?>
		<input id="modmergemessage" type="radio" name="moderation" value="modmergemessage" /><?php echo 'Merge Message'; ?> <br />
		<input id="modmovemessage" type="radio" name="moderation" value="modmovemessage" ><?php echo 'Move Message'; ?> <br />
		<input id="modsplitmultpost" type="radio" name="moderation" value="modsplitmultpost" ><?php echo JText::_('COM_KUNENA_MODERATION_SPLIT_CHOOSE2'); ?> <br />
<?php endif;
} else {
	switch ($this->moderateMultiplesChoices) {
		case 'modmergetopic':
?>
	<input id="modmergetopic" type="radio" name="moderation" value="modmergetopic" /><?php echo 'Merge topic'; ?> <br />
<?php
   	 	break;
   	 	case 'modmovetopic':
?>
	<input id="modmovetopic" type="radio" name="moderation" value="modmovetopic" ><?php echo 'Move Topic'; ?> <br />
<?php
   	 	break;
   	 	case 'modmergemessage':
?>
	<input id="modmergemessage" type="radio" name="moderation" value="modmergemessage" /><?php echo 'Merge Message'; ?> <br />
<?php
   	 	break;
   	 	case 'modmovemessage':
?>
	<input id="modmovemessage" type="radio" name="moderation" value="modmovemessage" ><?php echo 'Move Message'; ?> <br />
<?php
   	 	break;
   	 	case 'modsplitmultpost':
?>
	<input id="modsplitmultpost" type="radio" name="moderation" value="modsplitmultpost" ><?php echo JText::_('COM_KUNENA_MODERATION_SPLIT_CHOOSE2'); ?> <br />
<?php
   	 	break;
	}
} ?>
<br />
		<div id="modcategorieslist"><?php
		echo JText::_ ( 'COM_KUNENA_POST_PROCEED_MODERATION_CATEGORY' );
		?>: <br />

		<?php
		echo $this->selectlist;
		?></div> <br />


		<div id="modtopicslist" style="display:none;"><?php
		echo JText::_ ( 'COM_KUNENA_POST_PROCEED_MODERATION_TOPIC' );
		?>: <br />

		<?php
		echo $this->selectlistmessage;
		?></div> <br />

		<input type="checkbox" <?php if ($this->config->boxghostmessage): ?> checked="checked" <?php endif; ?> name="leaveGhost"  value="<?php echo $this->config->boxghostmessage ? '1' : '0'; ?>" /> <?php echo JText::_ ( 'COM_KUNENA_POST_MOVE_GHOST' ); ?>
		<br />
<br />
		<?php
		echo JText::_ ( 'COM_KUNENA_MODERATE_FIELD_ID' );
		?>: <br />
		<input type="text" name="cattopicid" value="" /><br />

<br />

<input type="submit" class="button"
	value="<?php
		echo JText::_ ( 'COM_KUNENA_POST_MODERATION_PROCEED' );
		?>" /></div>
</form>
	</div>
</div>
</div>
</div>
</div>
</div>