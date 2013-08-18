<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>

<div class="well well-small">
  <h2 class="page-header"> <span><?php echo $this->displayField('title') ?></span> </h2>
  <div class="row-fluid column-row"> <?php echo $this->displayActions() ?>
    <div class="span12 column-item">
      <div>
        <?php if ($this->showdate) : ?>
        <div title="<?php echo $this->displayField('created', 'ago'); ?>"> <?php echo $this->displayField('created', 'date_today') ?> </div>
        <?php endif; ?>
        <div><?php echo $this->displayField('description') ?></div>
      </div>
    </div>
  </div>
</div>
