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
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius">
		<div class="edituser block">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header"><a rel="kedit-user-information"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE') ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="detailsbox box-full box-border box-border_radius box-shadow">
					<ul class="kform user-edit-information-list clear" id="kedit-user-information">
						<li class="user-edit-information-row box-hover box-hover_list-row clear">
							<div class="form-label">
								<label for="kusername"><?php echo JText::_( 'COM_KUNENA_UNAME' ) ?></label>
							</div>
							<div class="form-field">
								<input type="text" value="<?php echo $this->escape($this->user->get('username'));?>" id="kusername" name="username" class="box-width inputbox required" <?php echo !$this->config->usernamechange ? 'disabled="disabled" ' : ''?>/>
							</div>
						</li>
						<li class="user-edit-information-row box-hover box-hover_list-row clear">
							<div class="form-label">
								<label for="kname"><?php echo JText::_( 'COM_KUNENA_USRL_NAME' ) ?></label>
							</div>
							<div class="form-field">
								<input type="text" value="<?php echo $this->escape($this->user->get('name'));?>" id="kname" name="name" class="box-width inputbox required" />
							</div>
						</li>
						<li class="user-edit-information-row box-hover box-hover_list-row clear">
							<div class="form-label">
								<label for="kemail"><?php echo JText::_( 'COM_KUNENA_USRL_EMAIL' ) ?></label>
							</div>
							<div class="form-field">
								<input type="text" value="<?php echo $this->escape($this->user->get('email'));?>" id="kemail" name="email" class="box-width inputbox required" />
							</div>
						</li>
						<?php if($this->user->get('password')) : ?>
						<li class="user-edit-information-row box-hover box-hover_list-row clear">
							<div class="form-label">
								<label for="kpassword"><?php echo JText::_( 'COM_KUNENA_PASS' ) ?></label>
							</div>
							<div class="form-field">
								<input type="text" value="" id="kpassword" name="password" class="box-width inputbox" />
							</div>
						</li>
						<li class="user-edit-information-row box-hover box-hover_list-row clear">
							<div class="form-label">
								<label for="kpassword2"><?php echo JText::_( 'COM_KUNENA_VPASS' ) ?></label>
							</div>
							<div class="form-field">
								<input type="text" value="" id="kpassword2" name="password2" class="box-width inputbox" />
							</div>
						</li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
<?php if(!empty($this->userparameters)) : ?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius">
		<div class="edituser block">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header"><a rel="kflattable"><?php echo JText::_('COM_KUNENA_GLOBAL_SETTINGS'); ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="detailsbox box-border box-border_radius box-shadow">
					<ul class="kform user-edit-information-list clear">
						<?php foreach ($this->userparameters as $userparam): ?>
						<li class="user-edit-information-row box-hover box-hover_list-row clear">
							<div class="form-label">
								<?php echo $userparam->label ?>
							</div>
							<div class="form-field">
								<?php echo $userparam->input ?>
							</div>
						</li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
<?php endif; ?>
