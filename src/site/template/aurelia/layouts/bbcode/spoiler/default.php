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

<p>
  <div class="kspoiler-title" style="display:inline-block;">
    <?php echo $this->title; ?>
  </div>
  <a class="btn btn-primary" id="collapse-btn" data-bs-toggle="collapse" href="#collapseSpoiler" role="button" aria-expanded="false" aria-controls="collapseSpoiler">
    <?php echo Text::_('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND'); ?>
  </a>
</p>
<div class="collapse" id="collapseSpoiler">
  <div class="card card-body">
    <?php echo $this->content; ?>
  </div>
</div>