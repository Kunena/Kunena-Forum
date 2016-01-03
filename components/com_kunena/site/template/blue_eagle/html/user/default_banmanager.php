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

JHtml::_('behavior.tooltip');
$i=1;
$j=0;
?>
<div class="kblock banmanager">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<table class="kblock-ban">
				<thead>
					<tr class="ksth">
						<th class="kid"> # </th>
						<th class="kbanned-user" width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?></th>
						<th class="kbanned-from" width="15%"><?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?></th>
						<th class="kbanned-start" width="32%"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
						<th class="kbanned-expire" width="32%"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( $this->bannedusers ) :
						foreach ($this->bannedusers as $userban) :
							$bantext = $userban->blocked ? JText::_('COM_KUNENA_BAN_UNBLOCK_USER') : JText::_('COM_KUNENA_BAN_UNBAN_USER');
							$j++;
					?>
					<tr class="krow<?php echo ($i^=1)+1;?>">
						<td class="kcol-first kid">
							<?php echo $j; ?>
						</td>
						<td class="kcol-mid kbanned-user">
							<a href="#"><?php echo $userban->getUser()->getLink() ?> </a>
						</td>
						<td class="kcol-mid kbanned-from">
							<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA'); ?></span>
						</td>
						<td class="kcol-mid kbanned-start">
							<span><?php echo KunenaDate::getInstance($userban->created_time)->toKunena('datetime'); ?></span>
						</td>
						<td class="kcol-mid kbanned-expire">
							<span><?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : KunenaDate::getInstance($userban->expiration)->toKunena('datetime'); ?></span>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php else : ?>
					<tr class="krow2">
						<td colspan="5" class="kcol-first">
							<?php echo JText::_('COM_KUNENA_BAN_NO_BANNED_USERS'); ?>
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
