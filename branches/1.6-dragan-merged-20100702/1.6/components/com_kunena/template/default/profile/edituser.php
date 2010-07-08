<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
$kunena_config = KunenaFactory::getConfig ();
?>
<div class="kblock kedituser">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE') ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : '' ?>" id="kflattable">
	<tbody class="kmyprofile_general">
		<tr class="krow2">
			<td class="kcol-first">
				<label for="username"><?php echo JText::_( 'COM_KUNENA_UNAME' ); ?></label>
			</td>
			<td class="kcol-mid">
				<input type="text" name="username" value="<?php echo $this->escape($this->user->get('username'));?>" <?php echo !$this->config->usernamechange ? 'disabled="disabled" ' : ''?>/>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first">
				<label for="name"><?php echo JText::_( 'COM_KUNENA_USRL_NAME' ); ?></label>
			</td>
					<td class="kcol-mid">
				<input class="inputbox required" type="text" id="name" name="name" value="<?php echo $this->escape($this->user->get('name'));?>" size="40" />
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first">
				<label for="email"><?php echo JText::_( 'COM_KUNENA_USRL_EMAIL' ); ?></label>
			</td>
					<td class="kcol-mid">
				<input class="inputbox required validate-email" type="text" id="email" name="email" value="<?php echo $this->escape($this->user->get('email'));?>" size="40" />
			</td>
		</tr>
		<?php if($this->user->get('password')) : ?>
		<tr class="krow1">
			<td class="kcol-first">
				<label for="password"><?php echo JText::_( 'COM_KUNENA_PASS' ); ?></label>
			</td>
					<td class="kcol-mid">
				<input class="inputbox validate-password" type="password" id="password" name="password" value="" size="40" />
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first">
				<label for="password2"><?php echo JText::_( 'COM_KUNENA_VPASS' ); ?></label>
			</td>
					<td class="kcol-mid">
				<input class="inputbox validate-passverify" type="password" id="password2" name="password2" value="" size="40" />
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
		</div>
	</div>
</div>
<?php if(!empty($this->userparams)) : ?>
<div class="kblock kedituser">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_GLOBAL_SETTINGS'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : '' ?>" id="kflattable">
				<tbody class="kmyprofile_params">
					<?php $i=0; foreach ($this->userparams as $userparam): ?>
					<tr class="krow<?php echo ($i^=1)+1;?>">
						<td class="kcol-first">
							<label for="params<?php echo $userparam[5] ?>" title="<?php echo $userparam[2] ?>"><?php echo $userparam[0] ?></label>
						</td>
						<td class="kcol-mid">
							<?php echo $userparam[1] ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php endif; ?>