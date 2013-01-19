<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<hr />
<div>
  <h4> <span> <?php echo $this->displayMessageField('subject') ?> </span> <span class="pull-right" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover') ?>"> <?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat') ?> <?php echo $this->numLink ?> </span> </h4>
</div>
<table class="table" style="border:none;" >
  <tbody>
    <tr>
      <td rowspan="2" class="span3" style="border:none;vertical-align:top !important;">
        <?php $this->displayMessageProfile('vertical') ?>
      </td>
      <td class="span9" style="border:none;">
        <?php $this->displayMessageContents() ?>
      </td>
    </tr>
    <tr>
      <td class="kbuttonbar-left" style="border:none;">
        <?php $this->displayMessageActions() ?>
      </td>
    </tr>
  </tbody>
</table>

<!-- Begin: Message Module Position -->
<?php $this->displayModulePosition('kunena_msg_' . $this->mmm) ?>
<!-- Finish: Message Module Position --> 
