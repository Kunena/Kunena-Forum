<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<div class="visible-lg">
	<?php
	if (KunenaFactory::getTemplate()->params->get('displayDropdownMenu'))
		:
		?>
		<?php echo $this->setLayout('desktop'); ?>
	<?php endif; ?>
</div>
<div class="hidden-lg">
	<div class="nav navbar-nav pull-left">
		<div><a class="btn btn-link" data-toggle="collapse" data-target=".knav-collapse"><?php echo KunenaIcons::hamburger(); ?></a></div>
		<div class="knav-collapse"><?php echo $this->subRequest('Widget/Menu'); ?></div>
	</div>
	<?php if (KunenaFactory::getTemplate()->params->get('displayDropdownMenu'))
		:
		?>
		<?php echo $this->setLayout('mobile'); ?>
	<?php endif; ?>
</div>