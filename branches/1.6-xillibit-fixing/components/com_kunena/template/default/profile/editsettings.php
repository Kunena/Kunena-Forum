<?php
/**
 * @version $Id: $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
require_once ( KUNENA_PATH_FUNCS .DS. 'profile.php');
$userprofile = new CKunenaProfile($this->user->id);
$kunena_config =& CKunenaConfig::getInstance();
?>

<h1><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->user->name; ?> (<?php echo $this->user->username; ?>)</h1>

<div class="kbt_cvr1">
<div class="kbt_cvr2">
<div class="kbt_cvr3">
<div class="kbt_cvr4">
<div class="kbt_cvr5">

<form action="<?php echo CKunenaLink::GetProfileSettingsURL($kunena_config, $this->user->id, '', $rel='nofollow', $redirect=false,'savesettings'); ?>" method="post" name="kprofileEditing">
<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '';
	?>" id="kflattable">
	<thead>
		<tr>
			<th
				colspan="2"><?php echo JText::_('COM_KUNENA_MYPROFILE_LOOK_AND_LAYOUT'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr  class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_USER_ORDER'); ?></td>
			<td><?php
					$mesordering[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_USER_ORDER_ASC'));
					$mesordering[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_USER_ORDER_DESC'));
					echo JHTML::_('select.genericlist', $mesordering, 'messageordering', 'class="inputbox" size="1"', 'value', 'text', $userprofile->profile->ordering);
			?></td>
		</tr>
		<tr  class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_USER_HIDEEMAIL'); ?></td>
			<td><?php
					$hideEmail[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_A_NO'));
					$hideEmail[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_A_YES'));
					echo JHTML::_('select.genericlist', $hideEmail, 'hidemail', 'class="inputbox" size="1"', 'value', 'text', $userprofile->profile->hideEmail);

					?></td>
		</tr>
		<tr  class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_USER_SHOWONLINE'); ?></td>
			<td><?php
					$showonline[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_A_NO'));
					$showonline[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_A_YES'));
					echo JHTML::_('select.genericlist', $showonline, 'showonline', 'class="inputbox" size="1"', 'value', 'text', $userprofile->profile->showOnline);

					?></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center" colspan="2"><input class="kbutton kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_GEN_SUBMIT'); ?>" name="Submit" /></td>
		</tr>
	</tbody>
</table>

</form>

</div>
</div>
</div>
</div>
</div>