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
?>
<!-- Module position: kunena_announcement -->
<?php $this->displayModulePosition ( 'kunena_announcement' ) ?>

<table class="table">
  <tr>
    <td> <strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_USERPOSTS') ?> </td>
    <td>
      <form action="<?php echo $this->escape(JUri::getInstance()->toString());?>" id="timeselect" name="timeselect" method="post" target="_self">
        <?php $this->displayTimeFilter('sel', 'class="inputboxusl" onchange="this.form.submit()" size="1"') ?>
      </form>
    </td>
    <td class="visible-desktop">
      <?php $this->displayForumJump () ?>
    </td>
    <td><?php echo $this->getPagination ( 5 ); ?></td>
  </tr>
</table>
<?php $this->displayTemplateFile('topics', 'posts', 'embed'); ?>
<table>
  <tr>
    <td> <strong><?php echo intval($this->total) ?></strong> <?php echo JText::_('COM_KUNENA_TOPICS')?> </td>
    <td><?php echo $this->getPagination ( 5 ); ?></td>
  </tr>
</table>
<?php
$this->displayWhoIsOnline ();
$this->displayStatistics ();
?>
