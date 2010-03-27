<?php
/**
 * @version $Id: $
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
?>
<form action="<?php
		echo CKunenaLink::GetPostURL ();
		?>"
	method="post" name="myform"><input type="hidden" name="do"
	value="domovemessage" /> <input type="hidden" name="id"
	value="<?php
		echo $this->id;
		?>" />

<p><?php
		echo JText::_ ( 'COM_KUNENA_GEN_TOPIC' );
		?>: <strong><?php
		echo kunena_htmlspecialchars ( stripslashes ( $this->message->subject ) );
		?></strong> <br />
		<?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY'); ?> :<strong><?php
		echo kunena_htmlspecialchars ( stripslashes ( $this->message->catname ) );
		?></strong> <br />

<br />
		<?php
		echo JText::_ ( 'COM_KUNENA_POST_MOVE_TOPIC' );
		?>: <br />

		<?php
		echo $this->selectlist;
		?> <br />

<input type="checkbox" checked name="leaveGhost" value="1" /> <?php
		echo JText::_ ( 'COM_KUNENA_POST_MOVE_GHOST' );
		?>

		<br />

<input type="submit" class="button"
	value="<?php
		echo JText::_ ( 'COM_KUNENA_GEN_MOVE' );
		?>" /></p>
</form>
