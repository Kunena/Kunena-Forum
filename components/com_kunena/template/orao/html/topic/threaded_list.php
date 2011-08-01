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
<?php $this->displayTopicActions(0) ?>
	<div class="kdetailsbox" id="ktopic-detailsbox">
		<ul class="kposts">
			<?php $this->displayMessage($this->state->get('item.mesid'), $this->messages[$this->state->get('item.mesid')], 'message') ?>
		</ul>
	</div>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo JText::sprintf('COM_KUNENA_TOPIC_REPLIES_TITLE', $this->escape($this->topic->subject)) ?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>

				<ul class="topiclist forums tk-poll-body">
					<?php foreach ( $this->messages as $id=>$message ) $this->displayMessage($id, $message, 'row') ?>
					<li>
					<dl class="">
						<dt>&nbsp;</dt>
						<dd class="tk-pagination" style="float:right;margin:5px;"><?php echo $this->getPagination(10) ?></dd>
					</dl>

					</li>
				</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>