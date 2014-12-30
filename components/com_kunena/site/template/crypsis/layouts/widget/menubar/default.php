<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
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
				<div style="float: left;padding-top: 10px"><a class="btn btn-link" style="color: inherit"  data-toggle="collapse" data-target="#nav-menu"> <i class="icon-large icon-list"></i> <b class="caret" style="color: inherit"></b> </a></div>
				<div class="nav-collapse collapse in" id="nav-menu"><?php echo $this->subRequest('Widget/Menu'); ?></div>
				<?php echo $this->subRequest('Widget/Login')->setLayout('Logout/mobile'); ?>
			</div>
		</div>
	</div>
</div>
