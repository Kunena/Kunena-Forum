<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<nav class="navbar navbar-default">
	<div class="navbar-inner">
		<div class="visible-lg">
			<?php echo $this->subRequest('Widget/Menu'); ?>
			<?php echo $this->subRequest('Widget/Login'); ?>
		</div>
		<div class="hidden-lg">
			<ul class="nav navbar-nav pull-left">
				<div><a class="btn btn-link" data-toggle="collapse" data-target=".knav-collapse"><i class="glyphicon glyphicon-large glyphicon-white glyphicon-list"></i> <b class="caret"></b> </a></div>
				<div class="knav-collapse"><?php echo $this->subRequest('Widget/Menu'); ?></div>
			</ul>
			<?php echo $this->subRequest('Widget/Login'); ?>
		</div>
	</div>
</nav>
