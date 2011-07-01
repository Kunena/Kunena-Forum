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
			<h2 class="kheader"><a title="<?php echo $this->header ?>" rel="kposts-detailsbox"><?php echo $this->header ?></a></h2>
			<div class="kdetailsbox krec-posts" id="kposts-detailsbox">
				<ul class="kposts">
					<?php if (empty($this->categories )) : ?>
					<li class="ktopics-row krow-odd">
						<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE'); ?>
					</li>
					<?php
					else :
						foreach ($this->categories as $this->category) {
							echo $this->loadTemplate('row');
						}
					endif
					?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>