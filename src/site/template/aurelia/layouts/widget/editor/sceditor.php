<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Joomla\CMS\Uri\Uri;

$this->wa->registerAndUseScript('sceditor/sceditor', 'media/kunena/core/js/sceditor/sceditor.js')
        ->registerAndUseScript('sceditor/bbcode', 'media/kunena/core/js/sceditor/bbcode.js')
        ->registerAndUseStyle('sceditor/themes/default', 'media/kunena/core/css/sceditor/themes/default.css');

$this->doc->addScriptOptions('com_kunena.sceditor_style_path', URI::root() . 'media/kunena/core/css/sceditor/themes/content/default.css');

Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_SOUNDCLOUD');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_EBAY');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_EBAY_ITEM_ID');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_INSTAGRAM');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_INSTAGRAM_ID');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_X_SOCIAL');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_X_SOCIAL_ID');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_HYBRID');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_ROADMAP');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_TERRAIN');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_SATELITE');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_ZOOM_LEVEL');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_CITY');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_URL');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_TYPE');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_SIZE');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_HEIGHT');
Text::script('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_WIDTH');
Text::script('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL');
Text::script('COM_KUNENA_SCEDITOR_BUTTON_INSERT_POLL_TITLE');
Text::script('COM_KUNENA_SCEDITOR_BUTTON_BUTTON_ADD_POLL_OPTION');
Text::script('COM_KUNENA_SCEDITOR_BUTTON_BUTTON_REMOVE_POLL_OPTION');
Text::script('COM_KUNENA_SCEDITOR_BUTTON_INSERT_POLL_LIFE_SPAN');

// $this->getAllowedtoUseLinksImages();

$this->doc->addScriptOptions('com_kunena.template_editor_buttons_configuration', $this->template->params->get('editorButtons'));
$this->doc->addScriptOptions('com_kunena.root_path', Uri::root(true));
$this->doc->addScriptOptions('com_kunena.editor_emoticons', json_encode(KunenaParser::getEmoticons(0, 1, 0)));

$this->wa->registerAndUseScript('sceditor', 'components/com_kunena/template/aurelia/assets/js/sceditor.js');
HTMLHelper::_('bootstrap.tab');
// Echo $this->subLayout('Widget/Datepicker');

$topictemplate = !$this->config->pickup_category;
$this->doc->addScriptOptions('com_kunena.ckeditor_emoticons', json_encode(KunenaParser::getEmoticons(0, 1, 0)));
?>
<script>
    function localstorage() {
        localStorage.getItem("copyKunenaeditor");
    }

    function localstorageremove() {
        localStorage.removeItem("copyKunenaeditor");
    }
</script>

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#message-tab" type="button" role="tab" aria-controls="home" aria-selected="true"><?php echo Text::_('COM_KUNENA_MESSAGE_TAB_MESSAGE') ?></button>
  </li>
  <?php if ($this->config->privateMessage): ?>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#message_private-tab" type="button" role="tab" aria-controls="profile" aria-selected="false"><?php echo Text::_('COM_KUNENA_MESSAGE_TAB_PRIVATE_MESSAGE') ?></button>
  </li>
  <?php endif; ?>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="message-tab" role="tabpanel" aria-labelledby="home-tab">
<textarea class="span12" name="message" id="message" rows="12" tabindex="7"
          placeholder="<?php echo Text::_('COM_KUNENA_ENTER_MESSAGE') ?>"><?php if (!empty($this->message->getCategory()->topictemplate) && !$this->message->getTopic()->first_post_id && $topictemplate) {
                echo $this->message->getCategory()->topictemplate;
                       }

                       if (!empty($this->message->message)) {
                           echo $this->escape($this->message->message);
                       } ?>
</textarea>
</div>
  <div class="tab-pane fade" id="message_private-tab" role="tabpanel" aria-labelledby="profile-tab">
<textarea class="span12" name="message_private" id="message_private" rows="12" tabindex="7"
          placeholder="<?php echo Text::_('COM_KUNENA_ENTER_MESSAGE') ?>"><?php if (!empty($this->message->getCategory()->topictemplate) && !$this->message->getTopic()->first_post_id && $topictemplate) {
                echo $this->message->getCategory()->topictemplate;
                       }

                       if (!empty($this->message->message)) {
                           echo $this->escape($this->message->message);
                       } ?>
</textarea>
</div>
</div>

<input type="hidden" name="nb_options_allowed" id="nb_options_allowed"
       value="<?php echo $this->config->pollNbOptions; ?>"/>

<!-- end of Bootstrap modal to be used with bbcode editor -->
<div class="control-group">
    <div class="controls">
        <input type="hidden" id="kurl_emojis" name="kurl_emojis"
               value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>"/>
        <input type="hidden" id="kemojis_allowed" name="kemojis_allowed"
               value="<?php echo $this->config->disableEmoticons ? 0 : 1 ?>"/>
    </div>
</div>
