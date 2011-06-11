<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined( '_JEXEC' ) or die();

JHTML::_('behavior.tooltip');
?>

<div class="kblock keditprofile">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE_TITLE'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : '';
	?>">
	<tbody>
		<!-- Kunena specific settings -->
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT'); ?></td>
			<td class="kcol-mid"><input type="text" maxlength="<?php echo intval($this->config->maxpersotext) ?>" name="personaltext" value="<?php echo $this->escape($this->profile->personalText); ?>" /></td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?></td>
			<?php $bithdate = explode('-',$this->profile->birthdate); ?>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE_DESC'); ?>" >
					<input type="text" size="4" maxlength="4" name="birthdate1" value="<?php echo $bithdate[0]; ?>" />
					<input type="text" size="2" maxlength="2" name="birthdate2" value="<?php echo $bithdate[1]; ?>" />
					<input type="text" size="2" maxlength="2" name="birthdate3" value="<?php echo $bithdate[2]; ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?></td>
			<td class="kcol-mid"><input type="text" name="location" value="<?php echo $this->profile->location; ?>" /></td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?></td>
			<td class="kcol-mid">
				<?php
				// make the select list for the view type
				$gender[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
				$gender[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
				$gender[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));
				// build the html select list
				echo JHTML::_('select.genericlist', $gender, 'gender', 'class="inputbox" size="1"', 'value', 'text', $this->escape($this->profile->gender));
				?>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME_DESC'); ?>" >
					<input type="text" name="websitename" value="<?php echo $this->escape($this->profile->websitename); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL_DESC'); ?>" >
					<input type="text" name="websiteurl" value="<?php echo $this->escape($this->profile->websiteurl); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_TWITTER'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_TWITTER'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_TWITTER_DESC'); ?>" >
					<input type="text" name="twitter" value="<?php echo $this->escape($this->profile->TWITTER); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_FACEBOOK'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_FACEBOOK'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_FACEBOOK_DESC'); ?>" >
					<input type="text" name="facebook" value="<?php echo $this->escape($this->profile->FACEBOOK); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_MYSPACE'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_MYSPACE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_MYSPACE_DESC'); ?>" >
					<input type="text" name="myspace" value="<?php echo $this->escape($this->profile->MYSPACE); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_SKYPE'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_SKYPE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_SKYPE_DESC'); ?>" >
					<input type="text" name="skype" value="<?php echo $this->escape($this->profile->SKYPE); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_LINKEDIN'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_LINKEDIN'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_LINKEDIN_DESC'); ?>" >
					<input type="text" name="linkedin" value="<?php echo $this->escape($this->profile->LINKEDIN); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_DELICIOUS'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_DELICIOUS'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_DELICIOUS_DESC'); ?>" >
					<input type="text" name="delicious" value="<?php echo $this->escape($this->profile->DELICIOUS); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_FRIENDFEED'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_FRIENDFEED'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_FRIENDFEED_DESC'); ?>" >
					<input type="text" name="friendfeed" value="<?php echo $this->escape($this->profile->FRIENDFEED); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_DIGG'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_DIGG'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_DIGG_DESC'); ?>" >
					<input type="text" name="digg" value="<?php echo $this->escape($this->profile->DIGG); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_YIM'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_YIM'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_YIM_DESC'); ?>" >
					<input type="text" name="yim" value="<?php echo $this->escape($this->profile->YIM); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_AIM'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_AIM'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_AIM_DESC'); ?>" >
					<input type="text" name="aim" value="<?php echo $this->escape($this->profile->AIM); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_GTALK'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_GTALK'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_GTALK_DESC'); ?>" >
					<input type="text" name="gtalk" value="<?php echo $this->escape($this->profile->GTALK); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_ICQ'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_ICQ'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_ICQ_DESC'); ?>" >
					<input type="text" name="icq" value="<?php echo $this->escape($this->profile->ICQ); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_MSN'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_MSN'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_MSN_DESC'); ?>" >
					<input type="text" name="msn" value="<?php echo $this->escape($this->profile->MSN); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_BLOGSPOT'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_BLOGSPOT'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_BLOGSPOT_DESC'); ?>" >
					<input type="text" name="blogspot" value="<?php echo $this->escape($this->profile->BLOGSPOT); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_FLICKR'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_FLICKR'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_FLICKR_DESC'); ?>" >
					<input type="text" name="flickr" value="<?php echo $this->escape($this->profile->FLICKR); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_BEBO'); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_BEBO'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_BEBO_DESC'); ?>" >
					<input type="text" name="bebo" value="<?php echo $this->escape($this->profile->BEBO); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></td>
			<?php // TODO: Add some bbcode functions ?>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE_DESC'); ?>" >
					<textarea class="ktxtarea required" name="signature" id="kbbcode-message" rows="10" cols="30"><?php echo $this->escape($this->profile->signature) ?></textarea>
				</span>
			</td>
		</tr>
	</tbody>
</table>
		</div>
	</div>
</div>