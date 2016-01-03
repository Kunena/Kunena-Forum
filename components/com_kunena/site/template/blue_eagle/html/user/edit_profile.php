<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage User
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.tooltip');
?>

<div class="kblock keditprofile">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE_TITLE'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table
	class="<?php
	echo isset ( $this->category->class_sfx ) ? ' kblocktable' . $this->escape($this->category->class_sfx) : '';
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
					<input type="text" size="4" maxlength="4" name="birthdate1" value="<?php echo $this->escape($bithdate[0]); ?>" />
					<input type="text" size="2" maxlength="2" name="birthdate2" value="<?php echo $this->escape($bithdate[1]); ?>" />
					<input type="text" size="2" maxlength="2" name="birthdate3" value="<?php echo $this->escape($bithdate[2]); ?>" />
				</span>
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?></td>
			<td class="kcol-mid"><input type="text" name="location" value="<?php echo $this->escape($this->profile->location); ?>" /></td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?></td>
			<td class="kcol-mid">
				<?php
				// make the select list for the view type
				$gender[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
				$gender[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
				$gender[] = JHtml::_('select.option', 2, JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));
				// build the html select list
				echo JHtml::_('select.genericlist', $gender, 'gender', 'class="inputbox" size="1"', 'value', 'text', $this->escape($this->profile->gender));
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
		<?php $i=1; foreach ($this->social as $social) : ?>
		<tr class="krow<?php echo (++$i & 1)+1;?>">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_'.$social); ?></td>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}"); ?>::<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}_DESC"); ?>" >
					<input type="text" name="<?php echo $social ?>" value="<?php echo $this->escape($this->profile->$social); ?>" />
				</span>
			</td>
		</tr>
		<?php endforeach ?>
		<tr class="krow<?php echo (++$i & 1)+1;?>">
			<td class="kcol-first"><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></td>
			<?php // TODO: Add some bbcode functions ?>
			<td class="kcol-mid">
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE_DESC'); ?>" >
					<textarea class="ktxtarea" maxlength="<?php echo intval($this->config->maxsig) ?>" name="signature" id="kbbcode-message" rows="10" cols="30"><?php echo $this->escape($this->profile->signature) ?></textarea>
				</span>
			</td>
		</tr>
	</tbody>
</table>
		</div>
	</div>
</div>
