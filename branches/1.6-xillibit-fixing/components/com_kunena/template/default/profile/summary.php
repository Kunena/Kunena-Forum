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


$document = & JFactory::getDocument ();
$document->addScriptDeclaration ( "window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new JTabs(tabs); }); });" );
?>

<div class="kbt_cvr1">
<div class="kbt_cvr2">
<div class="kbt_cvr3">
<div class="kbt_cvr4">
<div class="kbt_cvr5">
<h1><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->user->name; ?> (<?php echo $this->user->username; ?>)
<?php if ($this->do!='edit') echo '<span class="right">'.CKunenaLink::GetMyProfileLink ( $this->_config, $this->user->id, JText::_('COM_KUNENA_EDIT'), 'nofollow', 'edit' ).'</span>';?></h1>
	<div id="kprofile-container">
		<div id="kprofile-leftcol">
			<div class="avatar-lg"><img src="<?php echo $this->avatarurl; ?>" alt=""/></div>
			<div id="kprofile-stats">
				<ul>
					<li><span class="buttononline-<?php echo $this->online ? 'yes':'no'; ?> btn-left"><span class="online-<?php echo $this->online ? 'yes':'no'; ?>"><span><?php echo $this->online ? 'NOW ONLINE' : 'OFFLINE'; ?></span></span></span></li>
					<!-- Check this: -->
					<li class="usertype"><?php echo $this->user->usertype; ?></li>
					<!-- The class on the span below should be rank then hyphen then the rank name -->
					<li><strong>Rank: </strong><?php echo $this->rank_title; ?></li>
					<li class="kprofile-rank"><img src="<?php echo $this->rank_image; ?>" alt="<?php echo $this->rank_title; ?>" /></li>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->user->registerDate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->user->registerDate, 'date_today', 'utc'); ?></span></li>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->user->lastvisitDate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->user->lastvisitDate, 'date_today', 'utc'); ?></span></li>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong> GMT <?php echo CKunenaTimeformat::showTimezone($this->timezone); ?></li>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong> <?php echo CKunenaTimeformat::showDate('now', 'time', 'utc', $this->timezone); ?></li>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_POSTS'); ?>:</strong> <?php echo $this->profile->posts; ?></li>
					<!-- Profile view*s*? -->
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_PROFILEVIEW'); ?>:</strong> <?php echo $this->profile->uhits; ?></li>
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
</div>
</div>
