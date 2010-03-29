<?php
/**
 * @version $Id: postsplit.php 2068 2010-03-16 14:18:50Z mahagr $
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
add code for viewing a userban
<!--
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
		echo kunena_htmlspecialchars ( stripslashes ( $this->message->subject ) );
		?></strong> <br />

<br />
		<?php
		echo JText::_ ( 'COM_KUNENA_BUTTON_SPLIT_TOPIC' );
		?>: <br />

<input type="radio" name="split" value="splitpost" /> Split only this
message<br />
		<?php
		echo $this->selectlist;
		?><br />

<br />

<input type="submit" class="button"
	value="<?php
		echo JText::_ ( 'COM_KUNENA_BUTTON_SPLIT_TOPIC' );
		?>" /></p>
</form>
-->