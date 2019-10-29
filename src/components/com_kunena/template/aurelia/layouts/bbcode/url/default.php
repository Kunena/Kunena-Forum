<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

// [url="www.kunena.org" target="_blank"]Kunena.org[/url]

// Display URL.
if ($this->target)
{
	$target = ' target="' . $this->escape($this->target) . '"';
}
else
{
	$target = ' target="_blank"';
}

if (!$this->internal)
{
	$rel = 'rel="nofollow noopener noreferrer"';
}
else
{
	$rel = '';
}
?>
<a href="<?php echo $this->escape($this->url); ?>" class="bbcode_url<?php if (!empty($this->class)) echo ' ' . $this->class; ?>" <?php echo $rel . $target; ?>>
	<?php echo $this->content; ?>
</a>
