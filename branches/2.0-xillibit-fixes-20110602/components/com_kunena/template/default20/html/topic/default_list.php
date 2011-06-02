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
		<div class="ksection">
			<!-- a href="#" title="Topic RSS Feed"><span class="krss-icon">Topic RSS Feed</span></a -->
			<!-- a href="#" title="View Subscribers of this Topic" class="ktopic-subsc">4 Subscribers</a -->
			<h2 class="kheader"><?php echo JText::_('COM_KUNENA_TOPIC') ?> <a href="#" rel="ktopic-detailsbox"><?php echo $this->escape($this->topic->subject) ?></a></h2>
			<ul class="ktopic-taglist">
				<?php if (!empty($this->keywords)) : ?>
				<li class="ktopic-taglist-title">Topic Tags:</li>
				<li><a href="#">templates</a></li>
				<li><a href="#">design</a></li>
				<li><a href="#">css</a></li>
				<li><a href="#">colors</a></li>
				<li><a href="#">help</a></li>
				<?php else: ?>
				<li class="ktopic-taglist-title">No Tags</li>
				<?php endif ?>
				<li class="ktopic-taglist-edit"><a href="#">Add/edit tags</a></li>
			</ul>
			<div class="kdetailsbox" id="ktopic-detailsbox">
				<ul class="kposts">
					<?php foreach ( $this->messages as $id=>$message ) $this->displayMessage($id, $message, 'message') ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>