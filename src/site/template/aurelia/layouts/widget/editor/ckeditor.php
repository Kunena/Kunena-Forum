<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

// The editor of Kunena: CKEditor 5 with the BBCode of Kunena (built from editor/ckeditor5 into media/kunena/core/js/ckeditor5).
$this->wa->registerAndUseStyle('kunena.editor', 'media/kunena/core/js/ckeditor5/ckeditor5.css');

// The language of the editor: the one of the user, else the one of the page; CKEditor names it "de" or "pt-br".
$user         = Factory::getApplication()->getIdentity();
$userLanguage = $user->getParam('language', 'default');
$languageTag  = LanguageHelper::exists($userLanguage) ? $userLanguage : Factory::getApplication()->getLanguage()->getTag();
$editorLanguage = 'en';

foreach ([strtolower($languageTag), strtolower(substr($languageTag, 0, 2))] as $candidate) {
    if ($candidate !== 'en' && is_file(JPATH_ROOT . '/media/kunena/core/js/ckeditor5/translations/' . $candidate . '.js')) {
        $editorLanguage = $candidate;
        $this->wa->registerAndUseScript('kunena.editor.language', 'media/kunena/core/js/ckeditor5/translations/' . $candidate . '.js');
        break;
    }
}

$this->wa->registerAndUseScript('kunena.editor', 'media/kunena/core/js/ckeditor5/kunena-editor.js', [], ['defer' => false]);
$this->doc->addScriptOptions('com_kunena.ckeditor_config', 'ckeditor5');
$this->doc->addScriptOptions('com_kunena.ckeditor_language', $editorLanguage);
$this->doc->addScriptOptions('com_kunena.ckeditor_buttons_configuration', $this->template->params->get('editorButtons'));
$this->doc->addScriptOptions('com_kunena.ckeditor_smiley_path', Uri::root(true) . '/media/kunena/emoticons/');
$this->doc->addScriptOptions('com_kunena.localstorage', $this->template->params->get('localstorage'));

Text::script('COM_KUNENA_EDITOR_LINK_TARGET');
Text::script('COM_KUNENA_EDITOR_LINK_TARGET_SAME');
Text::script('COM_KUNENA_EDITOR_LINK_TARGET_NEW');

// The texts of the editor and of its dialogs (media/kunena/core/js/ckeditor5).
foreach ([
    'COM_KUNENA_EDITOR_CONFIDENTIAL',
    'COM_KUNENA_EDITOR_DIALOG_POLLS_PROPERTIES',
    'COM_KUNENA_EDITOR_EBAY',
    'COM_KUNENA_EDITOR_EMOTICONS',
    'COM_KUNENA_EDITOR_HIDE',
    'COM_KUNENA_EDITOR_INSTAGRAM',
    'COM_KUNENA_EDITOR_MAP',
    'COM_KUNENA_EDITOR_MAP_PLACE',
    'COM_KUNENA_EDITOR_MAP_TYPE',
    'COM_KUNENA_EDITOR_MAP_ZOOM',
    'COM_KUNENA_EDITOR_PASTE_IMAGES_NOT_ALLOWED',
    'COM_KUNENA_EDITOR_POLL',
    'COM_KUNENA_EDITOR_SIZE_1',
    'COM_KUNENA_EDITOR_SIZE_2',
    'COM_KUNENA_EDITOR_SIZE_3',
    'COM_KUNENA_EDITOR_SIZE_4',
    'COM_KUNENA_EDITOR_SIZE_5',
    'COM_KUNENA_EDITOR_SIZE_6',
    'COM_KUNENA_EDITOR_SOUNDCLOUD',
    'COM_KUNENA_EDITOR_SPOILER',
    'COM_KUNENA_EDITOR_TWEET',
    'COM_KUNENA_EDITOR_VIDEO',
    'COM_KUNENA_EDITOR_VIDEO_HEIGHT',
    'COM_KUNENA_EDITOR_VIDEO_ID',
    'COM_KUNENA_EDITOR_VIDEO_OR_PROVIDER',
    'COM_KUNENA_EDITOR_VIDEO_PROVIDER',
    'COM_KUNENA_EDITOR_VIDEO_SIZE',
    'COM_KUNENA_EDITOR_VIDEO_URL',
    'COM_KUNENA_EDITOR_VIDEO_WIDTH',
    'COM_KUNENA_POLL_ADD_POLL_OPTION',
    'COM_KUNENA_POLL_OPTION_NAME',
    'COM_KUNENA_POLL_REMOVE_POLL_OPTION',
    'COM_KUNENA_POLL_TIME_TO_LIVE',
    'COM_KUNENA_POLL_TITLE',
] as $key) {
    Text::script($key);
}

HTMLHelper::_('bootstrap.tab');

// The dialogs of the editor (map, link, poll, video...) are modals of Bootstrap (dialogs.js: window.bootstrap.Modal). Each script of
// Bootstrap adds only its own part: without this one a dialog was an error of the editor on a page which loads no other modal.
HTMLHelper::_('bootstrap.modal');

// $this->getAllowedtoUseLinksImages();

// $this->addScript('edit.js');


$topictemplate = !$this->config->pickupCategory;
$this->doc->addScriptOptions('com_kunena.ckeditor_emoticons', json_encode(KunenaParser::getEmoticons(0, 1, 0)));

$template = KunenaTemplate::getInstance();
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
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#message-tab" type="button" role="tab" aria-controls="message-tab" aria-selected="true"><?php echo Text::_('COM_KUNENA_MESSAGE_TAB_MESSAGE') ?></button>
    </li>
    <?php if ($this->config->privateMessage): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#message_private-tab" type="button" role="tab" aria-controls="message_private-tab" aria-selected="false"><?php echo Text::_('COM_KUNENA_MESSAGE_TAB_PRIVATE_MESSAGE') ?></button>
        </li>
    <?php endif; ?>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="message-tab" role="tabpanel" aria-labelledby="home-tab">
        <textarea class="span12" name="message" id="message" rows="12" tabindex="7"
            placeholder="<?php echo Text::_('COM_KUNENA_ENTER_MESSAGE') ?>"><?php if (!empty($this->message->getCategory()->topictemplate) && !$this->message->getTopic()->first_post_id && $topictemplate) {
                                                                                echo $this->escape($this->message->getCategory()->topictemplate);
                                                                            }

                                                                            if (!empty($this->message->message)) {
                                                                                echo $this->escape($this->message->message);
                                                                            } ?></textarea>
    </div>
    <div class="tab-pane fade" id="message_private-tab" role="tabpanel" aria-labelledby="profile-tab">
        <textarea class="span12" name="message_private" id="message_private" rows="12" tabindex="7"
            placeholder="<?php echo Text::_('COM_KUNENA_ENTER_PRIVATE_MESSAGE') ?>"><?php if (!empty($this->privateMessage->body)) {
                                                                                        echo $this->escape($this->privateMessage->body);
                                                                                    } ?></textarea>
    </div>
</div>

<input type="hidden" name="nb_options_allowed" id="nb_options_allowed"
    value="<?php echo $this->config->pollNbOptions; ?>" />

<!-- Hidden preview placeholder -->
<div class="controls" id="kbbcode-preview" style="display: none;"></div>

<!-- end of Bootstrap modal to be used with bbcode editor -->
<div class="control-group">
    <div class="controls">
        <input type="hidden" id="kurl_emojis" name="kurl_emojis"
            value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>" />
        <input type="hidden" id="kemojis_allowed" name="kemojis_allowed"
            value="<?php echo $template->params->get('disableEmoticons', '0') ? 0 : 1 ?>" />
    </div>
</div>