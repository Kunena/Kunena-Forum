<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();


$document = JFactory::getDocument ();
$document->addScriptDeclaration ( "window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new KunenaTabs(tabs); }); });" );
$private = KunenaFactory::getPrivateMessaging();
if ($this->my->id == $this->user->id) {
	$PMCount = $private->getUnreadCount($this->my->id);
	$PMlink = $private->getInboxLink($PMCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $PMCount) : JText::_('COM_KUNENA_PMS_INBOX'));
} else {
	$PMlink = $this->profile->profileIcon('private');
}
?>

<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->escape($this->name); ?></span>
		<?php if (!empty($this->editlink)) echo '<span class="kright">'.$this->editlink.'</span>';?></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
	<div id="kprofile-container">
		<div id="kprofile-leftcol">
			<?php if ($this->avatarlink) : ?>
			<div class="kavatar-lg"><?php echo $this->avatarlink; ?></div>
			<?php endif; ?>
			<div id="kprofile-stats">
				<ul>
					<?php if ( !empty($this->banReason) ) { ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>:</strong> <?php echo $this->escape($this->banReason); ?></li><?php } ?>
					<?php // FIXME: add translation: NOW ONLINE, OFFLINE ?>
					<li><span class="kicon-button kbuttononline-<?php echo $this->online ? 'yes':'no'; ?> btn-left"><span class="online-<?php echo $this->online ? 'yes':'no'; ?>"><span><?php echo $this->online ? 'NOW ONLINE' : 'OFFLINE'; ?></span></span></span></li>
					<?php if (!empty($this->usertype)): ?><li class="usertype"><?php echo $this->escape($this->usertype); ?></li><?php endif; ?>
					<?php if (!empty($this->rank_title)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_RANK'); ?>: </strong><?php echo $this->escape($this->rank_title); ?></li><?php endif; ?>
					<?php if (!empty($this->rank_image)): ?><li class="kprofile-rank"><img src="<?php echo $this->rank_image; ?>" alt="<?php echo $this->escape($this->rank_title); ?>" /></li><?php endif; ?>
					<?php if (!empty($this->registerdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->registerdate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->registerdate, 'date_today', 'utc'); ?></span></li><?php endif; ?>
					<?php if (!empty($this->lastvisitdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->lastvisitdate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->lastvisitdate, 'date_today', 'utc'); ?></span></li><?php endif; ?>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong> GMT <?php echo CKunenaTimeformat::showTimezone($this->timezone); ?></li>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong> <?php echo CKunenaTimeformat::showDate('now', 'time', 'utc', $this->timezone); ?></li>
					<?php if (!empty($this->posts)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_POSTS'); ?>:</strong> <?php echo intval($this->posts); ?></li><?php endif; ?>
					<?php // FIXME: Typo in translation: Profile view*s*? ?>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_PROFILEVIEW'); ?>:</strong> <?php echo intval($this->profile->uhits); ?></li>
					<li><?php echo $this->displayKarma(); ?></li>
					<?php if ($PMlink) {
							?>
						<li><?php echo $PMlink; ?></li>
					<?php  } ?>
					<?php if( !empty($this->personalText) ) { ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?>:</strong> <?php echo KunenaParser::parseText($this->personalText); ?></li><?php } ?>
				</ul>
			</div>
		</div>
		<div id="kprofile-rightcol">
			<?php $this->displayTab(); ?>
			<div class="clr"></div>
		</div>
	</div>
		</div>
	</div>
</div>
