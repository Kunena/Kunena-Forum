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
	<thead>
		<tr>
			<th class="span1 center">
				#
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
			<th class="span2">
				<?php echo JText::_('COM_KUNENA_BAN_CREATEDBY'); ?>
			</th>
			<th class="span2">
				<?php echo JText::_('COM_KUNENA_BAN_MODIFIEDBY'); ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php
		if (!empty($this->banHistory)) :
			$i = count($this->banHistory);

			// @var KunenaUserBan $banInfo

			foreach ($this->banHistory as $banInfo) :
		?>
		<tr>
			<td class="center">
				<?php echo $i--; ?>
			</td>
			<td>
				<?php echo $banInfo->blocked
					? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA')
					: JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA') ?>
			</td>
			<td>
				<?php echo $banInfo->getCreationDate()->toKunena('datetime'); ?>
			</td>
			<td>
				<?php echo $banInfo->isLifetime()
					? JText::_('COM_KUNENA_BAN_LIFETIME')
					: $banInfo->getExpirationDate()->toKunena('datetime'); ?>
			</td>
			<td>
				<?php echo $banInfo->getCreator()->getLink(); ?>
			</td>
			<td>
				<?php
				if ($banInfo->modified_by && $banInfo->modified_time) {
					echo $banInfo->getModifier()->getLink()
						. ' ' . $banInfo->getModificationDate()->toKunena('datetime'); }
				?>
			</td>
		</tr>

		<?php if ($banInfo->reason_public) : ?>
		<tr>
			<td></td>
			<td>
				<b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b>
			</td>
			<td colspan="4">
				<?php echo KunenaHtmlParser::parseText($banInfo->reason_public); ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php if($this->me->isModerator() && $banInfo->reason_private) : ?>
		<tr>
			<td></td>
			<td>
				<b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b></td>
			<td colspan="4">
				<?php echo KunenaHtmlParser::parseText($banInfo->reason_private); ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php
			if ($this->me->isModerator() && !empty($banInfo->comments)) {
				foreach ($banInfo->comments as $comment) :
		?>
		<tr>
			<td></td>
			<td>
				<strong>
					<?php echo JText::sprintf(
'COM_KUNENA_BAN_COMMENT_BY', KunenaFactory::getUser((int) $comment->userid)->getLink());
					?>
				</strong>
			</td>
			<td>
				<?php echo KunenaDate::getInstance($comment->time)->toKunena(); ?>
			</td>
			<td colspan="3">
				<?php echo KunenaHtmlParser::parseText($comment->comment); ?>
			</td>
		</tr>
		<?php endforeach;
}; ?>

		<?php endforeach; ?>

		<?php else : ?>

		<tr>
			<td colspan="6">
				<?php echo JText::sprintf('COM_KUNENA_BAN_USER_NOHISTORY', $this->escape($this->profile->getName())); ?>
			</td>
		</tr>
		<?php endif; ?>

	</tbody>
</table>
