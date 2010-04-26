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

$k = 0;
?>
<b><?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY' )?>:</b>
<?php echo $this->subject ?><br />
<?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_MAX' ) . ' ' . $this->config->historylimit . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_LAST' )?><br />
<table border="0" cellspacing="1" cellpadding="3" width="100%"
	class="kreview_table">
	<tr>
		<td class="kreview_header" width="20%" align="center">
			<strong><?php echo JText::_ ( 'COM_KUNENA_GEN_AUTHOR' )?></strong>
		</td>

		<td class="kreview_header" align="center">
			<strong><?php echo JText::_ ( 'COM_KUNENA_GEN_MESSAGE' )?></strong>
		</td>
	</tr>
	<?php foreach ( $this->messages as $mes ):?>
	<tr>
		<td class="kreview_body<?php echo $k = 1 - $k?>" valign="top">
			<?php echo kunena_htmlspecialchars ( stripslashes ( $mes->name ) )?>
		</td>

		<td class="kreview_body<?php echo $k?>">
			<div class="msgtext">
				<?php echo KunenaParser::parseBBCode( stripslashes($mes->message) )?>
			</div>
			<?php if ( !empty($this->attachmentslist[$mes->id]) ) $this->displayAttachments($this->attachmentslist[$mes->id]); ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
