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

// FIXME: add missing fields...

/** @var KunenaUser $profile */
$profile = $this->profile;

if ($this->config->showuserstats) {
	$rankImage = $profile->getRank (0, 'image');
	$rankTitle = $profile->getRank (0, 'title');
}
?>
<div class="row-fluid">
	<div class="span3 center">
		<div class="thumbnail" style="width: 200px; height: 200px; vertical-align: middle;">
			<?php echo $profile->getAvatarImage('img-rounded', 200, 200); ?>
		</div>
	</div>

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

			<?php if (!empty($this->banReason)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>
			</dt>
			<dd>
				<?php echo $this->escape($this->banReason); ?>
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

			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>
			</dt>
			<dd title="<?php echo $profile->getRegisterDate()->toKunena('ago'); ?>">
				<?php echo $profile->getRegisterDate()->toKunena('date_today', 'utc'); ?>
			</dd>

			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>
			</dt>
			<dd title="<?php echo $profile->getLastVisitDate()->toKunena('ago'); ?>">
				<?php echo $profile->getLastVisitDate()->toKunena('date_today', 'utc'); ?>
			</dd>

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

			<?php if (!empty($this->userpoints)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_AUP_POINTS'); ?>
			</dt>
			<dd>
				<?php echo (int) $this->userpoints; ?>
			</dd>
			<?php endif; ?>

			<?php if (!empty($this->usermedals)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_AUP_MEDALS'); ?>
			</dt>
			<dd>
				<?php echo implode(' ', $this->usermedals); ?>
			</dd>
			<?php endif; ?>

			<?php if (!empty($this->PMlink)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_SEND_MESSAGE'); ?>
			</dt>
			<dd>
				<?php echo $this->PMlink; ?>
			</dd>
			<?php endif ?>

			<?php if (!empty($this->personalText)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?>
			</dt>
			<dd>
				<?php echo KunenaHtmlParser::parseText($this->personalText); ?>
			</dd>
			<?php endif; ?>

		</dl>
	</div>

	<div class="span4">
		<?php echo $this->subLayout('User/Item/Social')->set('profile', $profile)->set('showAll', true); ?>
	</div>
</div>
<div class="clearfix"></div>
