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
// TODO: add support for announcement RSS feed
?>
		<div id="kannounce">
			<!-- a href="#" title="Announcement RSS Feed"><span class="krss-icon">Announcement RSS Feed</span></a -->
			<h2 class="kheader">
				<a href="<?php echo $this->annListURL ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST') ?>" rel="kannounce-detailsbox"><?php echo $this->annTitle ?></a>
			</h2>
			<div class="kdetailsbox" id="kannounce-detailsbox">
				<ul class="kheader-desc">
					<li class="kannounce-date"><?php echo $this->annDate->toKunena('date_today') ?></li>
					<li class="kannounce-desc"><p><?php echo $this->annDescription ?></p></li>
					<?php if ($this->annMoreURL) : ?>
					<li class="kannounce-desc kreadmore"><a href="<?php echo $this->annMoreURL ?>"><?php echo JText::_('COM_KUNENA_ANN_READMORE') ?></a></li>
					<?php endif ?>
				</ul>
			</div>
		</div>