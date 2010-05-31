<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<div>
	<div class="msgattach">
	<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>

			<ul class="kfile-attach-editing">
		<?php
		foreach($this->attachments as $attachment) :
		?>

			<li>
				<input type="checkbox" name="attach-id[]" checked="checked" value="<?php echo $attachment->id; ?>" /><span><?php echo $attachment->filename.'&nbsp; ('.number_format(($attachment->size)/1024,0,'',',').'KB)'; ?></span>
			</li>
		<?php
		endforeach;
		?>
			</ul>

	</div>
</div>