<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

$label       = $this->label;
$tooltip     = 'data-bs-toggle="tooltip"';
$description = isset($this->description) ? ' ' . $tooltip . ' data-bs-toggle="tooltip" title="' . $this->description . '"' : '';
$class       = ' class="badge bg-' . $this->state . '"';
?>
<span <?php echo $description . $class; ?> >
	<?php echo $label; ?>
</span>

