<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage User
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kblock kedituser">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE') ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table>
	<tbody class="kmyprofile_general">
		<tr class="krow2">
			<td class="kcol-first">
				<label for="username"><?php echo JText::_( 'COM_KUNENA_UNAME' ); ?></label>
			</td>
			<td class="kcol-mid">
				<input type="text" name="username" id="username" value="<?php echo $this->escape($this->user->get('username'));?>" <?php echo !$this->usernamechange ? 'disabled="disabled" ' : '' ?>/>
			</td>
		</tr>
		<tr class="krow1">
			<td class="kcol-first">
				<label for="name"><?php echo JText::_( 'COM_KUNENA_REALNAME' ); ?></label>
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
				<input class="inputbox validate-password" type="password" id="kpassword" name="password" value="" size="40" />
			</td>
		</tr>
		<tr class="krow2">
			<td class="kcol-first">
				<label for="password2"><?php echo JText::_( 'COM_KUNENA_VPASS' ); ?></label>
			</td>
					<td class="kcol-mid">
				<input class="inputbox validate-passverify" type="password" id="kpassword2" name="password2" value="" size="40" />
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
		</div>
	</div>
</div>
<?php if(!empty($this->userparameters)) : ?>
<div class="kblock kedituser">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_GLOBAL_SETTINGS'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<table>
				<tbody class="kmyprofile_params">
					<?php $i=0; foreach ($this->userparameters as $userparam): ?>
					<tr class="krow<?php echo ($i^=1)+1;?>">
						<td class="kcol-first">
							<?php echo $userparam->label ?>
						</td>
						<td class="kcol-mid">
							<?php echo $userparam->input ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php endif; ?>
