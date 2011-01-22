<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

// Kunena bbcode editor
require_once (KPATH_SITE . DS . 'lib' .DS. 'kunena.poll.class.php');
$kunena_poll =& CKunenaPolls::getInstance();
$kunena_config = KunenaFactory::getConfig ();
?>
<tr id="kpost-toolbar" class="krow<?php echo 1 + $this->k^=1;?>">
	<td class="kcol-first kcol-editor-label">
		<strong><?php echo JText::_('COM_KUNENA_BOARDCODE');?></strong></td>
	<td class="kcol-last kcol-editor-field">
	<table class="kpostbuttonset">
		<tr>
			<td class="kpostbuttons">
				<ul id="kbbcode-toolbar"><li></li></ul>
			</td>
		</tr>
		<!-- Start extendable secondary toolbar -->
		<tr>
			<td class="kpostbuttons">
			<div id="kbbcode-size-options" style="display: none;">
			<span class="kmsgtext-xs" title='[size=1]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XS');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-s" title='[size=2]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_S');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-m" title='[size=3]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_M');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-l" title='[size=4]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_L');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-xl" title='[size=5]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XL');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-xxl" title='[size=6]'
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XXL');?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			</div>
			<div id="kbbcode-colorpalette" style="display: none;">
				<script type="text/javascript">kGenerateColorPalette('4%', '15px');</script>
			</div>

			<div id="kbbcode-link-options" style="display: none;">
				<?php echo JText::_('COM_KUNENA_EDITOR_LINK_URL'); ?>&nbsp;
				<input id="kbbcode-link_url" name="url" type="text" size="40" value="http://"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_LINKURL'); ?>')" />
				<?php echo JText::_('COM_KUNENA_EDITOR_LINK_TEXT'); ?>&nbsp;
				<input name="text2" id="kbbcode-link_text" type="text" size="30" maxlength="150"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_LINKTEXT'); ?>')" />
				<input type="button" name="insterLink" value="<?php echo JText::_('COM_KUNENA_EDITOR_INSERT'); ?>"
					onclick="kbbcode.replaceSelection('[url=' + $('kbbcode-link_url').get('value') + ']' + $('kbbcode-link_text').get('value') + '[/url]'); kToggleOrSwap('kbbcode-link-options');"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_LINKAPPLY'); ?>')" />
			</div>

			<div id="kbbcode-image-options" style="display: none;">
				<?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_SIZE'); ?>&nbsp;
				<input id="kbbcode-image_size" name="size" type="text" size="10" maxlength="10"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE'); ?>')" />
				<?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_URL'); ?>&nbsp;
				<input name="url2" id="kbbcode-image_url" type="text" size="40" value="http://"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKURL'); ?>')" />&nbsp;
				<input type="button" name="Link" value="<?php echo JText::_('COM_KUNENA_EDITOR_INSERT'); ?>" onclick="kInsertImageLink()"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY'); ?>')" />
			</div>

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
			<span id="kpoll-not-allowed"><?php if(!$display_poll) { echo JText::_('COM_KUNENA_POLL_CATS_NOT_ALLOWED'); } ?></span>
			<div id="kpoll-hide-not-allowed" <?php if(!$display_poll) { ?> style="display:none;" <?php } ?> >

				<label class="kpoll-title-lbl" for="kpoll-title"><?php echo JText::_('COM_KUNENA_POLL_TITLE'); ?></label>
				<input type="text" class="inputbox" name="poll_title" id="kpoll-title"
					maxlength="100" size="40"
					value="<?php if(isset($this->polldatasedit[0]->title)) { echo $this->escape( $this->polldatasedit[0]->title); } ?>"
					onmouseover="javascript:$('helpbox').set('value', '<?php
					echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_POLLTITLE'); ?>')" />

				<img id="kbutton-poll-add"
					src="<?php echo JURI::root(); ?>/components/com_kunena/template/default/images/icons/poll_add_options.png"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_ADDPOLLOPTION'); ?>')" alt="<?php echo JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION'); ?>" />
				<img id="kbutton-poll-rem"
					src="<?php echo JURI::root(); ?>/components/com_kunena/template/default/images/icons/poll_rem_options.png"
					onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_REMPOLLOPTION'); ?>')" alt="<?php echo JText::_('COM_KUNENA_POLL_REMOVE_POLL_OPTION'); ?>" />

				<label class="kpoll-term-lbl" for="kpoll-time-to-live"><?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
				<?php echo JHTML::_('calendar', $this->escape($this->polldatasedit[0]->polltimetolive), 'poll_time_to_live', 'kpoll-time-to-live', '%Y-%m-%d',array('onmouseover'=>'javascript:$(\'helpbox\').set(\'value\', \''.KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_POLLLIFESPAN').'\')')); ?> 

			</div>
			<?php
			if(!empty( $html_poll_edit )) {
				echo $html_poll_edit;
			}
			?>
			<input type="hidden" name="nb_options_allowed" id="nb_options_allowed" value="<?php echo $kunena_config->pollnboptions; ?>" />
			<input type="hidden" name="number_total_options" id="numbertotal"
				value="<?php echo ! empty ( $this->polloptionstotal ) ? $this->escape($this->polloptionstotal) : '' ?>" />
			<?php } ?>
			</div>

			<?php
			if ($kunena_config->highlightcode) {
				$kunena_config = KunenaFactory::getConfig();
				if (substr(JVERSION, 0, 3) == 1.5) {
					$path = JPATH_ROOT.'/libraries/geshi/geshi';
				} else {
					$path = JPATH_ROOT.'/plugins/content/geshi/geshi/geshi';
				}
				if ( file_exists($path) ) {
					$files = JFolder::files($path, ".php"); ?>
					<div id="kbbcode-code-options" style="display: none;">
						<?php echo JText::_('COM_KUNENA_EDITOR_CODE_TYPE'); ?>
						<select id="kcodetype" name="kcode_type" class="kbutton"
							onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_CODETYPE'); ?>')">
						<?php
						echo '<option value = ""></option>';
						foreach ($files as $file)
							echo '<option value = "'.substr($file,0,-4).'">'.substr($file,0,-4).'</option>';
						?>
					</select>
					<input id="kbutton_addcode" type="button" name="Code" onclick="kInsertCode()" value="<?php echo JText::_('COM_KUNENA_EDITOR_CODE_INSERT'); ?>"
						onmouseover="javascript:$('helpbox').set('value', '<?php echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_CODEAPPLY'); ?>')" />
					</div>
			<?php }
			}
			if ($kunena_config->showvideotag) {
			?>

			<div id="kbbcode-video-options" style="display: none;"><?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_SIZE');
			?><input id="kvideosize"
				name="videosize" type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOSIZE');
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_WIDTH');
			?><input id="kvideowidth" name="videowidth"
				type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOWIDTH');
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_HEIGHT');
			?><input id="kvideoheight"
				name="videoheight" type="text" size="5" maxlength="5"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOHEIGHT');
				?>')" /> <br />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER');
			?> <select id="kvideoprovider"
				name="provider" class="kbutton"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOPROVIDER');
				?>')">
				<?php
				$vid_provider = array ('', 'AnimeEpisodes', 'Biku', 'Bofunk', 'Break', 'Clip.vn', 'Clipfish', 'Clipshack', 'Collegehumor', 'Current', 'DailyMotion', 'DivX,divx]http://', 'DownloadFestival', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'Fliptrack', 'Fliqz', 'Gametrailers', 'Gamevideos', 'Glumbert', 'GMX', 'Google', 'GooglyFoogly', 'iFilm', 'Jumpcut', 'Kewego', 'LiveLeak', 'LiveVideo', 'MediaPlayer,mediaplayer]http://', 'MegaVideo', 'Metacafe', 'Mofile', 'Multiply', 'MySpace', 'MyVideo', 'QuickTime,quicktime]http://', 'Quxiu', 'RealPlayer,realplayer]http://', 'Revver', 'RuTube', 'Sapo', 'Sevenload', 'Sharkle', 'Spikedhumor', 'Stickam', 'Streetfire', 'StupidVideos', 'Toufee', 'Tudou', 'Unf-Unf', 'Uume', 'Veoh', 'VideoclipsDump', 'Videojug', 'VideoTube', 'Vidiac', 'VidiLife', 'Vimeo', 'WangYou', 'WEB.DE', 'Wideo.fr', 'YouKu', 'YouTube' );
				foreach ( $vid_provider as $vid_type ) {
					$vid_type = explode ( ',', $vid_type );
					echo '<option value = "' . (! empty ( $vid_type [1] ) ? $this->escape($vid_type [1]) : JString::strtolower ( $this->escape($vid_type [0]) ) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
				}
				?>
			</select> <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_ID');
			?><input id="kvideoid"
				name="videoid" type="text" size="11" maxlength="14"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOID');
				?>')" />
			<input id="kbutton_addvideo1" type="button" name="Video" accesskey="p"
				onclick="kInsertVideo1()"
				value="<?php
						echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT');
						?>"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY1');
				?>')" /><br />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_URL');
			?><input id="kvideourl" name="videourl"
				type="text" size="30" maxlength="250" value="http://"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOURL');
				?>')" />
			<input id="kbutton_addvideo2" type="button" name="Video" accesskey="p"
				onclick="kInsertVideo2()"
				value="<?php
						echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT');
						?>"
				onmouseover="javascript:$('helpbox').set('value', '<?php
				echo KunenaParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY2');
				?>')" />
			</div>
			</td>
		</tr>
		<?php
		}
		if (!$this->config->disemoticons) : ?>
		<tr>
			<td class="kpostbuttons">
			<div id="smilie"><?php
			$emoticons = smile::getEmoticons(0, 1);
			foreach ( $emoticons as $emo_code=>$emo_url ) {
				echo '<img class="btnImage" src="' . $emo_url . '" border="0" alt="' . $emo_code . ' " title="' . $emo_code . ' " onclick="kbbcode.insert(\''. $emo_code .' \', \'after\', true);" style="cursor:pointer"/> ';
			}
			?>
			</div>

			</td>
		</tr>
		<?php endif; ?>
		<!-- end of extendable secondary toolbar -->
		<tr>
			<td class="kposthint"><input type="text" name="helpbox" id="helpbox" size="45" class="kinputbox" maxlength="100"
				value="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_HINT')); ?>" /></td>
		</tr>
	</table>
	</td>
</tr>

<tr id="kpost-message" class="krow<?php echo 1 + $this->k^=1;?>">
	<td class="kcol-first kcol-editor-label"><strong><?php
		echo (JText::_('COM_KUNENA_MESSAGE')) ;
	// TODO: Replace Enlarge and Shrink with icons
	?></strong><br />
	<span class="ks" onclick="kGrowShrinkMessage(100);"
		style="cursor: pointer"><?php echo JText::_('COM_KUNENA_EDITOR_ENLARGE'); ?></span>&nbsp;/&nbsp; <span
		class="ks" onclick="kGrowShrinkMessage(-100);"
		style="cursor: pointer"><?php echo JText::_('COM_KUNENA_EDITOR_SHRINK'); ?></span></td>

	<td class="kcol-last kcol-editor-field">
		<textarea class="ktxtarea required" name="message" id="kbbcode-message" rows="10" cols="50"><?php echo $this->escape($this->message_text); ?></textarea>
		<?php
		// Add an empty div for the preview.The class name will be set by js depending on horizontal or vertical split
		?>
		<!-- Hidden preview placeholder -->
		<div id="kbbcode-preview" style="display: none;"></div>
		<?php if ($this->kunena_editmode) : ?>
		<div class="clr"> </div>
		<fieldset>
			<legend><?php echo (JText::_('COM_KUNENA_EDITING_REASON')) ?></legend>
			<input class="kinputbox" name="modified_reason" size="40" maxlength="200" type="text" />
		</fieldset>
		<?php endif; ?>
	</td>
</tr>