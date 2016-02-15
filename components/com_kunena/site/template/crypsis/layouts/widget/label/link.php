<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$label = $this->label;
$link = ' href="' . $this->link . '"';
$tooltip = ' data-toggle="tooltip"';
$description = isset($this->description) ? $tooltip . ' title="' . $this->description . '"': '';
$class = ' class="label label-' . $this->state . ' ' . '"';
?>
<a <?php echo $link . $description . $class; ?> >
	<?php echo $label; ?>
</a>
