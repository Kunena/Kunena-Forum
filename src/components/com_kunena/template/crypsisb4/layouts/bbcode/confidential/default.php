<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

// [confidential]For moderators only[/confidential]

// Hide content from everyone except the author and moderators.
?>
<br/>
<strong><?php echo Text::_('COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT'); ?></strong>
<div class="kmsgtext-confidential"><?php echo $this->content; ?></div>
