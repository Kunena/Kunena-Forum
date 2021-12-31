<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

$label       = $this->label;
$tooltip     = 'data-toggle="tooltip"';
$description = isset($this->description) ? ' ' . $tooltip . ' title="' . $this->description . '"' : '';
$class       = ' class="' . ' badge badge-' . $this->state . '"';
?>
<span <?php echo $description . $class; ?> >
	<?php echo $label; ?>
</span>

