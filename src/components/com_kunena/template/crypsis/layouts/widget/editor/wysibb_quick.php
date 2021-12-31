<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;

$this->addScript('edit.js');

$this->addScript('jquery.wysibb.js');
$this->addStyleSheet('wbbtheme.css');
$this->addScript('jquery.caret.js');
$this->addScript('jquery.atwho.js');
$this->addStyleSheet('jquery.atwho.css');
$this->addScript('wysibb.lang.js');

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

$settings         = $this->template->params->get('wysibb');
?>
<script>
	var wbbOpt = {
		lang: "kunena",
		buttons: "<?php echo $settings;?>"
	};
	jQuery(function ($) {
		$('[id^=editor-]').wysibb(wbbOpt);
	});
</script>

<textarea class="span12" name="message" id="editor-<?php echo $this->message->id; ?>" rows="12" tabindex="7"></textarea>
