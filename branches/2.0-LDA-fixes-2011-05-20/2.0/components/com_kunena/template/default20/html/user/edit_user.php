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
?>
							<div class="kblock kedituser">
								<h2 class="kheader"><a rel="kedit-user-information"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE') ?></a></h2>
								<ul class="kform kedit-user-information clearfix" id="kedit-user-information">
									<li class="kedit-user-information-row krow-<?php echo $this->row(true) ?>">
										<div class="kform-label">
											<label for="kusername"><?php echo JText::_( 'COM_KUNENA_UNAME' ) ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="<?php echo $this->escape($this->user->get('username'));?>" id="kusername" name="username" class="kinputbox required" <?php echo !$this->config->usernamechange ? 'disabled="disabled" ' : ''?>/>
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kname"><?php echo JText::_( 'COM_KUNENA_USRL_NAME' ) ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="<?php echo $this->escape($this->user->get('name'));?>" id="kname" name="name" class="kinputbox required" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kemail"><?php echo JText::_( 'COM_KUNENA_USRL_EMAIL' ) ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="<?php echo $this->escape($this->user->get('email'));?>" id="kemail" name="email" class="kinputbox required" />
										</div>
									</li>
									<?php if($this->user->get('password')) : ?>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kpassword"><?php echo JText::_( 'COM_KUNENA_PASS' ) ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="" id="kpassword" name="password" class="kinputbox" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kpassword2"><?php echo JText::_( 'COM_KUNENA_VPASS' ) ?></label>
										</div>
										<div class="kform-field">
											<input type="text" value="" id="kpassword2" name="password2" class="kinputbox" />
										</div>
									</li>
									<?php endif ?>
								</ul>
							</div>

							<?php if(!empty($this->userparameters)) : ?>
							<div class="kblock kedituser">
								<h2 class="kheader"><a rel="kflattable"><?php echo JText::_('COM_KUNENA_GLOBAL_SETTINGS'); ?></a></h2>
								<ul class="kform kedit-user-information clearfix">
									<?php foreach ($this->userparameters as $userparam): ?>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<?php echo $userparam->label ?>
										</div>
										<div class="kform-field">
											<?php echo $userparam->input ?>
										</div>
									</li>
									<?php endforeach ?>
								</ul>
							</div>
							<?php endif; ?>