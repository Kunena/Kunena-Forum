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
$this->displayBreadcrumb ();

?>
<?php echo $this->subLayout('Page/Module')->set('position', 'kunena_announcement'); ?>

<table class="table">
  <tr>
    <td style="border:none;"> <strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_TOPICS')?> </td>
    <td style="border:none;">
      <?php $this->displayForumJump () ?>
    </td>
  </tr>
</table>
<div class="clearfix"></div>
<?php echo $this->render('user_embed'); ?>
<table>
  <tr>
    <td> <strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_TOPICS')?> </td>
  </tr>
</table>
<?php
$this->displayWhoIsOnline ();
$this->displayStatistics ();
?>
