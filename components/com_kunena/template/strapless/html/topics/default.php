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

<table class="table" style="border:none;">
  <tr>
    <td style="border:none;"> <strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_TOPICS')?> </td>
    <td style="border:none;" class="hidden-phone">
      <form action="<?php echo $this->escape(JUri::getInstance()->toString());?>" id="timeselect" name="timeselect" method="post" target="_self">
        <?php $this->displayTimeFilter('sel', 'class="inputboxusl" onchange="this.form.submit()" size="1"') ?>
      </form>
    </td>
    <td style="border:none;" class="hidden-phone">
      <?php $this->displayForumJump () ?>
    </td>
  </tr>
</table>
<?php $this->displayTemplateFile('topics', 'default', 'embed'); ?>
<table class="table">
  <tr>
    <td class="klist-actions-info-all" style="border:none;"> <strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_TOPICS')?> </td>
  </tr>
</table>
<?php
$this->displayWhoIsOnline ();
$this->displayStatistics ();
?>
