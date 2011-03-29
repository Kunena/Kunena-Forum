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
			<a href="#" title="Announcement RSS Feed"><span class="krss-icon">Topic RSS Feed</span></a>
			<a href="#" title="View Subscribers of this Topic" class="ktopic-subsc">4 Subscribers</a>
			<h2 class="kheader">TOPIC: <a href="#" title="Topic Title" rel="ktopic-detailsbox">Topic Title</a></h2>
			<ul class="ktopic-taglist">
				<li class="ktopic-taglist-title">Topic Tags:</li>
				<li><a href="#">templates</a></li>
				<li><a href="#">design</a></li>
				<li><a href="#">css</a></li>
				<li><a href="#">colors</a></li>
				<li><a href="#">help</a></li>
				<li class="ktopic-taglist-edit"><a href="#">Add/edit tags</a></li>
			</ul>
			<div class="kdetailsbox" id="ktopic-detailsbox">
				<ul class="kposts">
					<?php include 'default_row.php' ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>