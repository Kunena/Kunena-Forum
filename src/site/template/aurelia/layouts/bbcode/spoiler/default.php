<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 */

namespace Kunena\Forum\Site;

use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die();

// [spoiler="Do not look here!"]I said so![/spoiler]

// Get available in javascript topic.js the language strings
Text::script('COM_KUNENA_LIB_BBCODE_SPOILER_HIDE');
Text::script('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND');
?>

<div class="kspoiler">
    <div class="kspoiler-header">
        <div class="kspoiler-title" style="display:inline-block;">
            <?php echo $this->title; ?>
        </div>
        <div class="kspoiler-expand" style="display:inline-block;">
            <input class="btn btn-primary" type="button" id="kspoiler-show" value="<?php echo Text::_('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND'); ?>"/>
        </div>
        <div class="kspoiler-wrapper" style="display:none;">
            <div class="kspoiler-content">
                <?php echo $this->content; ?>
            </div>
        </div>
    </div>
</div>