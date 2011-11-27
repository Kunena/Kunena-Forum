<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id ="profilebox" style="display:none;">
			<div id="mb_login">
				<div class="tk-mb-header-login" style="display:none;">
					<span class="tk-mb-first"><?php echo JText::_('COM_KUNENA_TEMPLATE_MEMB_LOGIN');  ?></span>
				</div>
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="login">
			<input type="hidden" name="view" value="user" />
			<input type="hidden" name="task" value="login" />
			[K=TOKEN]

			<ul class="topiclist forums">
				<li class="rowfull">
					<dl class="icon profilebox">
						<dt>
						</dt>
						<dd class="first tk-welcome">
							<ul>
								<li>
									<input type="text" name="username" class="inputbox tk-username" alt="username" size="25" value="" />
								</li>
								<li>
									<input type="password" name="password" class="inputbox tk-password" size="25" alt="password" value="" />
								</li>
								<li>
							<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
							<input type="checkbox" name="remember" alt="" value="yes" />
							<?php echo JText::_('COM_KUNENA_LOGIN_REMEMBER_ME');  ?>
							<?php endif; ?><input type="submit" name="submit" class="tk-login-button" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?>" />
								</li>
							</ul>
						</dd>
						<dd class="tk-loginform">
							<ul>
								<li class="tk-lostpwdlink">
									<?php echo CKunenaLink::GetHrefLink($this->lostpassword, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD')) ?>
								</li>
								<li class="tk-lostuserlink">
									<?php echo CKunenaLink::GetHrefLink($this->lostusername, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME')) ?>
								</li>
							</ul>
						</dd>
					</dl>
				</li>
			</ul>
		</form>
	</div>
</div>

<?php include dirname ( __FILE__ ) . '/rules.php'; ?>
<?php include dirname ( __FILE__ ) . '/register.php'; ?>

<script type="text/javascript">
//<![CDATA[
var register=new switchcontent("register", "div")
register.setColor('black', '')
register.setPersist(false)
register.collapsePrevious(true)
register.init()
// ]]>
</script>