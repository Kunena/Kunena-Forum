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
?>
<script>
	jQuery(function ($) {
		$('[id^=editor-]').wysibb();
	})
</script>

<textarea class="span12" name="message" id="editor-<?php echo $this->message->id; ?>" rows="12" tabindex="7"
          required="required"></textarea>
