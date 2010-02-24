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


<h1><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->user->name; ?> (<?php echo $this->user->username; ?>)</h1>

<div class="kbt_cvr1">
<div class="kbt_cvr2">
<div class="kbt_cvr3">
<div class="kbt_cvr4">
<div class="kbt_cvr5">

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
			<div id="kprofile-rightcoltop">
				<div class="kprofile-rightcol2">

<?php
	CKunenaTools::loadTemplate('/profile/socialbuttons.php');
?>

				</div>
				<div class="kprofile-rightcol1">
					<ul>
						<li><span class="location"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?>:</strong> <?php echo $this->location; ?></li>
						<!--  The gender determines the suffix on the span class- gender-male & gender-female  -->
						<li><span class="gender-<?php echo $this->genderclass; ?>"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong> <?php echo $this->gender; ?></li>
						<li class="bd"><span class="birthdate"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->profile->birthdate, 'ago'); ?>"><?php echo CKunenaTimeformat::showDate($this->profile->birthdate, 'date'); ?></span>
						<!--  <a href="#" title=""><span class="bday-remind"></span></a> -->
						</li>
					</ul>
				</div>
			</div>
			<div class="clrline"></div>
			<div id="kprofile-rightcolbot">
				<div class="kprofile-rightcol2">
					<ul>
						<li><span class="email"></span><a href="mailto:<?php echo $this->user->email; ?>"><?php echo $this->user->email; ?></a></li>
						<li><span class="website"></span><a href="<?php echo kunena_htmlspecialchars(stripslashes($this->profile->websiteurl)); ?>" target="_blank"><?php echo kunena_htmlspecialchars(stripslashes($this->profile->websitename)); ?></a></li>
					</ul>
				</div>
				<div class="kprofile-rightcol1">
					<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?></h4>
					<p><?php echo $this->personalText; ?></p>
					<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></h4>
					<div class="msgsignature"><div><?php echo $this->signature; ?></div></div>
				</div>

			</div>
			<div class="clr"></div>

			<div id="kprofile-tabs">
				<dl class="tabs">
					<dt><?php echo JText::_('COM_KUNENA_OWNTOPICS'); ?></dt>
					<dd>
						<?php $this->displayOwnTopics(); ?>
					</dd>
					<dt><?php echo JText::_('COM_KUNENA_USERTOPICS'); ?></dt>
					<dd>
						<?php $this->displayUserTopics(); ?>
					</dd>
					<dt><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS'); ?></dt>
					<dd>
						<?php $this->displaySubscriptions(); ?>
					</dd>
					<dt><?php echo JText::_('COM_KUNENA_FAVORITES'); ?></dt>
					<dd>
						<?php $this->displayFavorites(); ?>
					</dd>

					<?php if (CKunenaTools::isModerator($this->my->id) && $this->my->id != $this->user->id): ?>
					<!-- Only visible to moderators and admins -->
					<dt class="kprofile-modbtn"><?php echo JText::_('COM_KUNENA_MODERATE_THIS_USER'); ?></dt>
					<dd class="kprofile-modtools">
						<h4><?php echo JText::_('COM_KUNENA_MODERATE_USERIPS'); ?>:</h4>
						<ul>
							<li><span><a href="http://ws.arin.net/whois/?queryinput=147.22.33.88" target="_blank">147.22.33.88</a></span> (<?php echo JText::_('COM_KUNENA_MODERATE_OTHER_USERS_WITH_IP'); ?>: <a href="#">marks</a>, <a href="#">killboy</a>, <a href="#">fxstein</a>)</li>
							<li><span><a href="http://ws.arin.net/whois/?queryinput=144.23.33.168" target="_blank">144.23.33.168</a></span> (<?php echo JText::_('COM_KUNENA_MODERATE_OTHER_USERS_WITH_IP'); ?>: None)</li>
						</ul>
						<h4><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_USER'); ?>:</h4>
						<form id="kform-ban" name="kformban" action="#" method="post">

							<input type="checkbox" id="ban-ip" name="banip" value="banip" class="kcheckbox" />
							<label for="ban-ip"><span onclick="document.kformban.banip.checked=(! document.kformban.banip.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_BANIP'); ?></span></label>
							<select>
								<option value=""><?php echo JText::_('COM_KUNENA_MODERATE_SELECT_IP'); ?></option>
								<option value="147.22.33.88">147.22.33.88</option>
								<option value="144.23.33.168">144.23.33.168</option>
								<option value="allips"><?php echo JText::_('COM_KUNENA_MODERATE_ALLIPS'); ?></option>
							</select>
							<input type="checkbox" id="ban-email" name="banemail" value="banemail" class="kcheckbox" />
							<label for="ban-email"><span onclick="document.kformban.banemail.checked=(! document.kformban.banemail.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_BANEMAIL'); ?></span></label>
							<input type="checkbox" id="ban-username" name="banusername" value="banusername" class="kcheckbox" />
							<label for="ban-username"><span onclick="document.kformban.banusername.checked=(! document.kformban.banusername.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_BANUSERNAME'); ?></span></label>
							<input type="checkbox" id="ban-delposts" name="bandelposts" value="bandelposts" class="kcheckbox" />
							<label for="ban-delposts"><span onclick="document.kformban.bandelposts.checked=(! document.kformban.bandelposts.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_ALL_POSTS'); ?></span></label>
							<input class="kbutton kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_MODERATE_DELETE_USER'); ?>" name="Submit" />
						</form>
					</dd>
					<?php endif; ?>
				</dl>
			</div>
			<div class="clr"></div>
		</div>
	</div>
</div>
</div>
</div>
</div>
</div>
