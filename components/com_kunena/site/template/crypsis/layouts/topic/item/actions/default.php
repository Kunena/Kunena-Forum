<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<div class="clearfix"></div>
<div class="btn-toolbar" id="topic-actions">
	<div>
		<?php if ($this->topicButtons->get('reply')
			|| $this->topicButtons->get('subscribe')
			|| $this->topicButtons->get('favorite')) : ?>
		<div class="btn-group">
			<a class="btn" data-toggle="dropdown"><i class="icon-pencil"></i> <?php echo JText::_('COM_KUNENA_TOPIC_ACTIONS_LABEL_ACTION') ?></a>
			<a class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><?php echo $this->topicButtons->get('reply') ?></li>
				<li><?php echo $this->topicButtons->get('subscribe') ?></li>
				<li><?php echo $this->topicButtons->get('favorite') ?></li>
			</ul>
		</div>
		<?php endif ?>

		<?php if ($this->topicButtons->get('delete')
			|| $this->topicButtons->get('undelete')
			|| $this->topicButtons->get('moderate')
			|| $this->topicButtons->get('sticky')
			|| $this->topicButtons->get('lock')) : ?>
		<div class="btn-group">
			<a class="btn btn-primary" data-toggle="dropdown"><i class="icon-shuffle"></i>	<?php echo JText::_('COM_KUNENA_TOPIC_ACTIONS_LABEL_MODERATION') ?></a>
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><?php echo $this->topicButtons->get('delete') ?></li>
				<li><?php echo $this->topicButtons->get('undelete') ?></li>
				<li><?php echo $this->topicButtons->get('moderate') ?></li>
				<li><?php echo $this->topicButtons->get('sticky') ?></li>
				<li><?php echo $this->topicButtons->get('lock') ?></li>
			</ul>
		</div>
		<?php endif ?>
	</div>
</div>
