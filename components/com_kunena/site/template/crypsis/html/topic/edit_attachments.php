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
  <div>
    <ul>
      <?php foreach($this->attachments as $attachment) : ?>
      <li> <span>
        <input type="hidden" name="attachments[<?php echo $attachment->id ?>]" value="<?php echo $this->escape($attachment->filename) ?>" />
        <input type="checkbox" name="attachment[<?php echo $attachment->id ?>]" checked="checked" value="<?php echo $attachment->id ?>" />
        <a href="#" class="kattachment-insert" style="display: none;"><?php echo  JText::_('COM_KUNENA_EDITOR_INSERT'); ?></a> </span> <?php echo $attachment->getThumbnailLink(); ?> <span> <?php echo $this->escape($attachment->filename); ?> <?php echo '('.number_format(intval($attachment->size)/1024,0,'',',').'KB)'; ?> </span> </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
