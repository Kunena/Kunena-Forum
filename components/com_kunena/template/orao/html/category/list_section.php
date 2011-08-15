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
	$colapse_class = ' close';
	//$colapse_style = ' style="display: none;"';
	$colapse_style = ' ';
	$template = KunenaFactory::getTemplate();
	$this->params = $template->params;
?>
<?php if (!empty($this->categories [$this->section->id])) : ?>
<div class="forumlist sectionsuffix-<?php echo intval($this->section->id);?>">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon cat_<?php echo intval($this->section->id);?>">
						<dt><a href="<?php echo $this->sectionURL ?>"><?php echo $this->escape($this->section->name) ?></a></dt>
						<?php if ($this->params->get('countcolumnShow') != 0):?>
						<dd class="topics"><?php echo JText::_('COM_KUNENA_GEN_TOPICS');?></dd>
						<dd class="posts"><?php echo JText::_('COM_KUNENA_GEN_REPLIES');?></dd>
						<?php endif?>
						<dd class="lastpost"><span><?php echo JText::_('COM_KUNENA_GEN_LAST_POST');?></span></dd>
						<dd class="rss">
							<?php if (!empty($this->sectionRssURL)) : ?>
							<a href="<?php echo $this->sectionRssURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_RSS_TITLE', $this->escape($this->section->name)) ?>">
								<span class="krss-icon"></span>
							</a>
							<?php endif ?>
						<?php if (!empty($this->sectionMarkReadURL)) : ?>
						<?php /*?>
						<form action="<?php echo $this->sectionMarkReadURL ?>" name="kunenaMarkAllRead" method="post">
							<input type="submit" class="kheader-link" value="<?php //echo JText::_('COM_KUNENA_VIEW_CATEGORY_LIST_MARKALL'); ?>" />
							<?php echo JHTML::_( 'form.token' ); ?>
						</form><?php */?>
							<a href="" title="<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_LIST_MARKALL'); ?>">
								<span class="kmarkread-icon"></span>
							</a>
							<?php endif ?>
						</dd>
						<dd class="tk-toggler"><a class="ktoggler<?php echo $colapse_class; ?>" rel="catid-<?php echo intval($this->section->id); ?>"></a></dd>
					</dl>
				</li>
			</ul>
			<?php if ($this->section->description) : ?>
			<div class="tksection-desc">
				<?php echo $this->parse($this->section->description) ?>
			</div>
			<?php endif ?>
			<div<?php echo $colapse_style; ?> id="catid-<?php echo intval($this->section->id); ?>">
				<?php if (!empty($this->categories [$this->section->id])) : ?>
				<ul class="topiclist forums">
					<?php foreach ( $this->categories [$this->section->id] as $category ) {
						echo $this->displayCategory($category);
					} ?>
				</ul>
				<?php else : ?>
				<ul class="topiclist forums">
					<li class="row tk-nopost-info" style="padding:5px !important;">
						<span><?php echo '' . JText::_('COM_KUNENA_GEN_NOFORUMS') . ''; ?></span>
					</li>
				</ul>
				<?php endif; ?>
			</div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>
<?php endif; ?>