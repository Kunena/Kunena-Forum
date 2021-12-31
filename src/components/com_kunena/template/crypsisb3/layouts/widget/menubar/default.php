<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<nav class="navbar navbar-default" itemscope itemtype="https://schema.org/SiteNavigationElement">
	<div class="navbar-inner">
		<div class="visible-lg">
			<?php echo $this->subRequest('Widget/Menu'); ?>
		</div>
		<div class="hidden-lg">
			<div class="nav navbar-nav pull-left">
				<div>
					<a class="btn btn-link" data-toggle="collapse" data-target=".knav-collapse"><?php echo KunenaIcons::hamburger(); ?></a>
				</div>
				<div class="knav-collapse"><?php echo $this->subRequest('Widget/Menu'); ?></div>
			</div>
		</div>
		<?php echo $this->subRequest('Widget/Login'); ?>
	</div>
</nav>
