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
JHTML::_('behavior.calendar');
?>
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
			<li class="bd"><span class="birthdate"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->profile->birthdate, 'ago', 'utc', 0); ?>"><?php echo CKunenaTimeformat::showDate($this->profile->birthdate, 'date', 'utc', 0); ?></span>
			<!--  <a href="#" title=""><span class="bday-remind"></span></a> -->
			</li>
		</ul>
	</div>
</div>

<div class="clrline"></div>
<div id="kprofile-rightcolbot">
	<div class="kprofile-rightcol2">
		<ul>
			<?php if ($this->config->showemail && (!$this->profile->hideEmail || CKunenaTools::isModerator($this->my->id))): ?><li><span class="email"></span><a href="mailto:<?php echo $this->user->email; ?>"><?php echo $this->user->email; ?></a></li><?php endif; ?>
			<?php // FIXME: we need a better way to add http/https ?>
			<li><span class="website"></span><a href="http://<?php echo kunena_htmlspecialchars($this->profile->websiteurl); ?>" target="_blank"><?php echo kunena_htmlspecialchars($this->profile->websitename); ?></a></li>
		</ul>
	</div>
	<div class="kprofile-rightcol1">
		<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></h4>
		<div class="msgsignature"><div><?php echo $this->signature; ?></div></div>
	</div>

</div>

<div class="clr"></div>

<div id="kprofile-tabs">
	<dl class="tabs">
		<dt class="open"><?php echo JText::_('COM_KUNENA_USERPOSTS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayUserPosts(); ?>
		</dd>
		<?php if ($this->my->id == $this->user->id): ?>
		<!--
		<dt class="closed"><?php echo JText::_('COM_KUNENA_OWNTOPICS'); ?></dt>
		<dd style="display: none;">
			<?php //$this->displayOwnTopics(); ?>
		</dd>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_USERTOPICS'); ?></dt>
		<dd style="display: none;">
			<?php //$this->displayUserTopics(); ?>
		</dd>
		-->
		<?php if ($this->config->allowsubscriptions) :?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displaySubscriptions(); ?>
		</dd>
		<?php endif; if ($this->config->allowfavorites) : ?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_FAVORITES'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayFavorites(); ?>
		</dd>
		<?php endif;?>
		<?php endif; if (CKunenaTools::isModerator($this->my->id) && $this->my->id == $this->user->id || CKunenaTools::isModerator($this->my->id)): ?>
		<dt class="closed"><?php echo JText::_('Ban Manager'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayUsersBanned(); ?>
		</dd>
		<?php endif;  if (CKunenaTools::isModerator($this->my->id) && $this->my->id != $this->user->id):?>
		<dt class="closed"><?php echo JText::_('Ban History'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayUserBanHistory(); ?>
		</dd>
		<?php endif; if ((CKunenaTools::isModerator($this->my->id) &&
						($this->my->id != $this->user->id)) &&
						(!CKunenaTools::isModerator($this->user->id)) ||
						(CKunenaTools::isAdmin($this->my->id)) &&
						(!CKunenaTools::isAdmin($this->user->id))) : ?>
		<dt class="closed"><?php echo JText::_('Add Ban'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayAddBan(); ?>
		</dd>

		<?php endif; if (CKunenaTools::isModerator($this->my->id) && $this->my->id != $this->user->id): ?>
		<!-- Only visible to moderators and admins -->
		<!--<dt class="kprofile-modbtn"><?php echo JText::_('COM_KUNENA_MODERATE_THIS_USER'); ?></dt>
		<dd class="kprofile-modtools">
			<?php
			$path = KUNENA_PATH_LIB.'/kunena.moderation.class.php';
			require_once ($path);
			$kunena_mod = CKunenaModeration::getInstance();
			$iplist = $kunena_mod->getUserIPs ($this->user->id);
			$useriplist = $kunena_mod->getUsernameMatchingIPs($this->user->id);
			?>
			<h4><?php echo JText::_('COM_KUNENA_MODERATE_USERIPS'); ?>:</h4>
			<ul>
				<?php
				$usernames = array();
				foreach ($iplist as $ip) {
					$usernames = array_merge($usernames,$useriplist[$ip->ip]);
					$username = array();
					foreach ($usernames as $user) {
						$username[] = CKunenalink::GetProfileLink($user->userid, $user->name);
					}
					$username=implode(', ',$username);

					if (!empty($useriplist[$ip->ip])) {
				?>
				<li><span><a href="http://ws.arin.net/whois/?queryinput=<?php echo $ip->ip; ?>" target="_blank"><?php echo $ip->ip; ?></a></span> (<?php echo JText::_('COM_KUNENA_MODERATE_OTHER_USERS_WITH_IP'); ?>: <?php echo $username; ?>)</li>
				<?php
					} else {
					?>
				<li><span><a href="http://ws.arin.net/whois/?queryinput=<?php echo $ip->ip; ?>" target="_blank"><?php echo $ip->ip; ?></a></span> (<?php echo JText::_('COM_KUNENA_MODERATE_OTHER_USERS_WITH_IP'); ?>: <?php echo JText::_('COM_KUNENA_MODERATION_USER_NONE_IPS'); ?>)</li>
				<?php
					}
				} ?>
			</ul>
			<h4><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_USER'); ?>:</h4>
			<form id="kform-ban" name="kformban" action="index.php" method="post">

				<label for="ban-ip">
				<span><?php echo JText::_('COM_KUNENA_MODERATE_BANIP'); ?></span>
				<?php
				$ipselect = array();
				foreach ($iplist as $ip) {
					$ipselect [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_MODERATE_SELECT_IP') );
					$ipselect [] = JHTML::_ ( 'select.option', $ip->ip, $ip->ip );
					$ipselect [] = JHTML::_ ( 'select.option', 'allips', JText::_('COM_KUNENA_MODERATE_ALLIPS') );
				}

				echo $lists = JHTML::_ ( 'select.genericlist', $ipselect, 'prof_ip_select', 'class="inputbox" size="1"', 'value', 'text' );
				?>
				</label>
				<label>
				<ul id="ban-fields" style="display:none;">
				<li>
				<span><?php echo JText::_('COM_KUNENA_BAN_EXPIRY'); ?></span>
				<input class="inputbox" type="text" maxlength="15" name="banexpiry" id="banexpiry" /> 
				<img src="templates/system/images/calendar.png" alt="Calendar" onclick="showCalendar('banexpiry','%Y-%m-%d');" /></li>
				<li>
				<span><?php echo JText::_('COM_KUNENA_BAN_MESSAGE'); ?></span>
				<input type="text" name="banmessage" /></li>
				</ul></label>-->
				<!--<label for="ban-email"><input type="checkbox" id="ban-email" name="banemail" value="banemail" class="kcheckbox" />
				<span onclick="document.kformban.banemail.checked=(! document.kformban.banemail.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_BANEMAIL'); ?></span></label>
				<label for="ban-username"><input type="checkbox" id="ban-username" name="banusername" value="banusername" class="kcheckbox" />
				<span onclick="document.kformban.banusername.checked=(! document.kformban.banusername.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_BANUSERNAME'); ?></span></label>-->
				<!--<label for="ban-delsignature"><input type="checkbox" id="ban-delsignature" name="delsignature" value="delsignature" class="kcheckbox" />
				<span onclick="document.kformban.bandelposts.checked=(! document.kformban.bandelposts.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_SIGNATURE'); ?></span></label>
				<label for="ban-user"><input type="checkbox" id="ban-user" name="banuser" value="banuser" class="kcheckbox" />
				<span id="ban-user-text" onclick="document.kformban.banuser.checked=(! document.kformban.banuser.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_BAN_USER'); ?></span></label>
				<label for="ban-delavatar"><input type="checkbox" id="ban-delavatar" name="delavatar" value="delavatar" class="kcheckbox" />
				<span onclick="document.kformban.delavatar.checked=(! document.kformban.delavatar.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_AVATAR'); ?></span></label>
				<label for="ban-delprofileinfo"><input type="checkbox" id="ban-delprofileinfo" name="delprofileinfo" value="delprofileinfo" class="kcheckbox" />
				<span onclick="document.kformban.delprofileinfo.checked=(! document.kformban.delprofileinfo.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_PROFILEINFO'); ?></span></label>
				<label for="ban-delposts"><input type="checkbox" id="ban-delposts" name="bandelposts" value="bandelposts" class="kcheckbox" />
				<span onclick="document.kformban.bandelposts.checked=(! document.kformban.bandelposts.checked);"><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_ALL_POSTS'); ?></span></label>
				<input class="kbutton kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_MODERATE_MODERATENOW'); ?>" name="Submit" />
				<input type="hidden" name="option" value="com_kunena" /> <input
				type="hidden" name="func" value="banactions" /> <input
				type="hidden" name="thisuserid" value="<?php echo $this->user->id; ?>" />
			</form>
	</dd>-->
	<?php endif; ?>
	</dl>
</div>
