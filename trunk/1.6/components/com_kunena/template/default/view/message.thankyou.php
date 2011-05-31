<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<div class="kpost-thankyou">
	<?php echo $this->message_thankyou; ?>
</div>
<?php if(!empty($this->thankyou)): ?>
<div class="kmessage-thankyou">
<?php
	echo JText::_('COM_KUNENA_THANKYOU').': ';
	echo implode(', ', $this->thankyou);
	if (count($this->total_thankyou) > $this->config->thankyou_max ) echo '...';
?>
</div>
<?php endif; ?>
