<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaUser $user */
$user = $this->user;
$avatar = $user->getAvatarImage('img-polaroid', 'post');
$show = KunenaConfig::getInstance()->showuserstats;
$activityIntegration = KunenaFactory::getActivityIntegration();
$points = $activityIntegration->getUserPoints($user->userid);
$medals = $activityIntegration->getUserMedals($user->userid);

if ($show)
{
	$rankImage = $user->getRank($this->category_id, 'image');
	$rankTitle = $user->getRank($this->category_id, 'title');
	$personalText = $user->getPersonalText();
}
?>
<ul class="unstyled center profilebox">
	<li>
		<strong><?php echo $user->getLink(null, null, 'nofollow', '', null, $this->category_id); ?></strong>
	</li>

	<?php if ($avatar) : ?>
	<li>
		<?php echo $user->getLink($avatar); ?>
		<?php if (isset($this->topic_starter) && $this->topic_starter) : ?>
				<span class="topic-starter"><?php echo JText::_('COM_KUNENA_TOPIC_AUTHOR') ?></span>
		<?php endif;?>
		<?php if (!$this->topic_starter && $user->isModerator()) : ?>
			<span class="topic-moderator"><?php echo JText::_('COM_KUNENA_MODERATOR') ?></span>
		<?php endif;?>
	</li>
	<?php endif; ?>

	<?php if ($user->exists()) : ?>
	<li>
		<?php echo $this->subLayout('User/Item/Status')->set('user', $user); ?>
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
<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_profile_default'); ?>
<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_topicprofile'); ?>
<?php if ($user->userid > 1) : ?>
<div class="profile-expand center">
	<span class="heading btn btn-small heading-less" style="display:none;"><i class="icon-arrow-up"></i> <?php echo JText::_('COM_KUNENA_USER_PROFILE_BUTTON_LABEL_LESS') ?></span>
	<span class="heading btn btn-small"><i class="icon-arrow-down"></i> <?php echo JText::_('COM_KUNENA_USER_PROFILE_BUTTON_LABEL_MORE') ?></span>
	<div class="content" style="display:none;">
		<ul>
			<?php if ($user->posts >= 1) : ?>
			<li>
				<?php echo JText::_('COM_KUNENA_POSTS') . ' ' . (int) $user->posts; ?>
			</li>
			<?php endif; ?>

			<?php if ($show && isset($user->thankyou)) : ?>
			<li>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_THANKYOU_RECEIVED') . ' ' . (int) $user->thankyou; ?>
			</li>
			<?php endif; ?>

			<?php if ($show && !empty($points)) : ?>
			<li>
				<?php echo JText::_('COM_KUNENA_AUP_POINTS') . ' '  . $points; ?>
			</li>
			<?php endif; ?>

			<?php if ($show && !empty($medals)) : ?>
			<li>
				<?php echo implode(' ', $medals); ?>
			</li>
			<?php endif; ?>

			<li>
				<?php echo $user->profileIcon('gender'); ?>
				<?php echo $user->profileIcon('birthdate'); ?>
				<?php echo $user->profileIcon('location'); ?>
				<?php echo $user->profileIcon('website'); ?>
				<?php echo $user->profileIcon('private'); ?>
				<?php echo $user->profileIcon('email'); ?>
			</li>
			<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_topicprofilemore'); ?>
		</ul>
	</div>
</div>
<?php endif; ?>
