<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaUser $profile */
$profile = $this->profile;
$me = KunenaUserHelper::getMyself();
$avatar = $profile->getAvatarImage('img-rounded', 128, 128);
$banInfo = $this->config->showbannedreason
	? KunenaUserBan::getInstanceByUserid($profile->userid)
	: null;
$private = $profile->getPrivateMsgLink();
$email = $profile->getEmailLink();
$www = $profile->getWebsiteLink();
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
      <div class="span12">
        <?php if ($avatar) : ?>
        <div class="span2">
          <div class="center"> <?php echo $avatar; ?> </div>
          <div class="center"> </br>
            <sup class="label label-<?php echo $this->profile->isOnline('success', 'important') ?>"> <?php echo $this->profile->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?> </sup> </div>
        </div>
        <?php endif; ?>
        <div class="span2">
          <dl class="dl-horizontal">
            <dt> <?php echo JText::_('COM_KUNENA_USERTYPE'); ?>: </dt>
            <dd class="<?php echo $profile->getType(0, true); ?>"> <?php echo JText::_($profile->getType()); ?> </dd>
            <?php if ($banInfo && $banInfo->reason_public) : ?>
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>: </dt>
            <dd> <?php echo $this->escape($banInfo->reason_public); ?> </dd>
            <?php endif ?>
            <?php if ($this->config->showuserstats) : ?>
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_RANK'); ?>: </dt>
            <dd>
							<div><?php echo $this->escape($rankTitle); ?></div>
              <div><?php echo $rankImage; ?></div>
            </dd>
            <?php endif; ?>
          </dl>
        </div>
        <div class="span2">
          <dl class="dl-horizontal">
            <?php if ($this->config->userlist_joindate || $me->isModerator()) : ?>
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>: </dt>
            <dd title="<?php echo $profile->getRegisterDate()->toKunena('ago'); ?>"> <?php echo $profile->getRegisterDate()->toKunena('date_today', 'utc'); ?> </dd>
            <?php endif; ?>
            <?php if ($this->config->userlist_lastvisitdate || $me->isModerator()) : ?>
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>: </dt>
            <dd title="<?php echo $profile->getLastVisitDate()->toKunena('ago'); ?>"> <?php echo $profile->getLastVisitDate()->toKunena('date_today', 'utc'); ?> </dd>
            <?php endif; ?>
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>: </dt>
            <dd> UTC <?php echo $profile->getTime()->toTimezone(); ?> </dd>
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>: </dt>
            <dd> <?php echo $profile->getTime()->toKunena('time'); ?> </dd>
          </dl>
        </div>
        <div class="span2">
          <dl class="dl-horizontal">
            <dt> <?php echo JText::_('COM_KUNENA_POSTS'); ?> </dt>
            <dd> <?php echo JText::sprintf((int) $profile->posts); ?> </dd>
            <dt> <?php echo JText::_('Profile views'); ?>: </dt>
            <dd> <?php echo JText::sprintf((int) $profile->uhits); ?> </dd>
            <dt> <?php echo JText::_('Thank you received'); ?>: </dt>
            <dd> <?php echo JText::sprintf((int) $profile->thankyou); ?> </dd>
            <?php if (isset($profile->points)) : ?>
            <dt> <?php echo JText::_('COM_KUNENA_AUP_POINTS'); ?> </dt>
            <dd> <?php echo (int) $profile->points; ?> </dd>
            <?php endif; ?>
          </dl>
        </div>
        <div class="span2">
          <dl class="dl-horizontal">
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?>: </dt>
            <dd>
              <?php if ($profile->location) : ?>
              <a href="http://maps.google.com?q=<?php echo $this->escape($profile->location); ?>"
                       target="_blank"><?php echo $this->escape($profile->location); ?></a>
              <?php else : ?>
              <?php echo JText::_('COM_KUNENA_LOCATION_UNKNOWN'); ?>
              <?php endif; ?>
            </dd>
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>: </dt>
            <dd> <?php echo $profile->getGender(); ?> </dd>
            <dt> <?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>: </dt>
            <dd> <?php echo KunenaDate::getInstance($profile->birthdate)->toSpan('date', 'ago', 'utc'); ?> </dd>
            <?php if (!empty($profile->medals)) : ?>
            <dt> <?php echo JText::_('COM_KUNENA_AUP_MEDALS'); ?> </dt>
            <dd> <?php echo implode(' ', $profile->medals); ?> </dd>
            <?php endif; ?>
          </dl>
        </div>
      </div>
    </div>
  </div>
  <div class="span11">
    <div class="span6"> </br>
      <blockquote>
        <?php if ($signature) : ?>
        <span><?php echo $signature; ?></span>
        <?php endif; ?>
      </blockquote>
      <blockquote>
        <?php if ($personalText) : ?>
        <span> <?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?> </span>
        <span> <?php echo $personalText; ?> </span>
        <?php endif; ?>
      </blockquote>
      <div>
        <?php if (!empty($private)) : ?>
        <?php // TODO: Fix mailto link ?>
        <a class="btn" href="<?php echo $private; ?>"><i class="icon-comments-2"></i></a>
        <?php endif; ?>
        <?php if ($email) : ?>
        <?php // TODO: Fix mailto link ?>
        <a class="btn" href="mailto:<?php echo $email; ?>"><i class="icon-mail"></i></a>
        <?php endif; ?>
        <?php if ($www) : ?>
        <?php // TODO: Fix link ?>
        <a class="btn" href="<?php echo $www->url; ?>"><i class="icon-bookmark"></i></a>
        <?php endif; ?>
      </div>
    </div>
    <div class="span6">
      <div class="well"> <?php echo $this->subLayout('User/Item/Social')->set('profile', $profile)->set('showAll', true); ?> </div>
    </div>
  </div>
</div>
