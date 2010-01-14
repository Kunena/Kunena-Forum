<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
defined( '_JEXEC' ) or die();

$kunena_config =& CKunenaConfig::getInstance();

$signature = $userinfo->signature;

$usr_signature = '';
if ($signature)
{
	$kunena_emoticons = smile::getEmoticons(0);
	$signature = stripslashes(smile::smileReplace($signature, 0, $kunena_config->disemoticons, $kunena_emoticons));
	$signature = str_replace("\n", "<br />", $signature);
	$signature = str_replace("<P>&nbsp;</P><br />", "", $signature);
	$signature = str_replace("</P><br />", "</P>", $signature);
	$signature = str_replace("<P><br />", "<P>", $signature);
	//wordwrap:
	$signature = nl2br($signature);
	//restore the \n (were replaced with _CTRL_) occurences inside code tags, but only after we have striplslashes; otherwise they will be stripped again
	//$signature = stripslashes($signature);
	//$signature = str_replace("_CRLF_", "\\n", $signature);
	$usr_signature = $signature;
}
?>
<!-- This loads the tabs and preferably should be in the document head -->
<script type="text/javascript">
  	window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new JTabs(tabs); }); });
</script>

<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">

	<div id="kprofile-container">
		<div id="kprofile-leftcol">
			<div class="avatar-lg"></div>
			<div id="kprofile-stats">
				<ul>
					<!-- The class on the first LI should be either this or online-status-no -->
					<li><span class="online-status-yes">NOW ONLINE</span></li>
					<!-- The class on the span below should be rank then hyphen then the rank name -->
					<li><span class="rankname"><?php echo $juserinfo->usertype; ?></span> <span class="rank-admin"></span></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_REGISTERDATE; ?>:</strong> <?php echo $juserinfo->registerDate; ?></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_LASTVISITDATE; ?>:</strong> <?php echo $juserinfo->lastvisitDate; ?></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_TIMEZONE; ?>:</strong> GMT +9</li>
					<li><strong><?php echo _KUNENA_MYPROFILE_POSTS; ?>:</strong> <?php echo $userinfo->posts; ?></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_PROFILEVIEW; ?>:</strong> <?php echo $userinfo->uhits; ?></li>
				</ul>
			</div>
		</div>
		<div id="kprofile-rightcol">
			<div id="kprofile-rightcoltop">
				<div class="kprofile-rightcol2">

					<div class="iconrow">
						<a href="<?php echo 'http://twitter.com/'.kunena_htmlspecialchars(stripslashes($userinfo->TWITTER)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_TWITTER; ?>"><span class="twitter"></span></a>
						<a href="<?php echo 'http://www.facebook.com/'.kunena_htmlspecialchars(stripslashes($userinfo->FACEBOOK)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_FACEBOOK; ?>"><span class="facebook"></span></a>
						<a href="<?php echo 'http://www.myspace.com/'.kunena_htmlspecialchars(stripslashes($userinfo->MYSPACE)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_MYSPACE; ?>"><span class="myspace"></span></a>
						<a href="<?php echo 'http://www.linkedin.com/in/'.kunena_htmlspecialchars(stripslashes($userinfo->LINKEDIN)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_LINKEDIN; ?>"><span class="linkedin"></span></a>
					</div>
					<div class="iconrow">
						<a href="<?php echo 'http://www.delicious.com/'.kunena_htmlspecialchars(stripslashes($userinfo->DELICIOUS)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_DELICIOUS; ?>"><span class="delicious"></span></a>
						<a href="<?php echo 'http://friendfeed.com/'.kunena_htmlspecialchars(stripslashes($userinfo->FRIENDFEED)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_FRIENDFEED; ?>"><span class="friendfeed"></span></a>
						<a href="<?php echo 'http://www.digg.com/'.kunena_htmlspecialchars(stripslashes($userinfo->DIGG)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_DIGG; ?>"><span class="digg"></span></a>
					</div>
					<div class="clr"></div>
					<div class="iconrow">
						<a href="skype:?call<?php echo kunena_htmlspecialchars(stripslashes($userinfo->SKYPE)); ?>" target="_blank" title="Call <?php echo kunena_htmlspecialchars(stripslashes($userinfo->SKYPE)); ?>"><span class="skype"></span></a>
						<a href="ymsgr:sendim?<?php echo kunena_htmlspecialchars(stripslashes($userinfo->YIM)); ?>" target="_blank" title="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->YIM)); ?>"><span class="yim"></span></a>
						<a href="aim:goim?screenname=<?php echo kunena_htmlspecialchars(stripslashes($userinfo->AIM)); ?>" target="_blank" title="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->AIM)); ?>"><span class="aim"></span></a>
						<a href="gtalk:chat?jid=<?php echo kunena_htmlspecialchars(stripslashes($userinfo->GTALK)); ?>" target="_blank" title="Chat with <?php echo kunena_htmlspecialchars(stripslashes($userinfo->GTALK)); ?>"><span class="gtalk"></span></a>
					</div>
					<div class="iconrow">
						<a href="<?php echo 'http://www.blogspot.com/'.kunena_htmlspecialchars(stripslashes($userinfo->BLOGSPOT)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_BLOGSPOT; ?>"><span class="blogspot"></span></a>
						<a href="<?php echo 'http://www.flickr.com/'.kunena_htmlspecialchars(stripslashes($userinfo->FLICKR)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_FLICKR; ?>"><span class="flickr"></span></a>
						<a href="<?php echo 'http://www.bebo.com/'.kunena_htmlspecialchars(stripslashes($userinfo->BEBO)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_BEBO; ?>"><span class="bebo"></span></a>
					</div>

				</div>
				<div class="kprofile-rightcol1">
					<ul>
						<li><span class="location"></span><strong><?php echo _KUNENA_MYPROFILE_LOCATION; ?>:</strong> <a href="http://maps.google.com?q=<?php echo kunena_htmlspecialchars(stripslashes($userinfo->location)); ?>" target="_blank"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->location)); ?></a></li>
						<!--  The gender determines the suffix on the span class- gender-male & gender-female  -->
						<li><span class="gender-<?php if( $userinfo->gender == 1 ) { echo _KUNENA_MYPROFILE_MALEC; } else if ( $userinfo->gender == 2 ) { echo _KUNENA_MYPROFILE_FEMALEC; }?>"></span><strong><?php echo _KUNENA_MYPROFILE_GENDER; ?>:</strong> <?php if( $userinfo->gender == 1 ) { echo _KUNENA_MYPROFILE_MALE; } else if ( $userinfo->gender == 2 ) { echo _KUNENA_MYPROFILE_FEMALE; }?></li>
						<li class="bd"><span class="birthdate"></span><strong><?php echo _KUNENA_MYPROFILE_BIRTHDATE; ?>:</strong> <?php echo kunena_htmlspecialchars(stripslashes($userinfo->birthdate)); ?> <a href="#" title="<?php echo _KUNENA_MYPROFILE_BIRTHDAYREMIND; ?>"><span class="bday-remind"></span></a></li>
					</ul>
				</div>
			</div>
			<div class="clrline"></div>
			<div id="kprofile-rightcolbot">
				<div class="kprofile-rightcol2">
					<ul>
						<li><span class="email"></span><a href="mailto:<?php echo $juserinfo->email; ?>"><?php echo $juserinfo->email; ?></a></li>
						<li><span class="website"></span><a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->websiteurl)); ?>" target="_blank"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->websitename)); ?></a></li>
					</ul>
				</div>
				<div class="kprofile-rightcol1">
					<h4><?php echo _KUNENA_MYPROFILE_ABOUTME; ?></h4>
					<p><?php echo kunena_htmlspecialchars(stripslashes($userinfo->personalText)); ?></p>
					<h4><?php echo _KUNENA_MYPROFILE_SIGNATURE; ?></h4>
					<div class="msgsignature"><div><?php echo $usr_signature; ?></div></div>
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
						<form id="kform-ban" name="kformban" action="#" method="">

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
							<input class="kbutton kbutton ks" type="submit" value="<?php echo _KUNENA_MODERATE_DELETE_USER; ?>" name="Submit"/>
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
</div>

