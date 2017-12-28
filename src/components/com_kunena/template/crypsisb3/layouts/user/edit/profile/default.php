<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>
		<tr>
			<td class="col-md-3">
				<label for="personaltext">
					<?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT'); ?>
				</label>
			</td>
			<td>
				<input class="form-control" id="personaltext" type="text" maxlength="<?php echo (int) $this->config->maxpersotext; ?>"
				       name="personaltext" value="<?php echo $this->escape($this->profile->personalText); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="birthdate">
					<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>
				</label>
			</td>
			<?php list ($birthYear, $birthMonth, $birthDay) = explode('-', $this->profile->birthdate); ?>
			<td>
				<span class="hasTooltip col-md-6 form-inline" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE')
					. '::' . JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE_DESC'); ?>">
					<input id="birthdate" class="form-control" type="text" size="4" maxlength="4"  name="birthdate1"
					       value="<?php echo $this->escape($birthYear); ?>" />
					<input class="form-control" type="text" size="2" maxlength="2" name="birthdate2"
					       value="<?php echo $this->escape($birthMonth); ?>" />
					<input class="form-control" type="text" size="2" maxlength="2" name="birthdate3"
					       value="<?php echo $this->escape($birthDay); ?>" />
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<label for="location">
					<?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?>
				</label>
			</td>
			<td>
				<input id="location" type="text" name="location" class="form-control"
				       value="<?php echo $this->escape($this->profile->location); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="gender">
					<?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>
				</label>
			</td>
			<td>
				<?php
				// Make the select list for the view type
				$gender[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
				$gender[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
				$gender[] = JHtml::_('select.option', 2, JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));
				// Build the html select list
				echo JHtml::_(
     'select.genericlist', $gender, 'gender', 'class="inputbox form-control" size="1"', 'value', 'text',
	$this->escape($this->profile->gender), 'gender');
				?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="social-site">
					<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME'); ?>
				</label>
			</td>
			<td>
				<span class="hasTooltip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME')
					. '::' . JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME_DESC'); ?>">
					<input id="social-site" type="text" name="websitename" class="form-control"
					       value="<?php echo $this->escape($this->profile->websitename); ?>" />
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<label for="social-url">
					<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL'); ?>
				</label>
			</td>
			<td>
				<span class="hasTooltip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL') . '::' . JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL_DESC'); ?>" >
					<input id="social-url" type="url" name="websiteurl" class="form-control"
					       value="<?php echo $this->escape($this->profile->websiteurl); ?>" />
				</span>
			</td>
		</tr>

		<?php foreach ($this->social as $social) : ?>
		<tr>
			<td>
				<label for="social-<?php echo $social; ?>">
					<?php echo JText::_('COM_KUNENA_MYPROFILE_' . $social); ?>
				</label>
			</td>
			<td>
				<?php if ($social != 'qq') :?>
				<span class="hasTooltip" title="<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}")
					. '::' . JText::_("COM_KUNENA_MYPROFILE_{$social}_DESC"); ?>" >
				<?php endif; ?>
					<input id="social-<?php echo $social; ?>" type="text" name="<?php echo $social ?>" class="form-control"
					       value="<?php echo $this->escape($this->profile->$social); ?>" />
				</span>
			</td>
		</tr>
		<?php endforeach; ?>

		<tr>
			<td>
				<label for="signature">
					<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?>
				</label>
			</td>
			<td>
				<span class="hasTooltip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE')
					. '::' . JText::_('COM_KUNENA_MYPROFILE_SIGNATURE_DESC'); ?>" >
					<textarea class="input-xxlarge form-control" maxlength="<?php echo (int) $this->config->maxsig; ?>"
					          name="signature" id="signature" rows="10"
					          cols="30"><?php echo $this->escape($this->profile->signature); ?></textarea>
				</span>
			</td>
		</tr>
	</tbody>
</table>
