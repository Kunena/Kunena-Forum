<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.BBCode
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// [spoiler="Do not look here!"]I said so![/spoiler]

// Display spoiler text (hidden until you click it open).

$hide = ' style="display:none"';
$hidden = !empty($this->hidden);
?>
<div class="kspoiler">
	<div class="kspoiler-header">
		<span class="kspoiler-title">
			<?php echo $this->title; ?>
		</span>
		<span class="kspoiler-expand"<?php if (!$hidden) { echo $hide; } ?>>
			<?php echo JText::_('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND'); ?>
		</span>
		<span class="kspoiler-hide"<?php if ($hidden) { echo $hide; } ?>>
			<?php echo JText::_('COM_KUNENA_LIB_BBCODE_SPOILER_HIDE'); ?>
		</span>
	</div>
	<div class="kspoiler-wrapper"><div class="kspoiler-content"<?php if ($hidden) { echo $hide; } ?>>
			<?php echo $this->content; ?>
		</div>
	</div>
</div>
