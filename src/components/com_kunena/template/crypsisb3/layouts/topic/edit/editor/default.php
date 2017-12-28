<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

$this->getBBcodesEnabled();

// Kunena bbcode editor
?>
<div class="form-group">
	<label class="control-label col-md-4"><?php echo (JText::_('COM_KUNENA_MESSAGE')); ?></label>
	<div class="controls" id="kbbcode-preview-real">
		<ul id="tabs_kunena_editor" class="nav nav-tabs">
			<li class="active"><a href="#write" data-toggle="tab"><?php echo JText::_('COM_KUNENA_EDITOR_TAB_WRITE_LABEL') ?></a></li>
			<li><a href="#preview" data-toggle="tab"><?php echo JText::_('COM_KUNENA_PREVIEW') ?></a></li>
		</ul>
		<textarea class="form-control" name="message" id="kbbcode-message" rows="12" tabindex="7" placeholder="<?php echo JText::_('COM_KUNENA_ENTER_MESSAGE') ?>"><?php echo $this->escape($this->message->message); ?></textarea>
	</div>

	<!-- Hidden preview placeholder -->
	<div class="controls" id="kbbcode-preview" style="display: none;"></div>
</div>

<!-- Bootstrap modal to be used with bbcode editor -->
<div id="modal-map" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_MAP_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_TYPE') ?>: <select id="modal-map-type"><option value="HYBRID"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_HYBRID') ?></option><option value="ROADMAP"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_ROADMAP') ?></option><option value="TERRAIN"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_TERRAIN') ?></option><option value="SATELLITE"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_SATELLITE') ?></option></select><br />
					<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_ZOOM_LEVEL') ?>: <select id="modal-map-zoomlevel"><option value="2">2</option><option value="4">4</option><option value="6">6</option><option value="8">8</option><option value="10">10</option><option value="12">12</option><option value="14">14</option><option value="16">16</option><option value="18">18</option></select><br />
					<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_CITY') ?>: <input name="modal-map-city" id="modal-map-city" type="text" value="" placeholder="<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_CITY_DESC') ?>" /></p>
			</div>
			<div class="modal-footer">
				<button id="map-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<?php $codeTypes = $this->getCodeTypes(); if (!empty($codeTypes)) :	?>
<div id="modal-code" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
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
<div id="modal-picture" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_SIZE') ?>:
				<select id="kpicture-size-list-modal"
			name="modal-picture-size" class="kbutton">
			<?php
				$vid_provider = array ('', '20', '40', '80', '100', '150', '200', '250', '500', '1000');
					foreach ( $vid_provider as $vid_type ) {
						$vid_type = explode ( ',', $vid_type );
						echo '<option value = "' . (!empty ( $vid_type [1] ) ? $this->escape($vid_type [1]) : Joomla\String\StringHelper::strtolower($this->escape($vid_type [0])) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
					}
				?>
		</select>
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_ALT') ?>: <input class="form-control" name="modal-picture-alt" id="modal-picture-alt" type="text" value="" placeholder="<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_ALT_PLACEHOLDER') ?>" />
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_URL') ?>: <input name="modal-picture-url" id="modal-picture-url" type="text" value="" placeholder="<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_URL_PLACEHOLDER') ?>" /></p>
			</div>
			<div class="modal-footer">
				<button id="picture-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<div id="modal-link" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_URL') ?>: <input class="form-control" name="modal-link-url" id="modal-link-url" type="text" value="" placeholder="<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_URL_PLACEHOLDER') ?>" />
				<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_TEXT') ?>: <input class="form-control" name="modal-link-text" id="modal-link-text" type="text" value="" placeholder="<?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_TEXT_PLACEHOLDER') ?>" /></p>
			</div>
			<div class="modal-footer">
				<button id="link-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<div id="modal-video-settings" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
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
									echo '<option value = "' . (!empty($vid_type [1]) ? $this->escape($vid_type [1]) : Joomla\String\StringHelper::strtolower($this->escape($vid_type [0])) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
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
<div id="modal-video-urlprovider" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_VIDEO') ?></h3>
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
<div id="modal-poll-settings" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_TITLE_POLL_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<div id="kbbcode-poll-options">
					<div class="input-group col-md-12" style="padding: 0;">
					<label class="kpoll-title-lbl" for="kpoll-title"><?php echo JText::_('COM_KUNENA_POLL_TITLE'); ?></label>
					<input type="text" class="inputbox form-control col-md-12" name="poll_title" id="kpoll-title"
						maxlength="150" size="40"
						value="<?php echo $this->escape($this->poll->title) ?>"
						/>
					</div>
					<div class="clearfix"></div>
					<?php echo KunenaIcons::poll_add();?>
					<?php echo KunenaIcons::poll_rem();?>
					<div class="clearfix"></div>
					<div id="datepoll-container">
						<label class="kpoll-term-lbl" for="kpoll-time-to-live"><?php echo JText::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
						<div class="input-group date">
							<input type="text" class="form-control" id="poll_time_to_live" data-date-format="mm/dd/yyyy" value="<?php echo !empty($this->poll->polltimetolive) ? $this->poll->polltimetolive : '' ?>">
							<span class="input-group-addon">
								<?php echo KunenaIcons::grid();?>
							</span>
						</div>
					</div>
					<div class="clearfix"></div>
					<br />
					<div id="kpoll-alert-error" class="alert alert-notice" style="display:none;">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<?php echo JText::sprintf('COM_KUNENA_ALERT_WARNING_X', JText::_('COM_KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW')) ?>
					</div>
					<?php
					if($this->poll->exists()) {
						$x = 1;
						foreach ($this->poll->getOptions() as $poll_option) {
							echo '<div class="polloption">Option ' . $x . ' <input type="text" class="form-control" size="100" id="field_option' . $x . ' ' . '" name="polloptionsID[' . $poll_option->id . ']" value="' . $poll_option->text . '")" /></div>';
							$x++;
						}
					}
					?>
					<input type="hidden" name="nb_options_allowed" id="nb_options_allowed" value="<?php echo $this->config->pollnboptions; ?>" />
					<input type="hidden" name="number_total_options" id="numbertotal"
						value="<?php echo !empty($this->polloptionstotal) ? $this->escape($this->polloptionstotal) : '' ?>" />
				</div>
			</div>
			<div class="modal-footer">
				<button id="poll-settings-modal-submit" class="btn btn-primary"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
				<button id="clearpoll" class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_CLEAR_MODAL') ?></button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<div id="modal-emoticons" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_EDITOR_EMOTICONS_LABEL') ?></h3>
			</div>
			<div class="modal-body">
				<div id="smilie"><?php
					$emoticons = KunenaHtmlParser::getEmoticons(0, 1);
					foreach ($emoticons as $emo_code => $emo_url) {
						$data = getimagesize(JPATH_ROOT . '/' . $emo_url);
						$width = $data[0];
						$height = $data[1];
						echo '<img class="smileyimage" src="' . $emo_url . '" border="0" width="' . $width .'" height="' . $height . '"  alt="' . $emo_code . ' " title="' . $emo_code . ' " style="cursor:pointer"/> ';
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
