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
<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
	<?php //if ($kunena_config->enablehelppage) {
	// TODO: Help link need to point by default to a bbcode help page on kunena wiki
	?>
	<!--<td class="kleftcolumn" valign="top"><strong><?php
	echo CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&amp;func=help', JText::_('COM_KUNENA_BOARDCODE') , NULL, 'follow', NULL, 'boardcode', 'target=\'_new\'' );
	?></strong>:
	</td>-->
	<?php //}else { 	?>
	<td class="kleftcolumn" valign="top"><strong><?php
	echo JText::_('COM_KUNENA_BOARDCODE');
	?></strong></td>
	<?php //} 	?>
	<td>
	<table class="k-postbuttonset">
		<tr>
			<td class="k-postbuttons">
			<ul id="kbbcode-toolbar"></ul>
			</td>
		</tr>
		<!-- Start extendable secondary toolbar -->
		<tr>
			<td class="k-postbuttons">
			<div id="kbbcode-size-options" style="display: none;"><span
				class="kmsgtext_xs" title='[size=1]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XS');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext_s" title='[size=2]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_S');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext_m" title='[size=3]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_M');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext_l" title='[size=4]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_L');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext_xl" title='[size=5]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XL');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext_xxl" title='[size=6]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XXL');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span></div>
			<div id="kbbcode-colorpalette" style="display: none;"><script
				type="text/javascript">kGenerateColorPalette('4%', '15px');</script>
			</div>

			<div id="kbbcode-link-options" style="display: none;"><?php
			echo JText::_('COM_KUNENA_EDITOR_LINK_URL');
			?>&nbsp;<input id="kbbcode-link_url" name="url" type="text" size="40"
				maxlength="255" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKURL');
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_LINK_TEXT');
			?>&nbsp;<input name="text2" id="kbbcode-link_text" type="text"
				size="30" maxlength="100"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKTEXT');
				?>')" /> <input type="button" name="insterLink"
				value="<?php
				echo JText::_('COM_KUNENA_EDITOR_LINK_INSERT');
				?>"
				onclick="kbbcode.replaceSelection('[url=' + $('kbbcode-link_url').get('value') + ']' + $('kbbcode-link_text').get('value') + '[/url]'); kToggleOrSwap('kbbcode-link-options');"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKAPPLY');
				?>')" /></div>

			<div id="kbbcode-image-options" style="display: none;"><?php
			echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_SIZE');
			?>&nbsp;<input id="kbbcode-image_size" name="size" type="text"
				size="10" maxlength="10"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo (addslashes ( JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE') )) ;
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_URL');
			?>&nbsp;<input name="url2" id="kbbcode-image_url" type="text"
				size="40" maxlength="250" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKURL');
				?>')" />&nbsp; <input type="button" name="Link"
				value="<?php
				echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_INSERT');
				?>"
				onclick="kInsertImageLink()"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY');
				?>')" /></div>

			<div id="kbbcode-poll-options" style="display: none;">
			<?php
			//Check if the poll is allowed and check if the polls is enabled
			if ($kunena_config->pollenabled) {
				$display_poll = CKunenaPolls::get_message_parent($this->id, $this->kunena_editmode);
				if($display_poll) {
					if (!empty($msg_cat->allow_polls) || $this->catid == '0')	{
						if (!isset($this->polldatasedit[0]->polltimetolive)) {
							$this->polldatasedit[0]->polltimetolive = '0000-00-00 00:00:00';
						}
						CKunenaPolls::call_js_poll_edit($this->kunena_editmode, $this->id);
						$html_poll_edit = CKunenaPolls::get_input_poll($this->kunena_editmode, $this->id, $this->polldatasedit);
						JHTML::_('behavior.calendar');
			?><span id="kpoll_not_allowed"></span>
			<div id="kpoll_hide_not_allowed">
			<?php echo JText::_('COM_KUNENA_POLL_TITLE');
			?>&nbsp;<input type="text" id="kpolltitle" name="poll_title"
				maxlength="25"
				value="<?php if(isset($this->polldatasedit[0]->title)) { echo $this->polldatasedit[0]->title; } ?>"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLTITLE'); ?>')" />
			<?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?>&nbsp;
			<input class="inputbox" type="text" maxlength="15"
				name="poll_time_to_live" id="poll_time_to_live"
				style="display: none"
				value="<?php echo $this->polldatasedit[0]->polltimetolive; ?>" /> <img
				src="templates/system/images/calendar.png" alt="Calendar"
				onclick="showCalendar('poll_time_to_live','%Y-%m-%d');$('poll_time_to_live').removeProperty('style');"
				onmouseover="javascript:$('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLLIFESPAN'); ?>')" />
			<img id="kbutton_poll_add"
				src="<?php echo JURI::root(); ?>/administrator/images/tick.png"
				onmouseover="javascript:$('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ADDPOLLOPTION'); ?>')" />
			<img id="kbutton_poll_rem"
				src="<?php echo JURI::root(); ?>/administrator/images/publish_x.png"
				onmouseover="javascript:$('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_REMPOLLOPTION'); ?>')" />
			</div>
			<?php
			if(!empty( $html_poll_edit )) {
							echo $html_poll_edit;
						}
						?>
			<input type="hidden" name="nb_options_allowed"
				id="nb_options_allowed"
				value="<?php
						echo $kunena_config->pollnboptions;
						?>"> <input
				type="hidden" name="number_total_options" id="numbertotal"
				value="<?php
						if (! empty ( $this->polloptionstotal )) {
							echo $this->polloptionstotal;
						}
						?>">

			<?php
					}
				}
			}
			?>
			</div>

			<div id="kbbcode-video-options" style="display: none;"><?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_SIZE');
			?><input id="kvideosize"
				name="videosize" type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOSIZE');
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_WIDTH');
			?><input id="kvideowidth" name="videowidth"
				type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOWIDTH');
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_HEIGHT');
			?><input id="kvideoheight"
				name="videoheight" type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOHEIGHT');
				?>')" /> <br />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER');
			?> <select id="kvideoprovider"
				name="kvid_code1" class="kbutton"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOPROVIDER');
				?>')">
				<?php
				$vid_provider = array ('', 'AnimeEpisodes', 'Biku', 'Bofunk', 'Break', 'Clip.vn', 'Clipfish', 'Clipshack', 'Collegehumor', 'Current', 'DailyMotion', 'DivX,divx]http://', 'DownloadFestival', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'Fliptrack', 'Fliqz', 'Gametrailers', 'Gamevideos', 'Glumbert', 'GMX', 'Google', 'GooglyFoogly', 'iFilm', 'Jumpcut', 'Kewego', 'LiveLeak', 'LiveVideo', 'MediaPlayer,mediaplayer]http://', 'MegaVideo', 'Metacafe', 'Mofile', 'Multiply', 'MySpace', 'MyVideo', 'QuickTime,quicktime]http://', 'Quxiu', 'RealPlayer,realplayer]http://', 'Revver', 'RuTube', 'Sapo', 'Sevenload', 'Sharkle', 'Spikedhumor', 'Stickam', 'Streetfire', 'StupidVideos', 'Toufee', 'Tudou', 'Unf-Unf', 'Uume', 'Veoh', 'VideoclipsDump', 'Videojug', 'VideoTube', 'Vidiac', 'VidiLife', 'Vimeo', 'WangYou', 'WEB.DE', 'Wideo.fr', 'YouKu', 'YouTube' );
				foreach ( $vid_provider as $vid_type ) {
					$vid_type = explode ( ',', $vid_type );
					echo '<option value = "' . (! empty ( $vid_type [1] ) ? $vid_type [1] : JString::strtolower ( $vid_type [0] ) . '') . '">' . $vid_type [0] . '</option>';
				}
				?>
			</select> <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_ID');
			?><input id="kvideoid"
				name="videoid" type="text" size="11" maxlength="11"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOID');
				?>')" />
			<input id="kbutton_addvideo1" type="button" name="Video" accesskey="p"
				onclick="kInsertVideo1()"
				value="<?php
						echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT');
						?>"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY1');
				?>')" /><br />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_URL');
			?><input id="kvideourl" name="videourl"
				type="text" size="30" maxlength="250" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOURL');
				?>')" />
			<input id="kbutton_addvideo2" type="button" name="Video" accesskey="p"
				onclick="kInsertVideo2()"
				value="<?php
						echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT');
						?>"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY2');
				?>')" />
			</div>
			<div id="kbbcode-map-options" style="display: none;"><?php
			echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_URL');
			?>&nbsp;<input name="map-url"
				id="kbbcode-map_url" type="text" size="40" maxlength="250" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKURL');
				?>')" />&nbsp;
			<input type="button" name="Link"
				value="<?php
				echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_INSERT');
				?>"
				onclick="kInsertMapLink()"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY');
				?>')" />
			</div>
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
		<!-- end of extendable secondary toolbar -->
		<tr>
			<td class="kposthint"><input type="text" name="helpbox" id="helpbox"
				size="45" class="kinputbox" maxlength="100"
				value="<?php
				echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_HINT')) ;
				?>" /></td>
		</tr>
	</table>
	</td>
</tr>

<tr class="ksectiontableentry<?php echo 1 + $this->k^=1;?>">
	<td valign="top" class="kleftcolumn"><strong><?php
		echo (JText::_('COM_KUNENA_MESSAGE')) ;
	// TODO: Replace Enlarge and Shrink with icons
	?></strong><br />
	<span class="ksmalltext" onclick="kGrowShrinkMessage(100);"
		style="cursor: pointer">Enlarge</span>&nbsp;/&nbsp; <span
		class="ksmalltext" onclick="kGrowShrinkMessage(-100);"
		style="cursor: pointer">Shrink</span></td>

	<td valign="top"><textarea class="ktxtarea required" name="message"
		id="kbbcode-message"><?php
			echo kunena_htmlspecialchars ( $this->message_text, ENT_QUOTES );
			?></textarea>
	<?php
	//
	// Add an empty div for the preview.The class name will be set by js depending on horizontal or vertical split
	//
	?>
	<!-- Hidden preview placeholder -->
	<div id="kbbcode-preview" style="display: none;"></div>
	<?php
	if ($this->kunena_editmode) {
		// Moderator edit area
		?>
	<fieldset style="width:585px;"><legend><?php
		echo (JText::_('COM_KUNENA_EDITING_REASON')) ?></legend> <input
		name="modified_reason" size="40" maxlength="200" type="text" /><br />
	</fieldset>
	<?php
	}
	?>
	</td>
</tr>