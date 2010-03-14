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
?>

<h2><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE_TITLE'); ?></h2>
<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '';
	?>" id="kflattable">
	<tbody>
		<!-- Kunena specific settings -->
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT'); ?></td>
			<td><input type="text" maxlength="<?php echo $this->_config->maxpersotext; ?>" name="personnaltext" value="<?php echo kunena_htmlspecialchars ($this->profile->personalText); ?>"  /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?></td>
			<?php $bithdate = explode('-',$this->profile->birthdate); ?>
			<td><input type="text" size="4" maxlength="4" name="birthdate1" value="<?php echo $bithdate[0]; ?>" />
			<input type="text" size="2" maxlength="2" name="birthdate2" value="<?php echo $bithdate[1]; ?>" />
			<input type="text" size="2" maxlength="2" name="birthdate3" value="<?php echo $bithdate[2]; ?>" />
			</td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?></td><td><input type="text" name="location" value="<?php echo $this->profile->location; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?></td><td><?php
					// make the select list for the view type
					$gender[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
					$gender[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
					$gender[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));
					// build the html select list
					echo JHTML::_('select.genericlist', $gender, 'gender', 'class="inputbox" size="1"', 'value', 'text', $this->profile->gender);

					?></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME'); ?></td><td><input type="text" name="websitename" value="<?php echo $this->profile->websitename; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL'); ?></td><td><input type="text" name="websiteurl" value="<?php echo $this->profile->websiteurl; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_TWITTER'); ?></td><td><input type="text" name="twitter" value="<?php echo $this->profile->TWITTER; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_FACEBOOK'); ?></td><td><input type="text" name="facebook" value="<?php echo $this->profile->SKYPE; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_MYSPACE'); ?></td><td><input type="text" name="myspace" value="<?php echo $this->profile->MYSPACE; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_SKYPE'); ?></td><td><input type="text" name="skype" value="<?php echo $this->profile->SKYPE; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_LINKEDIN'); ?></td><td><input type="text" name="linkedin" value="<?php echo $this->profile->LINKEDIN; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_DELICIOUS'); ?></td><td><input type="text" name="delicious" value="<?php echo $this->profile->DELICIOUS; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_FRIENDFEED'); ?></td><td><input type="text" name="friendfeed" value="<?php echo $this->profile->FRIENDFEED; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_DIGG'); ?></td><td><input type="text" name="digg" value="<?php echo $this->profile->DIGG; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_YIM'); ?></td><td><input type="text" name="yim" value="<?php echo $this->profile->YIM; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_AIM'); ?></td><td><input type="text" name="aim" value="<?php echo $this->profile->AIM; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_GTALK'); ?></td><td><input type="text" name="gtalk" value="<?php echo $this->profile->GTALK; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_ICQ'); ?></td><td><input type="text" name="icq" value="<?php echo $this->profile->ICQ; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_MSN'); ?></td><td><input type="text" name="msn" value="<?php echo $this->profile->MSN; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_BLOGSPOT'); ?></td><td><input type="text" name="blogspot" value="<?php echo $this->profile->BLOGSPOT; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_FLICKR'); ?></td><td><input type="text" name="flickr" value="<?php echo $this->profile->FLICKR; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry1">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_BEBO'); ?></td><td><input type="text" name="bebo" value="<?php echo $this->profile->BEBO; ?>" /></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></td>
			<!-- Add some bbcode functions -->
			<td><textarea class="ktxtarea required" name="signature"
		id="kbbcode-message"><?php
			echo kunena_htmlspecialchars ( $this->profile->signature );
			?></textarea></td>
		</tr>
	</tbody>
</table>
</form>
