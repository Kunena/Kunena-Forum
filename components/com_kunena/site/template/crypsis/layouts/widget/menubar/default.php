<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<div class="navbar">
	<div class="navbar-inner">
		<div class="hidden-phone hidden-tablet">
			<?php echo $this->subRequest('Widget/Menu'); ?>
			<?php echo $this->subRequest('Widget/Login'); ?>
		</div>
		<div class="visible-phone visible-tablet">
			<div class="nav navbar-nav">
				<div><a class="btn btn-link"  data-toggle="collapse" data-target="#nav-menu"> <i class="icon-large icon-list"></i> <b class="caret"></b> </a></div>
				<div class="nav-collapse collapse in" id="nav-menu"><?php echo $this->subRequest('Widget/Menu'); ?></div>
				<?php echo $this->subRequest('Widget/Login')->setLayout('Logout/mobile'); ?>
			</div>
		</div>
	</div>
</div>
