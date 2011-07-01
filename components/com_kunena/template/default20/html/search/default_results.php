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
			<h2 class="kheader"><a rel="ksearchresults-detailsbox"><?php echo JText::_('COM_KUNENA_SEARCH_RESULTS') ?></a></h2>
			<div class="kheader-desc"><?php echo JText::sprintf ('COM_KUNENA_FORUM_SEARCH', $this->escape($this->state->get('searchwords')) ) ?></div>
			<div class="kdetailsbox ksearchresults" id="ksearchresults-detailsbox">
				<ul class="kposts">
					<?php if (!empty($this->error )) : ?>
					<li class="ktopics-row krow-odd">
						<?php echo $this->error ?>
					</li>
					<?php else : $this->displayRows(); endif ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>