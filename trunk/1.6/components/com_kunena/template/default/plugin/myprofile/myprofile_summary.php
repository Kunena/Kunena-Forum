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
defined( '_JEXEC' ) or die('Restricted access');
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
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">

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
					<li><strong><?php echo _KUNENA_MYPROFILE_POSTS; ?>:</strong> <?php echo $userinfo->posts; ?></li>
					<li><strong><?php echo _KUNENA_MYPROFILE_PROFILEVIEW; ?>:</strong> <?php echo $userinfo->uhits; ?></li>
				</ul>
			</div>
		</div>
		<div id="kprofile-rightcol">
			<div id="kprofile-rightcoltop">
				<div class="kprofile-rightcol2">
					<div class="iconrow">
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->TWITTER)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_TWITTER; ?>"><span class="twitter"></span></a>
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->FACEBOOK)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_FACEBOOK; ?>"><span class="facebook"></span></a>
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->MYSPACE)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_MYSPACE; ?>"><span class="myspace"></span></a>
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->LINKEDIN)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_LINKEDIN; ?>"><span class="linkedin"></span></a>
					</div>
					<div class="iconrow">
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->DELICIOUS)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_DELICIOUS; ?>"><span class="delicious"></span></a>
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->FRIENDFEED)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_FRIENDFEED; ?>"><span class="friendfeed"></span></a>
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->DIGG)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_DIGG; ?>"><span class="digg"></span></a>
					</div>
					<div class="clr"></div>
					<div class="iconrow">
						<a href="skype:<?php echo kunena_htmlspecialchars(stripslashes($userinfo->SKYPE)); ?>?call" target="_blank" title="Call <?php echo kunena_htmlspecialchars(stripslashes($userinfo->SKYPE)); ?>"><span class="skype"></span></a>
						<a href="ymsgr:sendim?<?php echo kunena_htmlspecialchars(stripslashes($userinfo->YIM)); ?>" target="_blank" title="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->YIM)); ?>"><span class="yim"></span></a>
						<a href="aim:goim?screenname=<?php echo kunena_htmlspecialchars(stripslashes($userinfo->AIM)); ?>" target="_blank" title="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->AIM)); ?>"><span class="aim"></span></a>
						<a href="gtalk:chat?jid=<?php echo kunena_htmlspecialchars(stripslashes($userinfo->GTALK)); ?>" target="_blank" title="Chat with <?php echo kunena_htmlspecialchars(stripslashes($userinfo->GTALK)); ?>"><span class="gtalk"></span></a>
					</div>
					<div class="iconrow">
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->BLOGSPOT)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_BLOGSPOT; ?>"><span class="blogspot"></span></a>
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->FLICKR)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_FLICKR; ?>"><span class="flickr"></span></a>
						<a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->BEBO)); ?>" target="_blank" title="<?php echo _KUNENA_MYPROFILE_BEBO; ?>"><span class="bebo"></span></a>
					</div>

				</div>
				<div class="kprofile-rightcol1">
					<ul>
						<li><span class="location"></span><strong><?php echo _KUNENA_MYPROFILE_LOCATION; ?>:</strong> <?php echo kunena_htmlspecialchars(stripslashes($userinfo->location)); ?></li>
						<!--  The gender determines the suffix on the span class- gender-male & gender-female  -->
						<li><span class="gender-<?php if( $userinfo->gender == 1 ) { echo _KUNENA_MYPROFILE_MALEC; } else if ( $userinfo->gender == 2 ) { echo _KUNENA_MYPROFILE_FEMALEC; }?>"></span><strong><?php echo _KUNENA_MYPROFILE_GENDER; ?>:</strong> <?php if( $userinfo->gender == 1 ) { echo _KUNENA_MYPROFILE_MALE; } else if ( $userinfo->gender == 2 ) { echo _KUNENA_MYPROFILE_FEMALE; }?></li>
						<li><span class="birthdate"></span><strong><?php echo _KUNENA_MYPROFILE_BIRTHDATE; ?>:</strong> <?php echo kunena_htmlspecialchars(stripslashes($userinfo->birthdate)); ?> <span class="bday-remind">(<a href="#">Remind Me</a></span>)</li>
					</ul>
				</div>
			</div>
			<div class="clrline"></div>
			<div id="kprofile-rightcolbot">
				<div class="kprofile-rightcol2">
					<ul>
						<li><span class="email"></span><a href="mailto:<?php echo $juserinfo->email; ?>"><?php echo $juserinfo->email; ?></a></li>
						<li><span class="website"></span><a href="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->websitename)); ?>" target="_blank"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->websitename)); ?></a></li>
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
			<script type="text/javascript">
				$(function() {
				$("#tabs").tabs();
				});
			</script>
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">Posts</a></li>
					<li><a href="#tabs-2">Subscriptions</a></li>
					<li><a href="#tabs-3">Favorites</a></li>
				</ul>
				<div id="tabs-1">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
				<div id="tabs-2">Phasellus mattis tincidunt nibh. Cras orci urna, blandit id, pretium vel, aliquet ornare, felis. Maecenas scelerisque sem non nisl. Fusce sed lorem in enim dictum bibendum.</div>
				<div id="tabs-3">Nam dui erat, auctor a, dignissim quis, sollicitudin eu, felis. Pellentesque nisi urna, interdum eget, sagittis et, consequat vestibulum, lacus. Mauris porttitor ullamcorper augue.</div>
		</div>


		</div>
	</div>


</div>
</div>
</div>
</div>
</div>
