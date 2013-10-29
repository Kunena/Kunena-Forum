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
$avatar = $profile->getAvatarImage('img-rounded', 200, 200);
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

	<?php if ($avatar) : ?>
	<div class="span3 center">
		<div class="thumbnail" style="width: 200px; height: 200px;">
			<?php echo $avatar; ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="span4">
		<div class="badge badge-success">
			<?php echo JText::sprintf('COM_KUNENA_X_VIEWS', (int) $profile->uhits); ?>
		</div>
		<div class="badge badge-info">
			<?php echo JText::sprintf('COM_KUNENA_X_POSTS', (int) $profile->posts); ?>
		</div>
		<div class="badge badge-warning">
			<?php echo JText::sprintf('COM_KUNENA_X_THANKS', (int) $profile->thankyou); ?>
		</div>

		<dl class="dl-horizontal">

			<dt>
				<?php echo JText::_('COM_KUNENA_USERTYPE'); ?>
			</dt>
			<dd class="<?php echo $profile->getType(0, true); ?>">
				<?php echo JText::_($profile->getType()); ?>
			</dd>

			<?php if ($banInfo && $banInfo->reason_public) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>
			</dt>
			<dd>
				<?php echo $this->escape($banInfo->reason_public); ?>
			</dd>
			<?php endif ?>

			<?php if ($this->config->showuserstats) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_RANK'); ?>
			</dt>
			<dd>
				<div><?php echo $rankImage; ?></div>
				<div><?php echo $this->escape($rankTitle); ?></div>
			</dd>
			<?php endif; ?>

			<?php if ($this->config->userlist_joindate || $me->isModerator()) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>
			</dt>
			<dd title="<?php echo $profile->getRegisterDate()->toKunena('ago'); ?>">
				<?php echo $profile->getRegisterDate()->toKunena('date_today', 'utc'); ?>
			</dd>
			<?php endif; ?>

			<?php if ($this->config->userlist_lastvisitdate || $me->isModerator()) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>
			</dt>
			<dd title="<?php echo $profile->getLastVisitDate()->toKunena('ago'); ?>">
				<?php echo $profile->getLastVisitDate()->toKunena('date_today', 'utc'); ?>
			</dd>
			<?php endif; ?>

			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>
			</dt>
			<dd>
				UTC <?php echo $profile->getTime()->toTimezone(); ?>
			</dd>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>
			</dt>
			<dd>
				<?php echo $profile->getTime()->toKunena('time'); ?>
			</dd>

			<?php if (isset($profile->points)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_AUP_POINTS'); ?>
			</dt>
			<dd>
				<?php echo (int) $profile->points; ?>
			</dd>
			<?php endif; ?>

			<?php if (!empty($profile->medals)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_AUP_MEDALS'); ?>
			</dt>
			<dd>
				<?php echo implode(' ', $profile->medals); ?>
			</dd>
			<?php endif; ?>

			<?php if (!empty($private)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_SEND_MESSAGE'); ?>
			</dt>
			<dd>
				<?php echo $private; ?>
			</dd>
			<?php endif ?>

			<?php if ($email) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_EMAIL'); ?>
			</dt>
			<dd>
				<?php echo $email; ?>
			</dd>
			<?php endif; ?>


			<?php if ($www) : ?>
				<dt>
					<?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE'); ?>
				</dt>
				<dd>
					<?php echo $www; ?>
				</dd>
			<?php endif; ?>

			<?php if ($personalText) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?>
			</dt>
			<dd>
				<?php echo $personalText; ?>
			</dd>
			<?php endif; ?>

			<?php if ($signature) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?>
			</dt>
			<dd>
				<?php echo $signature; ?>
			</dd>
			<?php endif; ?>

			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?>
			</dt>
			<dd>
				<?php if ($profile->location) : ?>
				<a href="http://maps.google.com?q=<?php echo $this->escape($profile->location); ?>"
				   target="_blank"><?php echo $this->escape($profile->location); ?></a>
				<?php else : ?>
				<?php echo JText::_('COM_KUNENA_LOCATION_UNKNOWN'); ?>
				<?php endif; ?>
			</dd>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>
			</dt>
			<dd>
				<?php echo $profile->getGender(); ?>
			</dd>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>
			</dt>
			<dd>
				<?php echo KunenaDate::getInstance($profile->birthdate)->toSpan('date', 'ago', 'utc'); ?>
			</dd>
		</dl>
	</div>

	<div class="span4">
		<?php echo $this->subLayout('User/Item/Social')->set('profile', $profile)->set('showAll', true); ?>
	</div>
</div>
<div class="clearfix"></div>
