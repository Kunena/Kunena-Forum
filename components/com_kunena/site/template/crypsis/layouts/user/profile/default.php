<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaUser $user */
$user = $this->user;
$avatar = $user->getAvatarImage('img-polaroid', 'post');
$show = KunenaConfig::getInstance()->showuserstats;

if ($show)
{
	$rankImage = $user->getRank(0, 'image');
	$rankTitle = $user->getRank(0, 'title');
	$personalText = $user->getPersonalText();
}
?>
<ul class="unstyled center profilebox">
	<li>
		<strong><?php echo $user->getLink(); ?></strong>
	</li>

	<?php if ($avatar) : ?>
	<li>
		<?php echo $user->getLink($avatar); ?>
		<?php if (isset($this->topic_starter) && $this->topic_starter) : ?>
				<span class="topic-starter"></span>
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

			<?php if ($show && isset($user->points)) : ?>
			<li>
				<?php echo JText::_('COM_KUNENA_AUP_POINTS') . ' ' . (int) $user->points; ?>
			</li>
			<?php endif; ?>

			<?php if ($show && !empty($user->medals)) : ?>
			<li>
				<?php echo implode(' ', $user->medals); ?>
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
