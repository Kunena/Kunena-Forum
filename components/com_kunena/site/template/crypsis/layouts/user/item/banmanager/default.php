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
	<?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th class="span1 center"> # </th>
			<th class="span4"><?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?></th>
			<th class="span3"><?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?></th>
			<th class="span2"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
			<th class="span2"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if ($this->bannedusers) :
			$i = 0;
			foreach ($this->bannedusers as $userban) :
				$bantext = $userban->blocked ? JText::_('COM_KUNENA_BAN_UNBLOCK_USER') : JText::_('COM_KUNENA_BAN_UNBAN_USER');
				$i++;
		?>
		<tr>
			<td class="center">
				<?php echo $i; ?>
			</td>
			<td>
				<?php echo $userban->getUser()->getLink() ?>
			</td>
			<td>
				<?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA'); ?>
			</td>
			<td>
				<?php echo KunenaDate::getInstance($userban->created_time)->toKunena('datetime'); ?>
			</td>
			<td>
				<?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : KunenaDate::getInstance($userban->expiration)->toKunena('datetime'); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php else : ?>
		<tr>
			<td colspan="5">
				<?php echo JText::_('COM_KUNENA_BAN_NO_BANNED_USERS'); ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
