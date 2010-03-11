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
?>
<div id="kprofile-edit">
	<dl class="tabs">
		<dt class="open"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayEditUser(); ?>
		</dd>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayEditProfile(); ?>
		</dd>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayEditAvatar(); ?>
		</dd>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayEditSettings(); ?>
		</dd>
	</dl>
</div>
