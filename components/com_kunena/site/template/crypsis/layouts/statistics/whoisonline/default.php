<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Statistics
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<div class="kfrontend">
<h3 class="btn-link">
	<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_TITLE'); ?>
 	<span class="btn btn-small pull-right" data-toggle="collapse" data-target="#kwho">&times;</span>
</h3>

<div class="collapse in" id="kwho">
<div class="well well-small">
	<p>
		<?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline); ?>
	</p>

	<?php if (!empty($this->onlineList)) : ?>
	<p>

	<?php foreach ($this->onlineList as $user) : ?>
	<span><?php $onlinelist[] = $user->getLink(); ?></span>
	<?php endforeach; ?>
	<?php echo implode(', ', $onlinelist); ?>
	</p>
	<?php endif; ?>

	<?php if (!empty($this->hiddenList)) : ?>
	<p>
		<span><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>:</span>

	<?php foreach ($this->hiddenList as $user) : ?>
		<span><?php echo $user->getLink(); ?></span>
	<?php endforeach; ?>

	</p>
	<?php endif; ?>

	<?php if (!empty($this->onlineList)) : ?>
	<p>
		<span><?php echo JText::_('COM_KUNENA_LEGEND'); ?>:</span>
		<span class="kwho-admin">
			<?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?>
		</span>
		<span class="kwho-globalmoderator">
			<?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?>
		</span>
		<span class="kwho-moderator">
			<?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?>
		</span>
		<span class="kwho-banned">
			<?php echo JText::_('COM_KUNENA_COLOR_BANNED'); ?>
		</span>
		<span class="kwho-user">
			<?php echo JText::_('COM_KUNENA_COLOR_USER'); ?>
		</span>
		<span class="kwho-guest">
			<?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?>
		</span>
	</p>
	<?php endif; ?>
</div>
</div>
</div>
