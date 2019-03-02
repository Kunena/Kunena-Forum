<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-expand-md"
     itemscope="" itemtype="https://schema.org/SiteNavigationElement">
	<div class="navbar-inner col-lg-12">
		<div class="d-none d-lg-block d-xl-none">
			<?php echo $this->subRequest('Widget/Menu'); ?>?>
		</div>
		<div class="hidden-lg-block d-lg-none d-xl-block">
			<div class="nav navbar-nav float-left">
				<div class="d-lg-block d-xl-none">
					<a class="btn btn-link" data-toggle="collapse"
				        data-target=".knav-collapse"><?php echo KunenaIcons::hamburger(); ?></a>
				</div>
				<div class="knav-collapse">
					<?php echo $this->subRequest('Widget/Menu'); ?>
				</div>
			</div>
		</div>
		<?php echo $this->subRequest('Widget/Login'); ?> </div>
</nav>
