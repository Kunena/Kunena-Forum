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
$document->addScriptDeclaration ( "window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new KunenaTabs(tabs); }); });" );
?>

<div class="kbt_cvr1">
<div class="kbt_cvr2">
<div class="kbt_cvr3">
<div class="kbt_cvr4">
<div class="kbt_cvr5">
<h1><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->name; ?>
<?php if (!empty($this->editlink)) echo '<span class="right">'.$this->editlink.'</span>';?></h1>
	<div id="kprofile-container">
		<div id="kprofile-leftcol">
			<?php if ($this->avatarlink) : ?>
			<div class="avatar-lg"><?php echo $this->avatarlink; ?></div>
			<?php endif; ?>
			<div id="kprofile-stats">
				<ul>
					<?php if( !empty($this->personalText) ) { ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?>:</strong> <?php echo $this->personalText; ?></li>
					<li><span class="buttononline-<?php echo $this->online ? 'yes':'no'; ?> btn-left"><span class="online-<?php echo $this->online ? 'yes':'no'; ?>"><span><?php echo $this->online ? 'NOW ONLINE' : 'OFFLINE'; ?></span></span></span></li>
					<!-- Check this: -->
					<?php if (!empty($this->usertype)): ?><li class="usertype"><?php echo $this->usertype; ?></li><?php endif; ?>
					<!-- The class on the span below should be rank then hyphen then the rank name -->
					<?php if (!empty($this->rank_title)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_RANK'); ?>: </strong><?php echo $this->rank_title; ?></li><?php endif; ?>
					<?php if (!empty($this->rank_image)): ?><li class="kprofile-rank"><img src="<?php echo $this->rank_image; ?>" alt="<?php echo $this->rank_title; ?>" /></li><?php endif; ?>
					<?php if (!empty($this->registerdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->registerdate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->registerdate, 'date_today', 'utc'); ?></span></li><?php endif; ?>
					<?php if (!empty($this->lastvisitdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->lastvisitdate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->lastvisitdate, 'date_today', 'utc'); ?></span></li><?php endif; ?>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong> GMT <?php echo CKunenaTimeformat::showTimezone($this->timezone); ?></li>
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong> <?php echo CKunenaTimeformat::showDate('now', 'time', 'utc', $this->timezone); ?></li>
					<?php if (!empty($this->posts)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_POSTS'); ?>:</strong> <?php echo $this->posts; ?></li><?php endif; ?>
					<!-- Profile view*s*? -->
					<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_PROFILEVIEW'); ?>:</strong> <?php echo $this->profile->uhits; ?></li>
					<li><?php echo $this->displayKarma(); ?></li>
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
