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
$kunena_config = & CKunenaConfig::getInstance ();

if ( isset ( $this->msg_html->attachments ) ) { ?>
<div>
	<div class="msgattach">
	<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
		<ul class="kfile-attach">
		<?php
		foreach($this->msg_html->attachments as $attachment){
		?>
			<li>
			<?php
			// First lets check the attachment file type
			switch (strtolower($attachment->shorttype)){
				case 'image' :

					// TODO: Add check for thumbnail and display thumb instead

					// TODO: Add config size limiters to image
					echo '<a href="'.$attachment->folder.'/'.$attachment->filename.'" rel="nofollow">'.
						'<img width="64px" height="64px" src="'.$attachment->folder.'/'.$attachment->filename.'" alt="'.$attachment->filename.'" />'.
						'</a>'.
						'<span>'.$attachment->filename.'</span>';
					break;
				default :
					// Filetype without thumbnail or icon support - use default file icon

					// TODO: Add generic attachment icon
					// TODO: Replace href link with CKunenaLink::Call
					echo '<span><a href="'.$attachment->folder.'/'.$attachment->filename.'" rel="nofollow">'.$attachment->filename.'</a></span>';
			}
			?>
			</li>
		<?php
		}
		?>
		</ul>
	</div>
</div>
<?php } ?>
