<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<form enctype="multipart/form-data" name="postform" method="post" id="postform" class="postform form-validate" action="#">

	<div class="ksection">
		<h2 class="kheader"><span>Reply Post: Post Name</span></h2>

		<ul class="kform kpostmessage clearfix">

			<li style="display: none" id="kanynomous-check" class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="kanonymous">
						Anonymous Post
					</label>
				</div>
				<div class="kform-field">
					<input type="checkbox" value="1" name="anonymous" id="kanonymous" class="hasTip" title="Anonymous Post :: Check if you want post as Anonymous" />
					<div class="kform-note">This post contains sensitive information. Remove all user information from thispost.</div>
				</div>
			</li>

			<li style="display: none" id="kanynomous-check-name" class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="kauthorname">
						Name
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="" maxlength="35" class="kinputbox postinput required hasTip" size="35" name="authorname" id="kauthorname" disabled="disabled" title="Name :: Enter Your Name" />
				</div>
			</li>

			<li id="kpost-subject" class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="subject">
						Subject
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="Post Name" maxlength="100" size="35" id="subject" name="subject" class="kinputbox postinput required hasTip" title="Subject :: Enter Subject" />
				</div>
			</li>
			<li id="kpost-topicicons" class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="topic_emoticon_default">
						Topic icon
					</label>
				</div>
				<div class="kform-field">
					<ul>
						<li class="hasTip" title="Topic icon :: Default">
							<input type="radio" checked="checked" value="0" name="topic_emoticon" id="topic_emoticon_default"  />
							<label for="topic_emoticon_default" ><img border="0" alt="" src="images/icons/topic-default.gif" /></label>
						</li>
						<li class="hasTip" title="Topic icon :: Exclamation">
							<input type="radio" value="1" name="topic_emoticon" id="topic_emoticon_exclamation" />
							<label for="topic_emoticon_exclamation"><img border="0" alt="" src="images/icons/topic-exclamation.png" /></label>
						</li>
						<li class="hasTip" title="Topic icon :: Question">
							<input type="radio" value="2" name="topic_emoticon" id="topic_emoticon_question" />
							<label for="topic_emoticon_question"><img border="0" alt="" src="images/icons/topic-question.png" /></label>
						</li>
						<li class="hasTip" title="Topic icon :: Mark">
							<input type="radio" value="3" name="topic_emoticon" id="topic_emoticon_mark" />
							<label for="topic_emoticon_mark"><img border="0" alt="" src="images/icons/topic-mark.png" /></label>
						</li>
						<li class="hasTip" title="Topic icon :: Love">
							<input type="radio" value="4" name="topic_emoticon" id="topic_emoticon_love" />
							<label for="topic_emoticon_love"><img border="0" alt="" src="images/icons/topic-love.png" /></label>
						</li>
						<li class="hasTip" title="Topic icon :: Grin">
							<input type="radio" value="5" name="topic_emoticon" id="topic_emoticon_grin" />
							<label for="topic_emoticon_grin"><img border="0" alt="" src="images/icons/topic-grin.png" /></label>
						</li>
						<li class="hasTip" title="Topic icon :: Shock">
							<input type="radio" value="6" name="topic_emoticon" id="topic_emoticon_shock" />
							<label for="topic_emoticon_shock"><img border="0" alt="" src="images/icons/topic-shock.png" /></label>
						</li>
						<li class="hasTip" title="Topic icon :: Smile">
							<input type="radio" value="7" name="topic_emoticon" id="topic_emoticon_smile" />
							<label for="topic_emoticon_smile"><img border="0" alt="" src="images/icons/topic-smile.png" /></label>
						</li>
					</ul>
				</div>
			</li>

			<li id="kpost-toolbar" class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label>
						Boardcode
					</label>
				</div>
				<div class="kform-field">

					<div class="kpostbuttonset">
						<div class="kpostbuttons">
							<ul id="kbbcode-toolbar">
								<li></li>
							</ul>
						</div>
						<div class="kpostbuttons">
							<div style="display: none;" id="kbbcode-size-options">
								<span onmouseover="javascript:$('helpbox').set('value', 'Fontsize: [size=1]text size[/size] - Tip: sizes range from 1 to 6')" title="[size=1]" class="kmsgtext-xs">&nbsp;Sample Text&nbsp;</span>
								<span onmouseover="javascript:$('helpbox').set('value', 'Fontsize: [size=2]text size[/size] - Tip: sizes range from 1 to 6')" title="[size=2]" class="kmsgtext-s">&nbsp;Sample Text&nbsp;</span>
								<span onmouseover="javascript:$('helpbox').set('value', 'Fontsize: [size=3]text size[/size] - Tip: sizes range from 1 to 6')" title="[size=3]" class="kmsgtext-m">&nbsp;Sample Text&nbsp;</span>
								<span onmouseover="javascript:$('helpbox').set('value', 'Fontsize: [size=4]text size[/size] - Tip: sizes range from 1 to 6')" title="[size=4]" class="kmsgtext-l">&nbsp;Sample Text&nbsp;</span>
								<span onmouseover="javascript:$('helpbox').set('value', 'Fontsize: [size=5]text size[/size] - Tip: sizes range from 1 to 6')" title="[size=5]" class="kmsgtext-xl">&nbsp;Sample Text&nbsp;</span>
								<span onmouseover="javascript:$('helpbox').set('value', 'Fontsize: [size=6]text size[/size] - Tip: sizes range from 1 to 6')" title="[size=6]" class="kmsgtext-xxl">&nbsp;Sample Text&nbsp;</span>
							</div>
							<div style="display: none;" id="kbbcode-colorpalette">
								<script type="text/javascript">kGenerateColorPalette('4%', '15px');</script>
							</div>
							<div style="display: none;" id="kbbcode-link-options">
								URL&nbsp; <input type="text" onmouseover="javascript:$('helpbox').set('value', 'Link: URL of the link')" value="http://" size="40" name="url" id="kbbcode-link_url" />
								Text&nbsp; <input type="text" onmouseover="javascript:$('helpbox').set('value', 'Link: Text / Description of the link')" maxlength="150" size="30" id="kbbcode-link_text" name="text2" />
								<input type="button" onmouseover="javascript:$('helpbox').set('value', 'Link: Apply link')" onclick="kbbcode.replaceSelection('[url=' + $('kbbcode-link_url').get('value') + ']' + $('kbbcode-link_text').get('value') + '[/url]'); kToggleOrSwap('kbbcode-link-options');" value="Insert" name="insterLink" />
							</div>
							<div style="display: none;" id="kbbcode-image-options">Size&nbsp;
								<input type="text" onmouseover="javascript:$('helpbox').set('value', 'Image link: Size')" maxlength="10" size="10" name="size" id="kbbcode-image_size" />
								URL&nbsp; <input type="text" onmouseover="javascript:$('helpbox').set('value', 'Image link: URL of the image link')" value="http://" size="40" id="kbbcode-image_url" name="url2" />&nbsp;
								<input type="button" onmouseover="javascript:$('helpbox').set('value', 'Image link: Apply link')" onclick="kInsertImageLink()" value="Insert" name="Link" />
							</div>

							<div style="display: none;" id="kbbcode-poll-options">
								<span id="kpoll-not-allowed">&nbsp;</span>
								<div style="" id="kpoll-hide-not-allowed">
									<label for="kpoll-title" class="kpoll-title-lbl">Poll title</label>
									<input type="text" onmouseover="javascript:$('helpbox').set('value', 'Poll: Set a title for the poll')" value="" size="40" maxlength="100" id="kpoll-title" name="poll_title" class="kinputbox" />
									<img alt="Add Poll Option" onmouseover="javascript:$('helpbox').set('value', 'Poll: Add an option')" src="images/icons/poll_add_options.png" id="kbutton-poll-add" />
									<img alt="Remove Poll Option" onmouseover="javascript:$('helpbox').set('value', 'Poll: Remove an option')" src="images/icons/poll_rem_options.png" id="kbutton-poll-rem" />
									<label for="kpoll-time-to-live" class="kpoll-term-lbl">Poll life span (optional)</label>
									<input type="text" onmouseover="javascript:$('helpbox').set('value', 'Poll: Add a life time for the poll (optional)')" value="0000-00-00 00:00:00" id="kpoll-time-to-live" name="poll_time_to_live" />
									<img id="kpoll-time-to-live_img" alt="calendar" src="images/calendar.png" class="calendar" />
								</div>
								<input type="hidden" value="4" id="nb_options_allowed" name="nb_options_allowed" />
								<input type="hidden" value="" id="numbertotal" name="number_total_options" />
							</div>

							<div style="display: none;" id="kbbcode-code-options">
								Code
								<select onmouseover="javascript:$('helpbox').set('value', 'Select a programming language')" class="kbutton" name="kcode_type" id="kcodetype">
									<option value=""></option>
									<option value="css">css</option>
									<option value="diff">diff</option>
									<option value="html4strict">html4strict</option>
									<option value="ini">ini</option>
									<option value="javascript">javascript</option>
									<option value="mysql">mysql</option>
									<option value="php-brief">php-brief</option>
									<option value="php">php</option>
									<option value="sql">sql</option>
									<option value="xml">xml</option>
								</select>
								<input type="button" onmouseover="javascript:$('helpbox').set('value', 'Insert code')" value="Insert" onclick="kInsertCode()" name="Code" id="kbutton_addcode">
							</div>

							<div style="display: none;" id="kbbcode-video-options">
								Size
								<input type="text" onmouseover="javascript:$('helpbox').set('value', 'Video: Size of the video')" maxlength="5" size="5" name="videosize" id="kvideosize" />
								Width
								<input type="text" onmouseover="javascript:$('helpbox').set('value', 'Video: Width of the video')" maxlength="5" size="5" name="videowidth" id="kvideowidth" />
								Height
								<input type="text" onmouseover="javascript:$('helpbox').set('value', 'Video: Height of the video')" maxlength="5" size="5" name="videoheight" id="kvideoheight" /> <br />
								Provider
								<select onmouseover="javascript:$('helpbox').set('value', 'Video: Select video provider')" class="kbutton" name="provider" id="kvideoprovider">
									<option value=""></option>
									<option value="animeepisodes">AnimeEpisodes</option>
									<option value="biku">Biku</option>
									<option value="bofunk">Bofunk</option>
									<option value="break">Break</option>
									<option value="clip.vn">Clip.vn</option>
									<option value="clipfish">Clipfish</option>
									<option value="clipshack">Clipshack</option>
									<option value="collegehumor">Collegehumor</option>
									<option value="current">Current</option>
									<option value="dailymotion">DailyMotion</option>
									<option value="divx]http://">DivX</option>
									<option value="downloadfestival">DownloadFestival</option>
									<option value="flash]http://">Flash</option>
									<option value="flashvars param=]http://">FlashVars</option>
									<option value="fliptrack">Fliptrack</option>
									<option value="fliqz">Fliqz</option>
									<option value="gametrailers">Gametrailers</option>
									<option value="gamevideos">Gamevideos</option>
									<option value="glumbert">Glumbert</option>
									<option value="gmx">GMX</option>
									<option value="google">Google</option>
									<option value="googlyfoogly">GooglyFoogly</option>
									<option value="ifilm">iFilm</option>
									<option value="jumpcut">Jumpcut</option>
									<option value="kewego">Kewego</option>
									<option value="liveleak">LiveLeak</option>
									<option value="livevideo">LiveVideo</option>
									<option value="mediaplayer]http://">MediaPlayer</option>
									<option value="megavideo">MegaVideo</option>
									<option value="metacafe">Metacafe</option>
									<option value="mofile">Mofile</option>
									<option value="multiply">Multiply</option>
									<option value="myspace">MySpace</option>
									<option value="myvideo">MyVideo</option>
									<option value="quicktime]http://">QuickTime</option>
									<option value="quxiu">Quxiu</option>
									<option value="realplayer]http://">RealPlayer</option>
									<option value="revver">Revver</option>
									<option value="rutube">RuTube</option>
									<option value="sapo">Sapo</option>
									<option value="sevenload">Sevenload</option>
									<option value="sharkle">Sharkle</option>
									<option value="spikedhumor">Spikedhumor</option>
									<option value="stickam">Stickam</option>
									<option value="streetfire">Streetfire</option>
									<option value="stupidvideos">StupidVideos</option>
									<option value="toufee">Toufee</option>
									<option value="tudou">Tudou</option>
									<option value="unf-unf">Unf-Unf</option>
									<option value="uume">Uume</option>
									<option value="veoh">Veoh</option>
									<option value="videoclipsdump">VideoclipsDump</option>
									<option value="videojug">Videojug</option>
									<option value="videotube">VideoTube</option>
									<option value="vidiac">Vidiac</option>
									<option value="vidilife">VidiLife</option>
									<option value="vimeo">Vimeo</option>
									<option value="wangyou">WangYou</option>
									<option value="web.de">WEB.DE</option>
									<option value="wideo.fr">Wideo.fr</option>
									<option value="youku">YouKu</option>
									<option value="youtube">YouTube</option>
								</select>
								ID
								<input type="text" onmouseover="javascript:$('helpbox').set('value', 'Video: ID of the video - you can see it in the video URL')" maxlength="14" size="11" name="videoid" id="kvideoid" />
								<input type="button" onmouseover="javascript:$('helpbox').set('value', 'Video: [video size=100 width=480 height=360 provider=clipfish]3423432[/video]')" value="Insert video" onclick="kInsertVideo1()" accesskey="p" name="Video" id="kbutton_addvideo1" /><br />
								URL
								<input type="text" onmouseover="javascript:$('helpbox').set('value', 'Video: URL of the video')" value="http://" maxlength="250" size="30" name="videourl" id="kvideourl" />
								<input type="button" onmouseover="javascript:$('helpbox').set('value', 'Video: [video size=100 width=480 height=360]http://myvideodomain.com/myvideo[/video]')" value="Insert video" onclick="kInsertVideo2()" accesskey="p" name="Video" id="kbutton_addvideo2" />
							</div>
						</div>
						<div class="kpostbuttons">
							<div id="ksmiliebar">
								<img onclick="kbbcode.insert('B) ', 'after', true);" title="B) " alt="B) " src="images/emoticons/cool.png" class="btnImage" />
								<img onclick="kbbcode.insert(':( ', 'after', true);" title=":( " alt=":( " src="images/emoticons/sad.png" class="btnImage" />
								<img onclick="kbbcode.insert(':) ', 'after', true);" title=":) " alt=":) " src="images/emoticons/smile.png" class="btnImage" />
								<img onclick="kbbcode.insert(':laugh: ', 'after', true);" title=":laugh: " alt=":laugh: "src="images/emoticons/laughing.png" class="btnImage" />
								<img onclick="kbbcode.insert(':cheer: ', 'after', true);" title=":cheer: " alt=":cheer: " src="images/emoticons/cheerful.png" class="btnImage" />
								<img onclick="kbbcode.insert(';) ', 'after', true);" title=";) " alt=";) " src="images/emoticons/wink.png" class="btnImage" />
								<img onclick="kbbcode.insert(':P ', 'after', true);" title=":P " alt=":P " src="images/emoticons/tongue.png" class="btnImage" />
								<img onclick="kbbcode.insert(':angry: ', 'after', true);" title=":angry: " alt=":angry: " src="images/emoticons/angry.png" class="btnImage" />
								<img onclick="kbbcode.insert(':unsure: ', 'after', true);" title=":unsure: " alt=":unsure: " src="images/emoticons/unsure.png" class="btnImage" />
								<img onclick="kbbcode.insert(':ohmy: ', 'after', true);" title=":ohmy: " alt=":ohmy: " src="images/emoticons/shocked.png" class="btnImage" />
								<img onclick="kbbcode.insert(':huh: ', 'after', true);" title=":huh: " alt=":huh: " src="images/emoticons/wassat.png" class="btnImage" />
								<img onclick="kbbcode.insert(':dry: ', 'after', true);" title=":dry: " alt=":dry: " src="images/emoticons/ermm.png" class="btnImage" />
								<img onclick="kbbcode.insert(':lol: ', 'after', true);" title=":lol: " alt=":lol: " src="images/emoticons/grin.png" class="btnImage" />
								<img onclick="kbbcode.insert(':sick: ', 'after', true);" title=":sick: " alt=":sick: " src="images/emoticons/sick.png" class="btnImage" />
								<img onclick="kbbcode.insert(':silly: ', 'after', true);" title=":silly: " alt=":silly: " src="images/emoticons/silly.png" class="btnImage" />
								<img onclick="kbbcode.insert(':blink: ', 'after', true);" title=":blink: " alt=":blink: " src="images/emoticons/blink.png" class="btnImage" />
								<img onclick="kbbcode.insert(':blush: ', 'after', true);" title=":blush: " alt=":blush: " src="images/emoticons/blush.png" class="btnImage" />
								<img onclick="kbbcode.insert(':kiss: ', 'after', true);" title=":kiss: " alt=":kiss: " src="images/emoticons/kissing.png" class="btnImage" />
								<img onclick="kbbcode.insert(':woohoo: ', 'after', true);" title=":woohoo: " alt=":woohoo: " src="images/emoticons/w00t.png" class="btnImage" />
								<img onclick="kbbcode.insert(':side: ', 'after', true);" title=":side: " alt=":side: " src="images/emoticons/sideways.png" class="btnImage" />
								<img onclick="kbbcode.insert(':S ', 'after', true);" title=":S " alt=":S " src="images/emoticons/dizzy.png" class="btnImage" />
								<img onclick="kbbcode.insert(':evil: ', 'after', true);" title=":evil: " alt=":evil: " src="images/emoticons/devil.png" class="btnImage" />
								<img onclick="kbbcode.insert(':whistle: ', 'after', true);" title=":whistle: " alt=":whistle: " src="images/emoticons/whistling.png" class="btnImage" />
								<img onclick="kbbcode.insert(':pinch: ', 'after', true);" title=":pinch: " alt=":pinch: " src="images/emoticons/pinch.png" class="btnImage" />
							</div>
						</div>
						<div class="kposthint">
							<input type="text" value="bbCode Help - Tip: bbCode can be used on selected text!" maxlength="100" disabled="disabled" class="kinputbox" size="45" id="helpbox" name="helpbox" />
						</div>
					</div>

				</div>
			</li>

			<li class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="kbbcode-message">
						<strong>Message</strong><br />
						<span style="cursor: pointer;" onclick="kGrowShrinkMessage(100);" class="ks">Enlarge</span>&nbsp;/&nbsp;
						<span style="cursor: pointer;" onclick="kGrowShrinkMessage(-100);" class="ks">Shrink</span>
					</label>
				</div>
				<div class="kform-field">
					<textarea cols="50" rows="10" id="kbbcode-message" name="message" class="ktxtarea required hasTip" title="Message :: Enter your message here"></textarea>
					<!-- Hidden preview placeholder -->
					<div style="display: none;" id="kbbcode-preview"></div>
				</div>
			</li>

			<li class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="kupload">
						Attachments
					</label>
				</div>
				<div class="kform-field">
					<div id="kattachment-id" class="kattachment">
						<span class="kattachment-id-container"></span>
						<input class="kfile-input-textbox" type="text" readonly="readonly" />
						<div class="kfile-hide hasTip" title="Allowed file extensions::jpg,jpeg,gif,png,zip,txt,doc,gz,tgz" >
							<input type="button" value="Add File" class="kfile-input-button kbutton" />
							<input id="kupload" class="kfile-input hidden" name="kattachment" type="file" />
						</div>
						<a href="#" class="kattachment-remove kbutton" style="display: none">Remove File</a>
						<a href="#" class="kattachment-insert kbutton" style="display: none">Insert</a>
					</div>
				</div>
			</li>

			<li class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="mytags">
						My Own Tags
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="" maxlength="100" size="35" id="mytags" name="mytags" class="kinputbox postinput hasTip" title="My Own Tags :: Separate with comma" />
				</div>
			</li>

			<li class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="subscribe-me">
						Subscribe
					</label>
				</div>
				<div class="kform-field">
					<label for="subscribe-me" class="hasTip" title="Subscribe :: Check this box to be notified of replies to this topic"><input type="checkbox" value="1" name="subscribeMe" id="subscribe-me" /> <i>Check this box to be notified of replies to this topic.</i></label>
				</div>
			</li>
		</ul>

		<div class="kpost-buttons">
			<button class="kbutton" type="submit" title="Click here to submit your category"> Save </button>
			<button class="kbutton" type="button" title="Click here to cancel your message" onclick="javascript:window.history.back();"> Cancel </button>
		</div>

	</div>
	<script type="text/javascript">document.postform.subject.focus();</script>
</form>


<div class="ksection">
	<h2 class="kheader"><a rel="khistory">Topic History of: Post Name</a></h2>
	<p class="kheader-desc">Max. showing the last 10 posts  -  <em>(Last post first)</em></p>
	<div id="khistory" class="kdetailsbox">
		<ul class="ktopic-taglist">
			<li class="ktopic-taglist-title">Topic Tags:</li>
			<li><a href="#">templates</a></li>
			<li><a href="#">design</a></li>
			<li><a href="#">css</a></li>
			<li><a href="#">colors</a></li>
			<li><a href="#">help</a></li>
			<li class="ktopic-taglist-edit"><a href="#">Add/edit tags</a></li>
		</ul>
		<ul class="kposts">
			<li>
				<ul class="kposthead">
					<li class="kposthead-replytitle"><h3>Posted 13 hours, 44 minutes ago</h3></li>
					<li class="kposthead-postid" ><a href="#" title="Post #2 in this thread">#2</a></li>
				</ul>
				<ul class="kpost-user-details">
					<li class="kcategory-smavatar"><a title="View Username's Profile" href="#"><img alt="Username's Avatar" src="images/avatar.png"></a></li>
					<li class="kcategory-smdetails kauthor"><a title="View Author's Profile" href="#">Author Name</a></li>
				</ul>
				<div class="kpost-container">
					<ul class="kpost-post-body">
						<li class="kpost-body">
							<p>Ea falli mucius conclusionemque eum, et iudico omittam periculis has. Est lorem utroque deleniti te, ea nec fugit ornatus, oratio partem commune ut qui. Movet nobis oportere nam eu, eam suas ipsum nulla ei, iudico impetus reprehendunt has ex.</p>
							<p>No modo dico adversarium vix, veniam verear te vis. Nusquam intellegam reprehendunt at nam, vis ludus dolor no. Usu timeam invenire moderatius cu, cum tale harum alterum ne, an pro senserit euripidis efficiendi. </p>
							<p>In delenit lobortis cotidieque eum, usu ea tollit audiam. Qui diam aperiam liberavisse te, ei duo vivendo placerat iudicabit, pri ex epicurei placerat sadipscing. </p>
						</li>
					</ul>
				</div>
			</li>
			<li>
				<ul class="kposthead">
					<li class="kposthead-replytitle"><h3>4 days, 3 hours ago</h3></li>
					<li class="kposthead-postid" ><a href="#" title="Post #1 in this thread">#1</a></li>
				</ul>
				<ul class="kpost-user-details">
					<li class="kcategory-smavatar"><a title="View Username's Profile" href="#"><img alt="Username's Avatar" src="images/avatar.png"></a></li>
					<li class="kcategory-smdetails kauthor"><a title="View Author's Profile" href="#">Author Name</a></li>
				</ul>
				<div class="kpost-container">
					<ul class="kpost-post-body">
						<li class="kpost-body">
							<p>Ne nam eros accumsan qualisque, eum persius habemus propriae id.</p>
						</li>
					</ul>
				</div>
			</li>
		</ul>
	</div>
</div>