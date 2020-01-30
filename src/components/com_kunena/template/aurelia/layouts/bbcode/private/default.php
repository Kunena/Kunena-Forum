<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use function defined;

// [private=userid]

// Hide content from everyone except the author and moderators.
?>
<br/>
<strong><?php echo Text::_('COM_KUNENA_BBCODE_SECURE_TEXT'); ?></strong>
<div class="kmsgtext-confidential"><?php echo $this->content; ?></div>
