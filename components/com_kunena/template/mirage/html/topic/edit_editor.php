<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<li id="kpost-toolbar" class="postmessage-row kbox-hover kbox-hover_list-row">
	<div class="form-label">
		<div class="innerspacer-left kbox-full">
			<label>
				<?php echo JText::_('COM_KUNENA_BOARDCODE') ?>
			</label>
		</div>
	</div>
	<div class="form-field">
		<div class="innerspacer kbox-full">
			<div class="postbuttonset">
				<div class="postbuttons">
					<ul id="kbbcode-toolbar">
						<li>
							<script type="text/javascript">document.write('<?php echo JText::_('COM_KUNENA_BBCODE_EDITOR_JAVASCRIPT_LOADING', true) ?>')</script>
							<noscript><?php echo JText::_('COM_KUNENA_BBCODE_EDITOR_JAVASCRIPT_DISABLED') ?></noscript>
						</li>
					</ul>
				</div>

				<div class="postbuttons kbox-full">
					<div style="display: none;" id="kbbcode-size-options">
						<span class="kmsgtext-xs hasTip" title="[size=1]" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XS') ?>">
							<?php echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT') ?>
						</span>
						<span class="kmsgtext-s hasTip" title='[size=2]' title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_S') ?>">
							<?php echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT') ?>
						</span>
						<span class="kmsgtext-m hasTip" title='[size=3]' title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_M') ?>">
							<?php echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT') ?>
						</span>
						<span class="kmsgtext-l hasTip" title='[size=4]' title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_L') ?>">
							<?php echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT') ?>
						</span>
						<span class="kmsgtext-xl hasTip" title='[size=5]' title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XL') ?>">
							<?php echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT') ?>
						</span>
						<span class="kmsgtext-xxl hasTip" title='[size=6]' title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE_XXL') ?>">
							<?php echo JText::_('COM_KUNENA_EDITOR_SIZE_SAMPLETEXT') ?>
						</span>
					</div>

					<div style="display: none;" id="kbbcode-color-options">
						<script type="text/javascript">kGenerateColorPalette('4%', '15px');</script>
					</div>

					<div style="display: none;" id="kbbcode-link-options">
						<?php echo JText::_('COM_KUNENA_EDITOR_LINK_URL') ?>
						<input class="hasTip" id="kbbcode-link_url" name="url" type="text" size="40" value="http://" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKURL') ?>" />
						<?php echo JText::_('COM_KUNENA_EDITOR_LINK_TEXT') ?>
						<input class="hasTip" name="text2" id="kbbcode-link_text" type="text" size="30" maxlength="150" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKTEXT') ?>" />
						<input class="hasTip" type="button" name="insterLink" value="<?php echo JText::_('COM_KUNENA_EDITOR_INSERT') ?>"
							onclick="kbbcode.focus().replaceSelection('[url=' + document.id('kbbcode-link_url').get('value') + ']' + document.id('kbbcode-link_text').get('value') + '[/url]', false); kToggleOrSwap('kbbcode-link-options');"
							title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINKAPPLY') ?>" />
					</div>

					<div style="display: none;" id="kbbcode-image-options">
						<?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_SIZE') ?>
						<input class="hasTip" id="kbbcode-image_size" name="size" type="text" size="10" maxlength="10" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE') ?>" />
						<?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK_URL') ?>
						<input class="hasTip" name="url2" id="kbbcode-image_url" type="text" size="40" value="http://" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKURL') ?>" />
						<input class="hasTip" type="button" name="Link" value="<?php echo JText::_('COM_KUNENA_EDITOR_INSERT') ?>" onclick="kInsertImageLink()" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY') ?>" />
					</div>

					<?php if (!$this->message->parent && isset($this->poll)) : ?>
					<div style="display: none;" id="kbbcode-poll-options">
						<?php JHtml::_('behavior.calendar'); ?>
						<label for="kpoll-title" class="kpoll-title-lbl"><?php echo JText::_('COM_KUNENA_POLL_TITLE') ?></label>
						<input type="text" class="inputbox hasTip" name="poll_title" id="kpoll-title" maxlength="100" size="40"
							value="<?php echo $this->escape( $this->poll->title ) ?>" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLTITLE'); ?>" />
						<img class="hasTip" id="kbutton-poll-add"
							src="<?php echo $this->ktemplate->getImagePath('icons/poll_add_options.png') ?>"
							title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ADDPOLLOPTION'); ?>" alt="<?php echo JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION'); ?>" />
						<img class="hasTip" id="kbutton-poll-rem"
							src="<?php echo $this->ktemplate->getImagePath('icons/poll_rem_options.png') ?>"
							title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_REMPOLLOPTION'); ?>" alt="<?php echo JText::_('COM_KUNENA_POLL_REMOVE_POLL_OPTION'); ?>" />

						<label class="kpoll-term-lbl" for="kpoll-time-to-live"><?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
						<?php echo JHtml::_('calendar', isset($this->poll->polltimetolive) ? $this->escape($this->poll->polltimetolive) : '0000-00-00', 'poll_time_to_live', 'kpoll-time-to-live', '%Y-%m-%d',array('onmouseover'=>'javascript:document.id(\'helpbox\').set(\'value\', \''.JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLLIFESPAN',true).'\')')); ?>
						<?php
						if($this->poll->exists()) {
							$x = 1;
							foreach ($this->poll->getOptions() as $option) {
								echo '<div class="polloption">Option '.$x.' <input type="text" maxlength = "25" id="field_option'.$x.'" name="polloptionsID['.$option->id.']" value="'.$option->text.'" onmouseover="javascript:document.id(\'helpbox\').set(\'value\', \''.JText::_('COM_KUNENA_EDITOR_HELPLINE_OPTION', true).'\')" /></div>';
								$x++;
							}
						}
						?>
						<input type="hidden" name="nb_options_allowed" id="nb_options_allowed" value="<?php echo $this->config->pollnboptions; ?>" />
						<input type="hidden" name="number_total_options" id="numbertotal" value="<?php echo ! empty ( $this->polloptionstotal ) ? $this->escape($this->polloptionstotal) : '' ?>" />
					</div>
					<?php endif; ?>

					<?php
					if ($this->config->highlightcode) :
						$path = JPATH_ROOT.'/plugins/content/geshi/geshi/geshi';
						if ( file_exists($path) ) :
							$files = JFolder::files($path, ".php"); ?>
					<div style="display: none;" id="kbbcode-code-options">
						<?php echo JText::_('COM_KUNENA_EDITOR_CODE_TYPE') ?>
						<select id="kcodetype" name="kcode_type" class="kbutton" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODETYPE') ?>">
						<?php
						echo '<option value=""></option>';
						foreach ($files as $file) echo '<option value="'.substr($file,0,-4).'">'.substr($file,0,-4).'</option>';
						?>
						</select>
						<input id="kbutton_addcode" type="button" name="Code" onclick="kInsertCode()" value="<?php echo JText::_('COM_KUNENA_EDITOR_CODE_INSERT') ?>" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODEAPPLY') ?>" />
					</div>
					<?php endif ?>
					<?php endif ?>

					<?php if ($this->config->showvideotag) : ?>
					<div style="display: none;" id="kbbcode-video-options">
						<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO_SIZE') ?>
						<input id="kvideosize" name="videosize" type="text" size="5" maxlength="5" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOSIZE') ?>" />
						<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO_WIDTH') ?>
						<input id="kvideowidth" name="videowidth" type="text" size="5" maxlength="5" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOWIDTH') ?>" />
						<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO_HEIGHT') ?>
						<input id="kvideoheight" name="videoheight" type="text" size="5" maxlength="5" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOHEIGHT') ?>" />
						<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER'); ?>
						<select id="kvideoprovider" name="provider" class="kbutton" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOPROVIDER') ?>">
						<?php
						$vid_provider = array ('', 'Bofunk', 'Break', 'Clipfish', 'DivX,divx]http://', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'MediaPlayer,mediaplayer]http://', 'Metacafe', 'MySpace', 'QuickTime,quicktime]http://', 'RealPlayer,realplayer]http://', 'RuTube', 'Sapo', 'Streetfire', 'Veoh', 'Videojug', 'Vimeo', 'Wideo.fr', 'YouTube' );
						foreach ( $vid_provider as $vid_type ) {
							$vid_type = explode ( ',', $vid_type );
							echo '<option value="' . (! empty ( $vid_type [1] ) ? $this->escape($vid_type [1]) : JString::strtolower ( $this->escape($vid_type [0]) ) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
						}
						?>
						</select>
						<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO_ID') ?>
						<input id="kvideoid" name="videoid" type="text" size="11" maxlength="30" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOID') ?>" />
						<input id="kbutton_addvideo1" type="button" name="Video" accesskey="p" onclick="kInsertVideo1()" value="<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT') ?>" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY1') ?>" />
						<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO_URL') ?>
						<input id="kvideourl" name="videourl" type="text" size="30" maxlength="250" value="http://" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOURL') ?>" />
						<input id="kbutton_addvideo2" type="button" name="Video" accesskey="p" onclick="kInsertVideo2()" value="<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO_INSERT') ?>" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY2') ?>" />
					</div>
					<?php endif ?>
				</div>

				<?php if (!$this->config->disemoticons) : ?>
				<div class="postbuttons clear ">
					<div id="ksmiliebar"><?php
					$emoticons = KunenaHtmlParser::getEmoticons(0, 1);
					foreach ( $emoticons as $emo_code=>$emo_url ) {
						echo '<img class="btnImage hasTip" src="' . $emo_url . '" border="0" alt="' . $emo_code . ' " title="' . $emo_code . ' " onclick="kbbcode.focus().insert(\' '. $emo_code .' \', \'after\', false);" style="cursor:pointer"/> ';
					}
					?>
					</div>
				</div>
				<?php endif ?>

				<div class="kposthint kbox-full">
					<input type="text" value="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HINT') ?>" maxlength="100" disabled="disabled" class="kbox-width inputbox" size="45" id="helpbox" name="helpbox" />
				</div>
			</div>
		</div>
	</div>
</li>
<li class="postmessage-row kbox-hover kbox-hover_list-row">
	<div class="form-label">
		<div class="innerspacer-left kbox-full">
			<label for="kbbcode-message">
				<strong><?php echo (JText::_('COM_KUNENA_MESSAGE')) ?></strong><br />
				<span style="cursor: pointer;" onclick="kGrowShrinkMessage(100);" class="ks"><?php echo JText::_('COM_KUNENA_EDITOR_ENLARGE') ?></span>&nbsp;/&nbsp;
				<span style="cursor: pointer;" onclick="kGrowShrinkMessage(-100);" class="ks"><?php echo JText::_('COM_KUNENA_EDITOR_SHRINK') ?></span>
			</label>
		</div>
	</div>
	<div class="form-field">
		<div class="innerspacer kbox-full">
			<textarea cols="50" rows="10" id="kbbcode-message" name="message" class="kbox-width txtarea required hasTip" title="<?php echo (JText::_('COM_KUNENA_MESSAGE')) ?> :: <?php echo JText::_('COM_KUNENA_ENTER_MESSAGE') ?>"><?php echo $this->escape($this->message->message); ?></textarea>
			<!-- Hidden preview placeholder -->
			<div style="display: none;" id="kbbcode-preview"></div>
			<?php if ($this->message->exists()) : ?>
			<fieldset>
				<legend><?php echo (JText::_('COM_KUNENA_EDITING_REASON')) ?></legend>
				<input class="inputbox hasTip" name="modified_reason" size="95" maxlength="200" type="text" title="<?php echo (JText::_('COM_KUNENA_EDITING_REASON')) ?> :: <?php echo JText::_('COM_KUNENA_EDITING_ENTER_REASON') ?>" value="<?php echo $this->modified_reason; ?>" />
			</fieldset>
			<?php endif ?>
		</div>
	</div>
</li>
