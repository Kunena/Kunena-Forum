<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<div class="kuserlist">
			<a href="#" class="ksection-headericon"><?php echo $this->getImage('icon-whosonline-sm.png') ?></a>
			<h2 class="kheader"><a href="#" rel="ksection-detailsbox"><?php echo JText::_('COM_KUNENA_USRL_USERLIST') ?></a></h2>
			<div class="kuserlist-items">
				<?php foreach ($this->users as $user) { $this->displayUserRow($user); } ?>
			</div>
			<div class="clr"></div>
		</div>