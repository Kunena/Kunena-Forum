<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

$label       = $this->label;
$link        = ' href="' . $this->link . '"';
$description = isset($this->description) ? ' title="' . $this->description . '"' : '';
$class       = ' class="label label-' . $this->state . ' ' . KunenaTemplate::getInstance()->tooltips() . '"';
?>
<a <?php echo $link . $description . $class; ?>>
	<?php echo $label; ?>
</a>
