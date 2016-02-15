<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.BBCode
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// [url="www.kunena.org" target="_blank"]Kunena.org[/url]

// Display URL.
$target = ' target="' . $this->escape($this->target) . '"';
if (strpos($this->escape($this->url), '/index.php') !== 0)
{
	$rel = 'rel="nofollow"';
} else {
	$rel = '';
}
?>
<a href="<?php echo $this->escape($this->url); ?>" class="bbcode_url" <?php echo $rel . $target; ?>>
	<?php echo $this->content; ?>
</a>
