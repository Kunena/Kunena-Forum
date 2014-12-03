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

/** @var KunenaUser $profile */
$profile = $this->profile;
$me = KunenaUserHelper::getMyself();
$avatar = $profile->getAvatarImage('img-polaroid', 128, 128);
$banInfo = $this->config->showbannedreason
	? KunenaUserBan::getInstanceByUserid($profile->userid)
	: null;
$private = $profile->getPrivateMsgURL();
$privateLabel = $profile->getPrivateMsgLabel();
$websiteURL = $profile->getWebsiteURL();
$websiteName = $profile->getWebsiteName();
$personalText = $profile->getPersonalText();
$signature = $profile->getSignature();

if ($this->config->showuserstats)
{
	$rankImage = $profile->getRank(0, 'image');
	$rankTitle = $profile->getRank(0, 'title');
}
?>

<div class="row-fluid">
	<div class="span12">
		<div class="row-fluid">
			<?php if ($avatar) : ?>
				<div class="span2">
					<div class="center"> <?php echo $avatar; ?> </div>
					<div class="center"> </br>
						<sup class="label label-<?php echo $this->profile->isOnline('success', 'important') ?>"> <?php echo $this->profile->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?> </sup></div>
				</div>
			<?php endif; ?>
			<br />
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
							<?php echo $this->escape($rankTitle); ?>
							<?php echo $rankImage; ?>
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
						<span title="<?php echo $profile->getLastVisitDate()->toKunena('ago'); ?>"> <?php echo $profile->getLastVisitDate()->toKunena('config_post_dateformat', 'ago'); ?> </span>
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
				<li>
					<strong> <?php echo JText::_('COM_KUNENA_POSTS'); ?> </strong>
					<span> <?php echo JText::sprintf((int)$profile->posts); ?> </span>
				</li>
				<li>
					<strong> <?php echo JText::_('COM_KUNENA_PROFILE_VIEWS'); ?>:</strong>
					<span> <?php echo JText::sprintf((int)$profile->uhits); ?> </span>
				</li>
				<li>
					<strong> <?php echo JText::_('COM_KUNENA_THANK_YOU_RECEIVED'); ?>:</strong>
					<span> <?php echo JText::sprintf((int)$profile->thankyou); ?> </span>
				</li>
				<?php if (isset($profile->points)) : ?>
					<li>
						<strong> <?php echo JText::_('COM_KUNENA_AUP_POINTS'); ?> </strong>
						<span> <?php echo (int)$profile->points; ?> </span>
					</li>
				<?php endif; ?>
			</ul>
			<ul class="unstyled span3">
				<li>
					<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?>:</strong>
				<span>
					<?php if ($profile->location) : ?>
						<a href="http://maps.google.com?q=<?php echo $this->escape($profile->location); ?>"
						   target="_blank"><?php echo $this->escape($profile->location); ?></a>
					<?php else : ?>
						<?php echo JText::_('COM_KUNENA_LOCATION_UNKNOWN'); ?>
					<?php endif; ?>
				</span>
				</li>
				<li>
					<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong>
					<span> <?php echo $profile->getGender(); ?> </span>
				</li>
				<li>
					<strong> <?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong>
					<span> <?php echo KunenaDate::getInstance($profile->birthdate)->toSpan('date', 'ago', 'utc'); ?> </span>
				</li>
				<?php if (!empty($profile->medals)) : ?>
					<li>
						<strong> <?php echo JText::_('COM_KUNENA_AUP_MEDALS'); ?> </strong>
						<span> <?php echo implode(' ', $profile->medals); ?> </span>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
<br />
<div class="row-fluid">
	<div class="span12">
		<div class="span6">
			<?php if ($signature) : ?>
				<blockquote>
					<span><?php echo $signature; ?></span>
				</blockquote>
			<?php endif; ?>
			<?php if ($personalText) : ?>
				<blockquote>
					<span> <?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?> </span>
					<span> <?php echo $personalText; ?> </span>
				</blockquote>
			<?php endif; ?>
			<div class="btn-toolbar">
				<?php if (!empty($private)) : ?>
					<a class="btn btn-small" href="<?php echo $private; ?>">
						<i class="icon-comments-2"></i>
						<?php echo $privateLabel ?>
					</a>
				<?php endif; ?>
				<?php if ($profile->email) : ?>
					<a class="btn btn-small" href="mailto:<?php echo $profile->email; ?>"><i class="icon-mail"></i></a>
				<?php endif; ?>
				<?php if ($websiteName) : ?>
					<a class="btn btn-small" href="<?php echo $websiteURL ?>"><i class="icon-bookmark"></i> <?php echo $websiteName ?></a>
				<?php endif; ?>
			</div>
		</div>
		<div class="span6">
			<div class="well"> <?php echo $this->subLayout('User/Item/Social')->set('profile', $profile)->set('showAll', true); ?> </div>
		</div>
	</div>
</div>
