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
		<div class="kbuttonbar-topic">
			<div class="kpaginationbar">
				<?php echo $this->getPagination(4) ?>
			</div>
			<ul class="kmessage-buttons">
				<!-- User buttons -->
				<li><a class="kicon-button kbuttoncomm btn-left" href="#" title="Reply to Topic" rel="nofollow">
					<span class="reply"><span>Reply to Topic</span></span></a>
				</li>
				<li><a class="kicon-button kbuttonuser btn-left" href="#" title="Subscribe Category" rel="nofollow">
					<span class="subscribe"><span>Subscribe</span></span></a>
				</li>
				<li><a class="kicon-button kbuttonuser btn-left" href="#" title="Save Favorite" rel="nofollow">
					<span class="favorite"><span>Favorite</span></span></a>
				</li>
				<!-- Moderator buttons -->
				<li><a class="kicon-button kbuttonmod btn-left" href="#" title="Lock this topic" rel="nofollow">
					<span class="lock"><span>Lock</span></span></a>
				</li>
				<li><a class="kicon-button kbuttonmod btn-left" href="#" title="Make sticky" rel="nofollow">
					<span class="sticky"><span>Sticky</span></span></a>
				</li>
				<li><a class="kicon-button kbuttonmod btn-left" href="#" title="Moderate Topic" rel="nofollow">
					<span class="moderate"><span>Moderate Topic</span></span></a>
				</li>
				<li><a class="kicon-button kbuttonmod btn-left" href="#" title="Delete Topic" rel="nofollow">
					<span class="delete"><span>Delete Topic</span></span></a>
				</li>
			</ul>
		</div>