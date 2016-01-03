<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage User
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<?php if ($this->avatarlink) : ?>
<div class="kavatar-lg"><?php echo $this->avatarlink; ?></div>
<?php endif; ?>
<div id="kprofile-stats">
<ul>
	<?php if ( !empty($this->banReason) ) : ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>:</strong> <?php echo $this->escape($this->banReason); ?></li><?php endif ?>
	<li><span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline('yes', 'no') ?>"><span class="online-<?php echo $this->profile->isOnline('yes', 'no') ?>"><span><?php echo $this->profile->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?></span></span></span></li>
	<?php if (!empty($this->usertype)): ?><li class="usertype"><?php echo JText::_($this->usertype); ?></li><?php endif; ?>
	<?php if (!empty($this->rank_title)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_RANK'); ?>: </strong><?php echo $this->escape($this->rank_title); ?></li><?php endif; ?>
	<?php if (!empty($this->rank_image)): ?><li class="kprofile-rank"><?php echo $this->rank_image; ?></li><?php endif; ?>
	<?php if (!empty($this->registerdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong> <span title="<?php echo KunenaDate::getInstance($this->registerdate)->toKunena('ago'); ?>"><?php echo KunenaDate::getInstance($this->registerdate)->toKunena('date_today', 'utc'); ?></span></li><?php endif; ?>
	<?php if (!empty($this->lastvisitdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong> <span title="<?php echo KunenaDate::getInstance($this->lastvisitdate)->toKunena('ago'); ?>"><?php echo KunenaDate::getInstance($this->lastvisitdate)->toKunena('date_today', 'utc'); ?></span></li><?php endif; ?>
	<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong> GMT <?php echo $this->localtime->toTimezone(); ?></li>
	<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong> <?php echo $this->localtime->toKunena('time'); ?></li>
	<?php if (!empty($this->posts)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_POSTS'); ?>:</strong> <?php echo intval($this->posts); ?></li><?php endif; ?>
	<?php if (!empty($this->thankyou)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_THANKYOU_RECEIVED'); ?></strong> <?php echo intval($this->thankyou); ?></li><?php endif; ?>
	<?php if (!empty($this->userpoints)): ?><li><strong><?php echo JText::_('COM_KUNENA_AUP_POINTS'); ?></strong> <?php echo intval($this->userpoints); ?></li><?php endif; ?>
	<?php if (!empty($this->usermedals)) : ?><li><?php foreach ( $this->usermedals as $medal ) : echo $medal,' '; endforeach ?></li><?php endif ?>
	<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_PROFILEVIEW'); ?>:</strong> <?php echo intval($this->profile->uhits); ?></li>
	<li><?php echo $this->displayKarma(); ?></li>
	<?php if ($this->PMlink) : ?>
	<li><?php if( $this->me->userid != $this->user->id): ?><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_SEND_MESSAGE'); ?>:</strong> <?php  endif ?><?php echo $this->PMlink; ?></li>
	<?php  endif ?>
	<?php if( !empty($this->personalText) ) { ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?>:</strong> <?php echo KunenaHtmlParser::parseText($this->personalText); ?></li><?php } ?>
</ul>
</div>
