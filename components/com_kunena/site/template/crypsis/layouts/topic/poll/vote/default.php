<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="row-fluid">
	<a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a>
	<h3><?php echo JText::_('COM_KUNENA_POLL_NAME') .' '. KunenaHtmlParser::parseText($this->poll->title); ?></h3>

	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" id="kpoll-form-vote" method="post">
		<input type="hidden" name="view" value="topic" />
		<input type="hidden" name="task" value="vote" />
		<input type="hidden" name="catid" value="<?php echo $this->topic->category_id ?>" />
		<input type="hidden" name="id" value="<?php echo $this->topic->id ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<ul class="unstyled">
			<?php foreach ($this->poll->getOptions() as $key=>$poll_option) : ?>
			<li>
				<input class="kpoll-boxvote" type="radio" name="kpollradio" id="radio_name<?php echo intval($key) ?>" value="<?php echo intval($poll_option->id) ?>" <?php if ($this->voted && $this->voted->lastvote == $poll_option->id) echo 'checked="checked"' ?> />
				<?php echo KunenaHtmlParser::parseText($poll_option->text) ?>
			</li>
			<?php endforeach; ?>
		</ul>

		<input id="kpoll-button-vote" class="btn btn-success" type="submit" value="<?php echo $this->voted && $this->config->pollallowvoteone ? JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE') : JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
	</form>
</div>
<div class="clearfix"></div>
