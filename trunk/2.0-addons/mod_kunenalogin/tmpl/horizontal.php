<?php
/**
 * @version $Id: horizontal.php 4047 2010-12-21 07:59:21Z severdia $
 * Kunenalogin Module
 * @package Kunena Login
 *
 * @Copyright (C) 2010-2011 Kunena Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined('_JEXEC') or die();
?>
<div class="klogin-horiz">
	<?php if($this->type == 'logout') : ?>
		<form action="index.php" method="post" name="login">
			<div class="klogin-avatar">
				<?php if ($this->params->get('showav')) :
					$avatar =  $this->kunenaAvatar( $this->my->id ) ;
					echo $avatar;
				endif; ?>
			</div>
			<div class="klogin-middle">
				<ul>
				<?php if ($this->params->get('greeting')) : ?>
					<li class="klogin-hiname">
					<?php echo JText::sprintf('MOD_KUNENALOGIN_HINAME','<strong>'.CKunenaLink::GetProfileLink ( $this->me->userid, $this->me->getName()).'</strong>' ); ?>
					</li>
				<?php endif; ?>
				<?php if ($this->params->get('lastlog')) : ?>
					<li>
						<span class="klogin-lasttext"><?php echo JText::_('MOD_KUNENALOGIN_LASTVISIT'); ?></span>
						<?php echo $this->lastvisitDate->toSpan('date_today', 'ago', 'klogin-lastdate') ?>
					</li>
				<?php endif; ?>
					<li class="klogin-logout-button">
						<input type="submit" name="Submit" class="kbutton" value="<?php echo JText::_('MOD_KUNENALOGIN_BUTTON_LOGOUT'); ?>" />
					</li>
				</ul>
			</div>
			<div class="klogin-links">
				<ul class="klogin-loginlink">
				<?php if ($this->privateMessages) : ?>
					<li class="klogin-mypm"><?php echo $this->privateMessages; ?></li>
				<?php endif; ?>
				<?php if ($this->params->get('showprofile')) : ?>
					<li class="klogin-myprofile"><?php echo CKunenaLink::GetProfileLink ( $this->my->id, JText::_ ( 'MOD_KUNENALOGIN_MYPROFILE' ) ); ?></li>
				<?php endif; ?>
				<?php if (0 && $this->params->get('showmyposts')) : ?>
					<li class="klogin-mypost"><?php echo CKunenaLink::GetShowMyLatestLink ( JText::_ ( 'MOD_KUNENALOGIN_MYPOSTS' ) ); ?></li>
				<?php endif; ?>
				<?php if ($this->params->get('showrecent')) : ?>
					<li class="klogin-recent"><?php echo CKunenaLink::GetShowLatestLink ( JText::_ ( 'MOD_KUNENALOGIN_RECENT' ) ); ?></li>
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
		<form action="index.php" method="post" name="login" class="klogin-form-login" >
			<?php echo $this->params->get('pretext'); ?>
			<ul class="klogin-logoutfield">
				<li class="klogout-uname">
				<span class="klogin-form-login-username">
					<label for="klogin-username"><?php //echo JText::_('MOD_KUNENALOGIN_USERNAME') ?></label>
					<input class="klogin-username" type="text" name="<?php echo $this->login['field_username']; ?>" class="inputbox" alt="username" size="18" value="<?php echo JText::_('MOD_KUNENALOGIN_USERNAME'); ?>" onblur = "if(this.value=='') this.value='<?php echo JText::_('MOD_KUNENALOGIN_USERNAME'); ?>';" onfocus = "if(this.value=='<?php echo JText::_('MOD_KUNENALOGIN_USERNAME'); ?>') this.value='';" />
				</span>
				<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
				<span class="klogin-form-login-remember"><label for="klogin-remember">
				<input class="klogin-remember" type="checkbox" name="remember" value="yes" alt="<?php echo JText::_('MOD_KUNENALOGIN_REMEMBER_ME') ?>" />
					<?php echo JText::_('MOD_KUNENALOGIN_REMEMBER_ME') ?></label>
				</span>
				<?php endif; ?>
				</li>
				<li class="klogout-pwd">
				<span class="klogin-form-login-password">
					<label for="klogin-passwd"><?php //echo JText::_('MOD_KUNENALOGIN_PASSWORD') ?></label>
					<input class="klogin-passwd" type="password" name="<?php echo $this->login['field_password']; ?>" class="kinputbox" size="18" alt="password"  value="<?php echo JText::_('MOD_KUNENALOGIN_PASSWORD'); ?>" onblur = "if(this.value=='') this.value='<?php echo JText::_('MOD_KUNENALOGIN_PASSWORD'); ?>';" onfocus = "if(this.value=='<?php echo JText::_('MOD_KUNENALOGIN_PASSWORD'); ?>') this.value='';"/>
				</span>
				<input type="submit" name="Submit" class="kbutton" value="<?php echo JText::_('MOD_KUNENALOGIN_BUTTON_LOGIN') ?>" />
				</li>
			</ul>

			<ul class="klogin-logoutlink">
				<li class="klogin-forgotpass"><?php echo CKunenaLink::GetHrefLink($this->lostpassword, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD')) ?></li>
				<li class="klogin-forgotname"><?php echo CKunenaLink::GetHrefLink($this->lostusername, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME')) ?></li>
				<?php if ($this->register) : ?>
				<li class="klogin-register"><?php echo CKunenaLink::GetHrefLink($this->register, JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT')) ?></li>
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