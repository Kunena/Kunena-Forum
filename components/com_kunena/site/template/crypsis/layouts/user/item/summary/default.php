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

// @var KunenaUser $profile

$profile = $this->profile;
$me = KunenaUserHelper::getMyself();
$avatar = $profile->getAvatarImage('img-polaroid', 'post');
$banInfo = $this->config->showbannedreason
	? KunenaUserBan::getInstanceByUserid($profile->userid)
	: null;
$private = KunenaFactory::getPrivateMessaging();
$websiteURL = $profile->getWebsiteURL();
$websiteName = $profile->getWebsiteName();
$personalText = $profile->getPersonalText();
$signature = $profile->getSignature();
$email = $profile->email && !$profile->hideEmail && $this->config->showemail || $me->isModerator() || $profile->userid == $me->userid;
$activityIntegration = KunenaFactory::getActivityIntegration();
$points = $activityIntegration->getUserPoints($profile->userid);
$medals = $activityIntegration->getUserMedals($profile->userid);

if ($this->config->showuserstats)
{
	$rankImage = $profile->getRank(0, 'image');
	$rankTitle = $profile->getRank(0, 'title');
}
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<?php if ($avatar) : ?>
			<div class="span2">
				<div class="center"> <?php echo $avatar; ?> </div>
				<div class="center">
					<strong><?php echo $this->subLayout('User/Item/Status')->set('user', $profile); ?></strong>
				</div>
			</div>
			<?php endif; ?>
			<div class="span10">
				<div class="row-fluid">
					<div class="span12">
						<ul class="unstyled span2">
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_USERTYPE'); ?>:</strong>
								<span class="<?php echo $profile->getType(0, true); ?>"> <?php echo JText::_($profile->getType()); ?> </span>
							</li>
							<?php if ($banInfo && $banInfo->reason_public) : ?>
								<li>
									<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>:</strong>
									<span> <?php echo $this->escape($banInfo->reason_public); ?> </span>
								</li>
							<?php endif ?>
							<?php if ($this->config->showuserstats) : ?>
								<li>
									<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_RANK'); ?>:</strong>
									<span>
										<?php /** @var TYPE_NAME $rankTitle */
										echo $this->escape($rankTitle); ?>
										<?php /** @var TYPE_NAME $rankImage */
										echo $rankImage; ?>
									</span>
								</li>
							<?php endif; ?>
						</ul>
						<ul class="unstyled span3">
							<?php if ($this->config->userlist_joindate || $me->isModerator()) : ?>
								<li>
									<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong>
									<span title="<?php echo $profile->getRegisterDate()->toKunena('ago'); ?>"> <?php echo $profile->getRegisterDate()->toKunena('date_today', 'utc'); ?> </span>
								</li>
							<?php endif; ?>
							<?php if ($this->config->userlist_lastvisitdate || $me->isModerator()) : ?>
								<li>
									<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_LASTLOGIN'); ?>:</strong>
									<span title="<?php echo $profile->getLastVisitDate()->toKunena('ago'); ?>"> <?php echo $profile->getLastVisitDate()->toKunena('config_post_dateformat'); ?> </span>
								</li>
							<?php endif; ?>
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong>
								<span> UTC <?php echo $profile->getTime()->toTimezone(); ?> </span>
							</li>
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong>
								<span> <?php echo $profile->getTime()->toKunena('time'); ?> </span>
							</li>
						</ul>
						<ul class="unstyled span2">
							<?php if (!empty($profile->posts)) : ?>
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_POSTS'); ?> </strong>
								<span> <?php echo JText::sprintf((int) $profile->posts); ?> </span>
							</li>
							<?php endif; ?>
							<?php if (!empty($profile->uhits)) : ?>
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_PROFILE_VIEWS'); ?>:</strong>
								<span> <?php echo JText::sprintf((int) $profile->uhits); ?> </span>
							</li>
							<?php endif; ?>
							<?php if (!empty($profile->thankyou)) : ?>
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_THANK_YOU_RECEIVED'); ?>:</strong>
								<span> <?php echo JText::sprintf((int) $profile->thankyou); ?> </span>
							</li>
							<?php endif; ?>
							<?php if (!empty($points)) : ?>
								<li>
									<strong> <?php echo JText::_('COM_KUNENA_AUP_POINTS');?></strong>
									<span> <?php echo  $points; ?></span>
								</li>
							<?php endif; ?>
						</ul>
						<ul class="unstyled span3">
							<?php if (!empty($profile->location)) : ?>
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?>:</strong>
								<span>
									<?php if ($profile->location) : ?>
										<a href="https://maps.google.com?q=<?php echo $this->escape($profile->location); ?>"
										   target="_blank"><?php echo $this->escape($profile->location); ?></a>
									<?php else : ?>
										<?php echo JText::_('COM_KUNENA_LOCATION_UNKNOWN'); ?>
									<?php endif; ?>
								</span>
							</li>
							<?php endif; ?>
							<?php if ($profile->getGender() >= 1) : ?>
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong>
								<span> <?php echo $profile->getGender(); ?> </span>
							</li>
							<?php endif; ?>
							<?php if ($profile->birthdate >= '1901-01-01') : ?>
							<li>
								<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong>
								<span> <?php echo KunenaDate::getInstance($profile->birthdate)->toSpan('date', 'ago', 'utc'); ?> </span>
							</li>
							<?php endif; ?>
							<?php if (!empty($medals)) : ?>
								<li>
									<strong> <?php echo JText::_('COM_KUNENA_AUP_MEDALS'); ?> </strong>
									<span> <?php echo implode(' ', $medals); ?> </span>
								</li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="span12">
						<div class="span9">
							<?php echo $this->subLayout('User/Item/Social')->set('profile', $profile); ?>
						</div>
						<div class="span3 pull-right">
							<?php if ($private) : ?>
								<?php echo $private->shownewIcon($profile->userid); ?>
							<?php endif; ?>
							<?php if ($email) : ?>
								<a class="btn btn-small" href="mailto:<?php echo $profile->email; ?>"><i class="icon-mail"></i></a>
							<?php endif; ?>
							<?php if (!empty($websiteName) && $websiteURL != 'http://') : ?>
								<a class="btn btn-small" href="<?php echo $websiteURL ?>"><i class="icon-bookmark"></i> <?php echo $websiteName ?></a>
							<?php elseif(empty($websiteName) && $websiteURL != 'http://') : ?>
								<a class="btn btn-small" href="<?php echo $websiteURL ?>"><i class="icon-bookmark"></i> <?php echo $websiteURL ?></a>
							<?php elseif(!empty($websiteName) && $websiteURL == 'http://') : ?>
								<button class="btn btn-small"><i class="icon-bookmark"></i> <?php echo $websiteName ?></button>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br />
<div class="span12">
	<?php if ($signature) : ?>
	<blockquote>
		<span><?php echo $signature; ?></span>
	</blockquote>
	<?php endif; ?>
	<?php if ($personalText) : ?>
		<blockquote>
			<span> <?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?>: </span>
			<br />
			<span> <?php echo $personalText; ?> </span>
		</blockquote>
	<?php endif; ?>
</div>
