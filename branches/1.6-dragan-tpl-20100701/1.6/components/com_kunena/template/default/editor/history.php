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
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="history"></a></span>
		<h2><span><?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY' )?>: <?php echo $this->subject ?></span></h2>
		<div class="ktitle-desc km">
			<?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_MAX' ) . ' ' . $this->config->historylimit . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_LAST' )?>
		</div>
	</div>
	<div class="kcontainer" id="history">
		<div class="kbody">
<table border="0" cellspacing="1" cellpadding="3" width="100%"
	class="kreview-table">
	<tr>
		<td class="kreview-header" width="20%" align="center">
			<strong><?php echo JText::_ ( 'COM_KUNENA_GEN_AUTHOR' )?></strong>
		</td>

		<td class="kreview-header" align="center">
			<strong><?php echo JText::_ ( 'COM_KUNENA_GEN_MESSAGE' )?></strong>
		</td>
	</tr>
	<?php foreach ( $this->messages as $mes ):?>
	<tr>
		<td class="kreview-body<?php echo $k = 1 - $k?> kfirst" valign="top">
			<?php echo kunena_htmlspecialchars ( $mes->name )?>
		</td>

		<td class="kreview-body<?php echo $k?> kmiddle">
			<div class="kmsgbody">
				<div class="kmsgtext">
					<?php echo KunenaParser::parseBBCode( $mes->message )?>
				</div>
			</div>
			<?php if ( !empty($this->attachmentslist[$mes->id]) ) $this->displayAttachments($this->attachmentslist[$mes->id]); ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
        </div>
	</div>
</div>