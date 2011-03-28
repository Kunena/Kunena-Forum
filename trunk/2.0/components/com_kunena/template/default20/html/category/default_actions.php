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
		<div class="kbuttonbar">
			<div class="kpaginationbar">
				<?php echo $this->getPagination(7) ?>
			</div>
			<ul class="kmessage-buttons">
				<li><a class="kicon-button kbuttoncomm btn-left" href="#" title="New Topic" rel="nofollow">
					<span class="newtopic"><span>New Topic</span></span></a>
				</li>
				<li><a class="kicon-button kbuttonuser btn-left" href="#" title="Mark this Topics as Read" rel="nofollow">
					<span class="markread"><span>Mark Topics Read</span></span></a>
				</li>
				<li><a class="kicon-button kbuttonuser btn-left" href="#" title="Subscribe Category" rel="nofollow">
					<span class="subscribe"><span>Subscribe</span></span></a>
				</li>
			</ul>
		</div>