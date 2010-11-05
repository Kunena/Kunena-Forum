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
require_once (JPATH_COMPONENT . DS . 'lib' .DS. 'kunena.poll.class.php');
$kunena_poll =& CKunenaPolls::getInstance();
$kunena_config = KunenaFactory::getConfig ();
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
	<table class="kpostbuttonset">
		<tr>
			<td class="kpostbuttons">
			<ul id="kbbcode-toolbar"></ul>
			</td>
		</tr>
		<!-- Start extendable secondary toolbar -->
		<tr>
			<td class="kpostbuttons">
			<div id="kbbcode-size-options" style="display: none;"><span
				class="kmsgtext-xs" title='[size=1]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XS');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext-s" title='[size=2]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_S');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext-m" title='[size=3]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_M');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext-l" title='[size=4]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_L');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext-xl" title='[size=5]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XL');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span> <span class="kmsgtext-xxl" title='[size=6]'
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
				value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKURL');
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_LINK_TEXT');
			?>&nbsp;<input name="text2" id="kbbcode-link_text" type="text"
				size="30" maxlength="150"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKTEXT');
				?>')" /> <input type="button" name="insterLink"
				value="<?php
				echo JText::_('COM_KUNENA_EDITOR_INSERT');
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
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE');
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_URL');
			?>&nbsp;<input name="url2" id="kbbcode-image_url" type="text"
				size="40" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKURL');
				?>')" />&nbsp; <input type="button" name="Link"
				value="<?php
				echo JText::_('COM_KUNENA_EDITOR_INSERT');
				?>"
				onclick="kInsertImageLink()"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY');
				?>')" /></div>

			<div id="kbbcode-poll-options" style="display: none;">
			<?php
			//Check if the poll is allowed
			if ($kunena_config->pollenabled) {
				if ( empty($this->msg_cat->allow_polls) ) $this->msg_cat->allow_polls = '';

				$display_poll = $kunena_poll->get_poll_allowed($this->id, $this->parent, $this->kunena_editmode, $this->msg_cat->allow_polls);
				if (!isset($this->polldatasedit[0]->polltimetolive)) {
					$this->polldatasedit[0]->polltimetolive = '0000-00-00 00:00:00';
				}
				$kunena_poll->call_js_poll_edit($this->kunena_editmode, $this->id);
				$html_poll_edit = $kunena_poll->get_input_poll($this->kunena_editmode, $this->id, $this->polldatasedit);
				JHTML::_('behavior.calendar');
			?>
			<span id="kpoll-not-allowed"><?php if(!$display_poll) { echo JText::_('The polls are not allowed in this category'); } ?></span>
			<div id="kpoll-hide-not-allowed" <?php if(!$display_poll) { ?> style="display:none;" <?php } ?> >
			
				<label class="kpoll-title-lbl" for="kpoll-title"><?php echo JText::_('COM_KUNENA_POLL_TITLE'); ?></label>
				<input type="text" class="inputbox" name="poll_title" id="kpoll-title"
					maxlength="100" size="40"
					value="<?php if(isset($this->polldatasedit[0]->title)) { echo $this->polldatasedit[0]->title; } ?>"
					onmouseover="javascript:$('helpbox').set('value', '<?php
					echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLTITLE'); ?>')" />
				
				<img id="kbutton-poll-add"
					src="<?php echo JURI::root(); ?>/components/com_kunena/template/default/images/icons/karmaplus.png"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ADDPOLLOPTION'); ?>')" />
				<img id="kbutton-poll-rem"
					src="<?php echo JURI::root(); ?>/components/com_kunena/template/default/images/icons/karmaminus.png"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_REMPOLLOPTION'); ?>')" />
				
				<label class="kpoll-term-lbl" for="kpoll-time-to-live"><?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
				<input class="inputbox" type="text" maxlength="15"
					name="kpoll-time-to-live" id="kpoll-time-to-live"
					style="display: none"
					value="<?php echo $this->polldatasedit[0]->polltimetolive; ?>" /> <img
					src="templates/system/images/calendar.png" alt="Calendar"
					onclick="showCalendar('kpoll-time-to-live','%Y-%m-%d');$('kpoll-time-to-live').removeProperty('style');"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLLIFESPAN'); ?>')" />
				
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
			?>
			</div>
			<?php
			if ($kunena_config->highlightcode) {
				$path = $kunena_config->highlightcode_path;
				if (!$path) $path = '/libraries/geshi';
				$path = JPATH_ROOT.str_replace('/', DS, str_replace("\\", DS, $path));
				if (file_exists($path.DS."geshi.php")) {
					$path .= DS."geshi";
					$files = JFolder::files($path, ".php"); ?>
					<div id="kbbcode-code-options" style="display: none;"><?php
					echo JText::_('COM_KUNENA_EDITOR_CODE_TYPE');
					?> <select id="kcodetype"
						name="kcode_type" class="kbutton"
						onmouseover="javascript:$('helpbox').set('value', '<?php
						echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODETYPE');
						?>')">
						<?php
						foreach ($files as $file)
							echo '<option value = "'.substr($file,0,-4).'">'.substr($file,0,-4).'</option>';
						?>
					</select>
					<input id="kbutton_addcode" type="button" name="Code" onclick="kInsertCode()"
					value="<?php echo JText::_('COM_KUNENA_EDITOR_CODE_INSERT'); ?>"
					onmouseover="javascript:$('helpbox').set('value', '<?php
					echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODEAPPLY');
					?>')" />
					</div>
			<?php }
			}
			?>
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
			echo JText::_('COM_KUNENA_EDITOR_MAP');
			?>&nbsp;<input name="map-url"
				id="kbbcode-map_url" type="text" size="40" maxlength="250" value=""
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MAP');
				?>')" />&nbsp;
			<input type="button" name="Link"
				value="<?php
				echo JText::_('COM_KUNENA_EDITOR_INSERT');
				?>"
				onclick="kInsertMapLink()"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MAPAPPLY');
				?>')" />
			</div>
			</td>
		</tr>
		<?php if (!$this->config->disemoticons) : ?>
		<tr>
			<td class="kpostbuttons">
			<div id="smilie"><?php
			$kunena_db = &JFactory::getDBO ();
			$kunena_db->setQuery ( "SELECT code, location, emoticonbar FROM #__kunena_smileys ORDER BY id" );
			$set = $kunena_db->loadAssocList ();
			KunenaError::checkDatabaseError();
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
		<?php endif; ?>
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
	<span class="ks" onclick="kGrowShrinkMessage(100);"
		style="cursor: pointer"><?php echo JText::_('COM_KUNENA_EDITOR_ENLARGE'); ?></span>&nbsp;/&nbsp; <span
		class="ks" onclick="kGrowShrinkMessage(-100);"
		style="cursor: pointer"><?php echo JText::_('COM_KUNENA_EDITOR_SHRINK'); ?></span></td>

	<td valign="top"><textarea class="ktxtarea required" name="message"
		id="kbbcode-message"><?php
			echo $this->message_text;
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
	<div class="clr"> </div>
	<fieldset><legend><?php
		echo (JText::_('COM_KUNENA_EDITING_REASON')) ?></legend> <input class="kinputbox"
		name="modified_reason" size="40" maxlength="200" type="text" /><br />
	</fieldset>
	<?php
	}
	?>

	</td>
</tr>