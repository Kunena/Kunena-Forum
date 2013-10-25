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

<div class="row-fluid">
	<div class="span2 hidden-phone <?php echo $this->profile->isMyself() ? 'pull-left' : 'pull-right'; ?>">
		<?php echo $this->subLayout('User/Profile')->set('user', $this->profile)->setLayout('circle'); ?>
	</div>
	<div class="span10">
		<?php echo $this->subLayout('Message/Item')->setProperties($this->getProperties()) ?>
		<?php echo $this->subRequest('Message/Item/Actions')->set('mesid', $this->message->id) ?>
	</div>
</div>

<?php echo $this->subLayout('Page/Module')->set('position', 'kunena_msg_' . $this->location); ?>
