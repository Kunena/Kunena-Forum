<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

$socials             = $this->profile->socialButtons();
$socials             = ArrayHelper::toObject($socials);
$me                  = KunenaUserHelper::getMyself();
$avatar              = $this->profile->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'post');
$banInfo             = $this->config->showbannedreason
? KunenaUserBan::getInstanceByUserid($this->profile->userid)
: null;
$private             = KunenaFactory::getPrivateMessaging();
$websiteURL          = $this->profile->getWebsiteURL();
$websiteName         = $this->profile->getWebsiteName();
$personalText        = $this->profile->getPersonalText();
$signature           = $this->profile->getSignature();
$activityIntegration = KunenaFactory::getActivityIntegration();
$points              = $activityIntegration->getUserPoints($this->profile->userid);
$medals              = $activityIntegration->getUserMedals($this->profile->userid);

if ($this->config->showuserstats)
{
	$rankImage = $this->profile->getRank(0, 'image');
	$rankTitle = $this->profile->getRank(0, 'title');
}
?>
<div class="clearfix"></div>
<div class="row">
	<div class="col-md-3">
		<div class="center kwho-<?php echo $this->profile->getType(0, true); ?>">
			<?php echo $this->profile->getLink($avatar, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->profile->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), null, $this->config->avataredit); ?>
		</div>

		<?php if ($this->config->user_status)
		:
			?>
			<div class="center">
				<strong><?php echo $this->subLayout('User/Item/Status')->set('user', $this->profile); ?></strong>
			</div>
		<?php endif; ?>

		<ul class="list-group list-group-flush">
			<li class="list-group-item">
				<strong><?php echo Text::_('COM_KUNENA_USERTYPE'); ?>:</strong>
				<span class="<?php echo $this->profile->getType(0, true); ?>"><?php echo Text::_($this->profile->getType()); ?></span>
			</li>

			<?php if ($banInfo && $banInfo->reason_public)
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>:</strong>
					<span><?php echo $this->escape($this->banInfo->reason_public); ?></span>
				</li>
			<?php endif ?>

			<?php if ($this->config->showuserstats && $this->config->showranking)
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_RANK'); ?>:</strong>
					<span>
							<?php echo $this->escape($rankTitle); ?>
							<?php echo $rankImage; ?>
						</span>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="col-md-3">
		<ul class="list-group list-group-flush">
			<?php if ($this->config->userlist_joindate || $this->me->isModerator())
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong>
					<span
							title="<?php echo $this->profile->getRegisterDate()->toKunena('ago'); ?>"><?php echo $this->profile->getRegisterDate()->toKunena('date_today', 'utc'); ?></span>
				</li>
			<?php endif; ?>

			<?php if ($this->config->userlist_lastvisitdate || $this->me->isModerator())
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_LASTLOGIN'); ?>:</strong>
					<span
							title="<?php echo $this->profile->getLastVisitDate()->toKunena('ago'); ?>"><?php echo $this->profile->getLastVisitDate()->toKunena('config_post_dateformat'); ?></span>
				</li>
			<?php endif; ?>

			<li class="list-group-item">
				<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong>
				<span> UTC <?php echo $this->profile->getTime()->toTimezone(); ?></span>
			</li>
			<li class="list-group-item">
				<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong>
				<span><?php echo $this->profile->getTime()->toKunena('time'); ?></span>
			</li>
		</ul>
	</div>
	<div class="col-md-3">
		<ul class="list-group list-group-flush">
			<?php if (!empty($this->profile->posts))
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_POSTS'); ?></strong>
					<span><?php echo Text::sprintf((int) $this->profile->posts); ?></span>
				</li>
			<?php endif; ?>

			<?php if ($this->config->showuserstats && !empty($this->profile->karma) && $this->config->showkarma)
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_KARMA'); ?>:</strong>
					<span><?php echo Text::sprintf((int) $this->profile->karma); ?></span>
				</li>
			<?php endif; ?>

			<?php if (!empty($this->profile->uhits))
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_PROFILE_VIEWS'); ?>:</strong>
					<span><?php echo Text::sprintf((int) $this->profile->uhits); ?></span>
				</li>
			<?php endif; ?>

			<?php if (!empty($this->profile->thankyou) && $this->config->showthankyou)
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_THANK_YOU_RECEIVED'); ?>:</strong>
					<span><?php echo Text::sprintf((int) $this->profile->thankyou); ?></span>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="col-md-3">
		<ul class="list-group list-group-flush">
			<?php if (!empty($this->points))
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_AUP_POINTS'); ?></strong>
					<span><?php echo $this->points; ?></span>
				</li>
			<?php endif; ?>

			<?php if (!empty($this->profile->location))
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_LOCATION') ?>:</strong>
					<span>
						<?php if ($this->profile->location)
						:
							?>
							<a href="https://maps.google.com?q=<?php echo $this->escape($this->profile->location); ?>"
							   target="_blank"
							   rel="nofollow noopener noreferrer"><?php echo $this->escape($this->profile->location); ?></a>
						<?php else

						:
							?>
							<?php echo Text::_('COM_KUNENA_LOCATION_UNKNOWN'); ?>
						<?php endif; ?>
					</span>
				</li>
			<?php endif; ?>

			<?php if ($this->profile->getGender())
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong>
					<span><?php echo $this->profile->getGender(); ?></span>
				</li>
			<?php endif; ?>

			<?php if ($this->profile->birthdate)
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong>
					<span><?php echo KunenaDate::getInstance($this->profile->birthdate)->toSpan('date', 'ago', 'utc'); ?></span>
				</li>
			<?php endif; ?>

			<?php if (!empty($this->medals))
			:
				?>
				<li class="list-group-item">
					<strong><?php echo Text::_('COM_KUNENA_AUP_MEDALS'); ?> </strong>
					<span><?php echo implode(' ', $this->medals); ?></span>
				</li>
			<?php endif; ?>
		</ul>
		<br>
		<div class="float-right">
			<?php
			if ($this->candisplaymail)
			:
				?>
				<a class="btn btn-outline-primary border btn-sm" href="mailto:<?php echo $this->profile->email; ?>"
				   rel="nofollow"><?php echo KunenaIcons::email(); ?></a>
			<?php endif; ?>
			<?php
			if (!empty($this->profile->getWebsiteName()) && !empty($this->profile->getWebsiteURL()))
			:
				?>
				<a class="btn btn-outline-primary border btn-sm" rel="nofollow noopener noreferrer" target="_blank"
				   href="<?php echo $this->profile->getWebsiteURL() ?>"><?php echo KunenaIcons::globe() . ' ' . $this->profile->getWebsiteName(); ?></a>
			<?php elseif (empty($this->profile->getWebsiteName()) && !empty($this->profile->getWebsiteURL()))
			:
				?>
				<a class="btn btn-outline-primary border btn-sm" rel="nofollow noopener noreferrer" target="_blank"
				   href="<?php echo $this->profile->getWebsiteURL() ?>"><?php echo KunenaIcons::globe(); ?></a>
			<?php elseif (!empty($this->profile->getWebsiteName()) && empty($this->profile->getWebsiteURL()))
			:
				?>
				<button class="btn btn-outline-primary border btn-sm"><?php echo KunenaIcons::globe() . ' ' . $this->profile->getWebsiteName(); ?></button>
			<?php endif; ?>
		</div>
	</div>

	<div class="col-md-9">
		<?php echo $this->subLayout('User/Item/Social')->set('profile', $this->profile)->set('socials', $socials); ?>
	</div>
</div>

<br/>
<div class="col-md-12">
	<?php if ($this->profile->getSignature())
	:
		?>
		<blockquote>
			<span><?php echo $this->profile->getSignature(); ?></span>
		</blockquote>
	<?php endif; ?>
	<?php
	if ($this->profile->getPersonalText())
	:
		?>
		<blockquote>
			<span><?php echo Text::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?>: </span>
			<br/>
			<span><?php echo $this->profile->getPersonalText(); ?></span>
		</blockquote>
	<?php endif; ?>
</div>
