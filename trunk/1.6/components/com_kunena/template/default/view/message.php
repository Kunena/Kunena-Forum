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

$kunena_my = &JFactory::getUser ();
$kunena_config = & CKunenaConfig::getInstance ();
$kunena_db = &JFactory::getDBO ();

global $kunena_icons;

if ($kunena_config->avposition == 'top') { ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="ksth">
    	<th colspan="2" class="view-th ksectiontableheader">
    		<a name="<?php echo $msg_html->id; ?>"></a>
    		<?php echo CKunenaTools::getMessageId(); ?>
    	</th>
    </tr>
    <tr>
    	<td valign="top" class="kunena-profile-top"><?php
    		if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/profilebox/message.profilebox.php' )) {
    			include (KUNENA_ABSTMPLTPATH . '/plugin/profilebox/message.profilebox.php');
    		} else {
    			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/profilebox/message.profilebox.php');
    		} ?>
    	</td>
    </tr>
    <tr>
    	<td class="kunena-message-top">
    		<?php include (KUNENA_PATH_VIEWS . DS . 'message.php'); ?>
    	</td>
    </tr>
    <tr>
    	<td class="buttonbar-top">
    		<?php include (KUNENA_PATH_VIEWS . DS . 'button.php'); ?>
    	</td>
    </tr>
</table>

<?php } else if ($kunena_config->avposition == 'bottom') { ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="ksth">
    	<th colspan="2" class="view-th ksectiontableheader">
    		<a name="<?php echo $msg_html->id; ?>"></a>
    		<?php echo CKunenaTools::getMessageId(); ?>
    	</th>
    </tr>
    <tr>
    	<td class="kunena-message-bottom">
    		<?php include (KUNENA_PATH_VIEWS . DS . 'message.php'); ?>
    	</td>
    </tr>
    <tr>
    	<td class="buttonbar-bottom">
    		<?php include (KUNENA_PATH_VIEWS . DS . 'button.php'); ?>
    	</td>
    </tr>
    <tr>
    	<td valign="top" class="kunena-profile-bottom"><?php
    		if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/profilebox/message.profilebox.php' )) {
    			include (KUNENA_ABSTMPLTPATH . '/plugin/profilebox/message.profilebox.php');
    		} else {
    			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/profilebox/message.profilebox.php');
    		} ?>
    	</td>
    </tr>
</table>

<?php } else if ($kunena_config->avposition == 'left') { ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="ksth">
    	<th colspan="2" class="view-th ksectiontableheader">
    		<a name="<?php echo $msg_html->id; ?>"></a>
    		<?php echo CKunenaTools::getMessageId(); ?>
    	</th>
    </tr>
    <tr>
    	<td rowspan="2" valign="top" class="kunena-profile-left"><?php
    		if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/profilebox/message.profilebox.php' )) {
    			include (KUNENA_ABSTMPLTPATH . '/plugin/profilebox/message.profilebox.php');
    		} else {
    			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/profilebox/message.profilebox.php');
    		} ?>
    	</td>
    	<td class="kunena-message-left">
    		<?php include (KUNENA_PATH_VIEWS . DS . 'message.php'); ?>
    	</td>
    </tr>
    <tr>
    	<td class="buttonbar-left">
    		<?php include (KUNENA_PATH_VIEWS . DS . 'button.php'); ?>
    	</td>
    </tr>
</table>

<?php } else { ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="ksth">
    	<th colspan="2" class="view-th ksectiontableheader">
    		<a name="<?php echo $msg_html->id; ?>"></a>
    		<?php echo CKunenaTools::getMessageId(); ?>
    	</th>
    </tr>
    <tr>
    	<td class="kunena-message-right">
    		<?php include (KUNENA_PATH_VIEWS . DS . 'message.php'); ?>
    	</td>
    	<td rowspan="2" valign="top" class="kunena-profile-right"><?php
    		if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/profilebox/message.profilebox.php' )) {
    			include (KUNENA_ABSTMPLTPATH . '/plugin/profilebox/message.profilebox.php');
    		} else {
    			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/profilebox/message.profilebox.php');
    		} ?>
    	</td>
    </tr>
    <tr>
    	<td class="buttonbar-right">
    		<?php include (KUNENA_PATH_VIEWS . DS . 'button.php'); ?>
    	</td>
    </tr>
</table>

<?php } ?>
<!-- Begin: Message Module Positions -->
<?php
CKunenaTools::showModulePosition('kunena_msg_' . $this->mmm);
?>
<!-- Finish: Message Module Positions -->