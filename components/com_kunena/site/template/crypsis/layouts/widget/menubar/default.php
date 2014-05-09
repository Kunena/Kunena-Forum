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
		<div class="hidden-phone">
			<?php echo $this->subRequest('Widget/Menu'); ?>
			<?php echo $this->subRequest('Widget/Login'); ?>
		</div>
		<div class="visible-phone hidden-tablet">
			<ul class="nav navbar-nav pull-left">
				<a data-toggle="collapse" data-target=".nav-collapse" style="float:left"> <i class="icon-large icon-list"></i> <b class="caret"></b> </a>
				<div class="nav-collapse"><?php echo $this->subRequest('Widget/Menu'); ?></div>
			</ul>
			<?php	echo $this->subRequest('Widget/Login')->setLayout('mobile'); ?>
		</div>
	</div>
</div>
