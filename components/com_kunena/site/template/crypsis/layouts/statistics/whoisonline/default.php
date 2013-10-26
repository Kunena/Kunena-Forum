<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kfrontend">
 <h3 class="btn-link">
	<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_TITLE') ?>
 	<div class="btn btn-small pull-right" data-toggle="collapse" data-target="#kwho">X</div>
 </h3>
 
<div id="kwho">
<div class="well well-small">
	<p>
		<?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline) ?>
	</p>

	<?php if (!empty($this->onlineList)) : ?>
	<ul class="inline">
	<?php foreach ($this->onlineList as $user) : ?>
		<li><?php echo $user->getLink(); ?></li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>

	<?php if (!empty($this->hiddenList)) : ?>
	<ul class="inline">
		<li><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>: </li>
	<?php foreach ($this->hiddenList as $user) : ?>
		<li><?php echo $user->getLink(); ?></li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>

	<ul class="inline">
		<li><?php echo JText::_('COM_KUNENA_LEGEND'); ?>:</li>
		<li class = "kwho-admin" title = "<?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?></li>
		<li class = "kwho-globalmoderator" title = "<?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?></li>
		<li class = "kwho-moderator" title = "<?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?></li>
		<li class = "kwho-banned" title = "<?php echo JText::_('COM_KUNENA_COLOR_BANNED'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_BANNED'); ?></li>
		<li class = "kwho-user" title = "<?php echo JText::_('COM_KUNENA_COLOR_USER'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_USER'); ?></li>
		<li class = "kwho-guest" title = "<?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?></li>
	</ul>
	</div>
 </div>
</div>