<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div>
  <h2> <span class="kmsgtitle<?php echo $this->escape($this->msgsuffix) ?> kmsg-title-right"> <?php echo $this->displayMessageField('subject') ?> </span> <span title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover') ?>"> <?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat') ?> </span> <span class="kmsg-id-right"> <?php echo $this->numLink ?> </span> </h2>
</div>
<table class="<?php echo $this->class ?>">
  <tbody>
    <tr>
      <td>
        <?php $this->displayMessageContents() ?>
      </td>
      <td rowspan="2">
        <?php $this->displayMessageProfile('vertical') ?>
      </td>
    </tr>
    <tr>
      <td>
        <?php $this->displayMessageActions() ?>
      </td>
    </tr>
  </tbody>
</table>

<!-- Begin: Message Module Position -->
<?php $this->displayModulePosition('kunena_msg_' . $this->mmm) ?>
<!-- Finish: Message Module Position --> 
