<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$user              = $this->user;
$avatar            = $user->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'thumb');
$config            = KunenaConfig::getInstance();
$show              = $config->showuserstats;
$optional_username = KunenaFactory::getTemplate()->params->get('optional_username');

$canseekarma = false;
if ($config->showkarma)
{
	$canseekarma = $user->canSeeKarma();
}

if ($show)
{
	$rankImage    = $user->getRank($this->category_id, 'image');
	$rankTitle    = $user->getRank($this->category_id, 'title');
	$personalText = $user->getPersonalText();
}
?>

<div class="span2">
	<ul class="profilebox center">
		<li>
			<strong><?php echo $user->getLink(null, null, '', '', null, $this->category_id); ?></strong>
		</li>
		<?php if ($optional_username) : ?>
			<li>
				[<?php echo $user->getLinkNoStyle('', '', 'kpost-username-optional') ?>]
			</li>
		<?php endif; ?>
		<?php if ($user->exists() && $config->user_status) : ?>
			<li>
				<?php echo $user->getLink($avatar, null, '', '', null, 0, KunenaConfig::getInstance()->avataredit); ?>
				<?php
				if (isset($this->topic_starter) && $this->topic_starter)
					:
					?>
					<span class="hidden-phone topic-starter <?php if (KunenaFactory::getTemplate()->params->get('avatarType') == 'img-circle')
					{
						echo 'topic-starter-circle';
					} ?>"><?php echo Text::_('COM_KUNENA_TOPIC_AUTHOR') ?></span>
				<?php endif;
				?>
				<?php // If (!$this->topic_starter && $user->isModerator()) :
				?><!--
					<span class="<?php // If (KunenaFactory::getTemplate()->params->get('avatarType') == 'img-circle') {echo 'topic-moderator-circle';};
				?> topic-moderator"><?php // echo Text::_('COM_KUNENA_TEAM_MEMBER')
				?></span>
				--><?php // Endif;
				?>
			</li>
		<?php endif; ?>
		<?php
		if ($user->exists())
			:
			?>
			<li>
				<?php echo $this->subLayout('User/Item/Status')->set('user', $user); ?>
			</li>
		<?php endif; ?>
	</ul>
</div>
<div class="span2">
	<br>
	<?php if (!empty($rankTitle))
		:
		?>
		<li>
			<?php echo $this->escape($rankTitle); ?>
		</li>
	<?php endif; ?>

	<?php if (!empty($rankImage))
		:
		?>
		<li>
			<?php echo $rankImage; ?>
		</li>
	<?php endif; ?>

	<?php if (!empty($personalText))
		:
		?>
		<li>
			<?php echo $personalText; ?>
		</li>
	<?php endif; ?>
</div>

<div class="span2">
	<br>
	<?php if ($user->posts >= 1)
		:
		?>
		<li>
			<strong> <?php echo Text::_('COM_KUNENA_POSTS'); ?> </strong>
			<span> <?php echo Text::sprintf((int) $user->posts); ?> </span>
		</li>
	<?php endif; ?>

	<?php if ($canseekarma && $config->showkarma) :
		if ($user->karma > 0) :
			$karmanumber = $user->karma;
		else :
			$karmanumber = '';
		endif;?>
		<li>
			<strong> <?php echo Text::_('COM_KUNENA_KARMA'); ?>: <?php echo $karmanumber; ?></strong>
			<span> <?php $this->subLayout('Widget/Karma')->set('topicicontype', KunenaFactory::getTemplate()->params->get('topicicontype'))->set('userid', $user->userid)->set('karmatype', 'karmadown') . $this->subLayout('Widget/Karma')->set('topicicontype', KunenaFactory::getTemplate()->params->get('topicicontype'))->set('userid', $user->userid)->set('karmatype', 'karmadown'); ?> </span>
		</li>
	<?php endif; ?>

	<?php if ($show && isset($user->thankyou) && $config->showthankyou) : ?>
		<li>
			<strong> <?php echo Text::_('COM_KUNENA_THANK_YOU_RECEIVED'); ?>:</strong>
			<span> <?php echo Text::sprintf((int) $user->thankyou); ?> </span>
		</li>
	<?php endif; ?>
	<?php
	if (isset($user->points))
		:
		?>
		<li>
			<strong> <?php echo Text::_('COM_KUNENA_AUP_POINTS'); ?> </strong>
			<span> <?php echo (int) $user->points; ?> </span>
		</li>
	<?php endif; ?>
	<?php
	if ($show && !empty($user->medals))
		:
		?>
		<li>
			<?php echo implode(' ', $user->medals); ?>
		</li>
	<?php endif; ?>
</div>
<div class="span3">
	<br>
	<li>
		<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong>
		<span> <?php echo $user->getGender(); ?> </span>
	</li>
	<li>
		<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong>
		<span> <?php echo KunenaDate::getInstance($user->birthdate)->toSpan('date', 'ago', 'utc'); ?> </span>
	</li>
	<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_profile_horizontal'); ?>
	<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_profile_horizontal_' . $user->userid); ?>
	<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_profile_horizontal_' . $user->rank); ?>
</div>
