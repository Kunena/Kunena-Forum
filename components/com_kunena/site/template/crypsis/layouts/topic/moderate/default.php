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

$this->addScriptDeclaration("// <![CDATA[
kunena_url_ajax= '".KunenaRoute::_("index.php?option=com_kunena&view=category&format=raw")."';
// ]]>");
?>


<h2>
	<?php echo !isset($this->message) ? JText::_('COM_KUNENA_TITLE_MODERATE_TOPIC') : JText::_('COM_KUNENA_TITLE_MODERATE_MESSAGE'); ?>
</h2>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="myform" id="myform" class="form-horizontal">
	<input type="hidden" name="view" value="topic" />
	<input type="hidden" name="task" value="move" />
	<input type="hidden" name="catid" value="<?php echo $this->category->id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->topic->id; ?>" />
	<?php if (isset($this->message)) : ?>
	<input type="hidden" name="mesid" value="<?php echo $this->message->id; ?>" />
	<?php endif; ?>
	<?php echo JHtml::_( 'form.token' ); ?>

	<div class="well">
		<dl class="dl-horizontal">
			<dt>
				<?php echo JText::_('COM_KUNENA_MENU_TOPIC'); ?>
			</dt>
			<dd>
				<?php echo $this->escape($this->topic->subject); ?>
			</dd>

			<dt>
				<?php echo JText::_('COM_KUNENA_CATEGORY'); ?>
			</dt>
			<dd>
				<?php echo $this->escape($this->category->name) ?>
			</dd>

			<?php if (isset($this->userLink)) : ?>
			<dt>
				<?php echo JText::_('COM_KUNENA_MODERATE_THIS_USER'); ?>
			</dt>
			<dd>
				<strong><?php echo $this->userLink; ?></strong>
			</dd>
			<?php endif; ?>
		</dl>
		<?php if (isset($this->message)) : ?>
		<hr />
		<h3>
			<div class="pull-left thumbnail">
				<?php echo $this->user->getAvatarImage('', 'list'); ?>
			</div>
			<?php echo $this->escape($this->message->subject); ?>
			<br />
			<small>
				<?php echo JText::_('COM_KUNENA_POSTED_AT')?>
				<?php echo $this->message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
				<?php echo JText::_('COM_KUNENA_BY') . ' ' . $this->message->getAuthor()->getLink() ?>
			</small>
			<div class="clearfix"></div>
		</h3>
		<div>
			<?php echo KunenaHtmlParser::stripBBCode($this->message->message, 300) ?>
		</div>

		<hr />
		<?php endif; ?>
		<h3>
			<?php echo JText::_('COM_KUNENA_MODERATION_DEST');?>
		</h3>
		<div class="control-group">
			<label class="control-label" for="modcategorieslist">
				<?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST_CATEGORY' );?>
			</label>
			<div class="controls" id="modcategorieslist">
				<?php echo $this->getCategoryList() ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="modtopicslist">
				<?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST_TOPIC' ); ?>
			</label>
			<div class="controls" id="modtopicslist">
				<input id="kmod_targetid" type="text" size="7" name="targetid" value="" style="display: none" />
				<?php echo JHtml::_ ( 'select.genericlist', $this->getTopicOptions(), 'targettopic', '', 'value', 'text', 0, 'kmod_topics' ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="kmod_subject">
				<?php echo JText::_ ( 'COM_KUNENA_MODERATION_TITLE_DEST_SUBJECT' ); ?>
			</label>
			<div class="controls" id="kmod_subject">
				<input type="text" name="subject" value="<?php echo $this->escape(!isset($this->message) ? $this->topic->subject : $this->message->subject); ?>" />
			</div>
		</div>
		<?php if (!empty($this->replies)) : ?>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input id="kmoderate-mode-selected" type="radio" name="mode" checked="checked" value="selected" />
					<?php echo JText::_('COM_KUNENA_MODERATION_MOVE_SELECTED'); ?>
				</label>
				<label class="checkbox">
					<input id="kmoderate-mode-newer" type="radio" name="mode" value="newer" />
					<?php echo JText::sprintf('COM_KUNENA_MODERATION_MOVE_NEWER', $this->escape($this->replies)); ?>
				</label>
			</div>
		</div>
		<?php endif; ?>
		<div class="control-group">
			<label class="control-label" for=""></label>
			<div class="controls">
				<input type="checkbox" name="changesubject" value="1" />
				<?php echo JText::_ ( 'COM_KUNENA_MODERATION_CHANGE_SUBJECT_ON_REPLIES' ); ?>
			</div>
		</div>
		<?php if (!isset($this->message)) : ?>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" <?php if ($this->config->boxghostmessage): ?> checked="checked" <?php endif; ?> name="shadow" value="1" />
					<?php echo JText::_ ( 'COM_KUNENA_MODERATION_TOPIC_SHADOW' ); ?>
				</label>
			</div>
		</div>
		<?php endif ?>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('COM_KUNENA_MODERATION_CHANGE_TOPIC_ICON'); ?>
			</label>
			<div class="controls">
				<?php foreach ($this->topicIcons as $id=>$icon): ?>
					<label class="checkbox">
						<input type="radio" name="topic_emoticon" value="<?php echo $icon->id ?>" <?php echo ($icon->id == $this->topic->icon_id) ? ' checked="checked" ':'' ?> />
						<img src="<?php echo $this->template->getTopicIconIndexPath($icon->id, true);?>" alt="" border="0" />
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary" value="<?php echo JText::_ ( 'COM_KUNENA_POST_MODERATION_PROCEED' ); ?>" />
				<a href="javascript:history.back();" class="btn">
					<?php echo JText::_ ( 'COM_KUNENA_BACK' ); ?>
				</a>
			</div>
		</div>
	</div>
</form>
