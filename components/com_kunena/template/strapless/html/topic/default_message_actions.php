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

$dateshown = $datehover = '';
if ($this->message->modified_time) {
	$datehover = 'title="'.KunenaDate::getInstance($this->message->modified_time)->toKunena('config_post_dateformat_hover').'"';
	$dateshown = KunenaDate::getInstance($this->message->modified_time)->toKunena('config_post_dateformat' ).' ';
}
?>
<?php if ($this->signatureHtml) : ?>

<div>
  <div class="kmsgsignature"> <?php echo $this->signatureHtml ?> </div>
</div>
<?php endif ?>
<?php if ($this->message->modified_by && $this->config->editmarkup) : ?>
<div class="kmessage-editmarkup-cover">

  <span class="alert" <?php echo $datehover ?>> <?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . $dateshown . JText::_('COM_KUNENA_BY') . ' ' . $this->message->getModifier()->getLink() . '.'; ?>
  <?php if ($this->message->modified_reason) echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( $this->message->modified_reason ); ?>
  </span> <br />
  <br />
  <?php endif ?>
  <?php if (!empty($this->reportMessageLink)) :?>
  <span class="kmessage-informmarkup"><?php echo $this->reportMessageLink ?></span>
  <?php if (!empty($this->ipLink)) : ?>
  <span class="kmessage-informmarkup"><?php echo $this->ipLink ?></span>
  <?php endif ?>

</div>
<?php endif ?>

<?php if (empty($this->message_closed)) : ?>
<div class="btn-toolbar" style="margin: 0;">
  <div class="kmessage-buttons-cover">
    <div class="kmessage-buttons-row">
    <input type="button" class="btn" name="kreply-form" value="Quick Reply" onclick="document.getElementById('kreply<?php echo intval($this->message->id) ?>_form').style.display = 'block';" />
      <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><?php echo $this->messageButtons->get('reply'); ?></li>
          <li><?php echo $this->messageButtons->get('quote'); ?></li>
          <li><?php echo $this->messageButtons->get('edit'); ?></li>
        </ul>
      </div>
      <?php if ($this->topicButtons->get('moderate')) : ?>
      <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">Moderate <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><?php echo $this->messageButtons->get('moderate'); ?></li>
          <li><?php echo $this->messageButtons->get('delete'); ?></li>
          <?php if($this->message->authorise('permdelete')) : ?><li><?php echo $this->messageButtons->get('permdelete'); ?></li><?php endif; ?>
          <?php if($this->message->authorise('undelete')) : ?><li><?php echo $this->messageButtons->get('undelete'); ?></li><?php endif; ?>
          <?php if($this->message->authorise('approve')) : ?><li><?php echo $this->messageButtons->get('publish'); ?></li><?php endif; ?>
          <li><?php echo $this->messageButtons->get('spam'); ?></li>
        </ul>
      </div>
      <?php endif?>

    </div>
    <?php else : ?>
    <?php echo $this->message_closed; ?> </div>
  <?php endif ?>
</div>
<?php if($this->messageButtons->get('thankyou')): ?>
<div class="kpost-thankyou"> <?php echo $this->messageButtons->get('thankyou'); ?> </div>
<?php endif; ?>
<?php if(!empty($this->thankyou)): ?>
<div class="kmessage-thankyou">
  <?php
	echo JText::_('COM_KUNENA_THANKYOU').': ';
	echo implode(', ', $this->thankyou);
	if (count($this->thankyou) > 9) echo '...';
?>
</div>
<?php endif; ?>
