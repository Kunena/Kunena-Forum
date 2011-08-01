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
JHTML::_('behavior.formvalidation')
?>
<div id="tk-register" class="register">
<div class="forumlist">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon register">
						<dt>
							<span class="ktitle"><?php echo JText::_('COM_KUNENA_TEMPLATE_MEMBERS_REG'); ?></span>
						</dt>
					</dl>
				</li>
			</ul><div id="register_tbody">
			<ul class="topiclist forums">
				<li class="rowfull">
					<dl class="icon caticon-register">
						<dt></dt>
						<dd class="first" style="width:65%;padding-bottom: 15px !important;">
				<form action="<?php echo JRoute::_( 'index.php?option=com_user' ); ?>" method="post" id="josForm" name="josForm" class="form-validate">
			<div class="tk-reg">
					<div class="tk-reg_ins">
						<label id="namemsg" for="name">
							<?php echo JText::_( 'COM_KUNENA_GEN_NAME' ); ?>:
						</label>
						<input type="text" name="name" id="name" size="40" value="" class="inputbox required" maxlength="50" />
					</div>
					<div class="tk-reg_ins">
						<label id="usernamemsg" for="username">
							<?php echo JText::_( 'COM_KUNENA_BAN_USERNAME' ); ?>:
						</label>
						<input type="text" id="username" name="username" size="40" value="" class="inputbox required validate-username" maxlength="25" />
					</div>
					<div class="tk-reg_ins">
						<label id="emailmsg" for="email">
							<?php echo JText::_( 'COM_KUNENA_USRL_EMAIL' ); ?>:
						</label>
						<input type="text" id="email" name="email" size="40" value="" class="inputbox required validate-email" maxlength="100" />
					</div>
				<div class="tk-reg_ins">
						<label id="pwmsg" for="password">
							<?php echo JText::_( 'COM_KUNENA_TEMPLATE_PASSWORD' ); ?>:
						</label>
						<input class="inputbox required validate-password" type="password" id="password" name="password" size="40" value="" />
				</div>
					<div class="tk-reg_ins">
						<label id="pw2msg" for="password2">
							<?php echo JText::_( 'COM_KUNENA_TEMPLATE_CONFIRM_PASSWORD' ); ?>:
						</label>
						<input class="inputbox required validate-passverify" type="password" id="password2" name="password2" size="40" value="" />
				</div>
				</div>
					<input class="tk-submit-button validate" type="submit" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_REGISTER'); ?>" />
					<input class="tk-reset-button" type="reset" value="<?php echo JText::_('COM_KUNENA_RESET'); ?>" />
					<input class="tk-cancel-button" type="button" value="<?php echo JText::_('COM_KUNENA_CANCEL'); ?>" onclick="javascript:register.sweepToggle('contract')" />
					<input type="hidden" name="task" value="register_save" />
					<input type="hidden" name="id" value="0" />
					<input type="hidden" name="gid" value="0" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
						</dd>
						<dd style="float:left;width:23%;text-align: justify;padding: 0 20px;margin-top:10px; border:0;">
							<p><?php echo JText::_('COM_KUNENA_TEMPLATE_REGISTER_NOTE'); ?></p>
							<p class="information_td" style="color:red;"><?php echo JText::_( 'COM_KUNENA_TEMPLATE_REQUIRED' ); ?></p>
						</dd>
					</dl>
				</li>
			</ul><div class="body-bottom1"><div class="body-bottom2"><div class="body-bottom3"><div class="body-bottom4"></div></div></div></div></div>
			<span class="corners-bottom"><span></span></span>
		</div>
	</div>
</div>