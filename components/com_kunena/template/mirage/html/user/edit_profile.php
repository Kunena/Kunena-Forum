<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.tooltip');
?>
<div class="kmodule user-edit_profile">
	<div class="kbox-wrapper kbox-full">
		<div class="user-edit_profile-kbox kbox kbuox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kedit-profile-information"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE_TITLE'); ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="kform user-edit-information-list kbox-full" id="kedit-profile-information">
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kpersonaltext"><?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT'); ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer-left kbox-full">
									<input type="text" value="<?php echo $this->escape($this->profile->personalText); ?>" name="personaltext" id="kpersonaltext" maxlength="<?php echo intval($this->config->maxpersotext) ?>" class="kbox-width inputbox hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT_DESC') ?>" />
								</div>
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kbirthdate1"><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE_DESC'); ?>" >
										<?php $bithdate = explode('-',$this->profile->birthdate); ?>
										<input type="text" size="4" maxlength="4" name="birthdate1" id="kbirthdate1" value="<?php echo $this->escape($bithdate[0]); ?>" />
										<input type="text" size="2" maxlength="2" name="birthdate2" value="<?php echo $this->escape($bithdate[1]); ?>" />
										<input type="text" size="2" maxlength="2" name="birthdate3" value="<?php echo $this->escape($bithdate[2]); ?>" />
									</span>
								</div>
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="klocation"><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="<?php echo $this->escape($this->profile->location); ?>" name="location" id="klocation" class="kbox-width inputbox hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION_DESC') ?>" />
								</div>
									
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kgender"><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<?php
									// make the select list for the view type
									$gender[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
									$gender[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
									$gender[] = JHtml::_('select.option', 2, JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));
									// build the html select list
									echo JHtml::_('select.genericlist', $gender, 'gender', 'class="inputbox" size="1"', 'value', 'text', intval($this->profile->gender), 'kgender');
									?>
								</div>
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kwebsitename"><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="<?php echo $this->escape($this->profile->websitename) ?>" name="websitename" id="kwebsitename" class="kbox-width inputbox hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME_DESC') ?>" />
								</div>
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kwebsiteurl"><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="<?php echo $this->escape($this->profile->websiteurl) ?>" name="websiteurl" id="kwebsiteurl" class="kbox-width inputbox hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL_DESC') ?>" />
								</div>
							</div>
						</li>
						<?php foreach ($this->social as $social) : ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="k<?php echo $social ?>"><?php echo JText::_('COM_KUNENA_MYPROFILE_'.$social) ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="<?php echo $this->escape($this->profile->$social) ?>" name="<?php echo $social ?>" id="k<?php echo $social ?>" class="kbox-width inputbox hasTip" title="<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}") ?>::<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}_DESC") ?>" />
								</div>
							</div>
						</li>
						<?php endforeach ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="ksignature"><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<textarea class="kbox-width editlinktip hasTip" name="signature" id="ksignature" rows="2" cols="50" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE_DESC') ?>"><?php echo $this->escape($this->profile->signature) ?></textarea>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
