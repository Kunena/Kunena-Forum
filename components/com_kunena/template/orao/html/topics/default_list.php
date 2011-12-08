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
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post">
<div class="forumlist">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon flat">
						<dt><span class="ktitle"><?php echo $this->headerText ?></span></dt>
						<?php if ($this->params->get('countcolumnShow') != 0):?>
						<dd class="topics"><?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?></dd>
						<dd class="posts"><?php echo JText::_('COM_KUNENA_GEN_HITS');?></dd>
						<?php endif;?>
						<dd class="lastpost"><span><?php echo JText::_('COM_KUNENA_GEN_LAST_POST');?></span></dd>
					</dl>
				</li>
			</ul>
			<?php if (empty($this->topics )) : ?>
			<ul class="topiclist forums">
				<li class="row tk-nopost-info">
					<span><?php echo JText::_('COM_KUNENA_VIEW_RECENT_NO_TOPICS'); ?></span>
				</li>
			</ul>
			<?php else : ?>
			<ul class="topiclist forums">
				<?php $this->displayRows(); endif ?>
				<?php if ($this->topicActions) : ?>
				<li class="tk-modbox">
					<?php echo JHTML::_('select.genericlist', $this->topicActions, 'task', 'class="kinputbox" size="1"', 'value', 'text', 0, 'kmoderate-select');
					$options = array (JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
					echo JHTML::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="kinputbox" size="1" style="display:none;"', 'value', 'text', 0, 'kcategorytarget');?>
					<?php echo JText::_('COM_KUNENA_TEMPLATE_SELECT_ALL'); ?>
					<input id="kchecbox-all" type="checkbox" value="0" name="toggle" class="tk-kmoderate" />
					<input type="submit" name="" class="tk-go-button" value="<?php echo JText::_('COM_KUNENA_TOPICS_MODERATION_PERFORM') ?>" />
				</li>
				<?php endif ?>
			</ul>
			<span class="corners-bottom"><span></span></span>
		</div>
	</div>
	<input type="hidden" name="view" value="topics" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>