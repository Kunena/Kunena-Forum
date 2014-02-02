<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
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
			<?php echo $this->subLayout('Widget/Mobile/Login'); ?>
		</div>
	</div>
</div>
