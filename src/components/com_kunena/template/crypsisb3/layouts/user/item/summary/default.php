<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
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
	$showKarma = KunenaConfig::getInstance()->showkarma;
	$rankImage = $this->profile->getRank(0, 'image');
	$rankTitle = $this->profile->getRank(0, 'title');
}
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php if ($avatar) : ?>
				<div class="col-md-2">
					<div class="center kwho-<?php echo $this->profile->getType(0, true); ?>">
						<?php echo $this->profile->getLink($avatar, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->profile->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), null, KunenaConfig::getInstance()->avataredit); ?>
					</div>
					<?php if ($this->config->user_status): ?>
						<div class="center">
							<strong><?php echo $this->subLayout('User/Item/Status')->set('user', $this->profile); ?></strong>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="col-md-10">
				<div class="row">
					<div class="col-md-12">
						<ul class="list-unstyled col-md-3">
							<li>
								<strong> <?php echo Text::_('COM_KUNENA_USERTYPE'); ?>:</strong>
								<span class="<?php echo $this->profile->getType(0, true); ?>"> <?php echo Text::_($this->profile->getType()); ?> </span>
							</li>
							<?php if ($banInfo && $banInfo->reason_public) : ?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>:</strong>
									<span> <?php echo $this->escape($banInfo->reason_public); ?> </span>
								</li>
							<?php endif ?>
							<?php if ($this->config->showuserstats && $this->config->showranking) : ?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_RANK'); ?>:</strong>
									<span>
										<?php echo $this->escape($rankTitle); ?>
										<?php echo $rankImage; ?>
									</span>
								</li>
							<?php endif; ?>
						</ul>
						<ul class="list-unstyled col-md-3">
							<?php if ($this->config->userlist_joindate || $me->isModerator()) : ?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong>
									<span
											title="<?php echo $this->profile->getRegisterDate()->toKunena('ago'); ?>"> <?php echo $this->profile->getRegisterDate()->toKunena('date_today', 'utc'); ?> </span>
								</li>
							<?php endif; ?>
							<?php
							if ($this->config->userlist_lastvisitdate || $me->isModerator())
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_LASTLOGIN'); ?>:</strong>
									<span
											title="<?php echo $this->profile->getLastVisitDate()->toKunena('ago'); ?>"> <?php echo $this->profile->getLastVisitDate()->toKunena('config_post_dateformat'); ?> </span>
								</li>
							<?php endif; ?>
							<li>
								<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong>
								<span> UTC <?php echo $this->profile->getTime()->toTimezone(); ?> </span>
							</li>
							<li>
								<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong>
								<span> <?php echo $this->profile->getTime()->toKunena('time'); ?> </span>
							</li>
						</ul>
						<ul class="list-unstyled col-md-3">
							<?php if (!empty($this->profile->posts))
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_POSTS'); ?> </strong>
									<span> <?php echo Text::sprintf((int) $this->profile->posts); ?> </span>
								</li>
							<?php endif; ?>
							<?php
							if (!empty($showKarma) && !empty($this->profile->karma) && KunenaConfig::getInstance()->showkarma)
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_KARMA'); ?>:</strong>
									<span> <?php echo Text::sprintf((int) $this->profile->karma); ?> </span>
								</li>
							<?php endif; ?>
							<?php
							if (!empty($this->profile->uhits))
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_PROFILE_VIEWS'); ?>:</strong>
									<span> <?php echo Text::sprintf((int) $this->profile->uhits); ?> </span>
								</li>
							<?php endif; ?>
							<?php
							if (!empty($this->profile->thankyou) && KunenaConfig::getInstance()->showthankyou)
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_THANK_YOU_RECEIVED'); ?>:</strong>
									<span> <?php echo Text::sprintf((int) $this->profile->thankyou); ?> </span>
								</li>
							<?php endif; ?>
							<?php
							if (!empty($points))
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_AUP_POINTS'); ?></strong>
									<span> <?php echo $points; ?></span>
								</li>
							<?php endif; ?>
						</ul>
						<ul class="list-unstyled col-md-3">
							<?php if (!empty($this->profile->location))
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_LOCATION') ?>:</strong>
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
							<?php
							if ($this->profile->getGender() >= 1)
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong>
									<span> <?php echo $this->profile->getGender(); ?> </span>
								</li>
							<?php endif; ?>
							<?php
							if ($this->profile->birthdate >= '1901-01-01')
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong>
									<span> <?php echo KunenaDate::getInstance($this->profile->birthdate)->toSpan('date', 'ago', 'utc'); ?> </span>
								</li>
							<?php endif; ?>
							<?php
							if (!empty($medals))
								:
								?>
								<li>
									<strong> <?php echo Text::_('COM_KUNENA_AUP_MEDALS'); ?> </strong>
									<span> <?php echo implode(' ', $medals); ?> </span>
								</li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="col-md-12">
						<div class="col-md-9">
							<?php echo $this->subLayout('User/Item/Social')->set('profile', $this->profile)->set('socials', $socials); ?>
						</div>
						<div class="col-md-3 pull-right">
							<?php if ($private)
								:
								?>
								<?php echo $private->shownewIcon($this->profile->userid, 'btn btn-default btn-sm', 'glyphicon glyphicon-comment'); ?>
							<?php endif; ?>
							<?php if ($this->candisplaymail) : ?>
                                    <a class="btn btn-default btn-sm" href="mailto:<?php echo $this->profile->email; ?>"
                                       rel="nofollow"><?php echo KunenaIcons::email(); ?></a>
							<?php endif; ?>
							<?php
							if (!empty($websiteName) && !empty($websiteURL))
							:
							?>
							<a class="btn btn-default btn-sm" rel="nofollow noopener noreferrer" target="_blank"
							   href="<?php echo $websiteURL ?>"><?php echo KunenaIcons::globe() . ' ' . $websiteName; ?></a>
							<?php elseif (empty($websiteName) && !empty($websiteURL))
							:
							?>
							<a class="btn btn-default btn-sm" rel="nofollow noopener noreferrer" target="_blank"
							   href="<?php echo $websiteURL ?>"><?php echo KunenaIcons::globe(); ?></a>
							<?php elseif (!empty($websiteName) && empty($websiteURL))
							:
							?>
							<button class="btn btn-default btn-sm"><?php echo KunenaIcons::globe() . ' ' . $websiteName; ?></button>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br/>
<div class="col-md-12">
	<?php if ($signature)
		:
		?>
		<blockquote>
			<span><?php echo $signature; ?></span>
		</blockquote>
	<?php endif; ?>
	<?php
	if ($personalText)
		:
		?>
		<blockquote>
			<span> <?php echo Text::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?>: </span>
			<br/>
			<span> <?php echo $personalText; ?> </span>
		</blockquote>
	<?php endif; ?>
</div>
