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
?>
<div class="card">
	<h3 class="card-header"><?php echo $this->header; ?></h3>
	<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_custom_top'); ?>
	<div class="card-body">
		<?php echo $this->body; ?>
	</div>
	<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_custom_bottom'); ?>
</div>
