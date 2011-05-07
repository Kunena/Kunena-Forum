<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined( '_JEXEC' ) or die();


$document = JFactory::getDocument ();
$document->addScriptDeclaration ( "// <![CDATA[
window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new KunenaTabs(tabs); }); });
// ]]>" );
$private = KunenaFactory::getPrivateMessaging();
if ($this->my->id == $this->user->id) {
	$PMCount = $private->getUnreadCount($this->my->id);
	$PMlink = $private->getInboxLink($PMCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $PMCount) : JText::_('COM_KUNENA_PMS_INBOX'));
} else {
	$PMlink = $this->profile->profileIcon('private');
}
?>

<div class="kblock k-profile">
	<div class="kheader">
		<h2><span class="k-name"><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->escape($this->name); ?></span>
		<?php if (!empty($this->editlink)) echo '<span class="kheadbtn kright">'.$this->editlink.'</span>';?></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<table class = "kblocktable" id ="kprofile">
				<tr>
					<td class = "kcol-first kcol-left">
						<div id="kprofile-leftcol">
							<?php if ($this->avatarlink) : ?>
							<div class="kavatar-lg"><?php echo $this->avatarlink; ?></div>
							<?php endif; ?>
							<div id="kprofile-stats">
							<ul>
								<?php if ( !empty($this->banReason) ) { ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>:</strong> <?php echo $this->escape($this->banReason); ?></li><?php } ?>
								<li><span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline(true) ?>"><span class="online-<?php echo $this->profile->isOnline(true) ?>"><span><?php echo $this->profile->isOnline() ? JText::_('COM_KUNENA_ONLINE') : JText::_('COM_KUNENA_OFFLINE'); ?></span></span></span></li>
								<?php if (!empty($this->usertype)): ?><li class="usertype"><?php echo $this->escape($this->usertype); ?></li><?php endif; ?>
								<?php if (!empty($this->rank_title)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_RANK'); ?>: </strong><?php echo $this->escape($this->rank_title); ?></li><?php endif; ?>
								<?php if (!empty($this->rank_image)): ?><li class="kprofile-rank"><?php echo $this->rank_image; ?></li><?php endif; ?>
								<?php if (!empty($this->registerdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->registerdate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->registerdate, 'date_today', 'utc'); ?></span></li><?php endif; ?>
								<?php if ($this->lastvisitdate != "0000-00-00 00:00:00"): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->lastvisitdate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->lastvisitdate, 'date_today', 'utc'); ?></span></li><?php endif; ?>
								<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong> GMT <?php echo CKunenaTimeformat::showTimezone($this->timezone); ?></li>
								<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong> <?php echo CKunenaTimeformat::showDate('now', 'time', 'utc', $this->timezone); ?></li>
								<?php if (!empty($this->posts)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_POSTS'); ?>:</strong> <?php echo intval($this->posts); ?></li><?php endif; ?>
								<?php if (!empty($this->userpoints)): ?><li><strong><?php echo JText::_('COM_KUNENA_AUP_POINTS'); ?></strong> <?php echo intval($this->userpoints); ?></li><?php endif; ?>
								<?php if (!empty($this->usermedals)) : ?><li><?php foreach ( $this->usermedals as $medal ) : echo $medal,' '; endforeach ?></li><?php endif ?>
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
					</td>
					<td class="kcol-mid kcol-right">
						<div id="kprofile-rightcol">
							<?php $this->displayTab(); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
