<?php
/**
 * @version $Id: summary.php 1544 2010-01-08 19:57:41Z 810 $
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
<table class="fb_blocktable" cellpadding="0" cellspacing="0" border="0" width="100%">
<thead>
	<tr>
		<th>
			<div class="fb_title_cover fbm">
				<span class="fb_title fbl">
				<?php echo _USER_PROFILE; ?>
				<?php echo $this->user->name; ?>
				</span>

			</div>
		</th>
		<!-- B: PROFILE TOOLS -->
		<th align="right" width="1%">
			<?php
			//(JJ) BEGIN: RECENT POSTS
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/profiletools/profiletools.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/plugin/profiletools/profiletools.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/profiletools/profiletools.php');
			}
			?>
		</th>
	</tr>
	</thead>
</table>

<div class="fb_bt_cvr1">
<div class="fb_bt_cvr2">
<div class="fb_bt_cvr3">
<div class="fb_bt_cvr4">
<div class="fb_bt_cvr5">

	<div id="kprofile-container">
		<div id="kprofile-leftcol">
			<div class="avatar-lg"><img src="<?php echo $this->avatarurl; ?>" alt=""/></div>
			<div id="kprofile-stats">
				<ul>
					<li><span class="online-status-<?php echo $this->online ? 'yes':'no'; ?>"><?php echo $this->online ? 'NOW ONLINE' : 'OFFLINE'; ?></span></li>
					<!-- Check this: -->
					<li><span class="usertype">User Type:</span><span><?php echo $this->user->usertype; ?></span></li>
					<!-- The class on the span below should be rank then hyphen then the rank name -->
					<li><span class="rankname">User Rank:</span><span><?php echo $this->rank->rank_title; ?><br /><img src="<?php echo $this->rank->rank_image; ?>" alt="<?php echo $this->rank->rank_title; ?>" /></span></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_REGISTERDATE; ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->user->registerDate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->user->registerDate, 'date_today', 'utc'); ?></span></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_LASTVISITDATE; ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->user->lastvisitDate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->user->lastvisitDate, 'date_today', 'utc'); ?></span></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_TIMEZONE; ?>:</strong> GMT <?php echo CKunenaTimeformat::showTimezone($this->timezone); ?></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_LOCAL_TIME; ?>:</strong> <?php echo CKunenaTimeformat::showDate('now', 'time', 0, $this->timezone); ?></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_POSTS; ?>:</strong> <?php echo $this->profile->posts; ?></li>
					<!-- Profile view*s*? -->
					<li><strong><?php echo _KUNENA_MYPROFILE_PROFILEVIEW; ?>:</strong> <?php echo $this->profile->uhits; ?></li>
				</ul>
			</div>
		</div>
		<div id="kprofile-rightcol">
			<div id="kprofile-rightcoltop">
				<div class="kprofile-rightcol2">

<?php
	if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'profile' . DS . 'socialbuttons.php')) {
		include (KUNENA_ABSTMPLTPATH . DS . 'profile' . DS . 'socialbuttons.php');
	} else {
		include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'profile' . DS . 'socialbuttons.php');
	}
?>

				</div>
				<div class="kprofile-rightcol1">
					<ul>
						<li><span class="location"></span><strong><?php echo _KUNENA_MYPROFILE_LOCATION; ?>:</strong> <a href="http://maps.google.com?q=<?php echo kunena_htmlspecialchars(stripslashes($this->profile->location)); ?>" target="_blank"><?php echo kunena_htmlspecialchars(stripslashes($this->profile->location)); ?></a></li>
						<!--  The gender determines the suffix on the span class- gender-male & gender-female  -->
						<li><span class="gender-<?php if( $this->profile->gender == 1 ) { echo _KUNENA_MYPROFILE_MALEC; } else if ( $this->profile->gender == 2 ) { echo _KUNENA_MYPROFILE_FEMALEC; }?>"></span><strong><?php echo _KUNENA_MYPROFILE_GENDER; ?>:</strong> <?php if( $this->profile->gender == 1 ) { echo _KUNENA_MYPROFILE_MALE; } else if ( $this->profile->gender == 2 ) { echo _KUNENA_MYPROFILE_FEMALE; }?></li>
						<li class="bd"><span class="birthdate"></span><strong><?php echo _KUNENA_MYPROFILE_BIRTHDATE; ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->profile->birthdate, 'ago'); ?>"><?php echo CKunenaTimeformat::showDate($this->profile->birthdate, 'date'); ?></span> <a href="#" title="<?php echo _KUNENA_MYPROFILE_BIRTHDAYREMIND; ?>"><span class="bday-remind"></span></a></li>
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
					<h4><?php echo _KUNENA_MYPROFILE_ABOUTME; ?></h4>
					<p><?php echo $this->personalText; ?></p>
					<h4><?php echo _KUNENA_MYPROFILE_SIGNATURE; ?></h4>
					<div class="msgsignature"><div><?php echo $this->signature; ?></div></div>
				</div>

			</div>
			<div class="clr"></div>

			<div id="kprofile-tabs">
				<dl class="tabs">
					<dt>Posts</dt>
					<dd>
						<p>Add post listing here.</p>
					</dd>
					<dt>Subscriptions</dt>
					<dd>
						<p>Add subscription listing here.</p>
					</dd>
					<dt>Favorites</dt>
					<dd>
						<p>Add favorites listing here.</p>
					</dd>

					<!-- Only visible to moderators and admins -->
					<dt class="kprofile-modbtn"><?php echo _KUNENA_MODERATE_THIS_USER; ?></dt>
					<dd class="kprofile-modtools">
						<h4><?php echo _KUNENA_MODERATE_USERIPS; ?>:</h4>
						<ul>
							<li><span><a href="http://ws.arin.net/whois/?queryinput=147.22.33.88" target="_blank">147.22.33.88</a></span> (<?php echo _KUNENA_MODERATE_OTHER_USERS_WITH_IP; ?>: <a href="#">marks</a>, <a href="#">killboy</a>, <a href="#">fxstein</a>)</li>
							<li><span><a href="http://ws.arin.net/whois/?queryinput=144.23.33.168" target="_blank">144.23.33.168</a></span> (<?php echo _KUNENA_MODERATE_OTHER_USERS_WITH_IP; ?>: None)</li>
						</ul>
						<h4><?php echo _KUNENA_MODERATE_DELETE_USER; ?>:</h4>
						<form id="kform-ban" name="kformban" action="#" method="post">

							<input type="checkbox" id="ban-ip" name="banip" value="banip" class="kcheckbox" >
							<label for="ban-ip"><span onClick="document.kformban.banip.checked=(! document.kformban.banip.checked);"><?php echo _KUNENA_MODERATE_BANIP; ?></span></label>
							<select>
								<option value=""><?php echo _KUNENA_MODERATE_SELECT_IP; ?></option>
								<option value="147.22.33.88">147.22.33.88</option>
								<option value="144.23.33.168">144.23.33.168</option>
								<option value="allips"><?php echo _KUNENA_MODERATE_ALLIPS; ?></option>
							</select>
							<input type="checkbox" id="ban-email" name="banemail" value="banemail" class="kcheckbox" >
							<label for="ban-email"><span onClick="document.kformban.banemail.checked=(! document.kformban.banemail.checked);"><?php echo _KUNENA_MODERATE_BANEMAIL; ?></span></label>
							<input type="checkbox" id="banu-sername" name="banusername" value="banusername" class="kcheckbox" >
							<label for="ban-username"><span onClick="document.kformban.banusername.checked=(! document.kformban.banusername.checked);"><?php echo _KUNENA_MODERATE_BANUSERNAME; ?></span></label>
							<input type="checkbox" id="ban-delposts" name="bandelposts" value="bandelposts" class="kcheckbox" >
							<label for="ban-delposts"><span onClick="document.kformban.bandelposts.checked=(! document.kformban.bandelposts.checked);"><?php echo _KUNENA_MODERATE_DELETE_ALL_POSTS; ?></span></label>
							<input class="kbutton fb_button fbs" type="submit" value="<?php echo _KUNENA_MODERATE_DELETE_USER; ?>" name="Submit"/>
						</form>
						<div class="clr"></div>
					</dd>
				</dl>
			</div>
		</div>
	</div>

</div>
</div>
</div>
</div>
</div>
