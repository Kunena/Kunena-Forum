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
?>
<form action="<?php
		echo CKunenaLink::GetPostURL ();
		?>"
	method="post" name="myform"><input type="hidden" name="do"
	value="splitnow" /> <input type="hidden" name="id"
	value="<?php
		echo $this->id;
		?>" /> <input type="hidden" name="messubeject"
	value="<?php
		echo stripslashes ( $this->message->subject );
		?>" />

<p><?php
		echo JText::_ ( 'COM_KUNENA_GEN_TOPIC' );
		?>: <strong><?php
		echo KunenaParser::parseText ( stripslashes ( $this->message->subject ) );
		?></strong> <br />
		<?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY'); ?> :<strong><?php
		echo kunena_htmlspecialchars ( $this->message->catname );
		?></strong> <br />

<br />
		<input type="radio" name="split" value="splitpost" /><?php echo JText::_('COM_KUNENA_MODERATION_SPLIT_CHOOSE'); ?> <br />
 		<input type="radio" name="split" value="splitmultpost" ><?php echo JText::_('COM_KUNENA_MODERATION_SPLIT_CHOOSE2'); ?> <br />
<br />
		<?php
		echo JText::_ ( 'COM_KUNENA_BUTTON_SPLIT_TOPIC' );
		?>: <br />

		<?php
		echo $this->selectlist;
		?><br />
<br />
		<?php
		echo JText::_ ( 'COM_KUNENA_BUTTON_SPIT_CAT_ID' );
		?>: <br />
		<input type="text" name="splitcatid" value="" /><br />

<br />

<input type="submit" class="button"
	value="<?php
		echo JText::_ ( 'COM_KUNENA_BUTTON_SPLIT_TOPIC' );
		?>" /></p>
</form>