<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Topic
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

$this->addScript('assets/js/jquery.wysibb.js');
$this->addStyleSheet('assets/css/wbbtheme.css');
$this->addScript('assets/js/jquery.caret.js');
$this->addScript('assets/js/jquery.atwho.js');
$this->addStyleSheet('assets/css/jquery.atwho.css');
$this->addScript('assets/js/wysibb.lang.js');

JText::script('COM_KUNENA_WYSIBB_EDITOR_BOLD');
JText::script('COM_KUNENA_WYSIBB_EDITOR_ITALIC');
JText::script('COM_KUNENA_WYSIBB_EDITOR_UNDERLINE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_STRIKE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_LINK');
JText::script('COM_KUNENA_WYSIBB_EDITOR_IMAGE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SUP');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SUB');
JText::script('COM_KUNENA_WYSIBB_EDITOR_JUSTIFYLEFT');
JText::script('COM_KUNENA_WYSIBB_EDITOR_JUSTIFYCENTER');
JText::script('COM_KUNENA_WYSIBB_EDITOR_JUSTIFYRIGHT');
JText::script('COM_KUNENA_WYSIBB_EDITOR_TABLE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_BULLIST');
JText::script('COM_KUNENA_WYSIBB_EDITOR_NUMLIST');
JText::script('COM_KUNENA_WYSIBB_EDITOR_QUOTE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_OFFTOP');
JText::script('COM_KUNENA_WYSIBB_EDITOR_CODE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SPOILER');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FONTCOLOR');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FONTFAMILY');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_VERYSMALL');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_SMALL');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_NORMAL');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_BIG');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FONTSIZE_VERTBIG');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SMILEBOX');
JText::script('COM_KUNENA_WYSIBB_EDITOR_VIDEO');
JText::script('COM_KUNENA_WYSIBB_EDITOR_REMOVEFORMAT');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_LINK_TITLE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_LINK_TEXT');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_LINK_URL');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_EMAIL_TEXT');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_EMAIL_URL');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_LINK_TAB1');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMG_TITLE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMG_TAB1');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMG_TAB2');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMGSRC_TEXT');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_IMG_BTN');
JText::script('COM_KUNENA_WYSIBB_EDITOR_ADD_ATTACH');
JText::script('COM_KUNENA_WYSIBB_EDITOR_MODAL_VIDEO_TEXT');
JText::script('COM_KUNENA_WYSIBB_EDITOR_CLOSE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SAVE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_CANCEL');
JText::script('COM_KUNENA_WYSIBB_EDITOR_REMOVE');
JText::script('COM_KUNENA_WYSIBB_EDITOR_VALIDATION_ERR');
JText::script('COM_KUNENA_WYSIBB_EDITOR_ERROR_ONUPLOAD');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FILEUPLOAD_TEXT1');
JText::script('COM_KUNENA_WYSIBB_EDITOR_FILEUPLOAD_TEXT2');
JText::script('COM_KUNENA_WYSIBB_EDITOR_LOADING');
JText::script('COM_KUNENA_WYSIBB_EDITOR_AUTO');
JText::script('COM_KUNENA_WYSIBB_EDITOR_VIEWS');
JText::script('COM_KUNENA_WYSIBB_EDITOR_DOWNLOADS');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM1');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM2');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM3');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM4');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM5');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM6');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM7');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM8');
JText::script('COM_KUNENA_WYSIBB_EDITOR_SM9');
?>
<script>
	var wbbOpt = {
		lang: "kunena"
	};
	jQuery(function ($) {
		$('[id^=editor-]').wysibb(wbbOpt);
	});
</script>

<textarea class="span12" name="message" id="editor-<?php echo $this->message->id; ?>" rows="12" tabindex="7"
          required="required"></textarea>
