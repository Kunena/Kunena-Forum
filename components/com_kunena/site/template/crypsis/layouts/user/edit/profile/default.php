<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>
		<tr>
			<td class="span3">
				<?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT'); ?>
			</td>
			<td>
				<input type="text" maxlength="<?php echo intval($this->config->maxpersotext) ?>" name="personaltext" value="<?php echo $this->escape($this->profile->personalText); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>
			</td>
			<?php $bithdate = explode('-',$this->profile->birthdate); ?>
			<td>
				<span class="hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE_DESC'); ?>">
					<input class="span2" type="text" size="4" maxlength="4" name="birthdate1" value="<?php echo $this->escape($bithdate[0]); ?>" />
					<input class="span1" type="text" size="2" maxlength="2" name="birthdate2" value="<?php echo $this->escape($bithdate[1]); ?>" />
					<input class="span1" type="text" size="2" maxlength="2" name="birthdate3" value="<?php echo $this->escape($bithdate[2]); ?>" />
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?>
			</td>
			<td>
				<input type="text" name="location" value="<?php echo $this->escape($this->profile->location); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>
			</td>
			<td>
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
		<tr>
			<td>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME'); ?>
			</td>
			<td>
				<span class="hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME_DESC'); ?>">
					<input type="text" name="websitename" value="<?php echo $this->escape($this->profile->websitename); ?>" />
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL'); ?>
			</td>
			<td>
				<span class="hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL_DESC'); ?>" >
					<input type="text" name="websiteurl" value="<?php echo $this->escape($this->profile->websiteurl); ?>" />
				</span>
			</td>
		</tr>
		<?php foreach ($this->social as $social) : ?>
		<tr>
			<td>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_'.$social); ?>
			</td>
			<td>
				<span class="hasTip" title="<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}"); ?>::<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}_DESC"); ?>" >
					<input type="text" name="<?php echo $social ?>" value="<?php echo $this->escape($this->profile->$social); ?>" />
				</span>
			</td>
		</tr>
		<?php endforeach ?>
		<tr>
			<td>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?>
			</td>
			<td>
				<span class="hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE_DESC'); ?>" >
					<textarea class="input-xxlarge" maxlength="<?php echo intval($this->config->maxsig) ?>" name="signature" id="kbbcode-message" rows="10" cols="30"><?php echo $this->escape($this->profile->signature) ?></textarea>
				</span>
			</td>
		</tr>
	</tbody>
</table>
