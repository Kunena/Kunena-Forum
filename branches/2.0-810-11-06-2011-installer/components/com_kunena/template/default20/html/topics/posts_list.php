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
			<h2 class="kheader"><a title="Recent Posts" rel="kposts-detailsbox"><?php echo $this->headerText ?></a></h2>
			<div class="kdetailsbox krec-posts" id="kposts-detailsbox">
				<ul class="kposts">
					<?php if (empty($this->messages )) : ?>
					<li class="ktopics-row krow-odd">
						<?php echo JText::_('COM_KUNENA_NO_POSTS'); ?>
					</li>
					<?php else : $this->displayRows(); endif ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>
		<?php if ($this->postActions) : ?>
		<div id="ksection-modbox">
			<?php echo JHTML::_('select.genericlist', $this->postActions, 'task', 'class="kinputbox" size="1"', 'value', 'text', 0, 'kmoderate-select'); ?>
			<input type="checkbox" value="0" name="" class="kmoderate-topic-checkall" />
		</div>
		<?php endif ?>