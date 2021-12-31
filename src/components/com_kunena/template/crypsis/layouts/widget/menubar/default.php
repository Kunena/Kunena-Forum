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
?>

<div class="navbar" itemscope itemtype="https://schema.org/SiteNavigationElement">
	<div class="navbar-inner">
		<div class="visible-desktop">
			<?php echo $this->subRequest('Widget/Menu'); ?>
		</div>
		<div class="hidden-desktop">
			<div class="nav navbar-nav pull-left">
				<div>
					<a class="btn btn-link" data-toggle="collapse" data-target=".knav-collapse"><?php echo KunenaIcons::hamburger(); ?></a>
				</div>
				<div class="knav-collapse"><?php echo $this->subRequest('Widget/Menu'); ?></div>
			</div>
		</div>
		<?php echo $this->subRequest('Widget/Login'); ?>
	</div>
</div>
