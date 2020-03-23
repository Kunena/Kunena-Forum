<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$this->addScript('assets/js/jquery.wysibb.js');
$this->addStyleSheet('assets/css/wbbtheme.css');
$this->addScript('assets/js/wysibb.lang.js');
echo $this->subLayout('Widget/Datepicker');
$this->addScript('assets/js/jquery.caret.js');
$this->addScript('assets/js/jquery.atwho.js');
$this->addStyleSheet('assets/css/jquery.atwho.css');

Text::script('COM_KUNENA_WYSIBB_EDITOR_BOLD');
Text::script('COM_KUNENA_WYSIBB_EDITOR_ITALIC');
Text::script('COM_KUNENA_WYSIBB_EDITOR_UNDERLINE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_STRIKE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_LINK');
Text::script('COM_KUNENA_WYSIBB_EDITOR_IMAGE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SUP');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SUB');
Text::script('COM_KUNENA_WYSIBB_EDITOR_JUSTIFYLEFT');
Text::script('COM_KUNENA_WYSIBB_EDITOR_JUSTIFYCENTER');
Text::script('COM_KUNENA_WYSIBB_EDITOR_JUSTIFYRIGHT');
Text::script('COM_KUNENA_WYSIBB_EDITOR_TABLE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_BULLIST');
Text::script('COM_KUNENA_WYSIBB_EDITOR_NUMLIST');
Text::script('COM_KUNENA_WYSIBB_EDITOR_QUOTE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_OFFTOP');
Text::script('COM_KUNENA_WYSIBB_EDITOR_CODE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SPOILER');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FONTCOLOR');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FONTFAMILY');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_VERYSMALL');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_SMALL');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_NORMAL');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_BIG');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_VERTBIG');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SMILEBOX');
Text::script('COM_KUNENA_WYSIBB_EDITOR_VIDEO');
Text::script('COM_KUNENA_WYSIBB_EDITOR_REMOVEFORMAT');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_LINK_TITLE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_LINK_TEXT');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_LINK_URL');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_EMAIL_TEXT');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_EMAIL_URL');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_LINK_TAB1');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMG_TITLE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMG_TAB1');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMG_TAB2');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMGSRC_TEXT');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMG_BTN');
Text::script('COM_KUNENA_WYSIBB_EDITOR_ADD_ATTACH');
Text::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_VIDEO_TEXT');
Text::script('COM_KUNENA_WYSIBB_EDITOR_CLOSE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SAVE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_CANCEL');
Text::script('COM_KUNENA_WYSIBB_EDITOR_REMOVE');
Text::script('COM_KUNENA_WYSIBB_EDITOR_VALIDATION_ERR');
Text::script('COM_KUNENA_WYSIBB_EDITOR_ERROR_ONUPLOAD');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FILEUPLOAD_TEXT1');
Text::script('COM_KUNENA_WYSIBB_EDITOR_FILEUPLOAD_TEXT2');
Text::script('COM_KUNENA_WYSIBB_EDITOR_LOADING');
Text::script('COM_KUNENA_WYSIBB_EDITOR_AUTO');
Text::script('COM_KUNENA_WYSIBB_EDITOR_VIEWS');
Text::script('COM_KUNENA_WYSIBB_EDITOR_DOWNLOADS');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM1');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM2');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM3');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM4');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM5');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM6');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM7');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM8');
Text::script('COM_KUNENA_WYSIBB_EDITOR_SM9');

$this->ktemplate  = KunenaFactory::getTemplate();
$templatesettings = $this->ktemplate->params;
$topictemplate    = !KunenaConfig::getInstance()->pickup_category;
$settings         = $templatesettings->get('wysibb');
?>
<script>
	var wbbOpt = {
		lang: "kunena",
		buttons: "<?php echo $settings;?>"
	};
	jQuery(function ($) {
		$("#editor").wysibb(wbbOpt);
	});
</script>

<div class="control-group">
	<label class="control-label"><?php echo Text::_('COM_KUNENA_MESSAGE'); ?></label>
	<div class="controls">
		<textarea class="form-control" name="message" id="editor" rows="12" tabindex="7
		          placeholder="<?php echo Text::_('COM_KUNENA_ENTER_MESSAGE') ?>"><?php if (!empty($this->message->getCategory()->topictemplate) && !$this->message->getTopic()->first_post_id && $topictemplate)
			{
				echo $this->message->getCategory()->topictemplate;
			}
			else
			{
				echo $this->escape($this->message->message);
			} ?></textarea>
	</div>
</div>

<!-- Bootstrap modal to be used with bbcode editor -->
<div id="modal-map" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_MAP_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_TYPE') ?>: <select id="modal-map-type">
						<option value="HYBRID"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_HYBRID') ?></option>
						<option value="ROADMAP"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_ROADMAP') ?></option>
						<option value="TERRAIN"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_TERRAIN') ?></option>
						<option value="SATELLITE"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_SATELLITE') ?></option>
					</select><br/>
					<?php echo Text::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_ZOOM_LEVEL') ?>: <select
							id="modal-map-zoomlevel">
						<option value="2">2</option>
						<option value="4">4</option>
						<option type="6">6</option>
						<option value="8">8</option>
						<option value="10">10</option>
						<option value="12">12</option>
						<option value="14">14</option>
						<option value="16">16</option>
						<option value="18">18</option>
					</select><br/>
					<?php echo Text::_('COM_KUNENA_EDITOR_MODAL_MAP_SETTINGS_CITY') ?>: <input name="modal-map-city"
					                                                                            id="modal-map-city"
					                                                                            type="text"
					                                                                            value=""/></p>
			</div>
			<div class="modal-footer">
				<button id="map-modal-submit"
				        class="btn btn-primary"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal"
				        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<?php $codeTypes = $this->getCodeTypes();
if (!empty($codeTypes)) : ?>
	<div id="modal-code" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	     aria-hidden="true" style="display: none;">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_CODE_SETTINGS') ?></h3>
				</div>
				<div class="modal-body">
					<p>
						<?php echo $codeTypes; ?>
					</p>
				</div>
				<div class="modal-footer">
					<button id="code-modal-submit"
					        class="btn btn-primary"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
					<button class="btn btn-default" data-dismiss="modal"
					        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<div id="modal-picture" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_SIZE') ?>: <input
							class="form-control"
							name="modal-picture-size"
							id="modal-picture-size" type="text"
							value=""/>
					<?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_PICTURE_SETTINGS_URL') ?>: <input
							class="form-control" name="modal-picture-url"
							id="modal-picture-url" type="text" value=""/>
				</p>
			</div>
			<div class="modal-footer">
				<button id="picture-modal-submit"
				        class="btn btn-primary"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal"
				        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<div id="modal-link" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_URL') ?>: <input
							class="form-control" name="modal-link-url"
							id="modal-link-url" type="text" value=""/>
					<?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_TEXT') ?>: <input
							class="form-control" name="modal-link-text"
							id="modal-link-text" type="text" value=""/></p>
			</div>
			<div class="modal-footer">
				<button id="link-modal-submit"
				        class="btn btn-primary"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal"
				        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<div id="modal-video-settings" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_VIDEO_SETTINGS') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_SIZE') ?>: <input
							class="form-control" name="modal-video-size"
							id="modal-video-size" type="text" maxlength="5"
							size="5" value=""/>
					<?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_WIDTH') ?>: <input
							class="form-control" name="modal-video-width"
							id="modal-video-width" type="text"
							maxlength="5" size="5" value=""/>
					<?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_HEIGHT') ?>: <input
							class="form-control"
							name="modal-video-height"
							id="modal-video-height" type="text"
							maxlength="5" size="5" value=""/>
					<?php
					echo Text::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER');
					?>
					<select id="kvideoprovider-list-modal"
					        name="provider" class="kbutton form-control">
						<?php
						$vid_provider = array('', 'Bofunk', 'Break', 'Clipfish', 'DivX,divx]http://', 'Flash,flash]http://', 'FlashVars,flashvars param=]http://', 'MediaPlayer,mediaplayer]http://', 'Metacafe', 'MySpace', 'QuickTime,quicktime]http://', 'RealPlayer,realplayer]http://', 'RuTube', 'Sapo', 'Streetfire', 'Veoh', 'Videojug', 'Vimeo', 'Wideo.fr', 'YouTube');
						foreach ($vid_provider as $vid_type)
						{
							$vid_type = explode(',', $vid_type);
							echo '<option value = "' . (!empty($vid_type [1]) ? $this->escape($vid_type [1]) : Joomla\String\StringHelper::strtolower($this->escape($vid_type [0])) . '') . '">' . $this->escape($vid_type [0]) . '</option>';
						}
						?>
					</select>
					<?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_LINK_SETTINGS_ID') ?>: <input
							class="form-control" name="modal-video-id"
							id="modal-video-id" type="text" maxlength="30"
							size="11" value=""/></p>
			</div>
			<div class="modal-footer">
				<button id="videosettings-modal-submit"
				        class="btn btn-primary"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal"
				        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<div id="modal-video-urlprovider" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_VIDEO_URL_PROVIDER') ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_VIDEO_URL_PROVIDER_URL') ?>: <input
							class="form-control"
							name="modal-video-urlprovider-input"
							id="modal-video-urlprovider-input"
							type="text" value=""/></p>
			</div>
			<div class="modal-footer">
				<button id="videourlprovider-modal-submit"
				        class="btn btn-primary modal-submit"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
				<button class="btn btn-default" data-dismiss="modal"
				        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<?php if (!$this->message->parent && isset($this->poll)) : ?>
	<div id="modal-poll-settings" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	     aria-hidden="true" style="display: none;">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_TITLE_POLL_SETTINGS') ?></h3>
				</div>
				<div class="modal-body">
					<div id="kbbcode-poll-options">
						<label class="kpoll-title-lbl"
						       for="kpoll-title"><?php echo Text::_('COM_KUNENA_POLL_TITLE'); ?></label>
						<input type="text" class="inputbox form-control" name="poll_title" id="kpoll-title"
						       maxlength="150" size="40"
						       value="<?php echo $this->escape($this->poll->title) ?>"
						/>
						<i id="kbutton-poll-add" class="glyphicon glyphicon-plus btn btn-xs btn-default"
						   alt="<?php echo Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION'); ?>"> </i>
						<i id="kbutton-poll-rem" class="glyphicon glyphicon-minus btn btn-xs btn-default"
						   alt="<?php echo Text::_('COM_KUNENA_POLL_REMOVE_POLL_OPTION'); ?>"> </i>
						<br>
						<label class="kpoll-term-lbl"
						       for="kpoll-time-to-live"><?php echo Text::_('COM_KUNENA_POLL_TIME_TO_LIVE'); ?></label>
						<div id="datepoll-container" class="col-md-5">
							<div class="input-append date">
								<input type="text" class="form-control" name="poll_time_to_live"
								       data-date-format="mm/dd/yyyy"
								       value="<?php echo !empty($this->poll->polltimetolive) ? $this->poll->polltimetolive : '' ?>">
								<span class="input-group-addon">
								<?php echo KunenaIcons::grid(); ?>
							</span>
							</div>
						</div>
						<br>
						<div id="kpoll-alert-error" class="alert alert-notice" style="display:none;">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<?php echo Text::sprintf('COM_KUNENA_ALERT_WARNING_X', Text::_('COM_KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW')) ?>
						</div>
						<?php
						if ($this->poll->exists())
						{
							$x = 1;
							foreach ($this->poll->getOptions() as $poll_option)
							{
								echo '<div class="polloption">Option ' . $x . ' <input type="text" class="form-control" size="100" id="field_option' . $x . ' ' . '" name="polloptionsID[' . $poll_option->id . ']" value="' . $poll_option->text . '")" /></div>';
								$x++;
							}
						}
						?>
						<input type="hidden" name="nb_options_allowed" id="nb_options_allowed"
						       value="<?php echo $this->config->pollnboptions; ?>"/>
						<input type="hidden" name="number_total_options" id="numbertotal"
						       value="<?php echo !empty($this->polloptionstotal) ? $this->escape($this->polloptionstotal) : '' ?>"/>
					</div>
				</div>
				<div class="modal-footer">
					<button id="poll-settings-modal-submit"
					        class="btn btn-primary"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_ADD_LABEL') ?></button>
					<button class="btn btn-default" data-dismiss="modal"
					        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<div id="modal-emoticons" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Emoticons</h3>
			</div>
			<div class="modal-body">
				<div id="smilie"><?php
					$emoticons = KunenaHtmlParser::getEmoticons(0, 1);
					foreach ($emoticons as $emo_code => $emo_url)
					{
						echo '<img class="smileyimage" src="' . $emo_url . '" border="0" width="20" height="20" alt="' . $emo_code . ' " title="' . $emo_code . ' " style="cursor:pointer"/> ';
					}
					?>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal"
				        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end of Bootstrap modal to be used with bbcode editor -->
<div class="control-group">
	<div class="controls">
		<input type="hidden" id="kurl_emojis" name="kurl_emojis"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>"/>
		<input type="hidden" id="kemojis_allowed" name="kemojis_allowed"
		       value="<?php echo $this->config->disemoticons ?>"/>
	</div>
</div>

