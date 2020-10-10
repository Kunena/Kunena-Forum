<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;

$this->addScript('ckeditor.js');
$this->addScriptOptions('com_kunena.ckeditor_config', 'ckeditor_config.js');
$this->addScriptOptions('com_kunena.ckeditor_base', JUri::root());

$this->addScript('assets/js/edit.js');

echo $this->subLayout('Widget/Datepicker');

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('jquery.caret.js');
$this->addScript('jquery.atwho.js');
$this->addStyleSheet('jquery.atwho.css');
$pollcheck = isset($this->poll);
$topictemplate = !KunenaConfig::getInstance()->pickup_category;
?>
<script>
	function localstorage() {
		localStorage.getItem("copyKunenaeditor");
	}

	function localstorageremove() {
		localStorage.removeItem("copyKunenaeditor");
	}
</script>

<textarea class="span12" name="message" id="message" rows="12" tabindex="7" required="required"
          placeholder="<?php echo Text::_('COM_KUNENA_ENTER_MESSAGE') ?>"><?php if (!empty($this->message->getCategory()->topictemplate) && !$this->message->getTopic()->first_post_id && $topictemplate)
	{
		echo $this->message->getCategory()->topictemplate;
	}
	else
	{
		echo $this->escape($this->message->message);
	} ?>
</textarea>

<!-- Hidden preview placeholder -->
<div class="controls" id="kbbcode-preview" style="display: none;"></div>

<!-- end of Bootstrap modal to be used with bbcode editor -->
<div class="control-group">
	<div class="controls">
		<input type="hidden" id="kurl_emojis" name="kurl_emojis"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>"/>
		<input type="hidden" id="kemojis_allowed" name="kemojis_allowed"
		       value="<?php echo $this->config->disemoticons ? 0 : 1 ?>"/>
	</div>
</div>
