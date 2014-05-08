<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Kunena bbcode editor
?>
<tr id="kpost-toolbar" class="krow<?php echo 1 + $this->k^=1;?>">
	<td class="kcol-first kcol-editor-label">
		<strong><?php echo JText::_('COM_KUNENA_BOARDCODE');?></strong></td>
	<td class="kcol-last kcol-editor-field">
	<table class="kpostbuttonset">
		<tr>
			<td class="kpostbuttons">
				<ul id="kbbcode-toolbar">
					<li>
						<script type="text/javascript">document.write('<?php echo JText::_('COM_KUNENA_BBCODE_EDITOR_JAVASCRIPT_LOADING', true) ?>')</script>
						<noscript><?php echo JText::_('COM_KUNENA_BBCODE_EDITOR_JAVASCRIPT_DISABLED') ?></noscript>
					</li>
				</ul>
			</td>
		</tr>
		<!-- Start extendable secondary toolbar -->
		<tr>
			<td class="kpostbuttons">
			<div id="kbbcode-size-options" style="display: none;">
			<span class="kmsgtext-xs" title='[size=1]'
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XS', true);?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-s" title='[size=2]'
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_S', true);?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-m" title='[size=3]'
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_M', true);?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-l" title='[size=4]'
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_L', true);?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-xl" title='[size=5]'
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XL', true);?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			<span class="kmsgtext-xxl" title='[size=6]'
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XXL', true);?>')">&nbsp;<?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>&nbsp;</span>
			</div>
			<div id="kbbcode-color-options" style="display: none;">
				<script type="text/javascript">kGenerateColorPalette('4%', '15px');</script>
			</div>

			<div id="kbbcode-link-options" style="display: none;">
				<?php echo JText::_('COM_KUNENA_EDITOR_LINK_URL'); ?>&nbsp;
				<input id="kbbcode-link_url" name="url" type="text" size="40" value="http://"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKURL', true); ?>')" />
				<?php echo JText::_('COM_KUNENA_EDITOR_LINK_TEXT'); ?>&nbsp;
				<input name="text2" id="kbbcode-link_text" type="text" size="30" maxlength="150"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKTEXT', true); ?>')" />
				<input type="button" name="insterLink" value="<?php echo JText::_('COM_KUNENA_EDITOR_INSERT'); ?>"
					onclick="kbbcode.focus().replaceSelection('[url=' + document.id('kbbcode-link_url').get('value') + ']' + document.id('kbbcode-link_text').get('value') + '[/url]', false); kToggleOrSwap('kbbcode-link-options');"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKAPPLY', true); ?>')" />
			</div>

			<div id="kbbcode-image-options" style="display: none;">
				<?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_SIZE'); ?>&nbsp;
				<input id="kbbcode-image_size" name="size" type="text" size="10" maxlength="10"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE', true); ?>')" />
				<?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_URL'); ?>&nbsp;
				<input name="url2" id="kbbcode-image_url" type="text" size="40" value="http://"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKURL', true); ?>')" />&nbsp;
				<input type="button" name="Link" value="<?php echo JText::_('COM_KUNENA_EDITOR_INSERT'); ?>" onclick="kInsertImageLink()"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY', true); ?>')" />
			</div>

			<?php if (!$this->message->parent && isset($this->poll)) : ?>
			<div id="kbbcode-poll-options" style="display: none;">
			<?php JHtml::_('behavior.calendar'); ?>
				<label class="kpoll-title-lbl" for="kpoll-title"><?php echo JText::_('COM_KUNENA_POLL_TITLE'); ?></label>
				<input type="text" class="inputbox" name="poll_title" id="kpoll-title"
					maxlength="100" size="40"
					value="<?php echo $this->escape( $this->poll->title ) ?>"
					onmouseover="document.id('helpbox').set('value', '<?php
					echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLTITLE', true); ?>')" />

				<img id="kbutton-poll-add"
					src="<?php echo $this->ktemplate->getImagePath('icons/poll_add_options.png') ?>"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ADDPOLLOPTION', true); ?>')" alt="<?php echo JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION'); ?>" />
				<img id="kbutton-poll-rem"
					src="<?php echo $this->ktemplate->getImagePath('icons/poll_rem_options.png' ) ?>"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_REMPOLLOPTION', true); ?>')" alt="<?php echo JText::_('COM_KUNENA_POLL_REMOVE_POLL_OPTION'); ?>" />

				<label class="kpoll-term-lbl" for="kpoll-time-to-live"><?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
				<?php echo JHtml::_('calendar', isset($this->poll->polltimetolive) ? $this->escape($this->poll->polltimetolive) : '0000-00-00', 'poll_time_to_live', 'kpoll-time-to-live', '%Y-%m-%d',array('onmouseover'=>'document.id(\'helpbox\').set(\'value\', \''.JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLLIFESPAN', true).'\')')); ?>
				<?php
				if($this->poll->exists()) {
					$x = 1;
					foreach ($this->poll->getOptions() as $poll_option) {
						echo '<div class="polloption">Option '.$x.' <input type="text" maxlength = "25" id="field_option'.$x.'" name="polloptionsID['.$poll_option->id.']" value="'.$poll_option->text.'" onmouseover="document.id(\'helpbox\').set(\'value\', \''.JText::_('COM_KUNENA_EDITOR_HELPLINE_OPTION', true).'\')" /></div>';
						$x++;
					}
				}
				?>
				<input type="hidden" name="nb_options_allowed" id="nb_options_allowed" value="<?php echo $this->config->pollnboptions; ?>" />
				<input type="hidden" name="number_total_options" id="numbertotal"
					value="<?php echo ! empty ( $this->polloptionstotal ) ? $this->escape($this->polloptionstotal) : '' ?>" />
			</div>
			<?php endif;

			if (($codeTypes = $this->getCodeTypes())) :
			?>
			<div id="kbbcode-code-options" style="display: none;">
				<?php echo $codeTypes; ?>
				<input id="kbutton_addcode" type="button" name="Code" onclick="kInsertCode()" value="<?php echo JText::_('COM_KUNENA_EDITOR_CODE_INSERT'); ?>"
					onmouseover="document.id('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODEAPPLY', true); ?>')" />
			</div>
			<?php endif;

			if ($this->config->showvideotag) {
			?>

			<div id="kbbcode-video-options" style="display: none;"><?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_SIZE');
			?><input id="kvideosize"
				name="videosize" type="text" size="5" maxlength="5"
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOSIZE', true);
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_WIDTH');
			?><input id="kvideowidth" name="videowidth"
				type="text" size="5" maxlength="5"
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOWIDTH', true);
				?>')" />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_HEIGHT');
			?><input id="kvideoheight"
				name="videoheight" type="text" size="5" maxlength="5"
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOHEIGHT', true);
				?>')" /> <br />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER');
			?> <select id="kvideoprovider"
				name="provider" class="kbutton"
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOPROVIDER', true);
				?>')">
				<?php
				$vid_provider = array ('', 'Bofunk', 'Break', 'Clipfish', 'Dailymotion', 'DivX,divx]http://', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'MediaPlayer,mediaplayer]http://', 'Metacafe', 'MySpace', 'QuickTime,quicktime]http://', 'RealPlayer,realplayer]http://', 'RuTube', 'Sapo', 'Streetfire', 'Veoh', 'Videojug', 'Vimeo', 'Wideo.fr', 'YouTube', 'Youku' );
				foreach ( $vid_provider as $vid_type ) {
					$vid_type = explode ( ',', $vid_type );
					echo '<option value = "' . (! empty ( $vid_type [1] ) ? $this->escape($vid_type [1]) : JString::strtolower ( $this->escape($vid_type [0]) ) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
				}
				?>
			</select> <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_ID');
			?><input id="kvideoid"
				name="videoid" type="text" size="11" maxlength="30"
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOID', true);
				?>')" />
			<input id="kbutton_addvideo1" type="button" name="Video" accesskey="p"
				onclick="kInsertVideo1()"
				value="<?php
						echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT');
						?>"
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY1', true);
				?>')" /><br />
			<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_URL');
			?><input id="kvideourl" name="videourl"
				type="text" size="30" maxlength="250" value="http://"
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOURL', true);
				?>')" />
			<input id="kbutton_addvideo2" type="button" name="Video" accesskey="p"
				onclick="kInsertVideo2()"
				value="<?php
						echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT');
						?>"
				onmouseover="document.id('helpbox').set('value', '<?php
				echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY2', true);
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
			$emoticons = KunenaHtmlParser::getEmoticons(0, 1);
			foreach ( $emoticons as $emo_code=>$emo_url ) {
				echo '<img class="btnImage" src="' . $emo_url . '" border="0" alt="' . $emo_code . ' " title="' . $emo_code . ' " onclick="kbbcode.focus().insert(\' '. $emo_code .' \', \'after\', false);" style="cursor:pointer"/> ';
			}
			?>
			</div>

			</td>
		</tr>
		<?php endif; ?>
		<!-- end of extendable secondary toolbar -->
		<tr>
			<td class="kposthint"><input type="text" name="helpbox" id="helpbox" size="45" class="kinputbox" disabled="disabled" maxlength="100"
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
		<textarea class="ktxtarea required" name="message" id="kbbcode-message" rows="10" cols="50" tabindex="3"><?php echo $this->escape($this->message->message); ?></textarea>

		<!-- Hidden preview placeholder -->
		<div id="kbbcode-preview" style="display: none;"></div>
		<?php if ($this->message->exists()) : ?>
		<div class="clr"> </div>
		<fieldset>
			<legend><?php echo (JText::_('COM_KUNENA_EDITING_REASON')) ?></legend>
			<input class="kinputbox" name="modified_reason" size="40" maxlength="200" type="text" value="<?php echo $this->modified_reason; ?>" />
		</fieldset>
		<?php endif; ?>
	</td>
</tr>
