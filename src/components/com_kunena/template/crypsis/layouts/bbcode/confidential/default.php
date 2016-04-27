<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.BBCode
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// [confidential]For moderators only[/confidential]

// Hide content from everyone except the author and moderators.
?>
<strong><?php echo JText::_('COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT'); ?></strong>
<div class="kmsgtext-confidential"><?php echo $this->content; ?></div>
