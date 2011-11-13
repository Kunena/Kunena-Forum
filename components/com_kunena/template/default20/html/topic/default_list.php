<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<div class="ksection">
			<!-- a href="#" title="Topic RSS Feed"><span class="krss-icon">Topic RSS Feed</span></a -->
			<!-- a href="#" title="View Subscribers of this Topic" class="ktopic-subsc">4 Subscribers</a -->
			<h2 class="kheader"><?php echo JText::_('COM_KUNENA_TOPIC') ?> <a href="#" rel="ktopic-detailsbox"><?php echo $this->escape($this->topic->subject) ?></a></h2>
			<?php if ( $this->config->keywords ) : ?>
			<ul class="ktopic-taglist">
				<?php if (!empty($this->keywords)) : ?>
				<li class="ktopic-taglist-title"><?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', count($this->keywords)) ?></li>
				<li><a href="#">templates</a></li>
				<li><a href="#">design</a></li>
				<li><a href="#">css</a></li>
				<li><a href="#">colors</a></li>
				<li><a href="#">help</a></li>
				<?php else: ?>
				<li class="ktopic-taglist-title"><?php echo JText::_('COM_KUNENA_TOPIC_NO_TAGS') ?></li>
				<?php endif ?>
				<?php if ( $this->me->userid == $this->topic->first_post_userid || intval($this->me->isModerator('global')) ): ?><li class="ktopic-taglist-edit"><a href="#" id="edit_keywords"><?php echo JText::_('COM_KUNENA_TOPIC_TAGS_ADD_EDIT') ?></a></li><?php endif ?>
			</ul>
			<?php endif ?>
			<div class="kdetailsbox" id="ktopic-detailsbox">
				<ul class="kposts">
					<?php foreach ( $this->messages as $id=>$message ) $this->displayMessage($id, $message, 'message') ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>