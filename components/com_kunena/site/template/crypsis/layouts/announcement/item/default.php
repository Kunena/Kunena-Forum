<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Announcement
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$announcement = $this->announcement;
$actions = $this->getActions();
?>
<h2>
	<?php echo $announcement->displayField('title'); ?>

	<?php if ($this->announcement->showdate) : ?>
	<small title="<?php echo $announcement->displayField('created', 'ago'); ?>">
		<?php echo $announcement->displayField('created', 'date_today'); ?>
	</small>
	<?php endif; ?>

</h2>
<div class="well well-small">

	<?php if (!empty($actions)) : ?>
	<div>
		<?php echo implode(' ', $actions); ?>
	</div>
	<?php endif; ?>

	<div><?php echo $announcement->displayField('description'); ?></div>
</div>
