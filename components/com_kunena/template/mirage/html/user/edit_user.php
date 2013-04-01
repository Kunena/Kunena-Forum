<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule user-edit_user">
	<div class="kbox-wrapper kbox-full">
		<div class="user-edit_user-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kedit-user-information"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE') ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="kform user-edit-information-list kbox-full" id="kedit-user-information">
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kusername"><?php echo JText::_( 'COM_KUNENA_UNAME' ) ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="<?php echo $this->escape($this->user->get('username'));?>" id="kusername" name="username" class="kbox-width inputbox required" <?php echo !$this->usernamechange ? 'disabled="disabled" ' : '' ?>/>
								</div>
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kname"><?php echo JText::_( 'COM_KUNENA_REALNAME' ) ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="<?php echo $this->escape($this->user->get('name'));?>" id="kname" name="name" class="kbox-width inputbox required" />
								</div>
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kemail"><?php echo JText::_( 'COM_KUNENA_USRL_EMAIL' ) ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="<?php echo $this->escape($this->user->get('email'));?>" id="kemail" name="email" class="kbox-width inputbox required" />
								</div>
							</div>
						</li>
						<?php if($this->user->get('password')) : ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kpassword"><?php echo JText::_( 'COM_KUNENA_PASS' ) ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="" id="kpassword" name="password" class="kbox-width inputbox" />
								</div>
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kpassword2"><?php echo JText::_( 'COM_KUNENA_VPASS' ) ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="text" value="" id="kpassword2" name="password2" class="kbox-width inputbox" />
								</div>
							</div>
						</li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if(!empty($this->userparameters)) : ?>
<div class="kmodule user-edit_user">
	<div class="kbox-wrapper kbox-fuls">
		<div class="user-edit_user-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kflattable"><?php echo JText::_('COM_KUNENA_GLOBAL_SETTINGS'); ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="detailsbox kbox-border kbox-border_radius kbox-shadow">
					<ul class="kform user-edit-information-list kbox-full">
						<?php foreach ($this->userparameters as $userparam): ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<?php echo $userparam->label ?>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer-left kbox-full">
									<?php echo $userparam->input ?>
								</div>
							</div>
						</li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<?php endif; ?>
