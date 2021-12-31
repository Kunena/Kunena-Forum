<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<?php if ($this->plglogin): ?>
<div class="hidden-xs">
	<?php
	if (KunenaFactory::getTemplate()->params->get('displayDropdownMenu'))
		:
		?>
		<?php echo $this->setLayout('desktop'); ?>
	<?php endif; ?>
</div>
<div class="visible-xs-block">
	<?php if (KunenaFactory::getTemplate()->params->get('displayDropdownMenu'))
		:
		?>
		<?php echo $this->setLayout('mobile'); ?>
	<?php endif; ?>
</div>
<?php endif; ?>
