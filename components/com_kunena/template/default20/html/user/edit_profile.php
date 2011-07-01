<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHTML::_('behavior.tooltip');
?>
							<div class="kblock keditprofile">
								<h2 class="kheader"><a rel="kedit-profile-information"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE_TITLE'); ?></a></h2>

								<ul class="kform kedit-user-information clearfix" id="kedit-profile-information">
									<li class="kedit-user-information-row krow-<?php echo $this->row(true) ?>">
										<div class="kform-label">
											<label for="kpersonaltext"><?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT'); ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="<?php echo $this->escape($this->profile->personalText); ?>" name="personaltext" id="kpersonaltext" maxlength="<?php echo intval($this->config->maxpersotext) ?>" class="kinputbox hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT_DESC') ?>" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kbirthdate1"><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE') ?></label>
										</div>
										<div class="kform-field">
											<span class="editlinktip hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE_DESC'); ?>" >
												<?php $bithdate = explode('-',$this->profile->birthdate); ?>
												<input type="text" size="4" maxlength="4" name="birthdate1" id="kbirthdate1" value="<?php echo $this->escape($bithdate[0]); ?>" />
												<input type="text" size="2" maxlength="2" name="birthdate2" value="<?php echo $this->escape($bithdate[1]); ?>" />
												<input type="text" size="2" maxlength="2" name="birthdate3" value="<?php echo $this->escape($bithdate[2]); ?>" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="klocation"><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="<?php echo $this->escape($this->profile->location); ?>" name="location" id="klocation" class="kinputbox hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION_DESC') ?>" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kgender"><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER') ?></label>
										</div>
										<div class="kform-field">
										<?php
										// make the select list for the view type
										$gender[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
										$gender[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
										$gender[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));
										// build the html select list
										echo JHTML::_('select.genericlist', $gender, 'gender', 'class="inputbox" size="1"', 'value', 'text', intval($this->profile->gender), 'kgender');
										?>
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kwebsitename"><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME') ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="<?php echo $this->escape($this->profile->websitename) ?>" name="websitename" id="kwebsitename" class="kinputbox hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_NAME_DESC') ?>" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kwebsiteurl"><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL') ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="<?php echo $this->escape($this->profile->websiteurl) ?>" name="websiteurl" id="kwebsiteurl" class="kinputbox hasTip" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE_URL_DESC') ?>" />
										</div>
									</li>
									<?php foreach ($this->social as $social) : ?>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="k<?php echo $social ?>"><?php echo JText::_('COM_KUNENA_MYPROFILE_'.$social) ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="<?php echo $this->escape($this->profile->$social) ?>" name="<?php echo $social ?>" id="k<?php echo $social ?>" class="kinputbox hasTip" title="<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}") ?>::<?php echo JText::_("COM_KUNENA_MYPROFILE_{$social}_DESC") ?>" />
										</div>
									</li>
									<?php endforeach ?>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="ksignature"><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE') ?></label>
										</div>
										<div class="kform-field">
											<textarea class="editlinktip hasTip" name="signature" id="ksignature" rows="2" cols="50" title="<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE') ?>::<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE_DESC') ?>"><?php echo $this->escape($this->profile->signature) ?></textarea>
										</div>
									</li>
								</ul>
							</div>