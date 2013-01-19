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

<div class="btn-toolbar" style="margin: 0;">
  <?php if ($this->topicButtons->get('reply') || $this->topicButtons->get('subscribe') || $this->topicButtons->get('favorite') ) : ?>
  <div class="btn-group">
    <button class="btn dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><?php echo $this->topicButtons->get('reply') ?></li>
      <li><?php echo $this->topicButtons->get('subscribe') ?></li>
      <li><?php echo $this->topicButtons->get('favorite') ?></li>
    </ul>
  </div>
  <!-- /btn-group -->
  <?php endif ?>
  <?php if ($this->topicButtons->get('delete') || $this->topicButtons->get('moderate') || $this->topicButtons->get('sticky') || $this->topicButtons->get('lock')) : ?>
  <div class="btn-group">
    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Moderation <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="#"><?php echo $this->topicButtons->get('delete') ?></a></li>
      <li><a href="#"><?php echo $this->topicButtons->get('moderate') ?></a></li>
      <li><a href="#"><?php echo $this->topicButtons->get('sticky') ?></a></li>
      <li><a href="#"><?php echo $this->topicButtons->get('lock') ?></a></li>
    </ul>
  </div>
  <!-- /btn-group -->
  <?php endif ?>
  <?php if ($this->topicButtons->get('flat') || $this->topicButtons->get('threaded') || $this->topicButtons->get('indented')) : ?>
  <div class="btn-group">
    <button class="btn dropdown-toggle" data-toggle="dropdown">View <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <?php echo $this->topicButtons->get('flat') ?> <?php echo $this->topicButtons->get('threaded') ?> <?php echo $this->topicButtons->get('indented') ?>
    </ul>
  </div>
  <!-- /btn-group -->
  <?php endif ?>
</div>
<div class="clearfix"></div>
