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
$kunena_config =& CKunenaConfig::getInstance();
?>
<script type="text/javascript">
<!--
	Window.onDomReady(function(){
		document.formvalidator.setHandler('passverify', function (value) { return ($('password').value == value); }	);
	});
// -->
</script>

<h2><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE') ?></h2>
<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" name="userform" class="form-validate">
	<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '' ?>" id="kflattable">
	<tbody class="kmyprofile_general">
	<tr class="ksectiontableentry2">
		<td class="td-0 km center">
			<label for="username">
				<?php echo JText::_( 'COM_KUNENA_UNAME' ); ?>
			</label>
		</td>
		<td>
			<span><?php echo $this->user->get('username');?></span>
			<input type="hidden" name="username" value="<?php echo $this->user->get('username');?>" />
		</td>
	</tr>
	<tr class="ksectiontableentry1">
		<td class="td-0 km center" width="120">
			<label for="name">
				<?php echo JText::_( 'COM_KUNENA_USRL_NAME' ); ?>
			</label>
		</td>
		<td>
			<input class="inputbox required" type="text" id="name" name="name" value="<?php echo $this->escape($this->user->get('name'));?>" size="40" />
		</td>
	</tr>
	<tr class="ksectiontableentry2">
		<td class="td-0 km center">
			<label for="email">
				<?php echo JText::_( 'COM_KUNENA_USRL_EMAIL' ); ?>
			</label>
		</td>
		<td>
			<input class="inputbox required validate-email" type="text" id="email" name="email" value="<?php echo $this->escape($this->user->get('email'));?>" size="40" />
		</td>
	</tr>
	<?php if($this->user->get('password')) : ?>
	<tr class="ksectiontableentry1">
		<td class="td-0 km center">
			<label for="password">
				<?php echo JText::_( 'COM_KUNENA_PASS' ); ?>
			</label>
		</td>
		<td>
			<input class="inputbox validate-password" type="password" id="password" name="password" value="" size="40" />
		</td>
	</tr>
	<tr class="ksectiontableentry2">
		<td class="td-0 km center">
			<label for="password2">
				<?php echo JText::_( 'COM_KUNENA_VPASS' ); ?>
			</label>
		</td>
		<td>
			<input class="inputbox validate-passverify" type="password" id="password2" name="password2" size="40" />
		</td>
	</tr>
	<?php endif; ?>
	</tbody>
	</table>
	<?php if(!empty($this->userparams)) : ?>
	<h2><?php echo JText::_('Global Settings'); ?></h2>
	<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '' ?>" id="kflattable">
	<tbody class="kmyprofile_params">
	<?php $i=0; foreach ($this->userparams as $userparam): ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="td-0 km center" width="120">
			<label for="params<?php echo $userparam[5] ?>" title="<?php echo $userparam[2] ?>">
				<?php echo $userparam[0] ?>
			</label>
		</td>
		<td>
			<?php echo $userparam[1] ?>
		</td>
	</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>

	<button class="kbutton ks validate" type="submit" onclick="submitbutton( this.form );return false;"><?php echo JText::_('COM_KUNENA_GEN_SUBMIT'); ?></button>

	<input type="hidden" name="id" value="<?php echo $this->user->get('id');?>" />
	<input type="hidden" name="gid" value="<?php echo $this->user->get('gid');?>" />
	<input type="hidden" name="option" value="com_user" />
	<input type="hidden" name="task" value="save" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>