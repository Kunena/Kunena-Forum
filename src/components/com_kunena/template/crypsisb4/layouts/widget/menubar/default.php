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

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-lg rounded border"
	 itemscope="" itemtype="https://schema.org/SiteNavigationElement">
	<button class="navbar-toggler" aria-expanded="false" aria-controls="knav-collapse" aria-label="Toggle navigation"
			type="button" data-target=".knav-collapse" data-toggle="collapse">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="knav-collapse collapse navbar-collapse">
		<?php echo $this->subRequest('Widget/Menu'); ?>
	</div>
	<button class="navbar-toggler float-right" aria-expanded="false" aria-controls="knav-usercollapse"
			aria-label="Toggle navigation" type="button" data-target=".knav-usercollapse" data-toggle="collapse">
		<?php echo KunenaIcons::user(); ?>
	</button>
	<div class="knav-usercollapse navbar-collapse collapse">
		<?php echo $this->subRequest('Widget/Login'); ?>
	</div>
</nav>
