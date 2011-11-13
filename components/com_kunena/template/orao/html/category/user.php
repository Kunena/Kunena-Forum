<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo $this->header ?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>

			<div class="kdetailsbox krec-posts" id="kposts-detailsbox">
				<ul class="kposts">
					<?php if (empty($this->categories )) : ?>
					<li class="ktopics-row krow-odd">
						<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE'); ?>
					</li>
					<?php
					else :
						foreach ($this->categories as $this->category) {
							echo $this->loadTemplateFile('row');
						}
					endif
					?>
				</ul>
			</div>
			<div class="clr"></div>

		<span class="corners-bottom"><span></span></span>
	</div>
</div>