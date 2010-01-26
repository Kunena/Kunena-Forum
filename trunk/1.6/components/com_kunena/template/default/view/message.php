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

$kunena_config = & CKunenaConfig::getInstance ();
$kunena_db = &JFactory::getDBO ();

global $kunena_icons;

if ($kunena_config->avposition == 'top') { ?>

<table>
    <tr class="ksth">
    	<th colspan="2" class="view-th ksectiontableheader">
    		<a name="<?php echo $this->msg_html->id; ?>"></a>
    		<?php echo CKunenaTools::getMessageId(); ?>
    	</th>
    </tr>
    <tr>
    	<td valign="top" class="kunena-profile-top">
    		<?php $this->displayProfileBox() ?>
    	</td>
    </tr>
    <tr>
    	<td class="kunena-message-top">
    		<?php $this->displayMessageContents() ?>
    	</td>
    </tr>
    <tr>
    	<td class="buttonbar-top">
    		<?php $this->displayMessageActions() ?>
    	</td>
    </tr>
</table>

<?php } else if ($kunena_config->avposition == 'bottom') { ?>

<table>
    <tr class="ksth">
    	<th colspan="2" class="view-th ksectiontableheader">
    		<a name="<?php echo $this->msg_html->id; ?>"></a>
    		<?php echo CKunenaTools::getMessageId(); ?>
    	</th>
    </tr>
    <tr>
    	<td class="kunena-message-bottom">
    		<?php $this->displayMessageContents() ?>
    	</td>
    </tr>
    <tr>
    	<td class="buttonbar-bottom">
    		<?php $this->displayMessageActions() ?>
    	</td>
    </tr>
    <tr>
    	<td valign="top" class="kunena-profile-bottom">
    		<?php $this->displayProfileBox() ?>
    	</td>
    </tr>
</table>

<?php } else if ($kunena_config->avposition == 'left') { ?>

<table>
    <tr class="ksth">
    	<th colspan="2" class="view-th ksectiontableheader">
    		<a name="<?php echo $this->msg_html->id; ?>"></a>
    		<?php echo CKunenaTools::getMessageId(); ?>
    	</th>
    </tr>
    <tr>
    	<td rowspan="2" valign="top" class="kunena-profile-left">
    		<?php $this->displayProfileBox() ?>
   	</td>
    	<td class="kunena-message-left">
    		<?php $this->displayMessageContents() ?>
    	</td>
    </tr>
    <tr>
    	<td class="buttonbar-left">
    		<?php $this->displayMessageActions() ?>
    	</td>
    </tr>
</table>

<?php } else { ?>

<table>
    <tr class="ksth">
    	<th colspan="2" class="view-th ksectiontableheader">
    		<a name="<?php echo $this->msg_html->id; ?>"></a>
    		<?php echo CKunenaTools::getMessageId(); ?>
    	</th>
    </tr>
    <tr>
    	<td class="kunena-message-right">
    		<?php $this->displayMessageContents() ?>
    	</td>
    	<td rowspan="2" class="kunena-profile-right">
    		<?php $this->displayProfileBox() ?>
    	</td>
    </tr>
    <tr>
    	<td class="buttonbar-right">
    		<?php $this->displayMessageActions() ?>
    	</td>
    </tr>
</table>

<?php } ?>
<!-- Begin: Message Module Positions -->
<?php
CKunenaTools::showModulePosition('kunena_msg_' . $this->mmm);
?>
<!-- Finish: Message Module Positions -->