<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<h3>
	<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE') ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>
		<tr>
			<td class="span3">
				<label for="username"><?php echo JText::_( 'COM_KUNENA_UNAME' ); ?></label>
			</td>
			<td>
				<input type="text" name="username" id="username" value="<?php echo $this->escape($this->user->get('username'));?>" <?php echo !$this->usernamechange ? 'disabled="disabled" ' : '' ?>/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="name"><?php echo JText::_( 'COM_KUNENA_REALNAME' ); ?></label>
			</td>
			<td>
				<input class="inputbox required" type="text" id="name" name="name" value="<?php echo $this->escape($this->user->get('name'));?>" size="40" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="email"><?php echo JText::_( 'COM_KUNENA_USRL_EMAIL' ); ?></label>
			</td>
			<td>
				<input class="inputbox required validate-email" type="text" id="email" name="email" value="<?php echo $this->escape($this->user->get('email'));?>" size="40" />
			</td>
		</tr>
		<?php if($this->user->get('password')) : ?>
		<tr>
			<td>
				<label for="password"><?php echo JText::_( 'COM_KUNENA_PASS' ); ?></label>
			</td>
			<td>
				<input class="inputbox validate-password" type="password" id="kpassword" name="password" value="" size="40" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="password2"><?php echo JText::_( 'COM_KUNENA_VPASS' ); ?></label>
			</td>
			<td>
				<input class="inputbox validate-passverify" type="password" id="kpassword2" name="password2" value="" size="40" />
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<?php if(!empty($this->userparameters)) : ?>
<h3>
	<?php echo JText::_('COM_KUNENA_GLOBAL_SETTINGS'); ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>
		<?php foreach ($this->userparameters as $userparam): ?>
		<tr>
			<td class="span3">
				<?php echo $userparam->label ?>
			</td>
			<td>
				<?php echo $userparam->input ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
