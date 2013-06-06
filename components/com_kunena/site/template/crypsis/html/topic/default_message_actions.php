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

$dateshown = $datehover = '';
if ($this->message->modified_time) {
	$datehover = 'title="'.KunenaDate::getInstance($this->message->modified_time)->toKunena('config_post_dateformat_hover').'"';
	$dateshown = KunenaDate::getInstance($this->message->modified_time)->toKunena('config_post_dateformat' ).' ';
}
?>
<div>
	<?php if ($this->message->modified_by && $this->config->editmarkup) : ?>
		<div>
			<span class="alert" <?php echo $datehover ?>> <?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . $dateshown . JText::_('COM_KUNENA_BY') . ' ' . $this->message->getModifier()->getLink() . '.'; ?>
				<?php if ($this->message->modified_reason) echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( $this->message->modified_reason ); ?>
			</span> <br />
			<br />
	<?php endif ?>
	<?php if (empty($this->message_closed)) : ?>
	<div class="btn-toolbar btn-marging">
		<div>
			<div>
				<input type="button" class="btn" name="kreply-form" value="Quick Reply" onclick="document.getElementById('kreply<?php echo intval($this->message->id) ?>_form').style.display = 'block';" />
				<div class="btn-group">
					<button class="btn dropdown-toggle dropdown-border" data-toggle="dropdown" >Action</button>
					<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li><?php echo $this->messageButtons->get('reply'); ?></li>
						<li><?php echo $this->messageButtons->get('quote'); ?></li>
						<li><?php echo $this->messageButtons->get('edit'); ?></li>
					</ul>
				</div>
				<?php if ($this->topicButtons->get('moderate')) : ?>
					<div class="btn-group">
						<button class="btn dropdown-toggle dropdown-border" data-toggle="dropdown">Moderate</button>
						<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu">
							<li><?php echo $this->messageButtons->get('moderate'); ?></li>
							<li><?php echo $this->messageButtons->get('delete'); ?></li>
							<li><?php echo $this->messageButtons->get('undelete'); ?></li>
							<li><?php echo $this->messageButtons->get('permdelete'); ?></li>
							<li><?php echo $this->messageButtons->get('publish'); ?></li>
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
		<?php echo $this->messageButtons->get('thankyou'); ?>
	<?php endif; ?>
	</div>
	<?php if(!empty($this->thankyou)): ?>
		<div>
			<?php
			echo JText::_('COM_KUNENA_THANKYOU').': '.implode(', ', $this->thankyou).' ';
			if ($this->more_thankyou) echo JText::sprintf('COM_KUNENA_THANKYOU_MORE_USERS',$this->more_thankyou);
			?>
		</div>
	<?php endif; ?>
</div>
