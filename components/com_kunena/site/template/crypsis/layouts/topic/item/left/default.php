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
		<?php $this->displayMessageProfile('vertical') ?>
	</div>
	<div class="span10">
		<?php $this->displayMessageContents() ?>
		<?php $this->displayMessageActions() ?>
	</div>
</div>

<?php echo $this->subLayout('Page/Module')->set('position', 'kunena_msg_' . $this->mmm); ?>
