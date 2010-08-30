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
	<div class="kmsgattach">
	<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
		<ul class="kfile-attach">
		<?php foreach($this->attachments as $attachment) :
			if(!$attachment->disabledimgforguest && !$attachment->disabledfileforguest) {?>
			<li>
				<?php echo $attachment->thumblink; ?>
				<span>
					<?php echo $attachment->textLink; ?>
				</span>
			</li>
		<?php } elseif( $attachment->disabledimgforguest) {
			echo '<b>' . JText::_ ( 'COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG' ) . '</b>';
		 } elseif($attachment->disabledfileforguest) {
			 echo '<b>' . JText::_ ( 'COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE' ) . '</b>';
		 }
		 endforeach; ?>
		</ul>
	</div>
</div>
