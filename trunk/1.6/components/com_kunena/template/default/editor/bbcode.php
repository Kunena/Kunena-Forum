<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

// Kunena bbcode editor

$kunena_config = & CKunenaConfig::getInstance ();
?>
<tr class="ksectiontableentry1">
	<?php //if ($kunena_config->enablehelppage) {
	// TODO: Help link need to point by default to a bbcode help page on kunena wiki
	?>
	<!--<td class="kleftcolumn" valign="top"><strong><?php
	echo CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&amp;func=help', _COM_BOARDCODE , NULL, 'follow', NULL, 'boardcode', 'target=\'_new\'' );
	?></strong>:
	</td>-->
	<?php //}else { 	?>
	<td class="kleftcolumn" valign="top"><strong><?php
	echo _COM_BOARDCODE;
	?></strong>:
	</td>
	<?php //} 	?>
	<td>
	<table border="0" cellspacing="0" cellpadding="0"
		class="k-postbuttonset">
		<tr>
			<td class="k-postbuttons">
				<ul id="kbbcode-toolbar"></ul>
			</td>
		</tr>
		<!-- Start extendable fields -->
		<tr>
			<td class="k-postbuttons">
			<div id="kbbcode-size-options" style="display: none;"><?php
			echo (_KUNENA_EDITOR_FONTSIZESELECTION) ;?>&nbsp;
			<select id="kbbcode-size-selector" name="kbbcode-size-selector"
				title="<?php echo _KUNENA_EDITOR_FONTSIZESELECTION;?>"
				onmouseover="$('helpbox').set('value',
								'<?php echo _KUNENA_EDITOR_HELPLINE_FONTSIZESELECTION;?>')">
				<option value="1"><?php
				echo (_SIZE_VSMALL) ;
				?></option>
				<option value="2"><?php
				echo (_SIZE_SMALL) ;
				?></option>
				<option value="3" selected="selected"><?php
				echo (_SIZE_NORMAL) ;
				?></option>
				<option value="4"><?php
				echo (_SIZE_BIG) ;
				?></option>
				<option value="5"><?php
				echo (_SIZE_VBIG) ;
				?></option>
			</select>&nbsp;
			<input type="button" name="Apply"
				value="<?php
				echo (_KUNENA_EDITOR_APPLY_BUTTON) ;
				?>"
				onclick="kbbcode.replaceSelection('[size='+$('kbbcode-size-selector').get('value')+']'+
						 kbbcode.getSelection()+'[/size]')"
				onmouseover="$('helpbox').set('value', '<?php echo _KUNENA_EDITOR_HELPLINE_FONTSIZE_APPLY;?>')" />&nbsp;
			<input type="button" name="Hide"
				value="<?php
				echo (_KUNENA_EDITOR_HIDE_BUTTON) ;
				?>"
				onclick="$('kbbcode-size-options').set('style', 'display: none;')"
				onmouseover="$('helpbox').set('value', '<?php echo _KUNENA_EDITOR_HELPLINE_FONTSIZE_HIDE;?>')" />
			</div>

<!-- 			<div id="k-color_palette" style="display: none;"><script
 				type="text/javascript">
								function change_palette() {dE('k-color_palette');}
								colorPalette('h', '4%', '15px');
							</script></div>
 -->
			<div id="link" style="display: none;"><?php
			echo (_KUNENA_EDITOR_LINK_URL) ;
			?><input
				name="url" type="text" size="40" maxlength="100" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_LINKURL )) ;
				?>')" />
			<?php
			echo (_KUNENA_EDITOR_LINK_TEXT) ;
			?><input name="text2"
				type="text" size="30" maxlength="100"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_LINKTEXT )) ;
				?>')" />
			<input type="button" name="Link" accesskey="w"
				value="<?php
				echo (_KUNENA_EDITOR_LINK_INSERT) ;
				?>"
				onclick="bbfontstyle('[url=' + this.form.url.value + ']'+ this.form.text2.value,'[/url]')"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_LINKAPPLY )) ;
				?>')" />
			</div>

			<div id="image" style="display: none;"><?php
			echo (_KUNENA_EDITOR_IMAGE_SIZE) ;
			?><input
				name="size" type="text" size="10" maxlength="10"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE )) ;
				?>')" />
			<?php
			echo (_KUNENA_EDITOR_IMAGE_URL) ;
			?><input name="url2"
				type="text" size="40" maxlength="250" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_IMAGELINKURL )) ;
				?>')" />
			<input type="button" name="Link" accesskey="p"
				value="<?php
				echo (_KUNENA_EDITOR_IMAGE_INSERT) ;
				?>"
				onclick="check_image()"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY )) ;
				?>')" />
			<script type="text/javascript">
								function check_image() {
									if (document.postform.size.value == "") {
										bbfontstyle('[img]'+ document.postform.url2.value,'[/img]');
									} else {
										bbfontstyle('[img size=' + document.postform.size.value + ']'+ document.postform.url2.value,'[/img]');
									}
								}
							</script></div>

			<div id="video" style="display: none;"><?php
			echo (_KUNENA_EDITOR_VIDEO_SIZE) ;
			?><input
				name="videosize" type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_VIDEOSIZE )) ;
				?>')" />
			<?php
			echo (_KUNENA_EDITOR_VIDEO_WIDTH) ;
			?><input name="videowidth"
				type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_VIDEOWIDTH )) ;
				?>')" />
			<?php
			echo (_KUNENA_EDITOR_VIDEO_HEIGHT) ;
			?><input
				name="videoheight" type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_VIDEOHEIGHT )) ;
				?>')" />
			<br />
			<?php
			echo (_KUNENA_EDITOR_VIDEO_PROVIDER) ;
			?> <select
				name="kvid_code1" class="kbutton"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_VIDEOPROVIDER )) ;
				?>')">
				<?php
				$vid_provider = array ('', 'AnimeEpisodes', 'Biku', 'Bofunk', 'Break', 'Clip.vn', 'Clipfish', 'Clipshack', 'Collegehumor', 'Current', 'DailyMotion', 'DivX,divx]http://', 'DownloadFestival', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'Fliptrack', 'Fliqz', 'Gametrailers', 'Gamevideos', 'Glumbert', 'GMX', 'Google', 'GooglyFoogly', 'iFilm', 'Jumpcut', 'Kewego', 'LiveLeak', 'LiveVideo', 'MediaPlayer,mediaplayer]http://', 'MegaVideo', 'Metacafe', 'Mofile', 'Multiply', 'MySpace', 'MyVideo', 'QuickTime,quicktime]http://', 'Quxiu', 'RealPlayer,realplayer]http://', 'Revver', 'RuTube', 'Sapo', 'Sevenload', 'Sharkle', 'Spikedhumor', 'Stickam', 'Streetfire', 'StupidVideos', 'Toufee', 'Tudou', 'Unf-Unf', 'Uume', 'Veoh', 'VideoclipsDump', 'Videojug', 'VideoTube', 'Vidiac', 'VidiLife', 'Vimeo', 'WangYou', 'WEB.DE', 'Wideo.fr', 'YouKu', 'YouTube' );
				foreach ( $vid_provider as $vid_type ) {
					$vid_type = explode ( ',', $vid_type );
					echo '<option value = "' . (! empty ( $vid_type [1] ) ? $vid_type [1] : JString::strtolower ( $vid_type [0] ) . '') . '">' . $vid_type [0] . '</option>';
				}
				?>
			</select> <?php
			echo (_KUNENA_EDITOR_VIDEO_ID) ;
			?><input
				name="videoid" type="text" size="11" maxlength="11"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_VIDEOID )) ;
				?>')" />
			<input type="button" name="Video" accesskey="p"
				value="<?php
				echo (_KUNENA_EDITOR_IMAGE_INSERT) ;
				?>"
				onclick="check_video('video1')"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_VIDEOAPPLY1 )) ;
				?>')" /><br />
			<?php
			echo (_KUNENA_EDITOR_VIDEO_URL) ;
			?><input name="videourl"
				type="text" size="30" maxlength="250" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_VIDEOURL )) ;
				?>')" />
			<input type="button" name="Video" accesskey="p"
				value="<?php
				echo (_KUNENA_EDITOR_IMAGE_INSERT) ;
				?>"
				onclick="check_video('video2')"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( _KUNENA_EDITOR_HELPLINE_VIDEOAPPLY2 )) ;
				?>')" />
			<script type="text/javascript">
								function check_video(art) {
									var video;
									if (document.postform.videosize.value != "") {video = " size=" + document.postform.videosize.value;}
									else {video="";}
									if (document.postform.videowidth.value != "") {video = video + " width=" + document.postform.videowidth.value;}
									if (document.postform.videoheight.value != "") {video = video + " height=" + document.postform.videoheight.value;}
									if (art=='video1'){
									if (document.postform.fb_vid_code1.value != "") {video = video + " type=" + document.postform.fb_vid_code1.options[document.postform.fb_vid_code1.selectedIndex].value;}
									bbfontstyle('[video' + video + ']'+ document.postform.videoid.value,'[/video]');}
									else {bbfontstyle('[video' + video + ']'+ document.postform.videourl.value,'[/video]');}
								}
							</script></div>
			</td>
		</tr>
		<tr>
			<td class="k-postbuttons">
			<div id="smilie"><?php
			$kunena_db = &JFactory::getDBO ();
			$kunena_db->setQuery ( "SELECT code, location, emoticonbar FROM #__fb_smileys ORDER BY id" );
			$set = $kunena_db->loadAssocList ();
			check_dberror ( "Unable to fetch smileys." );
			$this->kunena_emoticons_rowset = array ();
			foreach ( $set as $smilies ) {
				$key_exists = false;
				foreach ( $this->kunena_emoticons_rowset as $check ) { //checks if the smiley (location) already exists with another code
					if ($check ['location'] == $smilies ['location']) {
						$key_exists = true;
					}
				}
				if ($key_exists == false) {
					$this->kunena_emoticons_rowset [] = array ('code' => $smilies ['code'], 'location' => $smilies ['location'], 'emoticonbar' => $smilies ['emoticonbar'] );
				}
			}
			reset ( $this->kunena_emoticons_rowset );
			foreach ( $this->kunena_emoticons_rowset as $data ) {
				echo '<img class="btnImage" src="' . KUNENA_URLEMOTIONSPATH . $data ['location'] . '" border="0" alt="' . $data ['code'] . ' " title="' . $data ['code'] . ' " onclick="kbbcode.insert(\''. $data ['code'] .' \', \'after\', true);" style="cursor:pointer"/> ';
			}
			?>
			</div>

			</td>
		</tr>
		<!-- end of extendable fiels -->
		<tr>
			<td class="kposthint"><input type="text" name="helpbox" id="helpbox"
				size="45" class="kinputbox" maxlength="100"
				value="<?php
				echo (_KUNENA_EDITOR_HELPLINE_HINT) ;
				?>" /></td>
		</tr>
	</table>
	</td>
</tr>

<tr class="ksectiontableentry2">
	<td valign="top" class="kleftcolumn"><strong><?php
	echo (_MESSAGE) ;
	?></strong>:<br />
	<b onclick="size_messagebox(100);" style="cursor: pointer">(+)</b><b> /
	</b><b onclick="size_messagebox(-100);" style="cursor: pointer">(-)</b> <?php
	if ($kunena_config->disemoticons != 1) {
		?>



	<?php
	}
	?></td>

	<td valign="top"><textarea cols="60" rows="6" class="ktxtarea"
		name="message" id="kbbcode-message"><?php
		echo kunena_htmlspecialchars ( $this->message_text, ENT_QUOTES );
		?></textarea>
	<?php
	if ($this->kunena_editmode) {
		// Moderator edit area
		?>
	<fieldset><legend><?php
		echo (_KUNENA_EDITING_REASON) ?></legend> <input
		name="modified_reason" size="40" maxlength="200" type="text" /><br />

	</fieldset>
	<?php
	}
	?></td>
</tr>