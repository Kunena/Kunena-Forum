<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="navbar">
	<div class="navbar-inner">
		<?php echo $this->subRequest('Page/Menu'); ?>
		<?php echo $this->subLayout('Page/MenuBar/' . ($this->me->exists() ? 'Logout' : 'Login' ))
			->set('me', $this->me); ?>
	</div>
</div>
