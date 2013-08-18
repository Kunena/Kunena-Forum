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
	<?php if(!empty($this->thankyou)): ?>
	<div>
		<?php
		echo JText::_('COM_KUNENA_THANKYOU').': '.implode(', ', $this->thankyou).' ';
		if ($this->more_thankyou) echo JText::sprintf('COM_KUNENA_THANKYOU_MORE_USERS',$this->more_thankyou);
		?>
	</div>
	<?php endif; ?>
		<?php echo $this->subRequest('Message/Actions')->set('mesid', $this->message->id); ?>
	</div>
	</div>
</div>

<?php echo $this->subLayout('Message/Edit')->set('message', $this->message)->setLayout('quickreply'); ?>
