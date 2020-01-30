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
$link        = ' href="' . $this->link . '"';
$description = isset($this->description) ? ' title="' . $this->description . '"' : '';
$class       = ' class="label label-' . $this->state . ' ' . '"';
?>
<a <?php echo $link . $description . $class; ?>>
	<?php echo $label; ?>
</a>
