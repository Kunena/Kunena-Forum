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

?>
<div class="kblock kwhoisonline">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kwhoisonline"></a></span>
		<h2><span><span class="ktitle km"><?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_TITLE') ?></span></span></h2>
	</div>
	<div class="kcontainer" id="kwhoisonline">
		<div class="kbody">
	<table class = "kblocktable">
		<tr class = "krow2">
			<td class = "kcol-first">
				<div class="kwhoicon"></div>
			</td>
			<td class = "kcol-mid km">
				<div class="kwhoonline kwho-total ks"><?php  echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline) ?></div>
				<div>
					<?php foreach ($this->onlineList as $user) : ?>
					 <?php echo $user->getLink() ?>
					<?php endforeach ?>
					<?php if (!empty($this->hiddenList)) : ?>
						<br />
						<span class="khidden-ktitle ks"><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>: </span>
						<br />
						<?php foreach ($this->hiddenList as $user) : ?>
						 <?php echo $user->getLink() ?>
						<?php endforeach ?>
					<?php endif; ?>
				</div>
				<div class="kwholegend ks">
					<span><?php echo JText::_('COM_KUNENA_LEGEND'); ?> :: </span>&nbsp;
					<span class = "kuser-admin" title = "<?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?></span>,&nbsp;
					<span class = "kuser-globalmod" title = "<?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?></span>,&nbsp;
					<span class = "kuser-moderator" title = "<?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?></span>,&nbsp;
					<span class = "kuser-user" title = "<?php echo JText::_('COM_KUNENA_COLOR_USER'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_USER'); ?></span>,&nbsp;
					<span class = "kuser-guest" title = "<?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?></span>
				</div>
			</td>
		</tr>
</table>
</div>
</div>
</div>
