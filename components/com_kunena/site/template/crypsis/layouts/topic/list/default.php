<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->displayAnnouncement ();
echo $this->subLayout('Page/Module')->set('position', 'kunena_announcement');
?>

<div>
	<strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_TOPICS')?>
	<form action="<?php echo $this->escape(JUri::getInstance()->toString());?>" id="timeselect" name="timeselect" method="post" target="_self">
		<?php $this->displayTimeFilter('sel', 'class="inputboxusl" onchange="this.form.submit()" size="1"') ?>
	</form>
	<?php echo $this->render('embed'); ?>
</div>
<?php
$this->displayWhoIsOnline ();
$this->displayStatistics ();
?>
