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

$document=JFactory::getDocument();
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);
//FIXME: announcement show only 5 ann. in table
?>

<div class="forumlist">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<div class="body-top1"><div class="body-top2"><div class="body-top3"><div class="body-top4"></div></div></div></div>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon annbox">
						<dt style="body">
							<span class = "ktitle"><?php echo $this->app->getCfg('sitename'); ?> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?> | <?php echo CKunenaLink::GetAnnouncementLink('add', NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?></span>
						</dt>
						<dd class="tk-toggler"><a class="ktoggler close" rel="annshow_body"></a></dd>
					</dl>
				</li>
			</ul>
			<ul id="annshow_body" class="topiclist forums">
				<li class="row tk-ann-show-header">
					<dl>
						<dt>
						 	<b><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></b>
						</dt>
						<dd class="tk-ann-date">
						 	<b><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?></b>
						</dd>
						<dd class="tk-ann-title">
						 	<b><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?></b>
						</dd>
						<dd class="tk-ann-publish">
						 	<b><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></b>
						</dd>
						<dd class="tk-ann-edit">
						 	<b><?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?></b>
						</dd>
						<dd class="tk-ann-delete">
						 	<b><?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?></b>
						</dd>
					</dl>
				</li>
		<?php
		if (!empty($this->announcements))
			foreach ($this->announcements as $ann) :
		?>
				<li class="row tk-ann-show-body">
					<dl>
						<dt>
						 	<?php echo intval($ann->id); ?>
						</dt>
						<dd class="tk-ann-date">
						 	<?php echo KunenaDate::getInstance($ann->created)->toKunena('date_today'); ?>
						</dd>
						<dd class="tk-ann-title">
						 	<?php echo CKunenaLink::GetAnnouncementLink('read', intval($ann->id), KunenaHtmlParser::parseText ($ann->title), KunenaHtmlParser::parseText ($ann->title), 'follow'); ?>
						</dd>
						<dd class="tk-ann-publish">
							<?php
								if ($ann->published > 0) {
									echo JText::_('COM_KUNENA_ANN_PUBLISHED');
								} else {
									echo JText::_('COM_KUNENA_ANN_UNPUBLISHED');
								}
							?>
						</dd>
						<dd class="tk-ann-edit">
						 	<?php echo CKunenaLink::GetAnnouncementLink('edit', intval($ann->id), JText::_('COM_KUNENA_ANN_EDIT'),JText::_('COM_KUNENA_ANN_EDIT')); ?>
						</dd>
						<dd class="tk-ann-delete">
						 	<?php echo CKunenaLink::GetAnnouncementLink('delete', intval($ann->id), JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?>
						</dd>
					</dl>
				</li>
				<?php endforeach; ?>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>