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

$modules = $this->renderPosition();

if (!$modules)
{
	return;
}

?>
<!-- Module position: <?php echo $this->position; ?> -->
<div class="well well-small">
	<?php echo $modules; ?>
</div>
