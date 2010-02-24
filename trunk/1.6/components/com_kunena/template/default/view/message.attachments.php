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

					// Check for thumbnail and if available, use for display
					if (file_exists(JPATH_ROOT.$attachment->folder.'/thumb/'.$attachment->filename)){
						$thumb = $attachment->folder.'/thumb/'.$attachment->filename;
						$imgsize = '';
					} else {
						$thumb = $attachment->folder.'/'.$attachment->filename;
						$imgsize = 'width="'.$kunena_config->thumbwidth.'px" height="'.$kunena_config->thumbheight.'px"';
					}

					echo '<a href="'.$attachment->folder.'/'.$attachment->filename.'" rel="nofollow">'.
						'<img '.$imgsize.' src="'.$thumb.'" alt="'.$attachment->filename.'" />'.
						'</a>'.
						'<span><a href="'.$attachment->folder.'/'.$attachment->filename.'" rel="nofollow">'.$attachment->filename.'</a></span>';
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
