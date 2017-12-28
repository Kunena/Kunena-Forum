<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>
		<tr>
			<td class="span3">
				<label for="username"><?php echo JText::_('COM_KUNENA_UNAME'); ?></label>
			</td>
			<td>
				<input type="text" name="username" id="username"
				       value="<?php echo $this->escape($this->user->get('username'));?>"
				       <?php if (!$this->changeUsername) { echo 'disabled="disabled"'; } ?> />
			</td>
		</tr>
		<tr>
			<td>
				<label for="name"><?php echo JText::_('COM_KUNENA_REALNAME'); ?></label>
			</td>
			<td>
				<input class="required" type="text" id="name" name="name"
				       value="<?php echo $this->escape($this->user->get('name')); ?>" size="40" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="email"><?php echo JText::_('COM_KUNENA_USRL_EMAIL'); ?></label>
			</td>
			<td>
				<input class="required validate-email" type="text" id="email" name="email"
				       value="<?php echo $this->escape($this->user->get('email')); ?>" size="40" />
			</td>
		</tr>
		<?php if($this->user->get('password')) : ?>
		<tr>
			<td>
				<label for="password"><?php echo JText::_('COM_KUNENA_PASS'); ?></label>
			</td>
			<td>
				<input class="validate-password" type="password" id="password" name="password"
				       value="" size="40" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="password2"><?php echo JText::_('COM_KUNENA_VPASS'); ?></label>
			</td>
			<td>
				<input class="validate-passverify" type="password" id="password2" name="password2"
				       value="" size="40" />
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<?php if(!empty($this->frontendForm)) : ?>
<h3>
	<?php echo JText::_('COM_KUNENA_GLOBAL_SETTINGS'); ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>

		<?php foreach ($this->frontendForm as $field): ?>
		<tr>
			<td class="span3">
				<?php echo $field->label; ?>
			</td>
			<td>
				<?php echo $field->input; ?>
			</td>
		</tr>
		<?php endforeach; ?>

	</tbody>
</table>
<?php endif;
