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
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$this->wa->registerAndUseScript('ckeditor', 'media/kunena/core/js/ckeditor.js');
$this->doc->addScriptOptions('com_kunena.ckeditor_config', $this->template->params->get('ckeditorcustomprefixconfigfile') . 'ckeditor_config.js');
$this->doc->addScriptOptions('com_kunena.ckeditor_buttons_configuration', $this->template->params->get('editorButtons'));
$this->doc->addScriptOptions('com_kunena.ckeditor_subfolder', Uri::root(true));
$this->doc->addScriptOptions('com_kunena.ckeditor_skiname', $this->template->params->get('nameskinckeditor'));

HTMLHelper::_('bootstrap.tab');

// $this->getAllowedtoUseLinksImages();

// $this->addScript('edit.js');

// Echo $this->subLayout('Widget/Datepicker');

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
/*
$this->addScript('jquery.caret.js');
$this->addScript('jquery.atwho.js');
$this->addStyleSheet('jquery.atwho.css');*/

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
          placeholder="<?php echo Text::_('COM_KUNENA_ENTER_PRIVATE_MESSAGE') ?>"><?php if (!empty($this->message->getCategory()->topictemplate) && !$this->message->getTopic()->first_post_id && $topictemplate) {
                echo $this->message->getCategory()->topictemplate;
                       }

                       if (!empty($this->privateMessage->body)) {
                            echo $this->escape($this->privateMessage->body);
                       } ?>
    </textarea>
  </div>
</div>

<input type="hidden" name="nb_options_allowed" id="nb_options_allowed"
       value="<?php echo $this->config->pollNbOptions; ?>"/>

<!-- Hidden preview placeholder -->
<div class="controls" id="kbbcode-preview" style="display: none;"></div>

<!-- end of Bootstrap modal to be used with bbcode editor -->
<div class="control-group">
    <div class="controls">
        <input type="hidden" id="kurl_emojis" name="kurl_emojis"
               value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>"/>
        <input type="hidden" id="kemojis_allowed" name="kemojis_allowed"
               value="<?php echo $this->config->disableEmoticons ? 0 : 1 ?>"/>
    </div>
</div>
