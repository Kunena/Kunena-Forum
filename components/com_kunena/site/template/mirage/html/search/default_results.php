<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule search-default_results">
	<div class="kbox-wrapper kbox-full">
		<div class="search-default_results kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header link-header2"><a rel="ksearchresults-detailsbox"><?php echo JText::_('COM_KUNENA_SEARCH_RESULTS') ?></a></h2>
					<div class="header-desc"><?php echo JText::sprintf ('COM_KUNENA_FORUM_SEARCH', $this->escape($this->state->get('searchwords')) ) ?></div>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="ksearchresults-detailsbox detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="kposts">
						<?php if (!empty($this->error )) : ?>
						<li class="ktopics-row krow-odd">
							<?php echo $this->error ?>
						</li>
						<?php else : $this->displayRows(); endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
