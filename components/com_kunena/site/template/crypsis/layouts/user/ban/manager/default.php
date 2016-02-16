<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th class="span1 center">
				#
			</th>
			<th class="span4">
				<?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?>
			</th>
			<th class="span3">
				<?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?>
			</th>
			<th class="span2">
				<?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?>
			</th>
			<th class="span2">
				<?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($this->userBans) :
			$i = 0;

			/** @var KunenaUserBan $banInfo */
			foreach ($this->userBans as $banInfo) :
				$bantext = $banInfo->blocked
					? JText::_('COM_KUNENA_BAN_UNBLOCK_USER')
					: JText::_('COM_KUNENA_BAN_UNBAN_USER');
		?>
		<tr>
			<td class="center">
				<?php echo ++$i; ?>
			</td>
			<td>
				<?php echo $banInfo->getUser()->getLink(); ?>
			</td>
			<td>
				<?php echo $banInfo->blocked
					? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA')
					: JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA'); ?>
			</td>
			<td>
				<?php echo $banInfo->getCreationDate()->toKunena('datetime'); ?>
			</td>
			<td>
				<?php echo $banInfo->isLifetime()
					? JText::_('COM_KUNENA_BAN_LIFETIME')
					: $banInfo->getExpirationDate()->toKunena('datetime'); ?>
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
