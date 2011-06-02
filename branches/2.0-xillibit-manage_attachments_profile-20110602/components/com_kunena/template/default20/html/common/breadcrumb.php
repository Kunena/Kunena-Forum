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
		<div class="kbreadcrumb">
			<ul class="kbreadcrumb-path">
				<?php $item = array_shift($this->pathway); ?>
				<li><a href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
				<?php foreach ($this->pathway as $item) : ?>
				<li> &raquo; <a href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
				<?php endforeach ?>
			</ul>
		</div>