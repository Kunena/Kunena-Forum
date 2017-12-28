<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

JHtml::_('behavior.tabstate');

$this->addScriptDeclaration("// <![CDATA[
kunena_url_ajax= '" . KunenaRoute::_("index.php?option=com_kunena&view=category&format=raw") . "';
// ]]>");

$this->addScript('assets/js/topic.js');
$this->ktemplate = KunenaFactory::getTemplate();
$topicicontype = $this->ktemplate->params->get('topicicontype');
$labels = $this->ktemplate->params->get('labels');
?>
<div class="well">
	<h3> <?php echo !isset($this->message)
			? JText::_('COM_KUNENA_TITLE_MODERATE_TOPIC')
			: JText::_('COM_KUNENA_TITLE_MODERATE_MESSAGE'); ?>
	</h3>
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic') ?>" method="post"
		  name="myform" id="myform" class="form-horizontal">
		<input type="hidden" name="task" value="move"/>
		<input type="hidden" name="catid" value="<?php echo $this->category->id; ?>"/>
		<input type="hidden" name="id" value="<?php echo $this->topic->id; ?>"/>
		<?php if (isset($this->message)) : ?>
			<input type="hidden" name="mesid" value="<?php echo $this->message->id; ?>"/>
		<?php endif; ?>
		<?php echo JHtml::_('form.token'); ?>
		<div>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_KUNENA_TITLE_MODERATE_TAB_BASIC_INFO'); ?></a></li>
				<li><a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_KUNENA_TITLE_MODERATE_TAB_MOVE_OPTIONS'); ?></a></li>
				<?php if (isset($this->message) && $this->message->getAuthor()->id != 0) : ?>
					<li><a href="#tab3" data-toggle="tab"><?php echo JText::_('COM_KUNENA_TITLE_MODERATE_TAB_BAN_HISTORY'); ?></a></li>
					<!--  <li><a href="#tab4" data-toggle="tab"><?php // echo JText::_('COM_KUNENA_TITLE_MODERATE_TAB_NEW_BAN'); ?></a></li> -->
				<?php endif; ?>
			</ul>
			<br>
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">

					<dl class="dl-horizontal">
						<dt> <?php echo JText::_('COM_KUNENA_MENU_TOPIC'); ?> </dt>
						<dd> <?php echo $this->topic->displayField('subject'); ?> </dd>
						<dt> <?php echo JText::_('COM_KUNENA_CATEGORY'); ?> </dt>
						<dd> <?php echo $this->category->displayField('name') ?> </dd>
						<?php if (isset($this->userLink)) : ?>
							<dt> <?php echo JText::_('JGLOBAL_USERNAME'); ?> </dt>
							<dd><strong> <?php echo $this->userLink; ?></strong></dd>
						<?php endif; ?>
					</dl>
					<?php if ($this->config->topicicons) : ?>
						<div><?php echo JText::_('COM_KUNENA_MODERATION_CHANGE_TOPIC_ICON'); ?>:</div>
						<br>
						<div class="kmoderate-topicicons">
							<?php foreach ($this->topicIcons as $id => $icon): ?>
								<input type="radio" id="radio<?php echo $icon->id ?>" name="topic_emoticon" value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ' : '' ?> />
							<?php if ($this->config->topicicons && $topicicontype == 'B3') : ?>
								<label class="radio inline" for="radio<?php echo $icon->id; ?>"><span class="glyphicon glyphicon-<?php echo $icon->b3; ?> glyphicon-topic" aria-hidden="true"></span>
							<?php elseif ($this->config->topicicons && $topicicontype == 'fa') : ?>
								<label class="radio inline" for="radio<?php echo $icon->id; ?>"><i class="fa fa-<?php echo $icon->fa; ?> glyphicon-topic fa-2x"></i>
							<?php else : ?>
								<label class="radio inline" for="radio<?php echo $icon->id; ?>"><img src="<?php echo $icon->relpath; ?>" alt="<?php echo $icon->name; ?>" border="0" />
							<?php endif; ?>
								</label>
							<?php endforeach; ?>
						</div>
					<?php elseif ($labels && !$this->config->topicicons) : ?>
						<div><strong><?php echo JText::_('COM_KUNENA_MODERATION_CHANGE_LABEL'); ?>:</strong></div>
						<br>
						<div class="kmoderate-topicicons">
							<?php foreach ($this->topicIcons as $id => $icon) : ?>
								<input type="radio" id="radio<?php echo $icon->id ?>" name="topic_emoticon" value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ' : '' ?> />
									<?php if ($topicicontype == 'B3') : ?>
										<label class="radio inline" for="radio<?php echo $icon->id; ?>"><span class="label label-<?php echo $icon->name; ?>"><span class="icon icon-<?php echo $icon->b3; ?>" aria-hidden="true"></span><span class="sr-only"></span><?php echo $icon->name; ?></span>
									<?php elseif ($topicicontype == 'B2') : ?>
										<label class="radio inline" for="radio<?php echo $icon->id; ?>"><span class="label label-<?php echo $icon->name; ?>"><span class="icon icon-<?php echo $icon->b2; ?>" aria-hidden="true"></span><span class="sr-only"></span><?php echo $icon->name; ?></span>
									<?php elseif ($topicicontype == 'fa') : ?>
										<label class="radio inline" for="radio<?php echo $icon->id; ?>"><i class="fa fa-<?php echo $icon->fa; ?> glyphicon-topic fa-2x"></i>
									<?php else : ?>
										<label class="radio inline" for="radio<?php echo $icon->id; ?>"><img src="<?php echo $icon->relpath; ?>" alt="<?php echo $icon->name; ?>" border="0" />
									<?php endif; ?>
										</label>
								<?php endforeach; ?>
						</div>
						<br>
					<?php endif; ?>
					<br>
					<?php if (isset($this->message)) : ?>
						<hr/>
						<h3>
							<div class="pull-left">
								<?php echo $this->message->getAuthor()->getAvatarImage('img-thumbnail', 'list'); ?>
							</div>
							<?php echo $this->message->displayField('subject'); ?>
							<br/>
							<small>
								<?php echo JText::_('COM_KUNENA_POSTED_AT') ?>
								<?php echo $this->message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
								<?php echo JText::_('COM_KUNENA_BY') . ' ' . $this->message->getAuthor()->getLink(); ?>
							</small>
							<div class="clearfix"></div>
						</h3>

						<div class="well well-small khistory">
							<?php echo $this->message->displayField('message'); ?>
						</div>

					<?php endif; ?>
				</div>

				<div class="tab-pane" id="tab2">
					<h3> <?php echo JText::_('COM_KUNENA_MODERATION_DEST'); ?> </h3>

					<div class="control-group">
						<label class="control-label" for="modcategorieslist"> <?php echo JText::_('COM_KUNENA_MODERATION_DEST_CATEGORY'); ?> </label>

						<div class="controls" id="modcategorieslist"> <?php echo $this->getCategoryList(); ?> </div>
					</div>
					<div class="control-group">
						<label class="control-label" for="modtopicslist"> <?php echo JText::_('COM_KUNENA_MODERATION_DEST_TOPIC'); ?> </label>

						<div class="controls" id="modtopicslist"> <?php echo JHtml::_(
								'select.genericlist', $this->getTopicOptions(), 'targettopic', 'class="form-control"', 'value', 'text', 0, 'kmod_topics'
							); ?> </div>
					</div>
					<div class="control-group" id="kmod_targetid" style="display: none;">
						<label class="control-label" for="modtopicslist"> <?php echo JText::_('COM_KUNENA_MODERATION_TARGET_TOPIC_ID'); ?> </label>

						<div class="controls">
							<input type="text"  size="7" name="targetid" value=""/>
						</div>
					</div>
					<div class="control-group" id="kmod_subject">
						<label class="control-label" for="kmod_subject"> <?php echo JText::_('COM_KUNENA_MODERATION_TITLE_DEST_SUBJECT'); ?> </label>

						<div class="controls">
							<input type="text" class="form-control" name="subject"  id="ktitle_moderate_subject" value="<?php echo !isset($this->message)
								? $this->topic->displayField('subject')
								: $this->message->displayField('subject'); ?>" maxlength="<?php echo $this->escape($this->ktemplate->params->get('SubjectLengthMessage')); ?>"/>
						</div>
					</div>
					<?php if (!empty($this->replies)) : ?>
						<div class="control-group">
							<div class="controls">
								<label class="checkbox">
									<input id="kmoderate-mode-selected" type="radio" name="mode" checked="checked" value="selected" style="display: inline-block;" />
									<?php echo JText::_('COM_KUNENA_MODERATION_MOVE_SELECTED'); ?> </label>
								<label class="checkbox">
									<input id="kmoderate-mode-newer" type="radio" name="mode" value="newer" style="display: inline-block;" />
									<?php echo JText::sprintf('COM_KUNENA_MODERATION_MOVE_NEWER', $this->escape($this->replies)); ?> </label>
							</div>
						</div>
					<?php endif; ?>

					<div class="control-group">
						<div class="controls">
							<label class="checkbox">
								<input type="checkbox" name="changesubject" value="1"/>
								<?php echo JText::_('COM_KUNENA_MODERATION_CHANGE_SUBJECT_ON_REPLIES'); ?> </label>
						</div>
					</div>
					<?php if (!isset($this->message)) : ?>
						<div class="control-group">
							<div class="controls">
								<label class="checkbox">
									<input type="checkbox" <?php if ($this->config->boxghostmessage) echo ' checked="checked"'; ?>
										   name="shadow" value="1"/>
									<?php echo JText::_('COM_KUNENA_MODERATION_TOPIC_SHADOW'); ?> </label>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<?php if (isset($this->message)) : ?>
					<div class="tab-pane" id="tab3">
						<?php echo $this->subLayout('User/Ban/History')->set('profile', $this->message->getAuthor())->set('headerText', JText::_('COM_KUNENA_TITLE_MODERATE_TAB_BAN_HISTORY'))->set('banHistory', $this->banHistory)->set('me', $this->me); ?>
					</div>

					<!-- FIXME: This adds a form which cause an issue, because there will be nested forms
				<div class="tab-pane" id="tab4">
					<?php //echo $this->subLayout('User/Ban/Form')->set('profile', $this->message->getAuthor())->set('banInfo', $this->banInfo)->set('headerText', JText::_('COM_KUNENA_TITLE_MODERATE_TAB_NEW_BAN')); ?>
				</div>-->
				<?php endif; ?>
				<hr/>
				<br/>

				<div class="control-group center">
					<input type="submit" class="btn btn-primary"
						   value="<?php echo JText::_('COM_KUNENA_POST_MODERATION_PROCEED'); ?>"/>
					<a href="javascript:window.history.back();" class="btn btn-default"> <?php echo JText::_('COM_KUNENA_BACK'); ?> </a>
				</div>
			</div>
		</div>
	</form>
</div>
