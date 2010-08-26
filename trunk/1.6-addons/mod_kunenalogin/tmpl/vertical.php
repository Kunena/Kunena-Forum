<?php
/**
 * @version $Id$
 * Kunenalogin Module
 * @package Kunena login
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined('_JEXEC') or die();
?>
<div id="Klogin-vert">
	<?php if($this->type == 'logout') : ?>
		<form action="index.php" method="post" name="login">
		<?php if ($this->params->get('greeting')) : ?>
			<div class="k_hiname">
			<?php if ($this->params->get('name')) :
				echo JText::sprintf('MOD_KUNENALOGIN_HINAME','<strong>'.CKunenaLink::GetProfileLink ( $this->my->id, $this->my->name).'</strong>' );
			else :
				echo JText::sprintf('MOD_KUNENALOGIN_HINAME','<strong>'.CKunenaLink::GetProfileLink ( $this->my->id, $this->my->username).'</strong>' );
			endif; ?>
			</div>
		<?php endif; ?>
	<div class="avatar">
		<?php if ($this->params->get('showav')) :
			$avatar =  $this->kunenaAvatar( $this->my->id ) ;
			echo $avatar;
		endif; ?>
	</div>
	<div>
	<?php if ($this->params->get('lastlog')) : ?>
	<div class="k_lastvisit">
		<ul>
			<li class="kms">
				<span class="k_lasttext"><?php echo JText::_('MOD_KUNENALOGIN_LASTVISIT'); ?></span>
				<span class="k_lastdate" title="<?php echo CKunenaTimeformat::showDate($this->my->lastvisitDate, 'date_today', 'utc'); ?>">
					<?php echo CKunenaTimeformat::showDate($this->my->lastvisitDate, 'ago', 'utc'); ?>
				</span>
			</li>
		</ul>
	</div>
	<?php endif; ?>
	</div>
	<div class="links">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('MOD_KUNENALOGIN_BUTTON_LOGOUT'); ?>" /></div>
		<div>
			<ul id="loginlink">
			<?php	if ($this->params->get('showmessage')) : ?>
				<?php if ($this->PMlink) : ?>
				<li class="mypm"><?php echo $this->PMlink; ?></li>
				<?php endif ?>
			<?php endif; ?>
			<?php if ($this->params->get('showprofile')) : ?>
				<li class="myprofile"><?php echo CKunenaLink::GetProfileLink ( $this->my->id, JText::_ ( 'MOD_KUNENALOGIN_MYPROFILE' ) ); ?></li>
			<?php endif; ?>
			<?php if ($this->params->get('showmyposts')) : ?>
				<li class="mypost"><?php echo CKunenaLink::GetShowMyLatestLink ( JText::_ ( 'MOD_KUNENALOGIN_MYPOSTS' ) ); ?></li>
			<?php endif; ?>
			<?php if ($this->params->get('showrecent')) : ?>
				<li class="recent"><?php echo CKunenaLink::GetShowLatestLink ( JText::_ ( 'MOD_KUNENALOGIN_RECENT' ) ); ?></li>
			<?php endif; ?>
			</ul>
		</div>
	<input type="hidden" name="option" value="<?php echo $this->logout['option']; ?>" />
	<?php if (!empty($this->logout['view'])) : ?>
	<input type="hidden" name="view" value="<?php echo $this->logout['view']; ?>" />
	<?php endif; ?>
	<input type="hidden" name="task" value="<?php echo $this->logout['task']; ?>" />
	<input type="hidden" name="<?php echo $this->logout['field_return']; ?>" value="<?php echo $this->return; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php else : ?>

<form action="index.php" method="post" name="login" id="form-login" >
	<?php echo $this->params->get('pretext'); ?>
	<fieldset class="input">
	<p id="form-login-username">
		<label for="modlgn_username"><?php echo JText::_('MOD_KUNENALOGIN_USERNAME') ?></label>
		<input id="modlgn_username" type="text" name="<?php echo $this->login['field_username']; ?>" class="inputbox" alt="username" size="18" />
	</p>
	<p id="form-login-password">
		<label for="modlgn_passwd"><?php echo JText::_('MOD_KUNENALOGIN_PASSWORD') ?></label>
		<input id="modlgn_passwd" type="password" name="<?php echo $this->login['field_password']; ?>" class="inputbox" size="18" alt="password" />
	</p>
	<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
	<p id="form-login-remember"><label for="modlgn_remember">
	<input id="modlgn_remember" type="checkbox" name="remember" value="yes" alt="<?php echo JText::_('MOD_KUNENALOGIN_REMEMBER_ME') ?>" />
		<?php echo JText::_('MOD_KUNENALOGIN_REMEMBER_ME') ?></label>
	</p>
	<?php endif; ?>
	<input type="submit" name="Submit" class="button" value="<?php echo JText::_('MOD_KUNENALOGIN_BUTTON_LOGIN') ?>" />
	</fieldset>
	<ul id="logoutlink">
		<li class="forgotpass"><?php echo CKunenaLogin::getLostPasswordLink (); ?></li>
		<li class="forgotname"><?php echo CKunenaLogin::getLostUserLink ();?></li>
		<?php
		$registration = CKunenaLogin::getRegisterLink ();
		if ($registration) : ?>
		<li class="register"><?php echo $registration ?></li>
		<?php endif; ?>
	</ul>
	<?php echo $this->params->get('posttext'); ?>
	<input type="hidden" name="option" value="<?php echo $this->login['option']; ?>" />
	<?php if (!empty($this->login['view'])) : ?>
	<input type="hidden" name="view" value="<?php echo $this->login['view']; ?>" />
	<?php endif; ?>
	<input type="hidden" name="task" value="<?php echo $this->login['task']; ?>" />
	<input type="hidden" name="<?php echo $this->login['field_return']; ?>" value="<?php echo $this->return; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
	<?php endif; ?>
</div>