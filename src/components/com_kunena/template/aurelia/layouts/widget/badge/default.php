<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use function defined;

$label       = $this->label;
$tooltip     = 'data-toggle="tooltip"';
$description = isset($this->description) ? ' ' . $tooltip . ' title="' . $this->description . '"' : '';
$class       = ' class="' . ' badge badge-' . $this->state . '"';
?>
<span <?php echo $description . $class; ?> >
	<?php echo $label; ?>
</span>

