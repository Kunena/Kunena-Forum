<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Kunena bbcode editor
?>

<!-- Bootstrap modal to be used with bbcode editor -->
<div id="modal-map" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Map setting</h3>
	</div>
	<div class="modal-body">
		<p>City: <input name="modal-map-city" id="modal-map-city" type="text" value="" /></p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close modal</button>
	</div>
</div>
<div id="modal-code" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Code setting</h3>
	</div>
	<div class="modal-body">
		<p><?php $codeTypes = $this->getCodeTypes(); if (!empty($codeTypes)) :	?>
			<?php echo $codeTypes; ?>
				<input id="kbutton-modal-addcode" type="button" name="Code" value="<?php echo JText::_('COM_KUNENA_EDITOR_CODE_INSERT'); ?>" />
		<?php endif; ?></p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close modal</button>
	</div>
</div>
<div id="modal-picture" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Picture settings</h3>
	</div>
	<div class="modal-body">
		<p>Size: <input name="modal-picture-size" id="modal-picture-size" type="text" value="" />
		Url : <input name="modal-picture-url" id="modal-picture-url" type="text" value="" /></p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close modal</button>
	</div>
</div>
<div id="modal-video-settings" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Video setting</h3>
	</div>
	<div class="modal-body">
		<p>Size: <input name="modal-video-size" id="modal-video-size" type="text" maxlength="5" size="5" value="" />
		Width: <input name="modal-video-width" id="modal-video-width" type="text" maxlength="5" size="5" value="" />
		Height: <input name="modal-video-height" id="modal-video-height" type="text" maxlength="5" size="5" value="" />
		<?php
			echo JText::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER');
		?>
		<select id="kvideoprovider-modal"
			name="provider" class="kbutton">
			<?php
				$vid_provider = array ('', 'Bofunk', 'Break', 'Clipfish', 'DivX,divx]http://', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'MediaPlayer,mediaplayer]http://', 'Metacafe', 'MySpace', 'QuickTime,quicktime]http://', 'RealPlayer,realplayer]http://', 'RuTube', 'Sapo', 'Streetfire', 'Veoh', 'Videojug', 'Vimeo', 'Wideo.fr', 'YouTube' );
					foreach ( $vid_provider as $vid_type ) {
						$vid_type = explode ( ',', $vid_type );
							echo '<option value = "' . (! empty ( $vid_type [1] ) ? $this->escape($vid_type [1]) : JString::strtolower ( $this->escape($vid_type [0]) ) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
					}
				?>
		</select>
		ID: <input name="modal-video-id" id="modal-video-id" type="text" maxlength="30" size="11" value="" /></p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close modal</button>
	</div>
</div>
<div id="modal-video-urlprovider" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Video URL provider</h3>
	</div>
	<div class="modal-body">
		<p>URL: <input name="modal-video-urlprovider-input" id="modal-video-urlprovider-input" type="text" value="" /></p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close modal</button>
	</div>
</div>
<div id="modal-poll-settings" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Poll settings</h3>
	</div>
	<div class="modal-body">
		<?php if (!$this->message->parent && isset($this->poll)) : ?>
			<div id="kbbcode-poll-options">
				<?php JHtml::_('behavior.calendar'); ?>
				<label class="kpoll-title-lbl" for="kpoll-title"><?php echo JText::_('COM_KUNENA_POLL_TITLE'); ?></label>
				<input type="text" class="inputbox" name="poll_title" id="kpoll-title"
						maxlength="100" size="40"
						value="<?php echo $this->escape( $this->poll->title ) ?>"
				/>
				<i id="kbutton-poll-add" class="icon-plus"
					alt="<?php echo JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION'); ?>"> </i> <i id="kbutton-poll-rem" class="icon-minus" alt="<?php echo JText::_('COM_KUNENA_POLL_REMOVE_POLL_OPTION'); ?>"> </i>
				<label class="kpoll-term-lbl" for="kpoll-time-to-live"><?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
				<?php echo JHtml::_('calendar', isset($this->poll->polltimetolive) ? $this->escape($this->poll->polltimetolive) : '0000-00-00', 'poll_time_to_live', 'kpoll-time-to-live', '%Y-%m-%d'); ?>
				<div id="kpoll-alert-error" class="alert" style="display:none;">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?php echo JText::sprintf('COM_KUNENA_ALERT_WARNING_X', JText::_('COM_KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW')) ?>
				</div>
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
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close modal</button>
	</div>
</div>

<!-- end of Bootstrap modal to be used with bbcode editor -->
<div class="control-group">
	<div class="controls">
		<input type="hidden" id="kurl_emojis" name="kurl_emojis" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>" />
		<input type="hidden" id="kemojis_allowed" name="kemojis_allowed" value="<?php echo $this->config->disemoticons ?>" />
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo (JText::_('COM_KUNENA_MESSAGE')) ; ?></label>
	<div class="controls">
		<textarea class="span12 qreply" name="message" id="kbbcode-message" rows="12" tabindex="3" required="required"><?php echo $this->escape($this->message->message); ?></textarea>
	</div>
	<!-- Hidden preview placeholder -->
	<div class="controls" id="kbbcode-preview" style="display: none;"></div>
</div>
<?php if ($this->message->exists()) : ?>

<div class="control-group">
	<label class="control-label"><?php echo (JText::_('COM_KUNENA_EDITING_REASON')) ?></label>
	<div class="controls">
		<textarea class="input-xlarge" name="modified_reason" size="40" maxlength="200" type="text" value="<?php echo $this->modified_reason; ?>"></textarea>
	</div>
</div>
<?php endif; ?>
