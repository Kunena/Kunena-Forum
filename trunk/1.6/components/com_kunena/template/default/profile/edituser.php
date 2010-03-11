<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
$kunena_config =& CKunenaConfig::getInstance();
?>

<h1><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE'); ?></h1>
<form action="<?php echo CKunenaLink::GetProfileSettingsURL($kunena_config, $this->user->id, '', $rel='nofollow', $redirect=false,'saveuser'); ?>" method="post" name="kprofileEditing">
<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '';
	?>" id="kflattable">
	<tbody>
		<!-- General settings -->
		<tr class="ksectiontableentry2">
			<?php if ( $kunena_config->usernamechange ) { ?>
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_UNAME'); ?></td><td><input type="hidden" name="username" value="<?php echo $this->user->username; ?>" /><?php echo $this->user->username; ?></td>
			<?php } else { ?>
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_UNAME'); ?></td><td><input type="text" name="username" value="<?php echo $this->user->username; ?>" /></td>
			<?php } ?>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_USRL_NAME'); ?></td><td><input type="text" name="name" value="<?php echo $this->user->name; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_USRL_EMAIL'); ?></td><td><input type="text" name="email" value="<?php echo $this->user->email; ?>" /></td>
		</tr>
		<tr  class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_PASS'); ?></td><td><input type="password" name="password" value="" /></td>
		</tr>
		<tr  class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_VPASS'); ?></td><td><input type="password" name="password2" value="" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center" colspan="2"><input class="kbutton kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_GEN_SUBMIT'); ?>" name="Submit" /></td>
		</tr>
	</tbody>
</table>
</form>
