<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

// [spoiler="Do not look here!"]I said so![/spoiler]

// Display spoiler text (hidden until you click it open).
$this->addScriptOptions('com_kunena.expand', Text::_('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND'));
$this->addScriptOptions('com_kunena.hide', Text::_('COM_KUNENA_LIB_BBCODE_SPOILER_HIDE'));
?>

<div class="kspoiler">
	<div class="kspoiler-header">
		<div class="kspoiler-title" style="display:inline-block;">
			<?php echo $this->title; ?>
		</div>
		<div class="kspoiler-expand" style="display:inline-block;">
			<input class="btn-link" type="button" id="kspoiler-show" value="<?php echo Text::_('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND'); ?>"/>
		</div>
		<div class="kspoiler-wrapper" style="display:none;">
			<div class="kspoiler-content">
				<?php echo $this->content; ?>
			</div>
		</div>
	</div>
</div>
