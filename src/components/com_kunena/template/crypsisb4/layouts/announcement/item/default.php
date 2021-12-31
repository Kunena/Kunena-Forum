<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crpsisb4
 * @subpackage      Layout.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

$announcement = $this->announcement;
$actions      = $this->getActions();
?>
<h3>
	<?php echo $announcement->displayField('title'); ?>

	<?php if ($announcement->showdate)
		:
		?>
		<small title="<?php echo $announcement->displayField('created', 'ago'); ?>">
			<?php echo $announcement->displayField('created', 'date_today'); ?>
		</small>
	<?php endif; ?>
</h3>

<?php if (!empty($actions))
	:
	?>
	<div>
		<?php echo implode(' ', $actions); ?>
	</div>
	<br>
<?php endif; ?>

<div class="shadow-lg rounded">
	<div><?php echo $announcement->displayField('sdescription'); ?></div>
	<div><?php echo $announcement->displayField('description'); ?></div>
</div>
