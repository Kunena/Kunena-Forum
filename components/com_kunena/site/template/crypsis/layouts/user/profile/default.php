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
$avatar = $user->getAvatarImage('img-polaroid', 120, 120);
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
	</li>
	<?php endif; ?>

	<?php if ($user->exists()) : ?>
	<li>
		<p></p>
		<span class="label label-<?php echo $user->isOnline('success', 'important') ?>">
			<?php echo $user->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?>
		</span>

	</li>

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
<div class="profile-expand center">
	<span class="heading btn btn-small heading-less" style="width:50px;display:none;"><i class="icon-arrow-up-4"></i> <?php echo JText::_('COM_KUNENA_USER_PROFILE_BUTTON_LABEL_LESS') ?></span>
	<span class="heading btn btn-small" style="width:50px;"><i class="icon-arrow-down-4"></i> <?php echo JText::_('COM_KUNENA_USER_PROFILE_BUTTON_LABEL_MORE') ?></span>
	<div class="content" style="display:none;">
		<ul>
			<li>
				<?php echo JText::_('COM_KUNENA_POSTS') . ' ' . (int) $user->posts; ?>
			</li>

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
				<?php  echo $user->profileIcon('birthdate'); ?>
				<?php  echo $user->profileIcon('location'); ?>
				<?php echo $user->profileIcon('website'); ?>
				<?php  echo $user->profileIcon('private'); ?>
				<?php  echo $user->profileIcon('email'); ?>
			</li>
		</ul>
	</div>
</div>
<?php endif; ?>
