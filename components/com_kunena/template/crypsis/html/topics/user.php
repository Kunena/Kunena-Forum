<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
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
<!-- Module position: kunena_announcement -->
<?php $this->displayModulePosition ( 'kunena_announcement' ) ?>

<table class="table">
  <tr>
    <td style="border:none;"> <strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_TOPICS')?> </td>
    <td style="border:none;">
      <?php $this->displayForumJump () ?>
    </td>
  </tr>
</table>
<div class="clearfix"></div>
<?php $this->displayTemplateFile('topics', 'user', 'embed'); ?>
<table class="klist-actions">
  <tr>
    <td class="klist-actions-info-all"> <strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_TOPICS')?> </td>
  </tr>
</table>
<?php
$this->displayWhoIsOnline ();
$this->displayStatistics ();
?>
