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

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

// The message of the exception is text: it can hold a value of the request.
$header  = htmlspecialchars((string) ($this->header ?? ''), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars((string) ($this->message ?? ''), ENT_QUOTES, 'UTF-8');
?>
<h1><?php echo $header . ' : ' . $message; ?></h1>
<p><strong><?php echo Text::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></strong></p>
<ol>
    <li><?php echo Text::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
    <li><?php echo Text::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
    <li><?php echo Text::_('JERROR_LAYOUT_MISTYPED_ADDRESS'); ?></li>
    <li><?php echo Text::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
    <li><?php echo Text::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'); ?></li>
    <li><?php echo Text::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></li>
</ol>
<p><?php echo Text::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
