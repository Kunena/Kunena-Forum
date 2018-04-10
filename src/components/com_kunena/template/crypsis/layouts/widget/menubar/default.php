<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<div class="navbar" itemscope itemtype="http://schema.org/SiteNavigationElement">
	<div class="navbar-inner">
		<div class="visible-desktop">
			<?php echo $this->subRequest('Widget/Menu'); ?>
		</div>
		<?php echo $this->subRequest('Widget/Login'); ?>
	</div>
</div>

