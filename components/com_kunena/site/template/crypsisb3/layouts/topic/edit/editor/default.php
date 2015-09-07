<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die ();

$this->getBBcodesEnabled();

// Kunena bbcode editor
?>
<div class="form-group">
	<label class="control-label col-md-4"><?php echo (JText::_('COM_KUNENA_MESSAGE')) ; ?></label>
	<div class="controls">
		<ul id="tabs_kunena_editor" class="nav nav-tabs">
			<li class="active"><a href="#write" data-toggle="tab"><?php echo JText::_('COM_KUNENA_EDITOR_TAB_WRITE_LABEL') ?></a></li>
			<li><a href="#preview" data-toggle="tab"><?php echo JText::_('COM_KUNENA_PREVIEW') ?></a></li>
		</ul>
		<textarea class="col-md-12 form-control" name="message" id="kbbcode-message" rows="12" tabindex="7" required="required"><?php echo $this->escape($this->message->message); ?></textarea>
	</div>

	<!-- Hidden preview placeholder -->
	<div class="controls" id="kbbcode-preview" style="display: none;"></div>
</div>

<!-- Bootstrap modal to be used with bbcode editor -->
<div id="modal-map" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_MAP_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_TYPE') ?>: <select id="modal-map-type" class="form-control"><option type="HYBRID"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_HYBRID') ?></option><option type="ROADMAP"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_ROADMAP') ?></option><option type="TERRAIN"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_TERRAIN') ?></option><option type="SATELLITE"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_SATELLITE') ?></option></select><br />
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_ZOOM_LEVEL') ?>: <select id="modal-map-zoomlevel" class="form-control"><option type="2">2</option><option type="4">4</option><option type="6">6</option><option type="8">8</option><option type="10">10</option><option type="12">12</option><option type="14">14</option><option type="16">16</option><option type="18">18</option></select><br />
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_CITY') ?>: <input name="modal-map-city" id="modal-map-city" type="text" value="" class="form-control" /></p>
			</div>
			<div class="modal-footer">
				<button id="map-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<?php $codeTypes = $this->getCodeTypes(); if (!empty($codeTypes)) :	?>
<div id="modal-code" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_CODE_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p>
					<?php echo $codeTypes; ?>
				</p>
			</div>
			<div class="modal-footer">
				<button id="code-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<div id="modal-picture" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_SIZE') ?>: <input class="form-control" name="modal-picture-size" id="modal-picture-size" type="text" value="" />
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_URL') ?>: <input class="form-control" name="modal-picture-url" id="modal-picture-url" type="text" value="" /></p>
			</div>
			<div class="modal-footer">
				<button id="picture-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<div id="modal-link" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_URL') ?>: <input class="form-control" name="modal-link-url" id="modal-link-url" type="text" value="" />
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_TEXT') ?>: <input class="form-control" name="modal-link-text" id="modal-link-text" type="text" value="" /></p>
			</div>
			<div class="modal-footer">
				<button id="link-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<div id="modal-video-settings" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_VIDEO_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_SIZE') ?>: <input class="form-control" name="modal-video-size" id="modal-video-size" type="text" maxlength="5" size="5" value="" />
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_WIDTH') ?>: <input class="form-control" name="modal-video-width" id="modal-video-width" type="text" maxlength="5" size="5" value="" />
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_HEIGHT') ?>: <input class="form-control" name="modal-video-height" id="modal-video-height" type="text" maxlength="5" size="5" value="" />
				<?php
					echo JText::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER');
				?>
				<select id="kvideoprovider-list-modal"
					name="provider" class="kbutton form-control">
					<?php
						$vid_provider = array ('', 'Bofunk', 'Break', 'Clipfish', 'DivX,divx]http://', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'MediaPlayer,mediaplayer]http://', 'Metacafe', 'MySpace', 'QuickTime,quicktime]http://', 'RealPlayer,realplayer]http://', 'RuTube', 'Sapo', 'Streetfire', 'Veoh', 'Videojug', 'Vimeo', 'Wideo.fr', 'YouTube' );
							foreach ( $vid_provider as $vid_type ) {
								$vid_type = explode ( ',', $vid_type );
									echo '<option value = "' . (! empty ( $vid_type [1] ) ? $this->escape($vid_type [1]) : Joomla\String\String::strtolower ( $this->escape($vid_type [0]) ) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
							}
						?>
				</select>
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_ID') ?>: <input class="form-control" name="modal-video-id" id="modal-video-id" type="text" maxlength="30" size="11" value="" /></p>
			</div>
			<div class="modal-footer">
				<button id="videosettings-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<div id="modal-video-urlprovider" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_VIDEO_URL_PROVIDER') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_VIDEO_URL_PROVIDER_URL') ?>: <input class="form-control" name="modal-video-urlprovider-input" id="modal-video-urlprovider-input" type="text" value="" /></p>
			</div>
			<div class="modal-footer">
				<button id="videourlprovider-modal-submit" class="btn btn-primary modal-submit"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<?php if (!$this->message->parent && isset($this->poll)) : ?>
<div id="modal-poll-settings" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_POLL_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<div id="kbbcode-poll-options">
					<?php JHtml::_('behavior.calendar'); ?>
					<label class="kpoll-title-lbl" for="kpoll-title"><?php echo JText::_('COM_KUNENA_POLL_TITLE'); ?></label>
					<input type="text" class="inputbox form-control" name="poll_title" id="kpoll-title"
						maxlength="100" size="40"
						value="<?php echo $this->escape( $this->poll->title ) ?>"
						/>
					<i id="kbutton-poll-add" class="glyphicon glyphicon-plus btn btn-xs btn-default"
						alt="<?php echo JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION'); ?>"> </i>
					<i id="kbutton-poll-rem" class="glyphicon glyphicon-minus btn btn-xs btn-default"
						alt="<?php echo JText::_('COM_KUNENA_POLL_REMOVE_POLL_OPTION'); ?>"> </i>
					<br>
					<label class="kpoll-term-lbl" for="kpoll-time-to-live"><?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
					<?php echo JHtml::_('calendar', isset($this->poll->polltimetolive) ? $this->escape($this->poll->polltimetolive) : '0000-00-00', 'poll_time_to_live', 'kpoll-time-to-live', '%Y-%m-%d', 'class="form-control"'); ?>
					<br>
					<div id="kpoll-alert-error" class="alert alert-notice" style="display:none;">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<?php echo JText::sprintf('COM_KUNENA_ALERT_WARNING_X', JText::_('COM_KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW')) ?>
					</div>
					<?php
					if($this->poll->exists()) {
						$x = 1;
						foreach ($this->poll->getOptions() as $poll_option) {
							echo '<div class="polloption">Option '.$x.' <input type="text" class="form-control" size="100" id="field_option'.$x.' '.'" name="polloptionsID['.$poll_option->id.']" value="'.$poll_option->text.'")" /></div>';
							$x++;
						}
					}
					?>
					<input type="hidden" name="nb_options_allowed" id="nb_options_allowed" value="<?php echo $this->config->pollnboptions; ?>" />
					<input type="hidden" name="number_total_options" id="numbertotal"
						value="<?php echo !empty ($this->polloptionstotal) ? $this->escape($this->polloptionstotal) : '' ?>" />
				</div>
			</div>
			<div class="modal-footer">
				<button id="poll-settings-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<div id="modal-emoticons" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Emoticons</h3>
			</div>
			<div class="modal-body">
				<div id="smilie"><?php
					$emoticons = KunenaHtmlParser::getEmoticons(0, 1);
					foreach ( $emoticons as $emo_code=>$emo_url ) {
						echo '<img class="smileyimage" src="' . $emo_url . '" border="0" alt="' . $emo_code . ' " title="' . $emo_code . ' " style="cursor:pointer"/> ';
					}
					?>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end of Bootstrap modal to be used with bbcode editor -->
<div class="control-group">
	<div class="controls">
		<input type="hidden" id="kurl_emojis" name="kurl_emojis" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>" />
		<input type="hidden" id="kemojis_allowed" name="kemojis_allowed" value="<?php echo $this->config->disemoticons ?>" />
	</div>
</div>
