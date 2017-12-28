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

// [url="www.kunena.org" target="_blank"]Kunena.org[/url]

// Display URL.
$target = ' target="' . $this->escape($this->target) . '"';
if (strpos($this->escape($this->url), '/index.php') !== 0)
{
	$rel = 'rel="nofollow noopener noreferrer"';
} else {
	$rel = '';
}
?>
<a href="<?php echo $this->escape($this->url); ?>" class="bbcode_url" <?php echo $rel . $target; ?>>
	<?php echo $this->content; ?>
</a>
