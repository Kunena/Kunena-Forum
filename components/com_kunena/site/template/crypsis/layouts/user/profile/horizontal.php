<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaUser $user */
$user = $this->user;
$avatar = $user->getAvatarImage('img-polaroid', 60, 60);
$show = KunenaConfig::getInstance()->showuserstats;
if ($show) {
	$rankImage = $user->getRank(0, 'image');
	$rankTitle = $user->getRank(0, 'title');
	$personalText = $user->getPersonalText();
}
?>


	<div class="span2">
		<ul class="profilebox center">
			<li>
				<strong><?php echo $user->getLink(); ?></strong>
			</li>
			<?php if ($avatar) : ?>

				<li>
					<?php echo $user->getLink($avatar); ?>
					<?php if (isset($this->topic_starter) && $this->topic_starter) : ?>
						<span class="topic-starter"></span>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="span2">
		<br>
		<ul class="profilebox center">
			<?php if ($user->exists()) : ?>
				<li>
						<span class="label label-<?php echo $user->isOnline('success', 'important') ?>">
							<?php echo $user->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?>
						</span>

				</li>
			<?php endif; ?>
			<?php if (!empty($rankTitle)) : ?>
				<li>
					<?php echo $this->escape($rankTitle); ?>
				</li>
			<?php endif; ?>

			<?php if (!empty($rankImage)) : ?>
				<li>
					<?php echo $rankImage; ?>
				</li>
			<?php endif; ?>

			<?php if (!empty($personalText)) : ?>
				<li>
					<?php echo $personalText; ?>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<br/>
	<ul class="span2">
		<li>
			<strong> <?php echo JText::_('COM_KUNENA_POSTS'); ?> </strong>
			<span> <?php echo JText::sprintf((int)$user->posts); ?> </span>
		</li>
		<li>
			<strong> <?php echo JText::_('COM_KUNENA_PROFILE_VIEWS'); ?>:</strong>
			<span> <?php echo JText::sprintf((int)$user->uhits); ?> </span>
		</li>
		<li>
			<strong> <?php echo JText::_('COM_KUNENA_THANK_YOU_RECEIVED'); ?>:</strong>
			<span> <?php echo JText::sprintf((int)$user->thankyou); ?> </span>
		</li>
		<?php if (isset($user->points)) : ?>
			<li>
				<strong> <?php echo JText::_('COM_KUNENA_AUP_POINTS'); ?> </strong>
				<span> <?php echo (int)$user->points; ?> </span>
			</li>
		<?php endif; ?>
	</ul>
	<ul class="span3">
		<li>
			<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong>
			<span> <?php echo $user->getGender(); ?> </span>
		</li>
		<li>
			<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong>
			<span> <?php echo KunenaDate::getInstance($user->birthdate)->toSpan('date', 'ago', 'utc'); ?> </span>
		</li>
		<?php if (!empty($user->medals)) : ?>
			<li>
				<strong> <?php echo JText::_('COM_KUNENA_AUP_MEDALS'); ?> </strong>
				<span> <?php echo implode(' ', $user->medals); ?> </span>
			</li>
		<?php endif; ?>
	</ul>
