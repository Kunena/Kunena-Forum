<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Kunena bbcode editor
?>

<div class="control-group">
  <label class="control-label"><?php echo JText::_('COM_KUNENA_BOARDCODE');?></label>
  <div class="controls">
    <ul id="kbbcode-toolbar">
      <li> 
        <script type="text/javascript">document.write('<?php echo JText::_('COM_KUNENA_BBCODE_EDITOR_JAVASCRIPT_LOADING', true) ?>')</script>
        <noscript>
        <?php echo JText::_('COM_KUNENA_BBCODE_EDITOR_JAVASCRIPT_DISABLED') ?>
        </noscript>
      </li>
    </ul>
  </div>
</div>

<!-- Start extendable secondary toolbar -->
<tr>
  <td class="kpostbuttons">
    <div id="kbbcode-size-options" style="display: none;"> <span class="kmsgtext-xs" title='[size=1]'
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XS');?>')">&nbsp;
      <?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>
      &nbsp;</span> <span class="kmsgtext-s" title='[size=2]'
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_S');?>')">&nbsp;
      <?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>
      &nbsp;</span> <span class="kmsgtext-m" title='[size=3]'
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_M');?>')">&nbsp;
      <?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>
      &nbsp;</span> <span class="kmsgtext-l" title='[size=4]'
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_L');?>')">&nbsp;
      <?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>
      &nbsp;</span> <span class="kmsgtext-xl" title='[size=5]'
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XL');?>')">&nbsp;
      <?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>
      &nbsp;</span> <span class="kmsgtext-xxl" title='[size=6]'
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XXL');?>')">&nbsp;
      <?php
				echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT');
				?>
      &nbsp;</span> </div>
    <div id="kbbcode-color-options" style="display: none;"> 
      <script type="text/javascript">kGenerateColorPalette('4%', '15px');</script> 
    </div>
    <div id="kbbcode-link-options" style="display: none;"> <?php echo JText::_('COM_KUNENA_EDITOR_LINK_URL'); ?>&nbsp;
      <input id="kbbcode-link_url" name="url" type="text" size="40" value="http://"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_LINKURL'); ?>')" />
      <?php echo JText::_('COM_KUNENA_EDITOR_LINK_TEXT'); ?>&nbsp;
      <input name="text2" id="kbbcode-link_text" type="text" size="30" maxlength="150"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_LINKTEXT'); ?>')" />
      <input type="button" name="insterLink" value="<?php echo JText::_('COM_KUNENA_EDITOR_INSERT'); ?>"
					onclick="kbbcode.focus().replaceSelection('[url=' + document.id('kbbcode-link_url').get('value') + ']' + document.id('kbbcode-link_text').get('value') + '[/url]', false); kToggleOrSwap('kbbcode-link-options');"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_LINKAPPLY'); ?>')" />
    </div>
    <div id="kbbcode-image-options" style="display: none;"> <?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_SIZE'); ?>&nbsp;
      <input id="kbbcode-image_size" name="size" type="text" size="10" maxlength="10"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE'); ?>')" />
      <?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_URL'); ?>&nbsp;
      <input name="url2" id="kbbcode-image_url" type="text" size="40" value="http://"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKURL'); ?>')" />
      &nbsp;
      <input type="button" name="Link" value="<?php echo JText::_('COM_KUNENA_EDITOR_INSERT'); ?>" onclick="kInsertImageLink()"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY'); ?>')" />
    </div>
    <?php if (!$this->message->parent && isset($this->poll)) : ?>
    <div id="kbbcode-poll-options" style="display: none;">
      <?php JHtml::_('behavior.calendar'); ?>
      <label class="kpoll-title-lbl" for="kpoll-title"><?php echo JText::_('COM_KUNENA_POLL_TITLE'); ?></label>
      <input type="text" class="inputbox" name="poll_title" id="kpoll-title"
					maxlength="100" size="40"
					value="<?php echo $this->escape( $this->poll->title ) ?>"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php
					echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_POLLTITLE'); ?>')" />
      <img id="kbutton-poll-add"
					src="<?php echo $this->ktemplate->getImagePath('icons/poll_add_options.png') ?>"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_ADDPOLLOPTION'); ?>')" alt="<?php echo JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION'); ?>" /> <img id="kbutton-poll-rem"
					src="<?php echo $this->ktemplate->getImagePath('icons/poll_rem_options.png' ) ?>"
					onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_REMPOLLOPTION'); ?>')" alt="<?php echo JText::_('COM_KUNENA_POLL_REMOVE_POLL_OPTION'); ?>" />
      <label class="kpoll-term-lbl" for="kpoll-time-to-live"><?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
      <?php echo JHtml::_('calendar', isset($this->poll->polltimetolive) ? $this->escape($this->poll->polltimetolive) : '0000-00-00', 'poll_time_to_live', 'kpoll-time-to-live', '%Y-%m-%d',array('onmouseover'=>'javascript:document.id(\'helpbox\').set(\'value\', \''.KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_POLLLIFESPAN').'\')')); ?>
      <?php
				if($this->poll->exists()) {
					$x = 1;
					foreach ($this->poll->getOptions() as $poll_option) {
						echo '<div class="polloption">Option '.$x.' <input type="text" maxlength = "25" id="field_option'.$x.'" name="polloptionsID['.$poll_option->id.']" value="'.$poll_option->text.'" onmouseover="javascript:document.id(\'helpbox\').set(\'value\', \''.KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_OPTION').'\')" /></div>';
						$x++;
					}
				}
				?>
      <input type="hidden" name="nb_options_allowed" id="nb_options_allowed" value="<?php echo $this->config->pollnboptions; ?>" />
      <input type="hidden" name="number_total_options" id="numbertotal"
					value="<?php echo ! empty ( $this->polloptionstotal ) ? $this->escape($this->polloptionstotal) : '' ?>" />
    </div>
    <?php endif; ?>
    <?php
			if ($this->config->highlightcode) {
				$path = JPATH_ROOT.'/plugins/content/geshi/geshi/geshi';
				if ( file_exists($path) ) {
					$files = JFolder::files($path, ".php"); ?>
    <div id="kbbcode-code-options" style="display: none;"> <?php echo JText::_('COM_KUNENA_EDITOR_CODE_TYPE'); ?>
      <select id="kcodetype" name="kcode_type" class="kbutton"
							onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_CODETYPE'); ?>')">
        <?php
						echo '<option value = ""></option>';
						foreach ($files as $file)
							echo '<option value = "'.substr($file,0,-4).'">'.substr($file,0,-4).'</option>';
						?>
      </select>
      <input id="kbutton_addcode" type="button" name="Code" onclick="kInsertCode()" value="<?php echo JText::_('COM_KUNENA_EDITOR_CODE_INSERT'); ?>"
						onmouseover="javascript:document.id('helpbox').set('value', '<?php echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_CODEAPPLY'); ?>')" />
    </div>
    <?php }
			}
			if ($this->config->showvideotag) {
			?>
    <div id="kbbcode-video-options" style="display: none;">
      <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_SIZE');
			?>
      <input id="kvideosize"
				name="videosize" type="text" size="5" maxlength="5"
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOSIZE');
				?>')" />
      <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_WIDTH');
			?>
      <input id="kvideowidth" name="videowidth"
				type="text" size="5" maxlength="5"
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOWIDTH');
				?>')" />
      <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_HEIGHT');
			?>
      <input id="kvideoheight"
				name="videoheight" type="text" size="5" maxlength="5"
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOHEIGHT');
				?>')" />
      <br />
      <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER');
			?>
      <select id="kvideoprovider"
				name="provider" class="kbutton"
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOPROVIDER');
				?>')">
        <?php
				$vid_provider = array ('', 'Bofunk', 'Break', 'Clipfish', 'DivX,divx]http://', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'MediaPlayer,mediaplayer]http://', 'Metacafe', 'MySpace', 'QuickTime,quicktime]http://', 'RealPlayer,realplayer]http://', 'RuTube', 'Sapo', 'Streetfire', 'Veoh', 'Videojug', 'Vimeo', 'Wideo.fr', 'YouTube' );
				foreach ( $vid_provider as $vid_type ) {
					$vid_type = explode ( ',', $vid_type );
					echo '<option value = "' . (! empty ( $vid_type [1] ) ? $this->escape($vid_type [1]) : JString::strtolower ( $this->escape($vid_type [0]) ) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
				}
				?>
      </select>
      <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_ID');
			?>
      <input id="kvideoid"
				name="videoid" type="text" size="11" maxlength="30"
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOID');
				?>')" />
      <input id="kbutton_addvideo1" type="button" name="Video" accesskey="p"
				onclick="kInsertVideo1()"
				value="<?php
						echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT');
						?>"
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY1');
				?>')" />
      <br />
      <?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_URL');
			?>
      <input id="kvideourl" name="videourl"
				type="text" size="30" maxlength="250" value="http://"
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOURL');
				?>')" />
      <input id="kbutton_addvideo2" type="button" name="Video" accesskey="p"
				onclick="kInsertVideo2()"
				value="<?php
						echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT');
						?>"
				onmouseover="javascript:document.id('helpbox').set('value', '<?php
				echo KunenaHtmlParser::JSText('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY2');
				?>')" />
    </div>
  </td>
</tr>
<?php
		}
		
		if (!$this->config->disemoticons) : ?>
<div class="control-group">
  <label class="control-label">Emoticons</label>
  <div class="controls">
    <?php
			$emoticons = KunenaHtmlParser::getEmoticons(0, 1);
			foreach ( $emoticons as $emo_code=>$emo_url ) {
				echo '<img class="btnImage" src="' . $emo_url . '" border="0" alt="' . $emo_code . ' " title="' . $emo_code . ' " onclick="kbbcode.focus().insert(\' '. $emo_code .' \', \'after\', false);" style="cursor:pointer"/> ';
			}
			?>
  </div>
</div>
<?php endif; ?>

<!-- end of extendable secondary toolbar -->
<div class="control-group">
  <div class="controls">
    <input type="text" name="helpbox" id="helpbox" size="88" class="kinputbox" disabled="disabled" maxlength="100"
value="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_HINT')); ?>" />
  </div>
</div>
<div class="control-group">
  <label class="control-label"><?php echo (JText::_('COM_KUNENA_MESSAGE')) ; ?></label>
  <div class="center"> <span class="center" onclick="kGrowShrinkMessage(100);"
		style="cursor: pointer"><?php echo JText::_('COM_KUNENA_EDITOR_ENLARGE'); ?></span>&nbsp;/&nbsp; <span class="center" onclick="kGrowShrinkMessage(-100);"
		style="cursor: pointer"><?php echo JText::_('COM_KUNENA_EDITOR_SHRINK'); ?></span></div>
  <div class="controls span5">
    <textarea class="ktxtarea required" name="message" id="kbbcode-message" rows="10" cols="90"  tabindex="3"><?php echo $this->escape($this->message->message); ?></textarea>
  </div>
</div>

<!-- Hidden preview placeholder -->
<div id="kbbcode-preview" style="display: none;"></div>
<?php if ($this->message->exists()) : ?>
<div class="clr"> </div>
<fieldset>
  <legend><?php echo (JText::_('COM_KUNENA_EDITING_REASON')) ?></legend>
  <input class="kinputbox" name="modified_reason" size="40" maxlength="200" type="text" value="<?php echo $this->modified_reason; ?>" />
</fieldset>
<?php endif; ?>
