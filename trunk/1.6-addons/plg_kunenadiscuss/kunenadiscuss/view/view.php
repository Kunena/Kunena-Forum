<?php
/**
 * @version $Id$
 * Kunena Discuss Plug-in
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010-2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die ( '' );

$msg = $this->messages[0];
?>
<div class="kdiscuss-title"><?php echo CKunenaLink::GetThreadLink ( 'view', $msg->catid, $msg->thread, JText::_('PLG_KUNENADISCUSS_POSTS'), 'follow') ?></div>

<?php
foreach ( $this->messages as $message ) {
	if ($message->parent) $this->displayMessage($message);
}
?>
<div class="kdiscuss-more"><?php echo CKunenaLink::GetThreadLink ( 'view', $msg->catid, $msg->thread, JText::_('COM_KUNENA_ANN_READMORE'), 'follow') ?></div>
